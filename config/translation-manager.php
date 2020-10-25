<?php

return [

    /**
    * Routes group config
    *
    * The default group settings for the elFinder routes.
    */
    'route' => [
        'prefix' => 'translations',
        'middleware' => [
            'auth'
        ],
    ],
    
    /**
     * Enable API endpoints
     *
     * If you want to use this package in an API, you can expose the translations with this flag.
     * See php artisan route:list or ManagerServiceProvider.php for available routes.
     */
    'api_endpoints_enabled' => env('TRANSLATION_API_ENDPOINTS', false),

    /**
     * Api route settings
     *
     * You can set route group configuration here.
     * If a parameter is not provided the default translation group setting will be used.
     *
     */
    'api_route' => [
        'prefix' => 'locale',
        'middleware' => [

        ],
    ],

    /**
     * Set if the API endpoints should use the database content, instead of the default translation files
     * Warning: Setting this to true will allow the query of unpublished translations.
     */
    'api_use_database' => env('TRANSLATION_API_USE_DB', false),

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

];
