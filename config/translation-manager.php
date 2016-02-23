<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Routes group config
    |--------------------------------------------------------------------------
    |
    | The default group settings for the elFinder routes.
    |
    */
    'route' => [
        'prefix' => 'translations',
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
	 * 	array(
	 *		'pagination',
	 *		'reminders',
	 *		'validation',
	 *	)
	 */
	'exclude_groups' => array(),

    /**
     * Laravel base path (path that should be scanned for translatable strings). Default is this installation itself.
     *
     * @type string
     */
    // 'base_path' => (__DIR__) . '/../../../some_path',

    /**
     * Laravel language directory path (path language files will be exported to). Default is this installation itself.
     *
     * @type string
     */
    // 'lang_path' => (__DIR__) . '/../../../some_path/resources/lang',

);
