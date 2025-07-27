<?php

declare(strict_types=1);

namespace Modules\Announcement\App\Enums;

enum CacheStrategy: string
{
    case ADMIN_FRESH = 'admin_fresh';
    case PUBLIC_CACHED = 'public_cached';
    case GLOBAL_CACHE = 'global_cache';
    
    public function shouldCache(): bool
    {
        return match($this) {
            self::ADMIN_FRESH => false,
            self::PUBLIC_CACHED => true,
            self::GLOBAL_CACHE => true,
        };
    }
    
    public function getCacheTtl(): int
    {
        return match($this) {
            self::ADMIN_FRESH => 0,
            self::PUBLIC_CACHED => 3600, // 1 hour
            self::GLOBAL_CACHE => 1800,  // 30 minutes
        };
    }
    
    public function getCacheKey(string $suffix = ''): string
    {
        $base = match($this) {
            self::ADMIN_FRESH => 'admin.announcements',
            self::PUBLIC_CACHED => 'public.announcements',
            self::GLOBAL_CACHE => 'global.announcements',
        };
        
        return $suffix ? "{$base}.{$suffix}" : $base;
    }
}