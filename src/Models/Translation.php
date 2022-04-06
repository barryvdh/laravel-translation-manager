<?php

namespace Barryvdh\TranslationManager\Models;

use App\Models\BaseModel;
use DB;
use Illuminate\Database\Eloquent\Model;

/**
 * Translation model.
 *
 * @property int $id
 * @property int $status
 * @property string  $locale
 * @property string  $group
 * @property string  $key
 * @property string  $value
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Translation extends BaseModel
{
    public const STATUS_SAVED = 0;

    public const STATUS_CHANGED = 1;

    protected $table = 'ltm_translations';

    protected $connection = 'mysql';

    public function __construct()
    {
        $this->connection = config('translation-manager.database.connection');
        $this->table = config('translation-manager.database.table');
    }

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function scopeOfTranslatedGroup($query, $group)
    {
        return $query->where('group', $group)->whereNotNull('value');
    }

    public function scopeOrderByGroupKeys($query, $ordered)
    {
        if ($ordered) {
            $query->orderBy('group')->orderBy('key');
        }

        return $query;
    }

    public function scopeSelectDistinctGroup($query)
    {
        $select = '';

        switch (DB::getDriverName()) {
            case 'mysql':
                $select = 'DISTINCT `group`';
                break;
            default:
                $select = 'DISTINCT "group"';
                break;
        }

        return $query->select(DB::raw($select));
    }
}
