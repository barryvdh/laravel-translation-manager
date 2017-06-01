<?php namespace Barryvdh\TranslationManager\Console;

use Barryvdh\TranslationManager\Manager;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ExportCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'translations:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export translations to PHP files';

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
        $json = $this->option('json');

        if (is_null($group) && !$json) {
            $this->warning("You must either specify a --group or export as -json");
            return;
        }

        if (!is_null($group) && $json) {
            $this->warning("You cannot use both --group argument and -json option at the same time");
            return;
        }

        $this->manager->exportTranslations($group, $json);

        $this->info("Done writing language files for " . (($group == '*') ? 'ALL groups' : $group . " group") );

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('group', InputArgument::OPTIONAL, 'The group to export (`*` for all).'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('json', "J", InputOption::VALUE_NONE, 'Export anonymous strings to JSON'),
        );
    }


}
