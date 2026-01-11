<?php

declare(strict_types=1);

namespace Modules\Shop\App\Enums;

enum CacheStrategy: string
{
    case NO_CACHE = 'no_cache';
    case WITH_CACHE = 'with_cache';
    case ADMIN_FRESH = 'admin_fresh';
    case PUBLIC_CACHED = 'public_cached';

    public function shouldCache(): bool
    {
        return match ($this) {
            self::NO_CACHE, self::ADMIN_FRESH => false,
            self::WITH_CACHE, self::PUBLIC_CACHED => true,
        };
    }

    public function getCacheTtl(): int
    {
        return match ($this) {
            self::NO_CACHE, self::ADMIN_FRESH => 0,
            self::WITH_CACHE => 1800, // 30 minutes
            self::PUBLIC_CACHED => 3600, // 1 hour
        };
    }

    public static function fromRequest(): self
    {
        $requested = request()->get('cache_strategy');

        if (is_string($requested)) {
            $strategy = self::tryFrom($requested);

            if ($strategy instanceof self) {
                return $strategy;
            }
        }

        return request()->is('admin*') ? self::ADMIN_FRESH : self::PUBLIC_CACHED;
    }
}
