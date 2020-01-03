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
    
    /**
     * Enable/Disable pagination.
     */
    'pagination_enabled' => true,
    
    /**
     * pagination per page limit.
     */
    'per_page' => 10,

    /**
     * Mysql table used in model for insertion and all other interactions.
     */
    'database' => [
        'translations_table' => 'ltm_translations',
    ],
    
    /**
     * Mysql tables List coming in change table's dropdown on view.
     */
    'translations_table_list' => [
        0 => 'ltm_translations',
        1 => 'book_translations',
    ],
    
];
