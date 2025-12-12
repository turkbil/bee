<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Activity Log Cache Service
 *
 * Slow query optimization için activity_log count sorgularını cache'ler
 * Original query: SELECT COUNT(*) FROM activity_log WHERE created_at > ? (280ms)
 * Cached query: ~5ms
 */
class ActivityLogCacheService
{
    const CACHE_TTL = 300; // 5 dakika
    const CACHE_PREFIX = 'activity_log_count_';

    /**
     * Get total activity log count with cache
     */
    public static function getTotalCount(): int
    {
        return Cache::remember(self::CACHE_PREFIX . 'total', self::CACHE_TTL, function () {
            return DB::table('activity_log')->count();
        });
    }

    /**
     * Get activity log count since a date with cache
     */
    public static function getCountSince(Carbon $date): int
    {
        $cacheKey = self::CACHE_PREFIX . 'since_' . $date->format('Y-m-d');

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($date) {
            return DB::table('activity_log')
                ->where('created_at', '>', $date)
                ->count();
        });
    }

    /**
     * Get activity log count for today with cache
     */
    public static function getTodayCount(): int
    {
        return self::getCountSince(now()->startOfDay());
    }

    /**
     * Get activity log count for this week with cache
     */
    public static function getThisWeekCount(): int
    {
        return self::getCountSince(now()->startOfWeek());
    }

    /**
     * Get activity log count for this month with cache
     */
    public static function getThisMonthCount(): int
    {
        return self::getCountSince(now()->startOfMonth());
    }

    /**
     * Get activity log count for this year with cache
     */
    public static function getThisYearCount(): int
    {
        return self::getCountSince(now()->startOfYear());
    }

    /**
     * Clear all activity log count caches
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_PREFIX . 'total');
        Cache::forget(self::CACHE_PREFIX . 'since_' . now()->startOfDay()->format('Y-m-d'));
        Cache::forget(self::CACHE_PREFIX . 'since_' . now()->startOfWeek()->format('Y-m-d'));
        Cache::forget(self::CACHE_PREFIX . 'since_' . now()->startOfMonth()->format('Y-m-d'));
        Cache::forget(self::CACHE_PREFIX . 'since_' . now()->startOfYear()->format('Y-m-d'));
    }

    /**
     * Increment cache count (yeni activity log eklendiğinde)
     *
     * Not: Observer'da kullanılabilir
     */
    public static function incrementCache(): void
    {
        // Total count cache'i artır
        if (Cache::has(self::CACHE_PREFIX . 'total')) {
            Cache::increment(self::CACHE_PREFIX . 'total');
        }

        // Today count cache'i artır
        $todayKey = self::CACHE_PREFIX . 'since_' . now()->startOfDay()->format('Y-m-d');
        if (Cache::has($todayKey)) {
            Cache::increment($todayKey);
        }

        // Week count cache'i artır
        $weekKey = self::CACHE_PREFIX . 'since_' . now()->startOfWeek()->format('Y-m-d');
        if (Cache::has($weekKey)) {
            Cache::increment($weekKey);
        }

        // Month count cache'i artır
        $monthKey = self::CACHE_PREFIX . 'since_' . now()->startOfMonth()->format('Y-m-d');
        if (Cache::has($monthKey)) {
            Cache::increment($monthKey);
        }

        // Year count cache'i artır
        $yearKey = self::CACHE_PREFIX . 'since_' . now()->startOfYear()->format('Y-m-d');
        if (Cache::has($yearKey)) {
            Cache::increment($yearKey);
        }
    }

    /**
     * Decrement cache count (activity log silindiğinde)
     */
    public static function decrementCache(): void
    {
        // Total count cache'i azalt
        if (Cache::has(self::CACHE_PREFIX . 'total')) {
            Cache::decrement(self::CACHE_PREFIX . 'total');
        }

        // Diğer cache'ler için clear yap (silinen kaydın tarihi belli değil)
        self::clearCache();
    }
}
