<?php
namespace Modules\ModuleManagement\App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Module extends Model
{
    use LogsActivity;

    protected $connection = 'mysql'; 
    protected $primaryKey = 'module_id';

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'version', 
        'settings',
        'type',
        'is_active'
    ];

    protected $casts = [
        'settings' => 'integer',
        'is_active' => 'boolean',
    ];

    public static function getTypes()
    {
        return self::whereNotNull('type')
            ->distinct()
            ->pluck('type')
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
    
    // Modül silinirken ilişkileri de sil
    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function($module) {
            $module->tenants()->detach();
        });
    }
}