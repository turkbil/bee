<?php
namespace Modules\ModuleManagement\App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Module extends BaseModel
{
    use SoftDeletes, LogsActivity;

    protected $connection = 'mysql'; // veya config('database.default')
    protected $primaryKey = 'module_id';

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'version', 
        'settings',
        'type',
        'group',
        'domains',
        'is_active'
    ];

    protected $casts = [
        'domains' => 'array',
        'settings' => 'integer',
        'is_active' => 'boolean',
    ];

    public static function getGroups()
    {
        return self::whereNotNull('group')
            ->distinct()
            ->pluck('group')
            ->toArray();
    }

    public function isDomainActive($domain)
    {
        if (!is_array($this->domains)) {
            return false;
        }
        
        return isset($this->domains[$domain]) && $this->domains[$domain];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Module');
    }
}