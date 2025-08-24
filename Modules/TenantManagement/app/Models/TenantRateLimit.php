<?php

namespace Modules\TenantManagement\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\Tenant;

class TenantRateLimit extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'tenant_id',
        'endpoint_pattern',
        'method',
        'requests_per_minute',
        'requests_per_hour',
        'requests_per_day',
        'burst_limit',
        'concurrent_requests',
        'ip_whitelist',
        'ip_blacklist',
        'throttle_strategy',
        'penalty_duration',
        'penalty_action',
        'is_active',
        'log_violations',
        'priority',
        'description',
    ];

    protected $casts = [
        'requests_per_minute' => 'integer',
        'requests_per_hour' => 'integer',
        'requests_per_day' => 'integer',
        'burst_limit' => 'integer',
        'concurrent_requests' => 'integer',
        'ip_whitelist' => 'array',
        'ip_blacklist' => 'array',
        'penalty_duration' => 'integer',
        'is_active' => 'boolean',
        'log_violations' => 'boolean',
        'priority' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'endpoint_pattern',
                'method',
                'requests_per_minute',
                'requests_per_hour',
                'requests_per_day',
                'is_active',
                'penalty_action'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * HTTP metod seçenekleri
     */
    public static function getHttpMethods(): array
    {
        return [
            '*' => 'Tüm Metodlar',
            'GET' => 'GET',
            'POST' => 'POST',
            'PUT' => 'PUT',
            'PATCH' => 'PATCH',
            'DELETE' => 'DELETE',
            'HEAD' => 'HEAD',
            'OPTIONS' => 'OPTIONS'
        ];
    }

    /**
     * Throttle strateji seçenekleri
     */
    public static function getThrottleStrategies(): array
    {
        return [
            'fixed_window' => 'Sabit Pencere',
            'sliding_window' => 'Kayan Pencere',
            'token_bucket' => 'Token Kovası'
        ];
    }

    /**
     * Penalty aksiyon seçenekleri
     */
    public static function getPenaltyActions(): array
    {
        return [
            'block' => 'Engelle',
            'delay' => 'Geciktir',
            'queue' => 'Kuyruğa Al',
            'warn' => 'Uyar'
        ];
    }

    /**
     * Varsayılan rate limit kuralları
     */
    public static function getDefaultRules(): array
    {
        return [
            [
                'endpoint_pattern' => '/api/*',
                'method' => '*',
                'requests_per_minute' => 100,
                'requests_per_hour' => 1000,
                'requests_per_day' => 10000,
                'description' => 'API endpointleri için genel limit'
            ],
            [
                'endpoint_pattern' => '/admin/*',
                'method' => '*',
                'requests_per_minute' => 200,
                'requests_per_hour' => 2000,
                'requests_per_day' => 20000,
                'description' => 'Admin paneli için limit'
            ],
            [
                'endpoint_pattern' => '/api/auth/*',
                'method' => 'POST',
                'requests_per_minute' => 10,
                'requests_per_hour' => 50,
                'requests_per_day' => 200,
                'description' => 'Kimlik doğrulama endpointleri için sıkı limit'
            ],
            [
                'endpoint_pattern' => '*',
                'method' => '*',
                'requests_per_minute' => 300,
                'requests_per_hour' => 3000,
                'requests_per_day' => 30000,
                'description' => 'Genel web sayfaları için limit'
            ]
        ];
    }

    /**
     * Tenant için varsayılan rate limit kuralları oluştur
     */
    public static function createDefaultRulesForTenant(int $tenantId): void
    {
        $priority = 10;
        
        foreach (self::getDefaultRules() as $rule) {
            self::create(array_merge($rule, [
                'tenant_id' => $tenantId,
                'burst_limit' => intval($rule['requests_per_minute'] * 0.2), // %20 burst
                'concurrent_requests' => 10,
                'throttle_strategy' => 'sliding_window',
                'penalty_duration' => 60,
                'penalty_action' => 'delay',
                'is_active' => true,
                'log_violations' => true,
                'priority' => $priority--,
            ]));
        }
    }

    /**
     * IP adresinin whitelist'te olup olmadığını kontrol et
     */
    public function isIpWhitelisted(string $ip): bool
    {
        if (!$this->ip_whitelist || empty($this->ip_whitelist)) {
            return false;
        }

        return in_array($ip, $this->ip_whitelist);
    }

    /**
     * IP adresinin blacklist'te olup olmadığını kontrol et
     */
    public function isIpBlacklisted(string $ip): bool
    {
        if (!$this->ip_blacklist || empty($this->ip_blacklist)) {
            return false;
        }

        return in_array($ip, $this->ip_blacklist);
    }

    /**
     * Endpoint pattern ile URL eşleşmesini kontrol et
     */
    public function matchesEndpoint(string $url): bool
    {
        // Wildcard desteği
        if ($this->endpoint_pattern === '*') {
            return true;
        }

        // Basit wildcard pattern matching
        $pattern = str_replace(['*', '/'], ['.*', '\/'], $this->endpoint_pattern);
        $pattern = '/^' . $pattern . '$/i';

        return preg_match($pattern, $url) === 1;
    }

    /**
     * HTTP metod eşleşmesini kontrol et
     */
    public function matchesMethod(string $method): bool
    {
        return $this->method === '*' || strtoupper($this->method) === strtoupper($method);
    }

    /**
     * Rate limit kuralının bir isteğe uygulanıp uygulanmayacağını kontrol et
     */
    public function shouldApply(string $url, string $method, string $ip): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // IP blacklist kontrolü
        if ($this->isIpBlacklisted($ip)) {
            return true; // Blacklist'teki IP'ler her zaman kısıtlanır
        }

        // IP whitelist kontrolü
        if ($this->isIpWhitelisted($ip)) {
            return false; // Whitelist'teki IP'ler kısıtlanmaz
        }

        // Endpoint ve metod eşleşmesi
        return $this->matchesEndpoint($url) && $this->matchesMethod($method);
    }

    /**
     * Rate limit kontrolü yap
     */
    public function checkRateLimit(string $identifier, int $currentRequests, string $period = 'minute'): array
    {
        $limitField = 'requests_per_' . $period;
        $limit = $this->{$limitField};

        if (!$limit) {
            return ['exceeded' => false, 'remaining' => null];
        }

        $exceeded = $currentRequests >= $limit;
        $remaining = max(0, $limit - $currentRequests);

        return [
            'exceeded' => $exceeded,
            'remaining' => $remaining,
            'limit' => $limit,
            'penalty_action' => $exceeded ? $this->penalty_action : null,
            'penalty_duration' => $exceeded ? $this->penalty_duration : null,
            'reset_time' => $this->calculateResetTime($period)
        ];
    }

    /**
     * Reset zamanını hesapla
     */
    private function calculateResetTime(string $period): int
    {
        $now = time();
        
        return match($period) {
            'minute' => $now + (60 - ($now % 60)),
            'hour' => $now + (3600 - ($now % 3600)),
            'day' => strtotime('tomorrow midnight'),
            default => $now + 60
        };
    }

    /**
     * Scope: Aktif kurallar
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Öncelik sırasına göre
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    /**
     * Scope: Endpoint pattern'e göre
     */
    public function scopeByEndpoint($query, string $pattern)
    {
        return $query->where('endpoint_pattern', $pattern);
    }

    /**
     * Scope: HTTP metoda göre
     */
    public function scopeByMethod($query, string $method)
    {
        return $query->where('method', $method);
    }
}