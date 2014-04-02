<?php namespace Barryvdh\TranslationManager;

use Illuminate\Support\ServiceProvider;

class ManagerServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('barryvdh/laravel-translation-manager');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->app['translation-manager'] = $this->app->share(function ($app){
            $manager = $app->make('Barryvdh\TranslationManager\Manager');
            return $manager;
        });

        $this->app['command.translation-manager.reset'] = $this->app->share(function($app)
        {
            return new Console\TranslationResetCommand;
        });
        $this->commands('command.translation-manager.reset');

        $this->app['command.translation-manager.import'] = $this->app->share(function($app)
        {
            return new Console\TranslationImportCommand($app['translation-manager']);
        });
        $this->commands('command.translation-manager.import');

        $this->app['command.translation-manager.export'] = $this->app->share(function($app)
        {
            return new Console\TranslationExportCommand($app['translation-manager']);
        });
        $this->commands('command.translation-manager.export');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('translation-manager', 'command.translation-manager.reset', 'command.translation-manager.import', 'command.translation-manager.export');
	}

}
