<?php

namespace Barryvdh\TranslationManager;

use Barryvdh\TranslationManager\Models\Translation;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Controller extends BaseController
{
    /** @var Manager */
    protected $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param  string  $group
     * @return Application|Factory|View
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getView($group = null)
    {
        return $this->getIndex($group);
    }

    /**
     * @param  string  $group
     * @return Application|Factory|View
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getIndex($group = null)
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
        $models = [];
        foreach (config('translation-manager.models') as $modelClass) {
            $modelTable = (new $modelClass)->getTable();
            $models[$modelTable] = $modelClass;
        }
        $models = ['' => 'Choose a model'] + $models;

        $numChanged = Translation::where('group', $group)->where('status', Translation::STATUS_CHANGED)->count();

        $allTranslations = Translation::where('group', $group)->orderBy('key', 'asc')->get();
        $numTranslations = count($allTranslations);
        $translations = [];
        foreach ($allTranslations as $translation) {
            $translations[$translation->key][$translation->locale] = $translation;
        }

        if ($this->manager->getConfig('pagination_enabled')) {
            $total = count($translations);
            $page = request()->get('page', 1);
            $perPage = $this->manager->getConfig('per_page');
            $offSet = ($page * $perPage) - $perPage;
            $itemsForCurrentPage = array_slice($translations, $offSet, $perPage, true);
            $prefix = $this->manager->getConfig('route')['prefix'];
            $path = url("$prefix/view/$group");

            if ($this->manager->getConfig('template') === 'bootstrap3') {
                LengthAwarePaginator::useBootstrapThree();
            } elseif ($this->manager->getConfig('template') === 'bootstrap4') {
                LengthAwarePaginator::useBootstrap();
            } elseif ($this->manager->getConfig('template') === 'bootstrap5') {
                LengthAwarePaginator::useBootstrap();
            }

            $paginator = new LengthAwarePaginator($itemsForCurrentPage, $total, $perPage, $page);
            $translations = $paginator->withPath($path);
        }

        return view('translation-manager::'.$this->manager->getConfig('template').'.index')
            ->with('translations', $translations)
            ->with('locales', $locales)
            ->with('groups', $groups)
            ->with('models', $models)
            ->with('group', $group)
            ->with('selectedModel', null)
            ->with('numTranslations', $numTranslations)
            ->with('numChanged', $numChanged)
            ->with('editUrl', $group ? action('\Barryvdh\TranslationManager\Controller@postEdit', [$group]) : null)
            ->with('paginationEnabled', $this->manager->getConfig('pagination_enabled'))
            ->with('deleteEnabled', $this->manager->getConfig('delete_enabled'));
    }

    /**
     * @param  string  $selectedModel
     * @return Application|Factory|View
     */
    public function getModelView($selectedModel = null)
    {
        if (empty($selectedModel)) {
            return $this->getIndex();
        }

        $locales = $this->manager->getLocales();
        $models = [];
        foreach (config('translation-manager.models') as $modelClass) {
            $modelTable = (new $modelClass)->getTable();
            $models[$modelTable] = $modelClass;
        }
        $models = $models === [] ? [] : ['' => 'Choose a model'] + $models;

        $allTranslationModels = (new $models[$selectedModel])->newQuery()->get();
        $translations = [];
        $numModelTranslations = count($allTranslationModels);
        $numTranslations = 0;
        $translatableSource = config('translation-manager.model-field-source');
        foreach ($allTranslationModels as $translationModel) {
            /* @var Model $translationModel */
            foreach ((new $models[$selectedModel])->$translatableSource as $field) {
                foreach ($locales as $locale) {
                    $translationValues = json_decode($translationModel->getAttributes()[$field] ?? '' ?: '{}', true) ?: [];

                    $translations[$translationModel->getKey()][$field][$locale] = empty($translationValues[$locale]) ? '' : $translationValues[$locale];
                }
                $numTranslations++;
            }
        }

        if ($this->manager->getConfig('pagination_enabled')) {
            $total = count($translations);
            $page = request()->get('page', 1);
            $perPage = $this->manager->getConfig('per_page');
            $offSet = ($page * $perPage) - $perPage;
            $itemsForCurrentPage = array_slice($translations, $offSet, $perPage, true);
            $prefix = $this->manager->getConfig('route')['prefix'];
            $path = url("$prefix/model/$selectedModel");

            if ($this->manager->getConfig('template') === 'bootstrap3') {
                LengthAwarePaginator::useBootstrapThree();
            } elseif ($this->manager->getConfig('template') === 'bootstrap4') {
                LengthAwarePaginator::useBootstrap();
            } elseif ($this->manager->getConfig('template') === 'bootstrap5') {
                LengthAwarePaginator::useBootstrap();
            }

            $paginator = new LengthAwarePaginator($itemsForCurrentPage, $total, $perPage, $page);
            $translations = $paginator->withPath($path);
        }

        return view('translation-manager::'.$this->manager->getConfig('template').'.index')
            ->with('translations', $translations)
            ->with('locales', $locales)
            ->with('models', $models)
            ->with('group', null)
            ->with('selectedModel', $selectedModel)
            ->with('numModelTranslations', $numModelTranslations)
            ->with('numTranslations', $numTranslations)
            ->with('editUrl', action('\Barryvdh\TranslationManager\Controller@postEditModel', [$selectedModel]))
            ->with('paginationEnabled', $this->manager->getConfig('pagination_enabled'))
            ->with('deleteEnabled', $this->manager->getConfig('delete_enabled'));
    }

    protected function loadLocales(): array
    {
        // Set the default locale as the first one.
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

    public function postAdd($group = null): RedirectResponse
    {
        $keys = explode("\n", request()->get('keys'));

        foreach ($keys as $key) {
            $key = trim($key);
            if ($group && $key) {
                $this->manager->missingKey('*', $group, $key);
            }
        }

        return redirect()->back();
    }

    public function postEdit($group = null)
    {
        if (! in_array($group, $this->manager->getConfig('exclude_groups'), true)) {
            $name = request()->get('name');
            $value = request()->get('value');

            [$locale, $key] = explode('|', $name, 2);
            $translation = Translation::firstOrNew([
                'locale' => $locale,
                'group' => $group,
                'key' => $key,
            ]);
            $translation->value = (string) $value ?: null;
            $translation->status = Translation::STATUS_CHANGED;
            $translation->save();

            return ['status' => 'ok'];
        }

        return null;
    }

    public function postEditModel($selectedModel)
    {
        $models = [];
        foreach (config('translation-manager.models') as $modelClass) {
            $modelTable = (new $modelClass)->getTable();
            $models[$modelTable] = $modelClass;
        }

        if (array_key_exists($selectedModel, $models)) {
            $name = request()->get('name');
            $value = request()->get('value');

            [$locale, $field, $key] = explode('|', $name, 3);

            /* @var Model $model */
            $model = (new $models[$selectedModel])->findOrFail($key);
            $translationValues = json_decode($model->getAttributes()[$field] ?? '' ?: '{}', true) ?: [];
            $translationValues[$locale] = $value ? (string) $value : null;

            $model->setRawAttributes([
                $field => json_encode($translationValues),
            ]);
            $model->save();

            return ['status' => 'ok'];
        }

        return null;
    }

    public function postDelete($group, $key)
    {
        if ($this->manager->getConfig('delete_enabled') && ! in_array($group, $this->manager->getConfig('exclude_groups'), true)) {
            Translation::where('group', $group)->where('key', $key)->delete();

            return ['status' => 'ok'];
        }

        return null;
    }

    public function postImport(Request $request): array
    {
        $replace = $request->get('replace', false);
        $counter = $this->manager->importTranslations($replace);

        return ['status' => 'ok', 'counter' => $counter];
    }

    public function postFind(): array
    {
        $numFound = $this->manager->findTranslations();

        return ['status' => 'ok', 'counter' => (int) $numFound];
    }

    public function postPublish($group = null): array
    {
        $json = false;

        if ($group === '_json') {
            $json = true;
        }

        $this->manager->exportTranslations($group, $json);

        return ['status' => 'ok'];
    }

    public function postAddGroup(Request $request): RedirectResponse
    {
        $group = str_replace('.', '', $request->input('new-group'));
        if ($group) {
            return redirect()->action('\Barryvdh\TranslationManager\Controller@getView', $group);
        }

        return redirect()->back();
    }

    /**
     * @throws FileNotFoundException
     */
    public function postAddLocale(Request $request): RedirectResponse
    {
        $locales = $this->manager->getLocales();
        $newLocale = str_replace([], '-', trim($request->input('new-locale')));
        if (! $newLocale || in_array($newLocale, $locales, true)) {
            return redirect()->back();
        }
        $this->manager->addLocale($newLocale);

        return redirect()->back();
    }

    /**
     * @throws FileNotFoundException
     */
    public function postRemoveLocale(Request $request): RedirectResponse
    {
        foreach ($request->input('remove-locale', []) as $locale => $val) {
            $this->manager->removeLocale($locale);
        }

        return redirect()->back();
    }

    public function postTranslateMissing(Request $request): RedirectResponse
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
                        'group' => $group,
                    ]
                );
            }

            return redirect()->back();
        }

        return redirect()->back();
    }
}
