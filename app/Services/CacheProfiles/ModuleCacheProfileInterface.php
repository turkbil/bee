<?php

namespace App\Services\CacheProfiles;

/**
 * Modül bazlı cache profile interface
 * Her modül kendi cache-bypass path'lerini tanımlar
 */
interface ModuleCacheProfileInterface
{
    /**
     * Dinamik sayfalar (kullanıcıya özel, cache'lenmemeli)
     * Örnek: favorites, my-playlists, dashboard
     */
    public function getDynamicPaths(): array;

    /**
     * Config excluded paths (auth olmayan kullanıcılar için de cache'lenmemeli)
     * Örnek: login, register, password reset
     */
    public function getExcludedPaths(): array;

    /**
     * Bu modül hangi tenant'larda aktif?
     * Örnek: [1001] = Sadece Muzibu, [] = Tüm tenant'lar
     */
    public function getTenantIds(): array;

    /**
     * Modül adı (debug için)
     */
    public function getModuleName(): string;
}
