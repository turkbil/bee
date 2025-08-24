<?php

namespace Modules\TenantManagement\App\Services;

use Modules\TenantManagement\App\Models\TenantRateLimit;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class TenantRateLimitService
{
    private const CACHE_PREFIX = 'rate_limit:';
    private const VIOLATION_PREFIX = 'violation:';
    
    /**
     * Rate limit kontrolü yap
     */
    public function checkRateLimit(int $tenantId, string $ip, string $url, string $method): array
    {
        // Tenant için aktif rate limit kurallarını al
        $rules = TenantRateLimit::where('tenant_id', $tenantId)
            ->active()
            ->byPriority()
            ->get();

        $identifier = $this->generateIdentifier($tenantId, $ip);
        
        foreach ($rules as $rule) {
            if (!$rule->shouldApply($url, $method, $ip)) {
                continue;
            }

            // Her zaman dilimi için kontrol et
            $periods = ['minute', 'hour', 'day'];
            
            foreach ($periods as $period) {
                $currentRequests = $this->getCurrentRequestCount($identifier, $rule->id, $period);
                $check = $rule->checkRateLimit($identifier, $currentRequests, $period);
                
                if ($check['exceeded']) {
                    $this->logViolation($tenantId, $rule, $ip, $url, $method, $period);
                    
                    return [
                        'allowed' => false,
                        'rule_id' => $rule->id,
                        'period' => $period,
                        'limit' => $check['limit'],
                        'remaining' => $check['remaining'],
                        'reset_time' => $check['reset_time'],
                        'penalty_action' => $check['penalty_action'],
                        'penalty_duration' => $check['penalty_duration'],
                        'retry_after' => $this->calculateRetryAfter($period),
                    ];
                }
            }
        }

        return [
            'allowed' => true,
            'remaining' => null,
            'reset_time' => null,
        ];
    }

    /**
     * İstek sayısını artır
     */
    public function incrementRequestCount(int $tenantId, string $ip, string $url, string $method): void
    {
        $rules = TenantRateLimit::where('tenant_id', $tenantId)
            ->active()
            ->get();

        $identifier = $this->generateIdentifier($tenantId, $ip);
        
        foreach ($rules as $rule) {
            if (!$rule->shouldApply($url, $method, $ip)) {
                continue;
            }

            $this->incrementForRule($identifier, $rule->id);
        }
    }

    /**
     * Kural için sayacı artır
     */
    private function incrementForRule(string $identifier, int $ruleId): void
    {
        $redis = Redis::connection();
        $now = Carbon::now();
        
        // Dakika bazında
        $minuteKey = self::CACHE_PREFIX . "{$identifier}:rule_{$ruleId}:minute:" . $now->format('Y-m-d-H-i');
        $redis->incr($minuteKey);
        $redis->expire($minuteKey, 120); // 2 dakika tutulacak
        
        // Saat bazında
        $hourKey = self::CACHE_PREFIX . "{$identifier}:rule_{$ruleId}:hour:" . $now->format('Y-m-d-H');
        $redis->incr($hourKey);
        $redis->expire($hourKey, 7200); // 2 saat tutulacak
        
        // Gün bazında
        $dayKey = self::CACHE_PREFIX . "{$identifier}:rule_{$ruleId}:day:" . $now->format('Y-m-d');
        $redis->incr($dayKey);
        $redis->expire($dayKey, 172800); // 2 gün tutulacak
    }

    /**
     * Mevcut istek sayısını al
     */
    private function getCurrentRequestCount(string $identifier, int $ruleId, string $period): int
    {
        $redis = Redis::connection();
        $now = Carbon::now();
        
        $format = match($period) {
            'minute' => 'Y-m-d-H-i',
            'hour' => 'Y-m-d-H',
            'day' => 'Y-m-d',
            default => 'Y-m-d-H-i'
        };
        
        $key = self::CACHE_PREFIX . "{$identifier}:rule_{$ruleId}:{$period}:" . $now->format($format);
        
        return (int) $redis->get($key) ?: 0;
    }

    /**
     * Benzersiz identifier oluştur
     */
    private function generateIdentifier(int $tenantId, string $ip): string
    {
        return "tenant_{$tenantId}_ip_" . md5($ip);
    }

    /**
     * İhlali kaydet
     */
    private function logViolation(int $tenantId, TenantRateLimit $rule, string $ip, string $url, string $method, string $period): void
    {
        if (!$rule->log_violations) {
            return;
        }

        try {
            $violationKey = self::VIOLATION_PREFIX . "tenant_{$tenantId}:" . Carbon::now()->format('Y-m-d');
            $violation = [
                'tenant_id' => $tenantId,
                'rule_id' => $rule->id,
                'ip' => $ip,
                'url' => $url,
                'method' => $method,
                'period' => $period,
                'timestamp' => Carbon::now()->toISOString(),
            ];

            $redis = Redis::connection();
            $redis->lpush($violationKey, json_encode($violation));
            $redis->expire($violationKey, 86400 * 7); // 7 gün tutulacak
            
        } catch (\Exception $e) {
            \Log::error('Failed to log rate limit violation', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Retry after süresini hesapla
     */
    private function calculateRetryAfter(string $period): int
    {
        return match($period) {
            'minute' => 60,
            'hour' => 3600,
            'day' => 86400,
            default => 60
        };
    }

    /**
     * Tenant için ihlal geçmişi al
     */
    public function getViolationHistory(int $tenantId, int $days = 7): array
    {
        $violations = [];
        $redis = Redis::connection();
        
        for ($i = 0; $i < $days; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $key = self::VIOLATION_PREFIX . "tenant_{$tenantId}:{$date}";
            
            $dayViolations = $redis->lrange($key, 0, -1);
            
            foreach ($dayViolations as $violation) {
                $decoded = json_decode($violation, true);
                if ($decoded) {
                    $violations[] = $decoded;
                }
            }
        }

        return array_reverse($violations); // En yeniden eskiye
    }

    /**
     * IP'yi geçici olarak engelle
     */
    public function blockIp(int $tenantId, string $ip, int $duration = 3600): void
    {
        $key = "blocked_ip:tenant_{$tenantId}:" . md5($ip);
        $redis = Redis::connection();
        
        $redis->setex($key, $duration, json_encode([
            'ip' => $ip,
            'tenant_id' => $tenantId,
            'blocked_at' => Carbon::now()->toISOString(),
            'expires_at' => Carbon::now()->addSeconds($duration)->toISOString(),
        ]));
    }

    /**
     * IP'nin engellenip engellenmediğini kontrol et
     */
    public function isIpBlocked(int $tenantId, string $ip): array
    {
        $key = "blocked_ip:tenant_{$tenantId}:" . md5($ip);
        $redis = Redis::connection();
        
        $blockData = $redis->get($key);
        
        if (!$blockData) {
            return ['blocked' => false];
        }

        $data = json_decode($blockData, true);
        
        return [
            'blocked' => true,
            'blocked_at' => $data['blocked_at'],
            'expires_at' => $data['expires_at'],
            'remaining_seconds' => $redis->ttl($key),
        ];
    }

    /**
     * IP engelini kaldır
     */
    public function unblockIp(int $tenantId, string $ip): bool
    {
        $key = "blocked_ip:tenant_{$tenantId}:" . md5($ip);
        $redis = Redis::connection();
        
        return $redis->del($key) > 0;
    }

    /**
     * Tenant için rate limit istatistikleri
     */
    public function getRateLimitStats(int $tenantId, int $hours = 24): array
    {
        $violations = $this->getViolationHistory($tenantId, ceil($hours / 24));
        $since = Carbon::now()->subHours($hours);
        
        $recentViolations = array_filter($violations, function($v) use ($since) {
            return Carbon::parse($v['timestamp'])->gte($since);
        });

        // Grup işlemleri
        $byRule = [];
        $byIp = [];
        $byHour = [];
        
        foreach ($recentViolations as $violation) {
            $ruleId = $violation['rule_id'];
            $ip = $violation['ip'];
            $hour = Carbon::parse($violation['timestamp'])->format('H');
            
            $byRule[$ruleId] = ($byRule[$ruleId] ?? 0) + 1;
            $byIp[$ip] = ($byIp[$ip] ?? 0) + 1;
            $byHour[$hour] = ($byHour[$hour] ?? 0) + 1;
        }

        return [
            'total_violations' => count($recentViolations),
            'violations_by_rule' => $byRule,
            'violations_by_ip' => $byIp,
            'violations_by_hour' => $byHour,
            'most_violated_rule' => $byRule ? array_search(max($byRule), $byRule) : null,
            'most_violating_ip' => $byIp ? array_search(max($byIp), $byIp) : null,
            'peak_hour' => $byHour ? array_search(max($byHour), $byHour) : null,
        ];
    }

    /**
     * Rate limit cache'lerini temizle
     */
    public function clearRateLimitCache(int $tenantId, string $ip = null): void
    {
        $redis = Redis::connection();
        
        if ($ip) {
            $identifier = $this->generateIdentifier($tenantId, $ip);
            $pattern = self::CACHE_PREFIX . "{$identifier}:*";
        } else {
            $pattern = self::CACHE_PREFIX . "tenant_{$tenantId}_*";
        }
        
        $keys = $redis->keys($pattern);
        
        if (!empty($keys)) {
            $redis->del($keys);
        }
    }

    /**
     * Otomatik temizlik
     */
    public function cleanupExpiredData(): int
    {
        $redis = Redis::connection();
        $cleaned = 0;
        
        // Rate limit cache'leri (Redis TTL ile otomatik temizlenir)
        // Violation logları için temizlik
        $cutoffDate = Carbon::now()->subDays(30);
        
        for ($i = 30; $i < 60; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $pattern = self::VIOLATION_PREFIX . "*:{$date}";
            
            $keys = $redis->keys($pattern);
            if (!empty($keys)) {
                $cleaned += $redis->del($keys);
            }
        }

        return $cleaned;
    }
}