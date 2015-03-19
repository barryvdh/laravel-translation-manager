<?php namespace Barryvdh\TranslationManager;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Events\Dispatcher;
use Barryvdh\TranslationManager\Models\Translation;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Finder\Finder;

class Manager
{

    /** @var \Illuminate\Foundation\Application */
    protected $app;
    /** @var \Illuminate\Filesystem\Filesystem */
    protected $files;
    /** @var \Illuminate\Events\Dispatcher */
    protected $events;

    protected $config;

    public
    function __construct(Application $app, Filesystem $files, Dispatcher $events)
    {
        $this->app = $app;
        $this->files = $files;
        $this->events = $events;
        $this->config = $app['config']['laravel-translation-manager::config'];
    }

    public
    function missingKey($namespace, $group, $key)
    {
        if (!in_array($group, $this->config['exclude_groups']))
        {
            Translation::firstOrCreate(array(
                'locale' => $this->app['config']['app.locale'],
                'group' => $group,
                'key' => $key,
            ));
        }
    }

    public
    function importTranslations($replace = false)
    {
        $counter = 0;
        foreach ($this->files->directories($this->app->make('path') . '/lang') as $langPath)
        {
            $locale = basename($langPath);

            foreach ($this->files->files($langPath) as $file)
            {

                $info = pathinfo($file);
                $group = $info['filename'];

                if (in_array($group, $this->config['exclude_groups']))
                {
                    continue;
                }

                $translations = array_dot(\Lang::getLoader()->load($locale, $group));
                foreach ($translations as $key => $value)
                {
                    $value = (string)$value;
                    $translation = Translation::firstOrNew(array(
                        'locale' => $locale,
                        'group' => $group,
                        'key' => $key,
                    ));

                    // Check if the database is different then the files, but set saved when importing a new translation
                    $newStatus = ($translation->value === $value || !$translation->exists) ? Translation::STATUS_SAVED : Translation::STATUS_CHANGED;
                    if ($newStatus !== (int)$translation->status)
                    {
                        $translation->status = $newStatus;
                    }

                    // Only replace when empty, or explicitly told so
                    if ($replace || !$translation->value)
                    {
                        $translation->value = $value;
                    }

                    $translation->save();

                    $counter++;
                }
            }
        }
        return $counter;
    }

    public
    function findTranslations($path = null)
    {

        $path = $path ?: $this->app['path'];
        $keys = array();
        $functions = array(
            'trans',
            'trans_choice',
            'Lang::get',
            'Lang::choice',
            'Lang::trans',
            'Lang::transChoice',
            '@lang',
            '@choice'
        );
        $pattern =                              // See http://regexr.com/392hu
            "(" . implode('|', $functions) . ")" .  // Must start with one of the functions
            "\(" .                               // Match opening parenthese
            "[\'\"]" .                           // Match " or '
            "(" .                                // Start a new group to match:
            "[a-zA-Z0-9_-]+" .               // Must start with group
            "([.][^\1)]+)+" .                // Be followed by one or more items/keys
            ")" .                                // Close group
            "[\'\"]" .                           // Closing quote
            "[\),]";                            // Close parentheses or new parameter

        // Find all PHP + Twig files in the app folder, except for storage
        $finder = new Finder();
        $finder->in($path)->exclude('storage')->name('*.php')->name('*.twig')->files();

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($finder as $file)
        {
            // Search the current file for the pattern
            if (preg_match_all("/$pattern/siU", $file->getContents(), $matches))
            {
                // Get all matches
                foreach ($matches[2] as $key)
                {
                    $keys[] = $key;
                }
            }
        }
        // Remove duplicates
        $keys = array_unique($keys);

        // Add the translations to the database, if not existing.
        foreach ($keys as $key)
        {
            // Split the group and item
            list($group, $item) = explode('.', $key, 2);
            $this->missingKey('', $group, $item);
        }

        // Return the number of found translations
        return count($keys);
    }

    protected
    function formatForExport($trans, $indent = 0)
    {
        $ind = str_repeat(' ', $indent * 4);
        $text = '';
        if (is_array($trans))
        {
            $text .= "array(\n";
            $indT = $ind . str_repeat(' ', 4);
            $max = 0;
            foreach ($trans as $key => $val)
            {
                if (!is_int($key) && strlen($key) > $max) $max = strlen($key);
            }
            $max += (($max+2) & 3) ? 4-(($max+2) & 3): 0;

            foreach ($trans as $key => $val)
            {
                $val = $this->formatForExport($val, $indent + 1);
                if (is_int($key))
                {
                    $text .= $indT . $val . ",\n";
                }
                else
                {
                    $pad = str_repeat(' ', $max - strlen($key));
                    $text .= $indT . "'$key'$pad=> $val,\n";
                }
            }
            $text .= $ind . ")";
        }
        else
        {
            $trans = str_replace("'", "\\'", $trans);
            if (strpos($trans, "\n") !== false)
            {
                $text = "<<<'TEXT'\n$trans\nTEXT\n";
            }
            else
            {
                $text = "'$trans'";
            }
        }
        return $text;
    }

    public
    function exportTranslations($group)
    {
        if (!in_array($group, $this->config['exclude_groups']))
        {
            if ($group == '*')
                return $this->exportAllTranslations();

            $tree = $this->makeTree(Translation::where('group', $group)->whereNotNull('value')->get());

            foreach ($tree as $locale => $groups)
            {
                if (isset($groups[$group]))
                {
                    $translations = $groups[$group];
                    $path = $this->app->make('path') . '/lang.corr/' . $locale . '/' . $group . '.php';
                    $output = "<?php\n\nreturn " . $this->formatForExport($translations) . ";\n";
                    $this->files->put($path, $output);
                }
            }
            Translation::where('group', $group)->whereNotNull('value')->update(array('status' => Translation::STATUS_SAVED));
        }
    }

    public
    function exportAllTranslations()
    {
        $groups = Translation::whereNotNull('value')->select(DB::raw('DISTINCT `group`'))->get('group');

        foreach ($groups as $group)
        {
            $this->exportTranslations($group->group);
        }
    }

    public
    function cleanTranslations()
    {
        Translation::whereNull('value')->delete();
    }

    public
    function truncateTranslations()
    {
        Translation::truncate();
    }

    protected
    function makeTree($translations)
    {
        $array = array();
        foreach ($translations as $translation)
        {
            array_set($array[$translation->locale][$translation->group], $translation->key, $translation->value);
        }
        return $array;
    }

    public
    function getConfig($key = null)
    {
        if ($key == null)
        {
            return $this->config;
        }
        else
        {
            return $this->config[$key];
        }
    }
}
