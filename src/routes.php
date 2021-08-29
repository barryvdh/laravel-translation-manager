<?php

declare(strict_types=1);


$config = array_merge(config('translation-manager.route'), ['namespace' => '\Barryvdh\TranslationManager']);
Route::group($config, function($router)
{
    $router->get('view/{groupKey?}', [\Barryvdh\TranslationManager\Controller::class,'getView'])->where('groupKey', '.*');
    $router->get('/{groupKey?}', [\Barryvdh\TranslationManager\Controller::class,'getIndex'])->where('groupKey', '.*');
    $router->post('/add/{groupKey}', [\Barryvdh\TranslationManager\Controller::class,'postAdd'])->where('groupKey', '.*');
    $router->post('/edit/{groupKey}', [\Barryvdh\TranslationManager\Controller::class,'postEdit'])->where('groupKey', '.*');
    $router->post('/groups/add', [\Barryvdh\TranslationManager\Controller::class,'postAddGroup']);
    $router->post('/delete/{groupKey}/{translationKey}', [\Barryvdh\TranslationManager\Controller::class,'postDelete'])->where('groupKey', '.*');
    $router->post('/import', [\Barryvdh\TranslationManager\Controller::class,'postImport']);
    $router->post('/find', [\Barryvdh\TranslationManager\Controller::class,'postFind']);
    $router->post('/locales/add', [\Barryvdh\TranslationManager\Controller::class,'postAddLocale']);
    $router->post('/locales/remove', [\Barryvdh\TranslationManager\Controller::class,'postRemoveLocale']);
    $router->post('/publish/{groupKey}', [\Barryvdh\TranslationManager\Controller::class,'postPublish'])->where('groupKey', '.*');
    $router->post('/translate-missing', [\Barryvdh\TranslationManager\Controller::class,'postTranslateMissing']);
});
