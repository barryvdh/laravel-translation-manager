<?php

declare(strict_types=1);

use Barryvdh\TranslationManager\Controller;

Route::group(config('translation-manager.route'), function ($router) {
    $router->get('/view/{groupKey?}', [Controller::class, 'getView'])->where('groupKey', '.*')->name( 'translation-manager.group.list' );
    $router->get('/search', [Controller::class, 'getSearchResults'])->name( 'translation-manager.search' );
    $router->get('/detail/{groupKey}/{translationKey}', [Controller::class, 'getDetail'])->name( 'translation-manager.translation' );
    $router->get('/{groupKey?}', [Controller::class, 'getIndex'])->where('groupKey', '.*')->name( 'translation-manager.index');


    $router->post('/add/{groupKey}', [Controller::class, 'postAdd'])->where('groupKey', '.*')->name('translation-manager.translation.add');
    $router->post('/edit/{groupKey}', [Controller::class, 'postEdit'])->where('groupKey', '.*')->name('translation-manager.translation.edit');
    $router->post('/edit-all/{groupKey}/{translationKey}', [Controller::class, 'postEditAll'])->name('translation-manager.translation.edit-all');

    $router->post('/groups/add', [Controller::class, 'postAddGroup']);
    $router->post('/delete/{groupKey}/{translationKey}', [Controller::class, 'postDelete'])->where('groupKey', '.*');
    $router->post('/import', [Controller::class, 'postImport']);
    $router->post('/find', [Controller::class, 'postFind']);
    $router->post('/locales/add', [Controller::class, 'postAddLocale']);
    $router->post('/locales/remove', [Controller::class, 'postRemoveLocale']);
    $router->post('/publish/{groupKey}', [Controller::class, 'postPublish'])->where('groupKey', '.*');
    $router->post('/translate-missing', [Controller::class, 'postTranslateMissing']);
});
