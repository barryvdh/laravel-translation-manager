<?php

namespace Barryvdh\TranslationManager\Models;

use Illuminate\Database\Eloquent\Model;

class TranslationNamespace extends Model
{
    protected $table = 'ltm_namespaces';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
