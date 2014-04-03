<?php namespace Barryvdh\TranslationManager;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Barryvdh\TranslationManager\Models\Translation;

class Controller extends BaseController
{

    public function getIndex($group = null)
    {
        $locales = $this->loadLocales();
        $groups = Translation::groupBy('group')->lists('group', 'group');
        $groups = array(''=>'Choose a group') + $groups;

        $translations = array();
        foreach(Translation::where('group', $group)->get() as $translation){
            $translations[$translation->key][$translation->locale] = $translation;
        }

        return \View::make('laravel-translation-manager::index')
            ->with('translations', $translations)
            ->with('locales', $locales)
            ->with('groups', $groups)
            ->with('group', $group)
            ->with('editUrl', URL::action(get_class($this).'@postEdit', [$group]))
        ;
    }

    protected function loadLocales()
    {
        //Set the default locale as the first one.
        $locales = array_merge(array(Config::get('app.locale')), Translation::groupBy('locale')->lists('locale'));
        return array_unique($locales);
    }

    public function postEdit($group)
    {
        $name = Input::get('name');
        $value = Input::get('value');

        list($locale, $key) = explode('|', $name, 2);
        $translation = Translation::firstOrNew(array(
            'locale' => $locale,
            'group' => $group,
            'key' => $key,
        ));
        $translation->value = (string) $value ?: null;
        $translation->save();
        return array('status' => 'ok');
    }

    public function postDelete($group)
    {
        $key = Input::get('key');
        Translation::where('group', $group)->where('key', $key)->delete();
        return array('status' => 'ok');
    }

}
