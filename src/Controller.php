<?php namespace Barryvdh\TranslationManager;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Barryvdh\TranslationManager\Models\Translation;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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

         return view('translation-manager::index')
            ->with('translations', $translations)
            ->with('locales', $locales)
            ->with('groups', $groups)
            ->with('group', $group)
            ->with('numTranslations', $numTranslations)
            ->with('numChanged', $numChanged)
            ->with('editUrl', $group ? action('\Barryvdh\TranslationManager\Controller@postEdit', [$group]) : null)
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
            $translate = request()->get('translate');

            list($locale, $key) = explode('|', $name, 2);
            $translation = Translation::firstOrNew([
                'locale' => $locale,
                'group' => $group,
                'key' => $key,
            ]);

            if($translate === 'auto') {
                // https://github.com/fzaninotto/Faker#fakerprovideruseragent
                $userAgents = [
                    'Mozilla/5.0 (Windows CE) AppleWebKit/5350 (KHTML, like Gecko) Chrome/13.0.888.0 Safari/5350',
                    'Mozilla/5.0 (Macintosh; PPC Mac OS X 10_6_5) AppleWebKit/5312 (KHTML, like Gecko) Chrome/14.0.894.0 Safari/5312',
                    'Mozilla/5.0 (X11; Linuxi686; rv:7.0) Gecko/20101231 Firefox/3.6',
                    'Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10_7_1 rv:3.0; en-US) AppleWebKit/534.11.3 (KHTML, like Gecko) Version/4.0 Safari/534.11.3',
                    'Opera/8.25 (Windows NT 5.1; en-US) Presto/2.9.188 Version/10.00',
                    'Mozilla/5.0 (compatible; MSIE 7.0; Windows 98; Win 9x 4.90; Trident/3.0)',
                ];
                $translateClient = new TranslateClient('en', $locale, [
                    'headers' => [
                        'User-Agent' => $userAgents[array_rand($userAgents)]
                    ]
                ]);
                $value = $translateClient->translate($value);
                $value = preg_replace('#\[__(.+?)__\]#i', ':$1', $value);
            }
            $translation->value = (string) $value ?: null;
            $translation->status = Translation::STATUS_CHANGED;
            $translation->save();
            return array('status' => 'ok', 'value' => $value);
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

    public function downloadLangFiles()
    {
        return response()->download($this->manager->zipLangFiles(),'lang.zip')->deleteFileAfterSend(true);
    }

    public function getLocales()
    {
        return response()->json($this->manager->getLocales());
    }

    public function getLocaleTranslations($locale)
    {
        if(!in_array($locale, $this->manager->getLocales())) {
            return response()->json(['error' => 'Locale not found'])->setStatusCode(404);
        }

        if($this->manager->getConfig('api_use_database', false)) {
            return response()->json($this->manager->getTranslationsFromDatabase($locale));
        }

        return response()->json($this->manager->getTranslationsFromFiles($locale));
    }

    public function postTranslateMissing(Request $request){
        $locales = $this->manager->getLocales();
        $newLocale = str_replace([], '-', trim($request->input('new-locale')));
        if($request->has('with-translations') && $request->has('base-locale') && in_array($request->input('base-locale'),$locales) && $request->has('file') && in_array($newLocale, $locales)){
            $base_locale = $request->get('base-locale');
            $group = $request->get('file');
            $base_strings = Translation::where('group', $group)->where('locale', $base_locale)->get();
            foreach ($base_strings as $base_string) {
                $base_query = Translation::where('group', $group)->where('locale', $newLocale)->where('key', $base_string->key);
                if ($base_query->exists() && $base_query->whereNotNull('value')->exists()) {
                    // Translation already exists. Skip
                    continue;
                }
                $translated_text = Str::apiTranslateWithAttributes($base_string->value, $newLocale, $base_locale);
                request()->replace([
                    'value' => $translated_text,
                    'name' => $newLocale . '|' . $base_string->key,
                ]);
                app()->call(
                    'Barryvdh\TranslationManager\Controller@postEdit',
                    [
                        'group' => $group
                    ]
                );
            }
            return redirect()->back();
        }
        return redirect()->back();
    }
}
