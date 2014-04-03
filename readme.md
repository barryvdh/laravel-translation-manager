## Laravel Translation Manager

This is a package to manage Laravel translation files.
It does not replace the Translation system, only import/export the php files to a database and make them editable through a webinterface.
The workflow would be:

    - Import translations: Read alle translation files and save them in the databse
    - Optionally: Listen to missing translation with the custom Translator
    - Translate all keys through the webinterface
    - Export: Write all translations back to the translation files.

This way, translations can be saved in git history and no overhead is introduced in production.

## Installation

Require this package in your composer.json and run composer update (or run `composer require barryvdh/laravel-translation-manager:*` directly):

    "barryvdh/laravel-translation-manager": "*"

After updating composer, add the ServiceProvider to the providers array in app/config/app.php

    'Barryvdh\TranslationManager\ManagerServiceProvider',

You need to run the migrations for this package

    $ php artisan migrate --package="barryvdh/laravel-translation-manager"

You have to add the Controller to your routes.php, so you can set your own url/filters.

     Route::controller('translations', 'Barryvdh\TranslationManager\Controller');

This example will make the translatio manager availbale at `http://yourdomain.com/translations`

## Usage

### Import command

The import command will search through app/lang and load all strings in the database, so you can easily manage them.

    $ php artisan translations:import
    
Note: this will override existing translations!

### Web interface

When you have imported your translation, you can view them in the webinterface (on the url you defined the with the controller).
You can click on a translation and an edit field will popup. Just click save and it is saved :)
When a translation is not yet created in a different locale, you can also just edit it to create it.

### Export command

The export command will write the contents of the database back to app/lang php files.
This will overwrite existing translations and remove all comments, so make sure to backup your data before using.
Supply the group name to define which groups you want to publish.

    $ php artisan translations:export <group>

For example, `php artisan translations:export reminders` when you have 2 locales (en/nl), will write to `app/lang/en/reminders.php` and `app/lang/nl/reminders.php`

### Clean command

The clean command will search for all translation that are NULL and delete them, so your interface is a bit cleaner. Note: empty translations are never exported.

    $ php artisan translations:clean

### Reset command

The reset command simply clears all translation in the database, so you can start fresh (by a new import). Make sure to export your work if needed before doing this.

    $ php artisan translations:reset


### Detect missing translations

To detect missing translations, we can swap the Laravel TranslationServicepProvider with a custom provider.
In your config/app.php, comment out the original TranslationServiceProvider and add the one from this package:

    //'Illuminate\Translation\TranslationServiceProvider',
    'Barryvdh\TranslationManager\TranslationServiceProvider',

This will extend the Translator and will create a new database entry, whenever a key is not found, so you have to visit the pages that use them.
This way it shows up in the webinterface and can be edited and later exported.
You shouldn't use this in production, just in production to translate your views, then just switch back.

## TODO

This package is still very alpha. Few thinks that are on the todo-list:

    - Add locales/groups via webinterface
    - Import/export via webinterface
    - Improve webinterface (more selection/filtering, behavior of popup after save etc)
    - Seed existing languages (https://github.com/caouecs/Laravel4-lang)
    - Suggestions are welcome :)
