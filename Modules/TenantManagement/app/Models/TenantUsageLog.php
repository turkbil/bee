<?php

namespace Modules\TenantManagement\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Tenant;
use Carbon\Carbon;

class TenantUsageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'resource_type',
        'usage_count',
        'cpu_usage_percent',
        'memory_usage_mb',
        'storage_usage_mb',
        'db_queries',
        'api_requests',
        'cache_size_mb',
        'active_connections',
        'response_time_ms',
        'additional_metrics',
        'status',
        'notes',
        'recorded_at',
    ];

    protected $casts = [
        'usage_count' => 'integer',
        'cpu_usage_percent' => 'decimal:2',
        'memory_usage_mb' => 'integer',
        'storage_usage_mb' => 'integer',
        'db_queries' => 'integer',
        'api_requests' => 'integer',
        'cache_size_mb' => 'integer',
        'active_connections' => 'integer',
        'response_time_ms' => 'decimal:2',
        'additional_metrics' => 'array',
        'recorded_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Status seçenekleri
     */
    public static function getStatusTypes(): array
    {
        return [
            'normal' => 'Normal',
            'warning' => 'Uyarı',
            'critical' => 'Kritik',
            'blocked' => 'Engellendi'
        ];
    }

    /**
     * Kaynak kullanımı kaydet
     */
    public static function recordUsage(int $tenantId, string $resourceType, array $metrics): self
    {
        return self::create([
            'tenant_id' => $tenantId,
            'resource_type' => $resourceType,
            'usage_count' => $metrics['usage_count'] ?? 0,
            'cpu_usage_percent' => $metrics['cpu_usage_percent'] ?? null,
            'memory_usage_mb' => $metrics['memory_usage_mb'] ?? null,
            'storage_usage_mb' => $metrics['storage_usage_mb'] ?? null,
            'db_queries' => $metrics['db_queries'] ?? null,
            'api_requests' => $metrics['api_requests'] ?? null,
            'cache_size_mb' => $metrics['cache_size_mb'] ?? null,
            'active_connections' => $metrics['active_connections'] ?? null,
            'response_time_ms' => $metrics['response_time_ms'] ?? null,
            'additional_metrics' => $metrics['additional_metrics'] ?? null,
            'status' => self::determineStatus($metrics),
            'notes' => $metrics['notes'] ?? null,
            'recorded_at' => now(),
        ]);
    }

    /**
     * Metriklere göre status belirle
     */
    private static function determineStatus(array $metrics): string
    {
        // CPU kullanımı kontrolü
        if (isset($metrics['cpu_usage_percent'])) {
            if ($metrics['cpu_usage_percent'] > 90) return 'critical';
            if ($metrics['cpu_usage_percent'] > 70) return 'warning';
        }

        // Memory kullanımı kontrolü
        if (isset($metrics['memory_usage_mb'])) {
            if ($metrics['memory_usage_mb'] > 1000) return 'critical';
            if ($metrics['memory_usage_mb'] > 700) return 'warning';
        }

        // Response time kontrolü
        if (isset($metrics['response_time_ms'])) {
            if ($metrics['response_time_ms'] > 5000) return 'critical';
            if ($metrics['response_time_ms'] > 2000) return 'warning';
        }

        return 'normal';
    }

    /**
     * Tenant için belirli süredeki kullanım özeti
     */
    public static function getTenantUsageSummary(int $tenantId, int $hours = 24): array
    {
        $since = Carbon::now()->subHours($hours);
        
        $logs = self::where('tenant_id', $tenantId)
            ->where('recorded_at', '>=', $since)
            ->get();

        $summary = [];
        
        foreach (TenantResourceLimit::getResourceTypes() as $type => $name) {
            $typeLogs = $logs->where('resource_type', $type);
            
            $summary[$type] = [
                'name' => $name,
                'total_usage' => $typeLogs->sum('usage_count'),
                'avg_cpu' => $typeLogs->avg('cpu_usage_percent'),
                'avg_memory' => $typeLogs->avg('memory_usage_mb'),
                'avg_response_time' => $typeLogs->avg('response_time_ms'),
                'peak_connections' => $typeLogs->max('active_connections'),
                'status_counts' => [
                    'normal' => $typeLogs->where('status', 'normal')->count(),
                    'warning' => $typeLogs->where('status', 'warning')->count(),
                    'critical' => $typeLogs->where('status', 'critical')->count(),
                    'blocked' => $typeLogs->where('status', 'blocked')->count(),
                ],
                'last_recorded' => $typeLogs->max('recorded_at'),
            ];
        }

        return $summary;
    }

    /**
     * Sistem geneli kullanım istatistikleri
     */
    public static function getSystemWideSummary(int $hours = 24): array
    {
        $since = Carbon::now()->subHours($hours);
        
        return [
            'total_logs' => self::where('recorded_at', '>=', $since)->count(),
            'active_tenants' => self::where('recorded_at', '>=', $since)->distinct('tenant_id')->count(),
            'avg_cpu_usage' => self::where('recorded_at', '>=', $since)->avg('cpu_usage_percent'),
            'avg_memory_usage' => self::where('recorded_at', '>=', $since)->avg('memory_usage_mb'),
            'avg_response_time' => self::where('recorded_at', '>=', $since)->avg('response_time_ms'),
            'status_distribution' => [
                'normal' => self::where('recorded_at', '>=', $since)->where('status', 'normal')->count(),
                'warning' => self::where('recorded_at', '>=', $since)->where('status', 'warning')->count(),
                'critical' => self::where('recorded_at', '>=', $since)->where('status', 'critical')->count(),
                'blocked' => self::where('recorded_at', '>=', $since)->where('status', 'blocked')->count(),
            ],
        ];
    }

    /**
     * Eski logları temizle
     */
    public static function cleanupOldLogs(int $keepDays = 30): int
    {
        $cutoffDate = Carbon::now()->subDays($keepDays);
        
        return self::where('recorded_at', '<', $cutoffDate)->delete();
    }

    /**
     * Scope: Belirli tarih aralığı
     */
    public function scopeBetweenDates($query, Carbon $start, Carbon $end)
    {
        return $query->whereBetween('recorded_at', [$start, $end]);
    }

    /**
     * Scope: Kaynak türüne göre
     */
    public function scopeByResourceType($query, string $resourceType)
    {
        return $query->where('resource_type', $resourceType);
    }

    /**
     * Scope: Status'a göre
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Kritik durumlar
     */
    public function scopeCritical($query)
    {
        return $query->whereIn('status', ['critical', 'blocked']);
    }

    /**
     * Scope: Son kayıtlar
     */
    public function scopeRecent($query, int $minutes = 60)
    {
        return $query->where('recorded_at', '>=', Carbon::now()->subMinutes($minutes));
    }
}