<?php namespace Barryvdh\TranslationManager;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Events\Dispatcher;
use Barryvdh\TranslationManager\Models\Translation;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Finder\Finder;

class Manager{

    /** @var \Illuminate\Foundation\Application  */
    protected $app;
    /** @var \Illuminate\Filesystem\Filesystem  */
    protected $files;
    /** @var \Illuminate\Events\Dispatcher  */
    protected $events;

    protected $config;

    public function __construct(Application $app, Filesystem $files, Dispatcher $events)
    {
        $this->app = $app;
        $this->files = $files;
        $this->events = $events;
        $this->config = $app['config']['translation-manager'];
    }

    public function missingKey($namespace, $group, $key)
    {
        if(!in_array($group, $this->config['exclude_groups'])) {
            Translation::firstOrCreate(array(
                'locale' => $this->app['config']['app.locale'],
                'group' => $group,
                'key' => $key,
            ));
        }
    }

    public function importTranslations($replace = false)
    {
        $counter = 0;
        foreach($this->files->directories($this->app['path.lang']) as $langPath){
            $locale = basename($langPath);

            foreach($this->files->allfiles($langPath) as $file) {

                $info = pathinfo($file);
                $group = $info['filename'];

                if(in_array($group, $this->config['exclude_groups'])) {
                    continue;
                }

                $subLangPath = str_replace($langPath . DIRECTORY_SEPARATOR, "", $info['dirname']);
                if ($subLangPath != $langPath) {
                    $group = $subLangPath . "/" . $group;
                }

                $translations = \Lang::getLoader()->load($locale, $group);
                if ($translations && is_array($translations)) {
                    foreach(array_dot($translations) as $key => $value){
                        // process only string values
                        if(is_array($value)){
                            continue;
                        }
                        $value = (string) $value;
                        $translation = Translation::firstOrNew(array(
                            'locale' => $locale,
                            'group' => $group,
                            'key' => $key,
                        ));

                        // Check if the database is different then the files
                        $newStatus = $translation->value === $value ? Translation::STATUS_SAVED : Translation::STATUS_CHANGED;
                        if($newStatus !== (int) $translation->status){
                            $translation->status = $newStatus;
                        }

                        // Only replace when empty, or explicitly told so
                        if($replace || !$translation->value){
                            $translation->value = $value;
                        }

                        $translation->save();

                        $counter++;
                    }
                }
            }
        }
        return $counter;
    }

    public function findTranslations($path = null)
    {
        $path = $path ?: base_path();
        $keys = array();
        $functions =  array('trans', 'trans_choice', 'Lang::get', 'Lang::choice', 'Lang::trans', 'Lang::transChoice', '@lang', '@choice', '__');
        $pattern =                              // See http://regexr.com/392hu
            "[^\w|>]".                          // Must not have an alphanum or _ or > before real method
            "(".implode('|', $functions) .")".  // Must start with one of the functions
            "\(".                               // Match opening parenthese
            "[\'\"]".                           // Match " or '
            "(".                                // Start a new group to match:
                "[a-zA-Z0-9_-]+".               // Must start with group
                "([.][^\1)]+)+".                // Be followed by one or more items/keys
            ")".                                // Close group
            "[\'\"]".                           // Closing quote
            "[\),]";                            // Close parentheses or new parameter

        // Find all PHP + Twig files in the app folder, except for storage
        $finder = new Finder();
        $finder->in($path)->exclude('storage')->name('*.php')->name('*.twig')->files();

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($finder as $file) {
            // Search the current file for the pattern
            if(preg_match_all("/$pattern/siU", $file->getContents(), $matches)) {
                // Get all matches
                foreach ($matches[2] as $key) {
                    $keys[] = $key;
                }
            }
        }
        // Remove duplicates
        $keys = array_unique($keys);

        // Add the translations to the database, if not existing.
        foreach($keys as $key){
            // Split the group and item
            list($group, $item) = explode('.', $key, 2);
            $this->missingKey('', $group, $item);
        }

        // Return the number of found translations
        return count($keys);
    }

    public function exportTranslations($group)
    {
        if(!in_array($group, $this->config['exclude_groups'])) {
            if($group == '*')
                return $this->exportAllTranslations();

            $tree = $this->makeTree(Translation::where('group', $group)->whereNotNull('value')->get());

            foreach($tree as $locale => $groups){
                if(isset($groups[$group])){
                    $translations = $groups[$group];
                    $path = $this->app['path.lang'].'/'.$locale.'/'.$group.'.php';
                    $output = "<?php\n\nreturn " . ($this->config['beauty_arrays'] ? $this->beautyPrintArray($translations) : var_export($translations, true)).";\n";
                
                    $this->files->put($path, $output);
                }
            }
            Translation::where('group', $group)->whereNotNull('value')->update(array('status' => Translation::STATUS_SAVED));
        }
    }

    private function beautyPrintArray($langValue, $indent="") {
        switch (gettype($langValue)) {
            case 'string':
                return str_replace("\"","'", '"' . addcslashes($langValue, "\\\"\r\n\t\v\f") . '"');
            case 'array':
                $indexed = array_keys($langValue) === range(0, count($langValue) - 1);
                $r = [];
                foreach ($langValue as $key => $value) {
                    $r[] = "$indent    "
                        . ($indexed ? "" : $this->beautyPrintArray($key) . " => ")
                        . $this->beautyPrintArray($value, "$indent    ");
                }
                return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
            default:
                return var_export($langValue, true);
        }
    }

    public function exportAllTranslations()
    {
        $select = '';

        switch (DB::getDriverName()) {
            case 'mysql':
                $select = 'DISTINCT `group`';
                break;

            default:
                $select = 'DISTINCT "group"';
                break;
        }

        $groups = Translation::whereNotNull('value')->select(DB::raw($select))->get('group');

        foreach($groups as $group){
            $this->exportTranslations($group->group);
        }
    }

    public function cleanTranslations()
    {
        Translation::whereNull('value')->delete();
    }

    public function truncateTranslations()
    {
        Translation::truncate();
    }

    protected function makeTree($translations)
    {
        $array = array();
        foreach($translations as $translation){
            array_set($array[$translation->locale][$translation->group], $translation->key, $translation->value);
        }
        return $array;
    }

    public function getConfig($key = null)
    {
        if($key == null) {
            return $this->config;
        }
        else {
            return $this->config[$key];
        }
    }

}
