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
	 * Determine output arrays in lang files, not support php < 5.4
 	 * By default, its using array syntax php < 5.4, (standart var_export function)
	 * If true, set short version of array and 4 space delimeter
	 *
	 * @type boolean
	 *
	 */
	'beauty_arrays' => false
);
