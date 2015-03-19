<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSrcReferenceColumnToTranslations extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public
    function up()
    {
        Schema::table('ltm_translations', function (Blueprint $table)
        {
            $table->string('source', 256)->nullable();
            $table->unique(['locale','group','key'], 'ixk_ltm_translations_locale_group_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public
    function down()
    {
        Schema::table('ltm_translations', function (Blueprint $table)
        {
            $table->dropColumn('source');
            $table->dropIndex('ixk_ltm_translations_locale_group_key');
        });
    }
}
