<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLtmTranslationsSourcesTable
    extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ltm_translation_sources', function (Blueprint $table)
        {
            $table->bigIncrements('id');

            $table->string('group');
            $table->text('key');

            $table->string('file_path');
            $table->integer('file_line');

            $table->index(['group', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ltm_translation_sources');
    }
}
