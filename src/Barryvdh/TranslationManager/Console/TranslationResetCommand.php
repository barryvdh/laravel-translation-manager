<?php namespace Barryvdh\TranslationManager\Console;

use Illuminate\Console\Command;
use Barryvdh\TranslationManager\Models\Translation;

class TranslationResetCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'translation:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset translations';


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        Translation::truncate();
        $this->info("All translations are deleted");

    }


}
