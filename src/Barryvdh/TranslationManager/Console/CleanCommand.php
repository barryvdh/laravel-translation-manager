<?php namespace Barryvdh\TranslationManager\Console;

use Barryvdh\TranslationManager\Models\Translation;
use Illuminate\Console\Command;

class CleanCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'translations:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean empty translations';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        Translation::whereNull('value')->delete();
        $this->info("Done cleaning translations");
    }

}
