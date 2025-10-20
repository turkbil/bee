<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Logo Service - Responsive Logo Management
 *
 * Özellikler:
 * - Light/Dark mode logo desteği
 * - Fallback sistemi (logo_2 -> logo_1 -> site_title)
 * - Intervention Image ile optimize
 * - SEO ve Schema.org uyumlu
 * - Tenant-aware
 */
class LogoService
{
    /**
     * Logo bilgilerini al (cache destekli)
     *
     * @return array{
     *   light_logo: string|null,
     *   dark_logo: string|null,
     *   light_logo_url: string|null,
     *   dark_logo_url: string|null,
     *   has_light: bool,
     *   has_dark: bool,
     *   has_both: bool,
     *   site_title: string,
     *   fallback_mode: string
     * }
     */
    public function getLogos(): array
    {
        $cacheKey = $this->getCacheKey('logos');

        return Cache::remember($cacheKey, 3600, function () {
            // Settings'ten logo bilgilerini al (artık media library URL'leri)
            $siteLogo = setting('site_logo');
            $siteKontrastLogo = setting('site_logo_2'); // Kontrast logo (Dark mode için)
            $siteTitle = setting('site_title', config('app.name'));

            // Logo varlık kontrolleri
            $hasLight = $this->isValidLogo($siteLogo);
            $hasDark = $this->isValidLogo($siteKontrastLogo);
            $hasBoth = $hasLight && $hasDark;

            // URL'ler (media library zaten optimize URL döndürüyor, direkt kullan)
            $lightLogoUrl = $hasLight ? $siteLogo : null;
            $darkLogoUrl = $hasDark ? $siteKontrastLogo : null;

            // Fallback mode belirleme
            $fallbackMode = $this->determineFallbackMode($hasLight, $hasDark);

            return [
                'light_logo' => $siteLogo,
                'dark_logo' => $siteKontrastLogo,
                'light_logo_url' => $lightLogoUrl,
                'dark_logo_url' => $darkLogoUrl,
                'has_light' => $hasLight,
                'has_dark' => $hasDark,
                'has_both' => $hasBoth,
                'site_title' => $siteTitle,
                'fallback_mode' => $fallbackMode,
            ];
        });
    }

    /**
     * Schema.org için logo URL'i al
     *
     * @return string|null
     */
    public function getSchemaLogoUrl(): ?string
    {
        $logos = $this->getLogos();

        // Öncelik: Light logo (normal logo)
        if ($logos['light_logo_url']) {
            return $logos['light_logo_url'];
        }

        // Fallback: Dark logo
        if ($logos['dark_logo_url']) {
            return $logos['dark_logo_url'];
        }

        return null;
    }

    /**
     * SEO için logo bilgilerini al (OG:image, Twitter:image)
     *
     * @return array{url: string|null, width: int, height: int, alt: string}
     */
    public function getSeoLogo(): array
    {
        $logos = $this->getLogos();
        $logoUrl = $this->getSchemaLogoUrl();

        return [
            'url' => $logoUrl,
            'width' => 512,
            'height' => 256,
            'alt' => $logos['site_title'] . ' Logo',
        ];
    }

    /**
     * Header için logo HTML'ini generate et
     *
     * @param array $options Ekstra CSS class'ları vs
     * @return string
     */
    public function renderHeaderLogo(array $options = []): string
    {
        $logos = $this->getLogos();
        $baseClass = $options['class'] ?? 'h-8 sm:h-10 w-auto';
        $priority = $options['priority'] ?? 'high'; // fetchpriority

        return view('components.logo.responsive-logo', [
            'logos' => $logos,
            'baseClass' => $baseClass,
            'priority' => $priority,
            'location' => 'header',
        ])->render();
    }

    /**
     * Footer için logo HTML'ini generate et
     *
     * @param array $options
     * @return string
     */
    public function renderFooterLogo(array $options = []): string
    {
        $logos = $this->getLogos();
        $baseClass = $options['class'] ?? 'h-8 w-auto';
        $priority = $options['priority'] ?? 'low'; // fetchpriority

        return view('components.logo.responsive-logo', [
            'logos' => $logos,
            'baseClass' => $baseClass,
            'priority' => $priority,
            'location' => 'footer',
        ])->render();
    }

    /**
     * Logo path'ini tenant-aware hale getir
     */
    protected function normalizeTenantPath(?string $path): ?string
    {
        if (!$path || $path === 'Logo yok') {
            return null;
        }

        $tenantId = function_exists('tenant_id') ? tenant_id() : null;

        if (!$tenantId) {
            return $path;
        }

        // Zaten tenant prefix'i varsa dokunma
        if (str_contains($path, 'tenant' . $tenantId)) {
            return $path;
        }

        // storage/ ile başlıyorsa tenant prefix ekle
        if (str_starts_with($path, 'storage/')) {
            return 'storage/tenant' . $tenantId . '/' . \Illuminate\Support\Str::after($path, 'storage/');
        }

        return $path;
    }

    /**
     * Logo geçerli mi kontrol et
     */
    protected function isValidLogo(?string $logoPath): bool
    {
        if (!$logoPath) {
            return false;
        }

        // "Logo yok" gibi placeholder değerleri filtrele
        if (in_array($logoPath, ['Logo yok', 'Favicon yok', 'null', ''])) {
            return false;
        }

        return true;
    }

    /**
     * Optimize edilmiş logo URL'ini al (thumbmaker ile)
     */
    protected function getOptimizedLogoUrl(?string $logoPath): ?string
    {
        if (!$logoPath) {
            return null;
        }

        try {
            // thumbmaker helper kullan (logo profile: 512x256 webp)
            return thumbmaker($logoPath, 'logo');
        } catch (\Exception $e) {
            \Log::warning('Logo optimize edilemedi, CDN fallback kullanılıyor', [
                'path' => $logoPath,
                'error' => $e->getMessage(),
            ]);

            // Fallback: CDN helper kullan
            return cdn($logoPath);
        }
    }

    /**
     * Fallback mode'u belirle
     *
     * @return string 'both'|'light_only'|'dark_only'|'title_only'
     */
    protected function determineFallbackMode(bool $hasLight, bool $hasDark): string
    {
        if ($hasLight && $hasDark) {
            return 'both'; // İkisi de var - ideal durum
        }

        if ($hasLight) {
            return 'light_only'; // Sadece light logo var
        }

        if ($hasDark) {
            return 'dark_only'; // Sadece dark logo var
        }

        return 'title_only'; // Hiçbiri yok - site title göster
    }

    /**
     * Cache key oluştur (tenant-aware)
     */
    protected function getCacheKey(string $suffix): string
    {
        $tenantId = function_exists('tenant_id') ? tenant_id() : 'central';

        return "logo_service_{$tenantId}_{$suffix}";
    }

    /**
     * Cache'i temizle
     */
    public function clearCache(): void
    {
        $cacheKey = $this->getCacheKey('logos');
        Cache::forget($cacheKey);
    }

    /**
     * Logo için structured data oluştur (Schema.org)
     *
     * @return array|null
     */
    public function getStructuredData(): ?array
    {
        $logoUrl = $this->getSchemaLogoUrl();

        if (!$logoUrl) {
            return null;
        }

        $logos = $this->getLogos();

        return [
            '@type' => 'ImageObject',
            'url' => $logoUrl,
            'width' => 512,
            'height' => 256,
            'caption' => $logos['site_title'] . ' Logo',
        ];
    }
}