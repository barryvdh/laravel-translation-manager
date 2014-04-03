<?php namespace Barryvdh\TranslationManager\Console;

use Illuminate\Console\Command;
use Barryvdh\TranslationManager\Models\Translation;

class ResetCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'translations:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all translation in the database';


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
