<?php namespace Barryvdh\TranslationManager\Console;

use Barryvdh\TranslationManager\Manager;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Barryvdh\TranslationManager\Models\Translation;
use Illuminate\Filesystem\Filesystem;

class TranslationExportCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'translation:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export translations';

    /** @var \Barryvdh\TranslationManager\Manager  */
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
        $group = $this->argument('group');

        $this->manager->exportTranslations($group);

        $this->info("Done writing language files for group $group");

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('group', InputArgument::REQUIRED, 'The group to export.'),
        );
    }




}
