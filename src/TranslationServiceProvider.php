<?php
namespace Barryvdh\TranslationManager;

use Illuminate\Translation\TranslationServiceProvider as BaseTranslationServiceProvider;
use Barryvdh\TranslationManager\Exceptions\InvalidConfiguration;
use Barryvdh\TranslationManager\Models\Translation;

class TranslationServiceProvider extends BaseTranslationServiceProvider {


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->registerLoader();

        $this->app->singleton('translator', function($app)
        {
            $loader = $app['translation.loader'];

            // When registering the translator component, we'll need to set the default
            // locale as well as the fallback locale. So, we'll grab the application
            // configuration so we can easily get both of these values from there.
            $locale = $app['config']['app.locale'];

            $trans = new Translator($loader, $locale);

            $trans->setFallback($app['config']['app.fallback_locale']);

            if($app->bound('translation-manager')){
                $trans->setTranslationManager($app['translation-manager']);
            }

            return $trans;
        });

    }

    public static function determineTranslationModel(): string
    {
        $translationModel = config('translation-manager.translation_model') ?? Translation::class;

        if (! is_a($translationModel, Translation::class, true)) {
            throw InvalidConfiguration::modelIsNotValid($translationModel);
        }

        return $translationModel;
    }

    public static function getTranslationModelInstance(): Translation
    {
        $translationModelClassName = self::determineTranslationModel();

        return new $translationModelClassName();
    }


}
