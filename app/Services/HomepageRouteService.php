<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

/**
 * Homepage Route Service
 * 
 * Homepage routing işlemlerini yönetir
 */
readonly class HomepageRouteService
{
    private const CACHE_KEY = 'homepage_route_info';
    private const CACHE_TTL = 60; // dakika

    /**
     * Model'in homepage olup olmadığını kontrol et
     */
    public function isHomepage(Model $model): bool
    {
        // Page modeli kontrolü
        if (!method_exists($model, 'getTable') || $model->getTable() !== 'pages') {
            return false;
        }

        // is_homepage flag kontrolü
        return isset($model->is_homepage) && $model->is_homepage === true;
    }

    /**
     * Homepage URL'i oluştur
     */
    public function getHomepageUrl(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $defaultLocale = get_tenant_default_locale();

        if ($locale === $defaultLocale) {
            return url('/');
        }

        return url("/{$locale}");
    }

    /**
     * Mevcut route'un homepage olup olmadığını kontrol et
     */
    public function isCurrentRouteHomepage(): bool
    {
        $currentRoute = Route::current();
        
        if (!$currentRoute) {
            return false;
        }

        $routeName = $currentRoute->getName();
        return in_array($routeName, ['home', 'home.locale']);
    }

    /**
     * Homepage için alternate links oluştur
     */
    public function getHomepageAlternateLinks(): array
    {
        return Cache::remember(self::CACHE_KEY . '_alternate', now()->addMinutes(self::CACHE_TTL), function () {
            $links = [];
            $currentLocale = app()->getLocale();
            $availableLocales = \App\Services\TenantLanguageProvider::getActiveLanguageCodes();

            foreach ($availableLocales as $locale) {
                $links[$locale] = [
                    'url' => $this->getHomepageUrl($locale),
                    'hreflang' => $locale,
                    'current' => $locale === $currentLocale
                ];
            }

            return $links;
        });
    }

    /**
     * Homepage model'ini getir
     */
    public function getHomepageModel(): ?Model
    {
        return Cache::remember(self::CACHE_KEY . '_model', now()->addMinutes(self::CACHE_TTL), function () {
            if (class_exists('Modules\Page\App\Models\Page')) {
                return \Modules\Page\App\Models\Page::where('is_homepage', true)
                    ->where('is_active', true)
                    ->first();
            }

            return null;
        });
    }

    /**
     * Cache'i temizle
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY . '_alternate');
        Cache::forget(self::CACHE_KEY . '_model');
    }
}