<?php

declare(strict_types=1);

$config = array_merge(config('translation-manager.route'), ['namespace' => 'Barryvdh\TranslationManager']);
Route::group($config, function ($router) use ($config) {
    $router->get('/locales/download', 'Controller@downloadLangFiles');
    $router->get('view/{groupKey?}', 'Controller@getView')->where('groupKey', '.*');
    $router->get('/{groupKey?}', 'Controller@getIndex')->where('groupKey', '.*');
    $router->post('/add/{groupKey}', 'Controller@postAdd')->where('groupKey', '.*');
    $router->post('/edit/{groupKey}', 'Controller@postEdit')->where('groupKey', '.*');
    $router->post('/groups/add', 'Controller@postAddGroup');
    $router->post('/delete/{groupKey}/{translationKey}', 'Controller@postDelete')->where('groupKey', '.*');
    $router->post('/import', 'Controller@postImport');
    $router->post('/find', 'Controller@postFind');
    $router->post('/locales/add', 'Controller@postAddLocale');
    $router->post('/locales/remove', 'Controller@postRemoveLocale');
    $router->post('/publish/{groupKey}', 'Controller@postPublish')->where('groupKey', '.*');
    $router->post('/translate-missing', 'Controller@postTranslateMissing');

    // Translation API routes
    if (config('translation-manager.api_endpoints_enabled', false)) {
        $apiConfig = array_merge($config, config('translation-manager.api_route', []));

        $router->group($apiConfig, function ($router) {
            $router->get('/locales', 'Controller@getLocales');
            $router->get('/locales/{slug}', 'Controller@getLocaleTranslations');
        });
    }
});
