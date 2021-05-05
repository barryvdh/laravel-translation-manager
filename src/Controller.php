<?php namespace Barryvdh\TranslationManager;

use Barryvdh\TranslationManager\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    /** @var \Barryvdh\TranslationManager\Manager */
    protected $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function getIndex($groupKey = null, $translationKey = null)
    {
        $locales = $this->manager->getLocales();
        $groups = Translation::groupBy('group');
        $excludedGroups = $this->manager->getConfig('exclude_groups');
        if ($excludedGroups) {
            $groups->whereNotIn('group', $excludedGroups);
        }

        $groups = $groups->select('group')->orderBy('group')->get()->pluck('group', 'group');
        if ($groups instanceof Collection) {
            $groups = $groups->all();
        }
        $groups = ['' => 'Choose a group'] + $groups;
        $numChanged = Translation::where('group', $groupKey)->where('status', Translation::STATUS_CHANGED)->count();


        $allTranslations = Translation::where('group', $groupKey);
        $allTranslations->orderBy('key', 'asc');

        $numTranslations = $allTranslations->count();
        /** @var \Illuminate\Database\Eloquent\Collection $allTranslations */
        $allTranslations = $allTranslations->get();

        $translations = [];
        foreach ($allTranslations as $translation) {
            $translations[$translation->key][$translation->locale] = $translation;
        }

        $prevTranslation = null;
        $nextTranslation = null;
        if ($translationKey) {
            $translationArrayKeys = array_keys($translations);

            $_index = array_search($translationKey, $translationArrayKeys);

            // find previous item
            if ($_index > 0) {
                $prevTranslation = [
                    "group" => $groupKey,
                    "key" => $translationArrayKeys[$_index - 1],
                ];
            }

            // find next item
            if ($_index < count($translationArrayKeys)) {
                $nextTranslation = [
                    "group" => $groupKey,
                    "key" => $translationArrayKeys[$_index + 1],
                ];
            }

            // replace array with one key only
            $newTranslations = [];
            $newTranslations[$translationKey] = $translations[$translationKey];
            $translations = $newTranslations;
        }

        return view('translation-manager::index')
            ->with('translations', $translations)
            ->with('locales', $locales)
            ->with('groups', $groups)
            ->with('group', $groupKey)
            ->with('key', $translationKey)
            ->with('nextTranslation', $nextTranslation)
            ->with('prevTranslation', $prevTranslation)
            ->with('numTranslations', $numTranslations)
            ->with('numChanged', $numChanged)
            ->with('editUrl', $groupKey ? route('translation-manager.translation.edit', ["groupKey" => $groupKey]) : null)
            ->with('deleteEnabled', $this->manager->getConfig('delete_enabled'));
    }

    public function getView($groupKey = null)
    {
        return $this->getIndex($groupKey);
    }

    public function getDetail($groupKey = null, $translationKey = null)
    {
        return $this->getIndex($groupKey, $translationKey);
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

    public function postAdd($groupKey = null)
    {
        $keys = explode("\n", request()->get('keys'));

        foreach ($keys as $key) {
            $key = trim($key);
            if ($groupKey && $key) {
                $this->manager->missingKey('*', $groupKey, $key);
            }
        }
        return redirect()->back();
    }

    public function postEdit($groupKey = null)
    {
        if (!in_array($groupKey, $this->manager->getConfig('exclude_groups'))) {
            $name = request()->get('name');
            $value = request()->get('value');

            list($locale, $key) = explode('|', $name, 2);
            $translation = Translation::firstOrNew([
                'locale' => $locale,
                'group' => $groupKey,
                'key' => $key,
            ]);
            $translation->value = (string) $value ?: null;
            $translation->status = Translation::STATUS_CHANGED;
            $translation->save();
            return array('status' => 'ok');
        }
    }

    public function postEditAll(Request $request, $groupKey, $translationKey)
    {
        if (!in_array($groupKey, $this->manager->getConfig('exclude_groups'))) {
            $values = request()->get('value');

            foreach ($values as $locale => $value) {
                $translation = Translation::firstOrNew([
                    'locale' => $locale,
                    'group' => $groupKey,
                    'key' => $translationKey,
                ]);

                if ((string) $translation->value != (string) $value) {
                    $translation->status = Translation::STATUS_CHANGED;
                }

                $translation->value = (string) $value ?? null;
                $translation->save();
            }
        }

        return back( )->with( 'successPublish', 'Saved!');
    }

    public function postDelete($groupKey, $key)
    {
        if (!in_array($groupKey, $this->manager->getConfig('exclude_groups')) && $this->manager->getConfig('delete_enabled')) {
            Translation::where('group', $groupKey)->where('key', $key)->delete();
            DB::table('ltm_translation_sources')->where('group', $groupKey)->where('key', $key)->delete();
            DB::table('ltm_translation_variables')->where('group', $groupKey)->where('key', $key)->delete();
            DB::table('ltm_translation_urls')->where('group', $groupKey)->where('key', $key)->delete();
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

    public function postPublish($groupKey = null)
    {
        $json = false;

        if ($groupKey === '_json') {
            $json = true;
        }

        $this->manager->exportTranslations($groupKey, $json);

        return ['status' => 'ok'];
    }

    public function postAddGroup(Request $request)
    {
        $group = str_replace(".", '', $request->input('new-group'));
        if ($group) {
            return redirect()->route('translation-manager.group.list', [ "groupKey" => $group ]);
        } else {
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

    public function postTranslateMissing(Request $request)
    {
        $locales = $this->manager->getLocales();
        $newLocale = str_replace([], '-', trim($request->input('new-locale')));
        if ($request->has('with-translations') && $request->has('base-locale') && in_array($request->input('base-locale'), $locales) && $request->has('file') && in_array($newLocale, $locales)) {
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
                    'name' => $newLocale.'|'.$base_string->key,
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
