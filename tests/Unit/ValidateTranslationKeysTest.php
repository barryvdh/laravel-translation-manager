<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class ValidateTranslationKeysTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     */
    public function validate_translation_keys_test()
    {
        DB::table( 'ltm_translations' )->truncate();
        $this->artisan( 'translations:find' );

        DB::table( 'ltm_translations' )->whereNotNull( 'key' )->update( [ 'value' => 'test' ] );

        Artisan::command( 'translations:export --all', function () {} );

        $translations = DB::table( 'ltm_translations' )->whereNotNull( 'value' )->get();
        foreach ( $translations as $translation ){
            $this->assertIsString( trans( $translation->group . "." . $translation->key ), "Key[" . $translation->group . "." . $translation->key . "] has more than one result!" );
        }
    }
}
