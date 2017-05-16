<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('ltm_translations', function(Blueprint $table)
        {
            $table->increments('id');
            $table->tinyInteger('status')->default(0);
            $table->string('locale', 32);
            $table->string('group', 64);
            $table->string('key', 128);
            $table->text('value')->nullable();
            $table->timestamps();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('ltm_translations');
	}

}
