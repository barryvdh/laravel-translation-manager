<?php namespace Barryvdh\TranslationManager;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Barryvdh\TranslationManager\Models\Translation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Pagination\LengthAwarePaginator;

class Controller extends BaseController
{
    /** @var \Barryvdh\TranslationManager\Manager  */
    protected $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function getIndex($group = null)
    {
        $locales = $this->manager->getLocales();
        $groups = Translation::groupBy('group');
        $excludedGroups = $this->manager->getConfig('exclude_groups');
        if($excludedGroups){
            $groups->whereNotIn('group', $excludedGroups);
        }

        $groups = $groups->select('group')->orderBy('group')->get()->pluck('group', 'group');
        if ($groups instanceof Collection) {
            $groups = $groups->all();
        }
        $groups = [''=>'Choose a group'] + $groups;
        $numChanged = Translation::where('group', $group)->where('status', Translation::STATUS_CHANGED)->count();


        $allTranslations = Translation::where('group', $group)->orderBy('key', 'asc')->get();
        $numTranslations = count($allTranslations);
        $translations = [];
        foreach($allTranslations as $translation){
            $translations[$translation->key][$translation->locale] = $translation;
        }
        
        if ($this->manager->getConfig('pagination_enabled')) {     
            $total = count($translations);
            $page = request()->get('page', 1);
            $per_page = $this->manager->getConfig('per_page');
            $offSet = ($page * $per_page) - $per_page;  
            $itemsForCurrentPage = array_slice($translations, $offSet, $per_page, true);
            $prefix = $this->manager->getConfig('route')['prefix'];
            $path = url("$prefix/view/$group");
            $paginator = new LengthAwarePaginator($itemsForCurrentPage, $total, $per_page, $page);
            $translations = $paginator->withPath($path);
        }

         return view('translation-manager::index')
            ->with('translations', $translations)
            ->with('locales', $locales)
            ->with('groups', $groups)
            ->with('group', $group)
            ->with('numTranslations', $numTranslations)
            ->with('numChanged', $numChanged)
            ->with('editUrl', action('\Barryvdh\TranslationManager\Controller@postEdit', [$group]))
            ->with('paginationEnabled', $this->manager->getConfig('pagination_enabled'))
            ->with('deleteEnabled', $this->manager->getConfig('delete_enabled'));
    }

    public function getView($group = null)
    {
        return $this->getIndex($group);
    }

    protected function loadLocales()
    {
        //Set the default locale as the first one.
        $locales = Translation::groupBy('locale')
            ->select('locale')
            ->get()
            ->pluck('locale');

        if ($locales instanceof Collection) {
            $locales = $locales->all();
        }
        $locales = array_merge([config('app.locale')], $locales);
        return array_unique($locales);
    }

    public function postAdd($group = null)
    {
        $keys = explode("\n", request()->get('keys'));

        foreach($keys as $key){
            $key = trim($key);
            if($group && $key){
                $this->manager->missingKey('*', $group, $key);
            }
        }
        return redirect()->back();
    }

    public function postEdit($group = null)
    {
        if(!in_array($group, $this->manager->getConfig('exclude_groups'))) {
            $name = request()->get('name');
            $value = request()->get('value');

            list($locale, $key) = explode('|', $name, 2);
            $translation = Translation::firstOrNew([
                'locale' => $locale,
                'group' => $group,
                'key' => $key,
            ]);
            $translation->value = (string) $value ?: null;
            $translation->status = Translation::STATUS_CHANGED;
            $translation->save();
            return array('status' => 'ok');
        }
    }

    public function postDelete($group = null, $key)
    {
        if(!in_array($group, $this->manager->getConfig('exclude_groups')) && $this->manager->getConfig('delete_enabled')) {
            Translation::where('group', $group)->where('key', $key)->delete();
            return ['status' => 'ok'];
        }
    }

    public function postImport(Request $request)
    {
        $replace = $request->get('replace', false);
        $counter = $this->manager->importTranslations($replace);

        return ['status' => 'ok', 'counter' => $counter];
    }

    public function postFind()
    {
        $numFound = $this->manager->findTranslations();

        return ['status' => 'ok', 'counter' => (int) $numFound];
    }

    public function postPublish($group = null)
    {
         $json = false;

        if($group === '_json'){
            $json = true;
        }

        $this->manager->exportTranslations($group, $json);

        return ['status' => 'ok'];
    }

    public function postAddGroup(Request $request)
    {
        $group = str_replace(".", '', $request->input('new-group'));
        if ($group)
        {
            return redirect()->action('\Barryvdh\TranslationManager\Controller@getView',$group);
        }
        else
        {
            return redirect()->back();
        }
    }

    public function postAddLocale(Request $request)
    {
        $locales = $this->manager->getLocales();
        $newLocale = str_replace([], '-', trim($request->input('new-locale')));
        if (!$newLocale || in_array($newLocale, $locales)) {
            return redirect()->back();
        }
        $this->manager->addLocale($newLocale);
        return redirect()->back();
    }

    public function postRemoveLocale(Request $request)
    {
        foreach ($request->input('remove-locale', []) as $locale => $val) {
            $this->manager->removeLocale($locale);
        }
        return redirect()->back();
    }

    public function postTranslateMissing(Request $request){
        $locales = $this->manager->getLocales();
        $newLocale = str_replace([], '-', trim($request->input('new-locale')));
        if($request->has('with-translations') && $request->has('base-locale') && in_array($request->input('base-locale'),$locales) && $request->has('file')){
            $json = false;
            $group = $request->get('file');
            if($group === '_json'){
                //$json = true;
                //$file_name = $newLocale.'_json';
                return redirect()->back();
            }else{
                $file_name = $group.'.php';
            }
            $this->manager->addLocale($newLocale);
            $this->manager->exportTranslations($group, $json);
            Artisan::call("translate:files",[
                '--baselocale' => $request->input('base-locale'),
                '--targetlocales' => $newLocale,
                '--targetfiles' => $file_name
            ]);
            $this->manager->importTranslations(false,null,$group);
            return redirect()->back();
        }
        return redirect()->back();
    }
    
    public function postChangeTable(Request $request)
    {
        $array = \Config::get('translation-manager');
        $array['database']['translations_table'] = $_POST['tb_translation'];
        $data = var_export($array, 1);
        if(\File::put(config_path() . '/translation-manager.php', "<?php\n return $data ;")) {
            $prefix = $this->manager->getConfig('route')['prefix'];
            return redirect("/$prefix");
        } 
        return redirect()->back();     
    }
    
}
