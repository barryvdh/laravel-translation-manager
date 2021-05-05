<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLtmTranslationsSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ltm_translation_sources', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('group');
            $table->text('key');

            $table->string( 'file_path' );
            $table->integer( 'file_line' );
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
