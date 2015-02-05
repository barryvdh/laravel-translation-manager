<?php namespace Barryvdh\TranslationManager;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;

class ManagerServiceProvider extends RouteServiceProvider {

    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'Barryvdh\TranslationManager';
    
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
     * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function boot(Router $router)
	{
	    parent::boot($router);
        
        $viewPath = __DIR__.'/../resources/views';
        $this->loadViewsFrom($viewPath, 'translation-manager');
        $this->publishes([
            $viewPath => base_path('resources/views/vendor/translation-manager'),
        ], 'views');
        
        $migrationPath = __DIR__.'/../database/migrations';
        $this->publishes([
            $migrationPath => base_path('database/migrations'),
        ], 'migrations');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
	    parent::register();
        
        // Register the config publish path
        $configPath = __DIR__ . '/../config/translation-manager.php';
        $this->mergeConfigFrom($configPath, 'translation-manager');
        $this->publishes([$configPath => config_path('translation-manager.php')]);
        
        $this->app['translation-manager'] = $this->app->share(function ($app){
            $manager = $app->make('Barryvdh\TranslationManager\Manager');
            return $manager;
        });

        $this->app['command.translation-manager.reset'] = $this->app->share(function($app)
        {
            return new Console\ResetCommand($app['translation-manager']);
        });
        $this->commands('command.translation-manager.reset');

        $this->app['command.translation-manager.import'] = $this->app->share(function($app)
        {
            return new Console\ImportCommand($app['translation-manager']);
        });
        $this->commands('command.translation-manager.import');
        
        $this->app['command.translation-manager.find'] = $this->app->share(function($app)
        {
            return new Console\FindCommand($app['translation-manager']);
        });
        $this->commands('command.translation-manager.find');

        $this->app['command.translation-manager.export'] = $this->app->share(function($app)
        {
            return new Console\ExportCommand($app['translation-manager']);
        });
        $this->commands('command.translation-manager.export');

        $this->app['command.translation-manager.clean'] = $this->app->share(function($app)
        {
            return new Console\CleanCommand($app['translation-manager']);
        });
        $this->commands('command.translation-manager.clean');
	}

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $config = $this->app['config']->get('translation-manager.route', []);
        $config['namespace'] = $this->namespace;

        $router = $this->app['Illuminate\Routing\Router'];
        $router->group($config, function($router)
        {
            $router->get('view/{group}', 'Controller@getView');
            $router->controller('/', 'Controller');
        });
    }
	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('translation-manager',
            'command.translation-manager.reset',
            'command.translation-manager.import',
            'command.translation-manager.find',
            'command.translation-manager.export',
            'command.translation-manager.clean'
        );
	}

}
