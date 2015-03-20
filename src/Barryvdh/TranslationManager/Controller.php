<?php namespace Barryvdh\TranslationManager;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Barryvdh\TranslationManager\Models\Translation;

class Controller extends BaseController
{
    /** @var \Barryvdh\TranslationManager\Manager */
    protected $manager;

    public
    function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public
    function getIndex($group = null)
    {
        $locales = $this->loadLocales();
        $groups = Translation::groupBy('group');
        $excludedGroups = $this->manager->getConfig('exclude_groups');
        if ($excludedGroups)
        {
            $groups->whereNotIn('group', $excludedGroups);
        }

        $groups = array('' => 'Choose a group') + $groups->lists('group', 'group');
        $numChanged = Translation::where('group', $group)->where('status', Translation::STATUS_CHANGED)->count();

        $allTranslations = Translation::where('group', $group)->orderBy('key', 'asc')->get();
        $numTranslations = count($allTranslations);
        $translations = array();
        foreach ($allTranslations as $translation)
        {
            $translations[$translation->key][$translation->locale] = $translation;
        }

        $stats = DB::select(<<<SQL
SELECT (mx.total_keys - lcs.total) missing, lcs.changed, lcs.locale, lcs.`group`
FROM
    (SELECT sum(total) total, sum(changed) changed, `group`, locale
     FROM
         (SELECT count(value) total, sum(status) changed, `group`, locale FROM ltm_translations lt GROUP BY `group`, locale
          UNION ALL
          SELECT DISTINCT 0, 0, `group`, locale FROM (SELECT DISTINCT locale FROM ltm_translations) lc
              CROSS JOIN (SELECT DISTINCT `group` FROM ltm_translations) lg) a
     GROUP BY `group`, locale) lcs
    JOIN (SELECT count(DISTINCT `key`) total_keys, `group` FROM ltm_translations GROUP BY `group`) mx
        ON lcs.`group` = mx.`group`
WHERE lcs.total < mx.total_keys OR lcs.changed > 0
SQL
        );

        // returned result set lists mising, changed, group, locale
        $summary = [];
        foreach ($stats as $stat)
        {
            if (!isset($summary[$stat->group]))
            {
                $item = $summary[$stat->group] = new \stdClass();
                $item->missing = '';
                $item->changed = '';
                $item->group = $stat->group;
            }
            $item = $summary[$stat->group];
            if ($stat->missing) $item->missing .= $stat->locale . ":" . $stat->missing . " ";
            if ($stat->changed) $item->changed .= $stat->locale . ":" . $stat->changed . " ";
        }

        // get mismatches
        $mismatches = DB::select(<<<SQL
SELECT DISTINCT lt.`group`, ft.*
FROM ltm_translations lt
    JOIN
    (SELECT DISTINCT mt.`key`, BINARY mt.ru ru, BINARY mt.en en
     FROM (SELECT lt.`group`, lt.`key`, group_concat(CASE lt.locale WHEN 'en' THEN VALUE ELSE NULL END) en, group_concat(CASE lt.locale WHEN 'ru' THEN VALUE ELSE NULL END) ru
           FROM (SELECT value, `group`, `key`, locale FROM ltm_translations
                 UNION ALL
                 SELECT NULL, `group`, `key`, locale FROM ((SELECT DISTINCT locale FROM ltm_translations) lc
                     CROSS JOIN (SELECT DISTINCT `group`, `key` FROM ltm_translations) lg)
                ) lt
           GROUP BY `group`, `key`) mt
         JOIN (SELECT lt.`group`, lt.`key`, group_concat(CASE lt.locale WHEN 'en' THEN VALUE ELSE NULL END) en, group_concat(CASE lt.locale WHEN 'ru' THEN VALUE ELSE NULL END) ru
               FROM (SELECT value, `group`, `key`, locale FROM ltm_translations
                     UNION ALL
                     SELECT NULL, `group`, `key`, locale FROM ((SELECT DISTINCT locale FROM ltm_translations) lc
                         CROSS JOIN (SELECT DISTINCT `group`, `key` FROM ltm_translations) lg)
                    ) lt
               GROUP BY `group`, `key`) ht ON mt.`key` = ht.`key`
     WHERE (mt.ru not like binary ht.ru AND mt.en like binary ht.en)
    ) ft
        ON (lt.locale = 'ru' AND lt.value LIKE BINARY ft.ru) AND lt.`key` = ft.key
ORDER BY `key`, `group`
SQL
        );

        // returned result set lists group key ru, en columns for the locale translations, ru has different values for same values in en
        return \View::make('laravel-translation-manager::index')
            ->with('translations', $translations)
            ->with('locales', $locales)
            ->with('groups', $groups)
            ->with('group', $group)
            ->with('numTranslations', $numTranslations)
            ->with('numChanged', $numChanged)
            ->with('editUrl', URL::action(get_class($this) . '@postEdit', array($group)))
            ->with('searchUrl', URL::action(get_class($this) . '@getSearch'))
            ->with('deleteEnabled', $this->manager->getConfig('delete_enabled'))
            ->with('stats', $summary)
            ->with('mismatches', $mismatches)
            ;
    }

    public
    function getSearch()
    {
        $q = \Input::get('q');
        $translations = Translation::where('key', 'like', "%$q%")->orWhere('value', 'like', "%$q%")->orderBy('group', 'asc')->orderBy('key', 'asc')->get();
        $numTranslations = count($translations);

        return \View::make('laravel-translation-manager::search')->with('translations', $translations)->with('numTranslations', $numTranslations);
    }

    protected
    function loadLocales()
    {
        //Set the default locale as the first one.
        $locales = array_merge(array(Config::get('app.locale')), Translation::groupBy('locale')->lists('locale'));
        return array_unique($locales);
    }

    public
    function postAdd($group)
    {
        $keys = explode("\n", Input::get('keys'));

        foreach ($keys as $key)
        {
            $key = trim($key);
            if ($group && $key)
            {
                $this->manager->missingKey('*', $group, $key);
            }
        }
        return Redirect::back();
    }

    public
    function postEdit($group)
    {
        if (!in_array($group, $this->manager->getConfig('exclude_groups')))
        {
            $name = Input::get('name');
            $value = Input::get('value');

            list($locale, $key) = explode('|', $name, 2);
            $translation = Translation::firstOrNew(array(
                'locale' => $locale,
                'group' => $group,
                'key' => $key,
            ));
            // strip off trailing spaces and eol's
            $value = trim((string)$value) ?: null;

            $translation->value = $value;
            $translation->status = $translation->isDirty() ? Translation::STATUS_CHANGED : Translation::STATUS_SAVED;
            $translation->save();
        }
        return array('status' => 'ok');
    }

    public
    function postDelete($group, $key)
    {
        if (!in_array($group, $this->manager->getConfig('exclude_groups')) && $this->manager->getConfig('delete_enabled'))
        {
            Translation::where('group', $group)->where('key', $key)->delete();
        }
        return array('status' => 'ok');
    }

    public
    function postImport()
    {
        $replace = Input::get('replace', false);
        $counter = $this->manager->importTranslations($replace);

        return Response::json(array('status' => 'ok', 'counter' => $counter));
    }

    public
    function postFind()
    {
        $numFound = $this->manager->findTranslations();

        return Response::json(array('status' => 'ok', 'counter' => (int)$numFound));
    }

    public
    function postPublish($group)
    {
        $this->manager->exportTranslations($group);

        return Response::json(array('status' => 'ok'));
    }
}
