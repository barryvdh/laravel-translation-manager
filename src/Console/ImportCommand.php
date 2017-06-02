<?php namespace Barryvdh\TranslationManager\Console;

use Barryvdh\TranslationManager\Manager;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class ImportCommand extends Command {

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
    protected $description = 'Import translations from the PHP sources';

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
        $replace = $this->option('replace');
        $json = $this->option('json');
        $counter = $this->manager->importTranslations($replace, $json);
        $this->info('Done importing, processed '.$counter. ' items!');

    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('replace', "R", InputOption::VALUE_NONE, 'Replace existing keys'),
            array('json', "J", InputOption::VALUE_NONE, 'Import from JSON translation files'),
        );
    }


}
