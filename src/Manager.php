<?php namespace Barryvdh\TranslationManager;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Events\Dispatcher;
use Barryvdh\TranslationManager\Models\Translation;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Finder\Finder;

class Manager{

    const JSON_GROUP = '_json';

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

        foreach ($this->files->directories($this->app['path.lang']) as $langPath) {
            $locale = basename($langPath);
            foreach ($this->files->allfiles($langPath) as $file) {
                $info = pathinfo($file);
                $group = $info['filename'];
                if (in_array($group, $this->config['exclude_groups'])) {
                    continue;
                }
                $subLangPath = str_replace($langPath . DIRECTORY_SEPARATOR, "", $info['dirname']);
                $subLangPath = str_replace(DIRECTORY_SEPARATOR, "/", $subLangPath);
                if ($subLangPath != $langPath) {
                    $group = $subLangPath . "/" . $group;
                }
                $translations = \Lang::getLoader()->load($locale, $group);
                if ($translations && is_array($translations)) {
                    foreach (array_dot($translations) as $key => $value) {
                        $importedTranslation = $this->importTranslation($key, $value, $locale, $group, $replace);
                        $counter += $importedTranslation ? 1 : 0;
                    }
                }
            }
        }

        foreach ($this->files->files($this->app['path.lang']) as $jsonTranslationFile) {
            if (strpos($jsonTranslationFile, '.json') === false) {
                continue;
            }
            $locale = basename($jsonTranslationFile, '.json');
            $group = self::JSON_GROUP;
            $translations = \Lang::getLoader()->load($locale, '*', '*'); // Retrieves JSON entries of the given locale only
            if ($translations && is_array($translations)) {
                foreach ($translations as $key => $value) {
                    $importedTranslation = $this->importTranslation($key, $value, $locale, $group, $replace);
                    $counter += $importedTranslation ? 1 : 0;
                }
            }
        }

        return $counter;
    }

    public function importTranslation($key, $value, $locale, $group, $replace = false) {
        // process only string values
        if (is_array($value)) {
            return false;
        }
        $value = (string)$value;
        $translation = Translation::firstOrNew(array(
            'locale' => $locale,
            'group'  => $group,
            'key'    => $key,
        ));

        // Check if the database is different then the files
        $newStatus = $translation->value === $value ? Translation::STATUS_SAVED : Translation::STATUS_CHANGED;
        if ($newStatus !== (int)$translation->status) {
            $translation->status = $newStatus;
        }

        // Only replace when empty, or explicitly told so
        if ($replace || !$translation->value) {
            $translation->value = $value;
        }

        $translation->save();
        return true;
    }

    public function findTranslations($path = null)
    {
        $path = $path ?: base_path();
        $groupKeys = array();
        $stringKeys = array();
        $functions =  array('trans', 'trans_choice', 'Lang::get', 'Lang::choice', 'Lang::trans', 'Lang::transChoice', '@lang', '@choice', '__');

        $groupPattern =                              // See http://regexr.com/392hu
            "[^\w|>]".                          // Must not have an alphanum or _ or > before real method
            "(".implode('|', $functions) .")".  // Must start with one of the functions
            "\(".                               // Match opening parenthesis
            "[\'\"]".                           // Match " or '
            "(".                                // Start a new group to match:
                "[a-zA-Z0-9_-]+".               // Must start with group
                "([.|\/][^\1)]+)+".             // Be followed by one or more items/keys
            ")".                                // Close group
            "[\'\"]".                           // Closing quote
            "[\),]";                            // Close parentheses or new parameter

        $stringPattern =
            "[^\w|>]".                                     // Must not have an alphanum or _ or > before real method
            "(".implode('|', $functions) .")".             // Must start with one of the functions
            "\(".                                          // Match opening parenthesis
            "(?P<quote>['\"])".                            // Match " or ' and store in {quote}
            "(?P<string>(?:\\\k{quote}|(?!\k{quote}).)*)". // Match any string that can be {quote} escaped
            "\k{quote}".                                   // Match " or ' previously matched
            "[\),]";                                       // Close parentheses or new parameter

        // Find all PHP + Twig files in the app folder, except for storage
        $finder = new Finder();
        $finder->in($path)->exclude('storage')->name('*.php')->name('*.twig')->name('*.vue')->files();

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($finder as $file) {
            // Search the current file for the pattern
            if(preg_match_all("/$groupPattern/siU", $file->getContents(), $matches)) {
                // Get all matches
                foreach ($matches[2] as $key) {
                    $groupKeys[] = $key;
                }
            }

            if(preg_match_all("/$stringPattern/siU", $file->getContents(), $matches)) {
                foreach ($matches['string'] as $key) {
                    if (preg_match("/(^[a-zA-Z0-9_-]+([.][^\1)\ ]+)+$)/siU", $key, $groupMatches)) {
                        // group{.group}.key format, already in $groupKeys but also matched here
                        // do nothing, it has to be treated as a group
                        continue;
                    }
                    $stringKeys[] = $key;
                }
            }
        }
        // Remove duplicates
        $groupKeys = array_unique($groupKeys);
        $stringKeys = array_unique($stringKeys);

        // Add the translations to the database, if not existing.
        foreach($groupKeys as $key) {
            // Split the group and item
            list($group, $item) = explode('.', $key, 2);
            $this->missingKey('', $group, $item);
        }

        foreach($stringKeys as $key){
            $group = self::JSON_GROUP;
            $item = $key;
            $this->missingKey('', $group, $item);
        }


        // Return the number of found translations
        return count($groupKeys + $stringKeys);
    }

    public function exportTranslations($group = null, $json = false)
    {
        if (!is_null($group) && !$json) {
            if (!in_array($group, $this->config['exclude_groups'])) {
                if ($group == '*')
                    return $this->exportAllTranslations();

                $tree = $this->makeTree(Translation::ofTranslatedGroup($group)->orderByGroupKeys(array_get($this->config, 'sort_keys', false))->get());

                foreach ($tree as $locale => $groups) {
                    if (isset($groups[$group])) {
                        $translations = $groups[$group];
                        $path = $this->app['path.lang'] . '/' . $locale;
                        if(!is_dir($path)){
                            mkdir($path, 0777, true);
                        }
                        $path = $path . '/' . $group . '.php';
                        
                        $output = "<?php\n\nreturn " . var_export($translations, true) . ";".\PHP_EOL;
                        $this->files->put($path, $output);
                    }
                }
                Translation::ofTranslatedGroup($group)->update(array('status' => Translation::STATUS_SAVED));
            }
        }

        if ($json) {
            $tree = $this->makeTree(Translation::ofTranslatedGroup(self::JSON_GROUP)->orderByGroupKeys(array_get($this->config, 'sort_keys', false))->get(), true);

            foreach($tree as $locale => $groups){
                if(isset($groups[self::JSON_GROUP])){
                    $translations = $groups[self::JSON_GROUP];
                    $path = $this->app['path.lang'].'/'.$locale.'.json';
                    $output = json_encode($translations, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE);
                    $this->files->put($path, $output);
                }
            }

            Translation::ofTranslatedGroup(self::JSON_GROUP)->update(array('status' => Translation::STATUS_SAVED));
        }
    }

    public function exportAllTranslations()
    {
        $groups = Translation::whereNotNull('value')->selectDistinctGroup()->get('group');

        foreach($groups as $group){
            if ($group == self::JSON_GROUP) {
                $this->exportTranslations(null, true);
            } else {
                $this->exportTranslations($group->group);
            }
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

    protected function makeTree($translations, $json = false)
    {
        $array = array();
        foreach($translations as $translation){
            if ($json) {
                $this->jsonSet($array[$translation->locale][$translation->group], $translation->key, $translation->value);
            } else {
                array_set($array[$translation->locale][$translation->group], $translation->key, $translation->value);
            }
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

    public function jsonSet(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $array[$key] = $value;

        return $array;
    }

}
