<?php

declare(strict_types=1);

use Barryvdh\TranslationManager\Controller;
use Illuminate\Support\Facades\Route;

$config = array_merge(config('translation-manager.route'), ['namespace' => '\Barryvdh\TranslationManager']);
Route::group($config, function ($router) {
    $router->get('/model/{selectedModel?}', [Controller::class, 'getModelView'])->where('selectedModel', '.*');
    $router->get('/view/{groupKey?}', [Controller::class, 'getView'])->where('groupKey', '.*');
    $router->get('/{groupKey?}', [Controller::class, 'getIndex'])->where('groupKey', '.*');
    $router->post('/add/{groupKey}', [Controller::class, 'postAdd'])->where('groupKey', '.*');
    $router->post('/model/edit/{selectedModel}', [Controller::class, 'postEditModel'])->where('selectedModel', '.*');
    $router->post('/edit/{groupKey}', [Controller::class, 'postEdit'])->where('groupKey', '.*');
    $router->post('/groups/add', [Controller::class, 'postAddGroup']);
    $router->post('/delete/{groupKey}/{translationKey}', [Controller::class, 'postDelete'])->where('groupKey', '.*');
    $router->post('/import', [Controller::class, 'postImport']);
    $router->post('/find', [Controller::class, 'postFind']);
    $router->post('/locales/add', [Controller::class, 'postAddLocale']);
    $router->post('/locales/remove', [Controller::class, 'postRemoveLocale']);
    $router->post('/publish/{groupKey}', [Controller::class, 'postPublish'])->where('groupKey', '.*');
    $router->post('/translate-missing', [Controller::class, 'postTranslateMissing']);
});
