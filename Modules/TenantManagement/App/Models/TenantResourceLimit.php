<?php

namespace Modules\TenantManagement\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\Tenant;

class TenantResourceLimit extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'tenant_id',
        'resource_type',
        'hourly_limit',
        'daily_limit',
        'monthly_limit',
        'concurrent_limit',
        'storage_limit_mb',
        'memory_limit_mb',
        'cpu_limit_percent',
        'connection_limit',
        'additional_settings',
        'is_active',
        'enforce_limit',
        'limit_action',
        'description',
    ];

    protected $casts = [
        'hourly_limit' => 'integer',
        'daily_limit' => 'integer',
        'monthly_limit' => 'integer',
        'concurrent_limit' => 'integer',
        'storage_limit_mb' => 'integer',
        'memory_limit_mb' => 'integer',
        'cpu_limit_percent' => 'decimal:2',
        'connection_limit' => 'integer',
        'additional_settings' => 'array',
        'is_active' => 'boolean',
        'enforce_limit' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'resource_type',
                'hourly_limit',
                'daily_limit',
                'monthly_limit',
                'is_active',
                'enforce_limit',
                'limit_action'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Kaynak türü seçenekleri
     */
    public static function getResourceTypes(): array
    {
        return [
            'api' => 'API İstekleri',
            'database' => 'Veritabanı Sorguları',
            'cache' => 'Cache Kullanımı',
            'storage' => 'Depolama Alanı',
            'ai' => 'Yapay Zeka Token',
            'cpu' => 'CPU Kullanımı',
            'memory' => 'RAM Kullanımı',
            'connections' => 'Eşzamanlı Bağlantılar'
        ];
    }

    /**
     * Limit aksiyonu seçenekleri
     */
    public static function getLimitActions(): array
    {
        return [
            'block' => 'Engelle',
            'throttle' => 'Yavaşlat',
            'warn' => 'Uyar',
            'queue' => 'Kuyruğa Al'
        ];
    }

    /**
     * Belirli bir limit türü için varsayılan değerler
     */
    public static function getDefaultLimits(string $resourceType): array
    {
        $defaults = [
            'api' => [
                'hourly_limit' => 1000,
                'daily_limit' => 10000,
                'monthly_limit' => 100000,
                'concurrent_limit' => 10,
            ],
            'database' => [
                'hourly_limit' => 5000,
                'daily_limit' => 50000,
                'monthly_limit' => 500000,
                'concurrent_limit' => 20,
            ],
            'cache' => [
                'memory_limit_mb' => 256,
                'concurrent_limit' => 100,
            ],
            'storage' => [
                'storage_limit_mb' => 1024, // 1GB
                'monthly_limit' => 10240, // 10GB/ay
            ],
            'ai' => [
                'hourly_limit' => 5000,
                'daily_limit' => 25000,
                'monthly_limit' => 100000,
            ],
            'cpu' => [
                'cpu_limit_percent' => 25.00,
                'concurrent_limit' => 5,
            ],
            'memory' => [
                'memory_limit_mb' => 512,
                'concurrent_limit' => 10,
            ],
            'connections' => [
                'connection_limit' => 50,
                'concurrent_limit' => 10,
            ],
        ];

        return $defaults[$resourceType] ?? [];
    }

    /**
     * Tenant için tüm varsayılan limitler oluştur
     */
    public static function createDefaultLimitsForTenant(int $tenantId): void
    {
        foreach (self::getResourceTypes() as $type => $name) {
            self::updateOrCreate(
                ['tenant_id' => $tenantId, 'resource_type' => $type],
                array_merge(
                    self::getDefaultLimits($type),
                    [
                        'is_active' => true,
                        'enforce_limit' => true,
                        'limit_action' => 'throttle',
                        'description' => $name . ' için varsayılan limit'
                    ]
                )
            );
        }
    }

    /**
     * Limit kontrolü yap
     */
    public function checkLimit(int $currentUsage, string $period = 'hourly'): array
    {
        $limitField = $period . '_limit';
        $limit = $this->{$limitField};

        if (!$limit || !$this->is_active || !$this->enforce_limit) {
            return ['exceeded' => false, 'remaining' => null];
        }

        $exceeded = $currentUsage >= $limit;
        $remaining = max(0, $limit - $currentUsage);

        return [
            'exceeded' => $exceeded,
            'remaining' => $remaining,
            'limit' => $limit,
            'action' => $exceeded ? $this->limit_action : null
        ];
    }

    /**
     * Scope: Aktif limitler
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Zorunlu limitler
     */
    public function scopeEnforced($query)
    {
        return $query->where('enforce_limit', true);
    }

    /**
     * Scope: Kaynak türüne göre
     */
    public function scopeByResourceType($query, string $resourceType)
    {
        return $query->where('resource_type', $resourceType);
    }
}