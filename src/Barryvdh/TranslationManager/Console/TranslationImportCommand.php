<?php namespace Barryvdh\TranslationManager\Console;

use Barryvdh\TranslationManager\Manager;
use Illuminate\Console\Command;
use Barryvdh\TranslationManager\Models\Translation;

class TranslationImportCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'translations:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import translations';

    /** @var  \Barryvdh\TranslationManager\Manager  */
    protected $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $counter = $this->manager->importTranslations();

        $this->info('Done importing, processed '.$counter. ' items!');

    }


}
