<?php namespace Barryvdh\TranslationManager\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model{

    protected $table = 'ltm_translations';
    protected $guarded = array('id', 'created_at', 'updated_at');

}
