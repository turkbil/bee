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
        'is_active'
    ];

    protected $casts = [
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

    public function tenants()
    {
        return $this->belongsToMany(
            \Stancl\Tenancy\Database\Models\Tenant::class,
            'module_tenants',
            'module_id',
            'tenant_id'
        )->withPivot('is_active')
        ->withTimestamps();
    }

    public function isDomainActive($domain)
    {
        $tenant = $this->tenants()->where('id', $domain)->first();
        return $tenant && $tenant->pivot->is_active;
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