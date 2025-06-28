<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains, LogsActivity;
    
    protected $guarded = [];
    
    public $timestamps = true;
    protected $table = 'tenants';
    public $incrementing = true;
    
    protected $casts = [
        'is_active' => 'boolean',
        'central' => 'boolean',
        'data' => 'array',
        'theme_id' => 'integer',
    ];
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'is_active', 'data'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    
    public function domains()
    {
        return $this->hasMany(\Stancl\Tenancy\Database\Models\Domain::class, 'tenant_id', 'id');
    }

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
            'theme_id',
            'admin_default_language',
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

    /**
     * Site languages relationship - tenant context'te çalışır
     */
    public function siteLanguages()
    {
        // Central tenant ise ana veritabanından, değilse tenant'ın kendi veritabanından
        if ($this->central) {
            // Central tenant - ana veritabanından al
            return \Modules\LanguageManagement\app\Models\SiteLanguage::on('mysql')->query();
        } else {
            // Normal tenant - tenant veritabanından al
            return \Modules\LanguageManagement\app\Models\SiteLanguage::query();
        }
    }

    /**
     * Module settings relationship
     */
    public function moduleSettings()
    {
        // Basit fallback - data field'ından module ayarlarını döndür
        $data = $this->data ?? [];
        return (object) ($data['module_settings'] ?? []);
    }
}