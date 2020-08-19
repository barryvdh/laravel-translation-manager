<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Routes group config
    |--------------------------------------------------------------------------
    |
    | The default group settings for the elFinder routes.
    |
    */
    'route'          => [
        'prefix'     => 'translations',
        'middleware' => 'auth',
    ],

    /**
     * Enable deletion of translations
     *
     * @type boolean
     */
    'delete_enabled' => true,

    /**
     * Exclude specific groups from Laravel Translation Manager.
     * This is useful if, for example, you want to avoid editing the official Laravel language files.
     *
     * @type array
     *
     *    array(
     *        'pagination',
     *        'reminders',
     *        'validation',
     *    )
     */
    'exclude_groups' => [],
    
     /**
     * Add additional file-extensions for the file-search than the standard twig, php and vue
     *
     * @type array
     *
     *    array(
     *        'js'
     *    )
     */
    'find_file_extensions' => [],
    
    /**
     * Disable Group Detection for the php artisan translations:find command
     * Can be useful if you have group signs in the translation string keys (like points)
     * 
     * @type boolean
     * 
     */
    'disable_group_detection_for_find'  => true,

    /**
     * Exclude specific languages from Laravel Translation Manager.
     *
     * @type array
     *
     *    array(
     *        'fr',
     *        'de',
     *    )
     */
    'exclude_langs'  => [],

    /**
     * Export translations with keys output alphabetically.
     */
    'sort_keys '     => false,

    /**
     * Exclude specific folders from the translations:find command
     *
     * @type array
     *
     *    array(
     *        'storage',
     *        'vendor',
     *    )
     */
    'find_exclude_folders'  => [
        'storage',
        'vendor'
    ],

    'trans_functions' => [
        'trans',
        'trans_choice',
        'Lang::get',
        'Lang::choice',
        'Lang::trans',
        'Lang::transChoice',
        '@lang',
        '@choice',
        '__',
        '$trans.get',
    ],

];
