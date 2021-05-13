<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLtmTranslationsUrlsTable
    extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ltm_translation_urls', function (Blueprint $table)
        {
            $table->bigIncrements('id');

            $table->string('group');
            $table->text('key');

            $table->string('url');

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
        Schema::dropIfExists('ltm_translation_urls');
    }
}
