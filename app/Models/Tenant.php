<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;
    
    protected $guarded = [];
    
    public $timestamps = true;
    protected $table = 'tenants';
    public $incrementing = true;
    
    protected $casts = [
        'is_active' => 'boolean',
        'central' => 'boolean',
        'data' => 'json',
    ];
    
    public function getDatabaseName()
    {
        return $this->tenancy_db_name;
    }
    
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'title',
            'tenancy_db_name',
            'is_active',
            'central',
            'data',
        ];
    }

    public function modules()
    {
        return $this->belongsToMany(
            \Modules\ModuleManagement\App\Models\Module::class,
            'module_tenants',
            'tenant_id',
            'module_id'
        )->withPivot('is_active')
        ->withTimestamps();
    }

}