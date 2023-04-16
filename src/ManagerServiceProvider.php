<?php

namespace Barryvdh\TranslationManager;

use Illuminate\Support\ServiceProvider;

class ManagerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/translation-manager.php', 'translation-manager');

        $this->app->singleton('translation-manager', Manager::class);

        $this->registerCommands([
            'reset' => Console\ResetCommand::class,
            'import' => Console\ImportCommand::class,
            'find' => Console\FindCommand::class,
            'export' => Console\ExportCommand::class,
            'clean' => Console\CleanCommand::class,
        ]);
    }

    private function registerCommands(array $commands): void
    {
        foreach ($commands as $name => $class) {
            $this->app->singleton("command.translation-manager.{$name}", function ($app) use ($class) {
                return new $class($app['translation-manager']);
            });

            $this->commands("command.translation-manager.{$name}");
        }
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'translation-manager');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/translation-manager'),
        ], 'views');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'migrations');

        $this->loadRoutesFrom(__DIR__.'/routes.php');
    }

    public function provides(): array
    {
        return [
            'translation-manager',
            'command.translation-manager.reset',
            'command.translation-manager.import',
            'command.translation-manager.find',
            'command.translation-manager.export',
            'command.translation-manager.clean',
        ];
    }
}
