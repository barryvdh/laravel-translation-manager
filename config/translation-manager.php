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
    'sort_keys'     => false,

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
     * if true finding new translations will be disabled while users browse application
     */
    'ignore_new_trans' => false,

    /**
     * When project does not user JSON alternative it can be ignored
     */
    'ignore_json' => true,

    /**
     * translations without source position will be marked as red
     */
    'warn_in_code' => false,

];
