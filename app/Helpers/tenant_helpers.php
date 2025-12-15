<?php

use App\Services\TenantCacheService;
use App\Services\TenantSessionService;

if (!function_exists('tenant_cache')) {
    /**
     * Tenant cache service instance
     */
    function tenant_cache(): TenantCacheService
    {
        return app(TenantCacheService::class);
    }
}

if (!function_exists('tenant_session')) {
    /**
     * Tenant session service instance
     */
    function tenant_session(): TenantSessionService
    {
        return app(TenantSessionService::class);
    }
}

if (!function_exists('tenant_cache_key')) {
    /**
     * Tenant cache key oluştur
     */
    function tenant_cache_key(string $prefix, string $key, array $params = []): string
    {
        return tenant_cache()->key($prefix, $key, $params);
    }
}

if (!function_exists('tenant_cache_remember')) {
    /**
     * Tenant cache remember kısayolu
     */
    function tenant_cache_remember(string $prefix, string $key, $ttl, callable $callback, array $params = [])
    {
        return tenant_cache()->remember($prefix, $key, $ttl, $callback, $params);
    }
}

if (!function_exists('tenant_cache_forget')) {
    /**
     * Tenant cache forget kısayolu
     */
    function tenant_cache_forget(string $prefix, string $key, array $params = []): bool
    {
        return tenant_cache()->forget($prefix, $key, $params);
    }
}

if (!function_exists('tenant_cache_flush')) {
    /**
     * Tenant cache flush kısayolu
     */
    function tenant_cache_flush(): void
    {
        tenant_cache()->flushTenant();
    }
}

if (!function_exists('tenant_session_put')) {
    /**
     * Tenant session put kısayolu
     */
    function tenant_session_put(string $prefix, string $key, $value): void
    {
        tenant_session()->put($prefix, $key, $value);
    }
}

if (!function_exists('tenant_session_get')) {
    /**
     * Tenant session get kısayolu
     */
    function tenant_session_get(string $prefix, string $key, $default = null)
    {
        return tenant_session()->get($prefix, $key, $default);
    }
}

if (!function_exists('tenant_session_forget')) {
    /**
     * Tenant session forget kısayolu
     */
    function tenant_session_forget(string $prefix, string $key): void
    {
        tenant_session()->forget($prefix, $key);
    }
}

if (!function_exists('tenant_flash')) {
    /**
     * Tenant flash message kısayolu
     */
    function tenant_flash(string $type, string $message): void
    {
        tenant_session()->flash($type, $message);
    }
}

if (!function_exists('module_cache')) {
    /**
     * Modül cache kısayolu
     */
    function module_cache(string $module, string $key, $ttl, callable $callback, array $params = [])
    {
        return tenant_cache()->moduleCache($module, $key, $ttl, $callback, $params);
    }
}

if (!function_exists('language_cache')) {
    /**
     * Dil cache kısayolu
     */
    function language_cache(string $key, $ttl, callable $callback, ?string $locale = null)
    {
        return tenant_cache()->languageCache($key, $ttl, $callback, $locale);
    }
}

if (!function_exists('user_cache')) {
    /**
     * Kullanıcı cache kısayolu
     */
    function user_cache(string $key, $ttl, callable $callback, ?int $userId = null)
    {
        return tenant_cache()->userCache($key, $ttl, $callback, $userId);
    }
}

if (!function_exists('route_cache')) {
    /**
     * Route cache kısayolu
     */
    function route_cache(string $routeName, $ttl, callable $callback, array $routeParams = [])
    {
        return tenant_cache()->routeCache($routeName, $ttl, $callback, $routeParams);
    }
}

if (!function_exists('module_session')) {
    /**
     * Modül session kısayolu
     */
    function module_session(string $module, string $key, $value = null)
    {
        return tenant_session()->moduleSession($module, $key, $value);
    }
}

if (!function_exists('user_session')) {
    /**
     * Kullanıcı session kısayolu
     */
    function user_session(string $key, $value = null)
    {
        return tenant_session()->userSession($key, $value);
    }
}

if (!function_exists('language_session')) {
    /**
     * Dil session kısayolu
     */
    function language_session(string $key, $value = null)
    {
        return tenant_session()->languageSession($key, $value);
    }
}

if (!function_exists('form_session')) {
    /**
     * Form session kısayolu
     */
    function form_session(string $formId, ?array $data = null)
    {
        return tenant_session()->formSession($formId, $data);
    }
}

if (!function_exists('cart_session')) {
    /**
     * Cart session kısayolu
     */
    function cart_session(?array $items = null)
    {
        return tenant_session()->cartSession($items);
    }
}

if (!function_exists('filter_session')) {
    /**
     * Filter session kısayolu
     */
    function filter_session(string $page, ?array $filters = null)
    {
        return tenant_session()->filterSession($page, $filters);
    }
}

if (!function_exists('megamenu_html')) {
    /**
     * Get cached megamenu HTML
     *
     * Usage in blade: {!! megamenu_html('products') !!}
     *
     * @param string $type Menu type (products, hakkimizda)
     * @return string Cached HTML
     */
    function megamenu_html(string $type = 'products'): string
    {
        return \App\Services\MegaMenuCacheService::getHtml($type);
    }
}

if (!function_exists('megamenu_invalidate')) {
    /**
     * Invalidate megamenu cache
     *
     * Usage: megamenu_invalidate() - clears all megamenu caches
     */
    function megamenu_invalidate(): void
    {
        \App\Services\MegaMenuCacheService::invalidate();
    }
}

if (!function_exists('megamenu_warmup')) {
    /**
     * Warm up megamenu cache
     *
     * Usage: megamenu_warmup() - regenerates cache
     */
    function megamenu_warmup(): void
    {
        \App\Services\MegaMenuCacheService::warmUp();
    }
}