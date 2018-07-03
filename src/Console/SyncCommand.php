<?php

namespace Barryvdh\TranslationManager\Console;

use Illuminate\Console\Command;
use Barryvdh\TranslationManager\Manager;
use Symfony\Component\Console\Input\InputOption;

class SyncCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'translations:sync';

    protected $signature = 'translations:sync '
        . '{--locales= : Locales to sync. Default: all locales, Example: en,hu}'
        . '{--url= : Source url with the api route config prefix} '
        . '{--simulate : Do not save the result, only display it} '
        . '{--export : Export all translation groups to php files before exit}'
    ;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize translations from a source';

    /** @var \Barryvdh\TranslationManager\Manager */
    protected $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if(!$this->option('url')) {
            $this->error('No url given!');
            return 1;
        }

        $this->info('Getting remote locales at: ' . $this->option('url') . '/locales');
        $remoteLocales = $this->manager->getLocalesFromUrl($this->option('url'));
        $this->info('Found remote locales: ' . implode(', ', $remoteLocales));

        $localLocales = $this->manager->getLocales();
        $localesToSync = explode(',', $this->option('locales'));

        if(is_null($localesToSync) || count($localesToSync) == 0 || array_first($localesToSync) == '') {
            $this->info('No locales to sync provided. Selecting all...');
            $localesToSync = $localLocales;
        }

        $this->info('Selected locales: ' . implode(', ', $localesToSync));

        $localesToSync = array_intersect($localesToSync, $remoteLocales);

        $this->info('Found local locales: ' . implode(', ', $localLocales));
        $this->info('Locales to sync: ' . implode(', ', $localesToSync));

        foreach ($localesToSync as $locale) {
            $this->info('');
            $this->info('Syncing locale: ' . $locale);

            $localTranslation = $this->manager->getTranslationsFromDatabase($locale);
            $remoteTranslation = $this->manager->getTranslationsFromUrl($this->option('url'), $locale);

            $table = [];
            $translationsToSave = [];

            $keys = array_intersect(array_keys($localTranslation), array_keys($remoteTranslation));
            foreach ($keys as $key) {
                $localValue = null;
                if(array_key_exists($key, $localTranslation)) {
                    $localValue = $localTranslation[$key];
                }

                $remoteValue = null;
                if(array_key_exists($key, $remoteTranslation)) {
                    $remoteValue = $remoteTranslation[$key];
                }

                if($remoteValue != $localValue) {
                    $translationsToSave[$key] = $remoteValue;
                    $table[$key] = [$key, str_limit($localValue, 75), str_limit($remoteValue, 75)];
                }
            }

            $this->info('Changed translations: ' . count($table));
            if(count($table) > 0) {
                $this->table(['Key', 'Local database translation', 'Remote translation'], $table);
            }

            if(!$this->option('simulate')) {
                $this->info('Saving...');
                $this->info('Saved: ' . $this->manager->saveTranslations($locale, $translationsToSave));
            }
        }

        $this->info('');
        $this->info('Sync finished!');
        $this->info('');

        if(!$this->option('simulate') && $this->option('export')) {
            $this->info('Exporting...');
            $this->call('translations:export', [
                'group' => '*',
            ]);
        }
    }
}
