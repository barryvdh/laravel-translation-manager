<?php namespace Barryvdh\TranslationManager;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Events\Dispatcher;
use Barryvdh\TranslationManager\Models\Translation;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;

class Manager{

    /** @var \Illuminate\Foundation\Application  */
    protected $app;
    /** @var \Illuminate\Filesystem\Filesystem  */
    protected $files;
    /** @var \Illuminate\Events\Dispatcher  */
    protected $events;

    public function __construct(Application $app, Filesystem $files, Dispatcher $events)
    {
        $this->app = $app;
        $this->files = $files;
        $this->events = $events;
    }

    public function missingKey($namespace, $group, $key)
    {
        Translation::firstOrCreate(array(
            'locale' => $this->app['config']['app.locale'],
            'group' => $group,
            'key' => $key,
        ));
    }

    public function importTranslations($replace = false)
    {
        $counter = 0;
        foreach($this->files->directories($this->app->make('path').'/lang') as $langPath){
            $locale = basename($langPath);

            foreach($this->files->files($langPath) as $file){

                $info = pathinfo($file);
                $group = $info['filename'];

                $translations = array_dot(\Lang::getLoader()->load($locale, $group));
                foreach($translations as $key => $value){
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
        return $counter;
    }

    public function exportTranslations($group)
    {
        if($group == '*')
            return $this->exportAllTranslations();

        $tree = $this->makeTree(Translation::where('group', $group)->whereNotNull('value')->get());

        foreach($tree as $locale => $groups){
            if(isset($groups[$group])){
                $translations = $groups[$group];
                $path = $this->app->make('path').'/lang/'.$locale.'/'.$group.'.php';
                $output = "<?php\n\nreturn ".var_export($translations, true).";\n";
                $this->files->put($path, $output);
            }
        }
        Translation::where('group', $group)->whereNotNull('value')->update(array('status' => Translation::STATUS_SAVED));
    }
    
    public function exportAllTranslations()
    {
        $groups = Translation::whereNotNull('value')->select(DB::raw('DISTINCT `group`'))->get('group');

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

}
