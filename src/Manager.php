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

    /** @var \Illuminate\Translation\LoaderInterface  */
    protected $loader;
    /** @var  array */
    protected $hints;

    /**
     * Manager constructor.
     * @param Application $app
     * @param Filesystem $files
     * @param Dispatcher $events
     */
    public function __construct(Application $app, Filesystem $files, Dispatcher $events)
    {
        $this->app = $app;
        $this->files = $files;
        $this->events = $events;
        $this->config = $app['config']['translation-manager'];
        $this->loader = \Lang::getLoader();
        $this->updateHints();
    }

    protected function updateHints()
    {
        if (method_exists(get_class($this->loader), 'getHints')) {
            $this->hints = $this->loader->getHints();
        }
    }

    /**
     * @param string|null $namespace
     * @param string $group
     * @param string $key
     */
    public function missingKey($namespace = null, $group, $key)
    {
        $group = ($namespace ? "{$namespace}::{$group}" : $group);
        if(!in_array($group, $this->config['exclude_groups']) && !in_array($namespace, $this->config['exclude_packages'])) {
            Translation::firstOrCreate(array(
                'locale' => $this->app['config']['app.locale'],
                'group' => $group,
                'key' => $key,
            ));
        }
    }

    /**
     * @param bool $replace
     * @return int
     */
    public function importTranslations($replace = false)
    {
        $counter = 0;

        $updated = [];
        $hint = null;
        foreach((new Finder())->in(array_merge([$this->app->langPath()], $this->hints))->files() as $file) {
            $pathname = str_replace([
                '\\'.$file->getRelativePathname(),
                '/'.$file->getRelativePathname()
            ], '', $file->getPathname());

            if ($this->hints) {
                $hint = in_array($pathname, $this->hints) ? array_search($pathname, $this->hints) : null;
            }
            $langPath = $file->getPath();
            $locale = basename($langPath);

            $info = pathinfo($file);
            $group = $info['filename'];
            $updated[] = $group;

            if (in_array(($hint ? "{$hint}::{$group}" : $group), $this->config['exclude_groups'])) {
                continue;
            }

            if ($this->hints && in_array($hint, $this->config['exclude_packages'])) {
                continue;
            }

            $subLangPath = str_replace($langPath . DIRECTORY_SEPARATOR, "", $info['dirname']);
            if ($subLangPath != $langPath) {
                $group = $subLangPath . "/" . $group;
            }

            $translations = $this->loader->load($locale, $group, $hint);
            if ($translations && is_array($translations)) {
                foreach(array_dot($translations) as $key => $value){
                    // process only string values
                    if(is_array($value)){
                        continue;
                    }
                    $value = (string) $value;
                    $translation = Translation::firstOrNew(array(
                        'locale' => $locale,
                        'group' => $hint ? "{$hint}::{$group}" : $group,
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

        return $counter;
    }

    /**
     * @param null|string $path
     * @return int
     */
    public function findTranslations($path = null)
    {
        $path = $path ?: base_path();
        $keys = array();
        $functions =  array('trans', 'trans_choice', 'Lang::get', 'Lang::choice', 'Lang::trans', 'Lang::transChoice', '@lang', '@choice');
        $pattern =                              // See http://regexr.com/3e8ap, (original: http://regexr.com/392hu)
            "[^\w|>]".                          // Must not have an alphanum or _ or > before real method
            "(".implode('|', $functions) .")".  // Must start with one of the functions
            "\(".                               // Match opening parenthese
            "[\'\"]".                           // Match " or '
            "(".                                // Start a new group to match:
            "[a-zA-Z0-9:_-]+".               // Must start with group
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
            if ($namespace = strstr($group, '::', true))
                $group = substr($group, strpos($group, '::') + 2);

            $this->missingKey($namespace, $group, $item);
        }

        // Return the number of found translations
        return count($keys);
    }

    /**
     * @param $group
     */
    public function exportTranslations($group)
    {
        if(!in_array($group, $this->config['exclude_groups'])) {
            if($group == '*')
                return $this->exportAllTranslations();

            $tree = $this->makeTree(Translation::where('group', $group)->whereNotNull('value')->get());

            $updatedLocales = [];
            foreach($tree as $locale => $groups){
                if(isset($groups[$group])){
                    $translations = $groups[$group];

                    $path = $this->app->langPath().'/'.$locale.'/'.$group.'.php';

                    if ($namespace = strstr($group, '::', true)) {
                        $nGroup = substr($group, strpos($group, '::') + 2);
                        if (array_key_exists($namespace, $this->hints))
                            $path = $this->hints[$namespace].'/'.$locale.'/'.$nGroup.'.php';
                    }

                    $output = "<?php\n\nreturn ".var_export($translations, true).";\n";

                    try {
                        //When using packages, not all of them already has multiple language
                        // folder created, we will do it for you
                        if(!$this->files->isDirectory(($dir = pathinfo($path, PATHINFO_DIRNAME))))
                            $this->files->makeDirectory($dir, intval('0755', 8), true);
                        $this->files->put($path, $output);
                        $updatedLocales[] = $locale;
                    } catch (\Exception $e) {
                        //In most cases php user doesn't have permission to write in vendor folder.
                        //@todo: return some kind of notification to console and
                        //@todo  web interface about skipped to save locales.
                    }
                }
            }

            Translation::where('group', $group)
                ->whereIn('locale', $updatedLocales)
                ->whereNotNull('value')
                ->update(array('status' => Translation::STATUS_SAVED));
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
