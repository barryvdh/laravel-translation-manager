<?php

namespace Barryvdh\TranslationManager\Console;

use Illuminate\Console\Command;
use Barryvdh\TranslationManager\Manager;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ExportCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'translations:export {group}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export translations to PHP files';

    /**
     * @var \Barryvdh\TranslationManager\Manager
     */
    protected $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $group = $this->option('all') ? '*' : $this->argument('group');
        $json = $this->option('json');

        if (is_null($group) && !$json) {
            $this->warn('You must either specify a group argument or export as --json');

            return;
        }

        if (!is_null($group) && $json) {
            $this->warn('You cannot use both group argument and --json option at the same time');

            return;
        }

        if ('*' === $group) {
            $this->manager->exportAllTranslations();
        } else {
            $this->manager->exportTranslations($group, $json);
        }

        if (!is_null($group)) {
            $this->info('Done writing language files for '.(('*' === $group) ? 'ALL groups' : $group.' group'));
        } elseif ($json) {
            $this->info('Done writing JSON language files for translation strings');
        }
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['group', InputArgument::OPTIONAL, 'The group to export (--all for all).'],
        ];
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['json', 'J', InputOption::VALUE_NONE, 'Export anonymous strings to JSON'],
            ['all', 'A', InputOption::VALUE_NONE, 'Export all groups'],
        ];
    }
}
