<?php

namespace App\Services\CacheProfiles;

/**
 * Blog modülü cache profile
 * Blog modülü olan tenant'lar için
 */
class BlogCacheProfile implements ModuleCacheProfileInterface
{
    public function getDynamicPaths(): array
    {
        return [
            // Yorum sistemi (kullanıcıya özel)
            'blog/*/comment',
            'blog/my-comments',
        ];
    }

    public function getExcludedPaths(): array
    {
        return [];
    }

    public function getTenantIds(): array
    {
        // Tüm tenant'larda aktif
        return [];
    }

    public function getModuleName(): string
    {
        return 'Blog';
    }
}
