<?php

namespace App\Services;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Modules\ThemeManagement\App\Models\Theme;

class ThemeService
{
    protected ?object $activeTheme = null;
    
    public function __construct()
    {
        // Basit constructor - dependency olmadan çalışsın
    }
    
    /**
     * Aktif temayı getirir
     */
    public function getActiveTheme(): ?object
    {
        // Theme preview modu kontrolü (admin panelden önizleme için)
        if (request()->has('theme_preview')) {
            $previewThemeName = request()->get('theme_preview');
            $previewTheme = Theme::on('mysql')
                ->where('name', $previewThemeName)
                ->where('is_active', true)
                ->first();

            if ($previewTheme) {
                return $previewTheme;
            }
        }

        if ($this->activeTheme === null) {
            $this->activeTheme = $this->loadActiveTheme();
        }

        return $this->activeTheme;
    }
    
    /**
     * Aktif temayı yükle - ThemeRepository ile uyumlu cache keys
     */
    protected function loadActiveTheme(): ?object
    {
        try {
            // Repository ile aynı cache key'leri kullan (duplication önleme)
            // Redis cache kullan (file cache yerine)
            $cache = Cache::store('redis');

            if (function_exists('tenant') && $t = tenant()) {
                // Tenant tema cache - Repository ile uyumlu
                $cacheKey = "theme:tenant_{$t->id}";

                $theme = $cache->remember($cacheKey, now()->addHours(1), function() use ($t) {
                    // theme_id kullan (theme değil)
                    if (isset($t->theme_id) && $t->theme_id) {
                        $selectedTheme = Theme::on('mysql')->where('theme_id', $t->theme_id)
                                      ->where('is_active', true)
                                      ->first();

                        return $selectedTheme;
                    }

                    return null;
                });

                if ($theme) {
                    return $theme;
                }
            }

            // 🔥 CENTRAL DOMAIN: Tenant yüklenmediyse ama domain database'de varsa
            // (Merkezi domain için tema yükleme - muzibu.com gibi)
            if (!tenant() && request()) {
                $host = request()->getHost();
                // www prefix'i kaldır
                $host = preg_replace('/^www\./', '', $host);

                // Domain'e göre tenant ve tema bul
                $cacheKey = "theme:central_domain_{$host}";
                $theme = $cache->remember($cacheKey, now()->addHours(1), function() use ($host) {
                    // Domain'i bul ve tenant'ını çek
                    $domain = \Stancl\Tenancy\Database\Models\Domain::with('tenant')
                        ->where('domain', $host)
                        ->first();

                    if ($domain && $domain->tenant && $domain->tenant->theme_id) {
                        return Theme::on('mysql')
                            ->where('theme_id', $domain->tenant->theme_id)
                            ->where('is_active', true)
                            ->first();
                    }

                    return null;
                });

                if ($theme) {
                    return $theme;
                }
            }

            // Default tema cache - Repository ile uyumlu
            $cacheKey = 'theme:default';

            return $cache->remember($cacheKey, now()->addHours(24), function() {
                // Default tema ara
                $theme = Theme::on('mysql')
                    ->where('is_default', true)
                    ->where('is_active', true)
                    ->first();

                if (!$theme) {
                    // Herhangi bir aktif tema ara
                    $theme = Theme::on('mysql')
                        ->where('is_active', true)
                        ->first();
                }

                if (!$theme) {
                    // Emergency fallback tema oluştur
                    return (object) [
                        'id' => 0,
                        'name' => 'emergency',
                        'display_name' => 'Emergency Theme',
                        'is_active' => true,
                        'is_default' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                return $theme;
            });

        } catch (\Exception $e) {
            Log::error('ThemeService error: ' . $e->getMessage());

            // Emergency fallback
            return (object) [
                'id' => 0,
                'name' => 'emergency',
                'display_name' => 'Emergency Theme',
                'is_active' => true,
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
    }
    
    /**
     * Tema view path'lerini ayarlar
     */
    public function setupThemeViews(): void
    {
        $theme = $this->getActiveTheme();
        
        if (!$theme) {
            return;
        }
        
        // Tema view path'ini ayarla
        $themePath = resource_path("views/themes/{$theme->name}");
        
        if (is_dir($themePath)) {
            View::addLocation($themePath);
        }
    }
    
    /**
     * Tema asset URL'ini getirir
     */
    public function getThemeAssetUrl(string $asset): string
    {
        $theme = $this->getActiveTheme();
        
        if (!$theme) {
            return asset($asset);
        }
        
        return asset("themes/{$theme->name}/{$asset}");
    }
    
    /**
     * Tema bilgilerini getirir
     */
    public function getThemeInfo(): array
    {
        $theme = $this->getActiveTheme();
        
        if (!$theme) {
            return [
                'name' => 'emergency',
                'display_name' => 'Emergency Theme',
                'is_active' => true
            ];
        }
        
        return [
            'name' => $theme->name,
            'display_name' => $theme->display_name ?? $theme->name,
            'is_active' => $theme->is_active ?? true,
            'is_default' => $theme->is_default ?? false
        ];
    }
    
    /**
     * Tema cache'ini temizler - Repository ile uyumlu
     */
    public function clearThemeCache(?string $tenantId = null): void
    {
        $this->activeTheme = null; // Instance cache'i temizle

        if ($tenantId) {
            // Belirli tenant'ın tema cache'ini temizle
            Cache::forget("theme:tenant_{$tenantId}");
        } else {
            // Default tema cache'ini temizle
            Cache::forget('theme:default');

            // Tüm tenant tema cache'lerini de temizle
            try {
                $redis = Cache::getRedis();
                $pattern = config('database.redis.options.prefix') . ':theme:tenant_*';
                $keys = $redis->keys($pattern);

                if (!empty($keys)) {
                    $redis->del($keys);
                }
            } catch (\Exception $e) {
                // Redis hatası - sessizce devam et
                \Log::debug('Theme cache clear failed: ' . $e->getMessage());
            }
        }
    }
    
    /**
     * Tema view path'ini getirir (modül desteği ile)
     * 4 seviyeli fallback: resources/views tema → modül tema → simple → front
     *
     * ÖNCELİK SIRASI:
     * 1. resources/views/themes/{tema}/{view} - Ana tema klasörü (TEK DOSYA PRENSİBİ)
     * 2. modül::themes.{tema}.{view} - Modül içi tema (fallback)
     * 3. modül::themes.simple.{view} - Simple tema fallback
     * 4. modül::front.{view} - Front fallback
     */
    public function getThemeViewPath(string $view, string $module = null): string
    {
        $theme = $this->getActiveTheme();
        $themeName = $theme ? $theme->name : 'simple';

        if ($module) {
            // Modül namespace'i küçük harfle kullanılmalı (Laravel convention)
            $moduleNamespace = strtolower($module);

            // 🎯 YENİ: resources/views/themes/ klasöründe ARA (TEK DOSYA PRENSİBİ)
            // Tema dosyaları SADECE resources/views/themes/ altında olmalı
            // Önce modül klasörü ile ara: themes.t-3.page.show
            $resourceThemeModuleView = "themes.{$themeName}.{$moduleNamespace}.{$view}";
            if (view()->exists($resourceThemeModuleView)) {
                return $resourceThemeModuleView;
            }

            // Sonra direkt ara: themes.t-3.show (eski uyumluluk)
            $resourceThemeView = "themes.{$themeName}.{$view}";
            if (view()->exists($resourceThemeView)) {
                return $resourceThemeView;
            }

            // 2. Modül tema view'ı (eski sistem - fallback)
            $activeThemeView = "{$moduleNamespace}::themes.{$themeName}.{$view}";
            if (view()->exists($activeThemeView)) {
                return $activeThemeView;
            }

            // 3. Simple tema fallback (aktif tema simple değilse)
            if ($themeName !== 'simple') {
                // Önce modül simple (module-specific öncelikli!)
                $simpleView = "{$moduleNamespace}::themes.simple.{$view}";
                if (view()->exists($simpleView)) {
                    Log::debug("Theme fallback: {$activeThemeView} → {$simpleView}");
                    return $simpleView;
                }

                // Sonra resources/views/themes/simple (generic fallback)
                $simpleResourceView = "themes.simple.{$view}";
                if (view()->exists($simpleResourceView)) {
                    Log::debug("Theme fallback: {$resourceThemeView} → {$simpleResourceView}");
                    return $simpleResourceView;
                }
            }

            // 4. Front fallback (son çare)
            $frontView = "{$moduleNamespace}::front.{$view}";
            if (view()->exists($frontView)) {
                Log::debug("Theme fallback: {$activeThemeView} → {$frontView}");
                return $frontView;
            }

            // Hiçbiri bulunamadı - hata logla ve resources tema dön (Laravel hata verecek)
            Log::warning("Theme view not found: {$view} in module {$module}");
            return $resourceThemeView;
        }

        // Genel tema view'ı (layout için)
        // 1. Aktif tema
        $themeViewPath = "themes.{$themeName}.{$view}";
        if (view()->exists($themeViewPath)) {
            return $themeViewPath;
        }

        // 2. Simple fallback
        if ($themeName !== 'simple') {
            $simpleView = "themes.simple.{$view}";
            if (view()->exists($simpleView)) {
                Log::debug("Theme fallback: {$themeViewPath} → {$simpleView}");
                return $simpleView;
            }
        }

        // 3. Direct view fallback
        if (view()->exists($view)) {
            return $view;
        }

        Log::warning("Theme view not found: {$view}");
        return $themeViewPath;
    }

    /**
     * Subheader view path'ini getirir
     * Öncelik: tema özel subheader → tenant ayarı → default
     */
    public function getSubheader(): string
    {
        $theme = $this->getActiveTheme();
        $themeName = $theme ? $theme->name : 'simple';

        // 1. Tema kendi subheader dosyası var mı?
        $themeSubheader = "themes.{$themeName}.layouts.partials.subheader";
        if (view()->exists($themeSubheader)) {
            return $themeSubheader;
        }

        // 2. Tenant theme_settings'den stil al
        $style = $this->getSubheaderStyle();

        // 3. Seçilen stili döndür
        $componentPath = "components.subheaders.{$style}";
        if (view()->exists($componentPath)) {
            return $componentPath;
        }

        // 4. Default fallback
        return "components.subheaders.glass";
    }

    /**
     * Tenant'ın subheader stilini getirir
     */
    public function getSubheaderStyle(): string
    {
        if (function_exists('tenant') && $tenant = tenant()) {
            $settings = $tenant->theme_settings ?? [];
            return $settings['subheader_style'] ?? 'glass';
        }

        return 'glass';
    }

    /**
     * Tema kendi subheader'ına sahip mi?
     */
    public function hasCustomSubheader(): bool
    {
        $theme = $this->getActiveTheme();
        $themeName = $theme ? $theme->name : 'simple';

        return view()->exists("themes.{$themeName}.layouts.partials.subheader");
    }
    
    /**
     * Tema değiştirildiğinde çağrılır
     */
    public function refreshTheme(): void
    {
        $this->activeTheme = null;
        $this->setupThemeViews();
    }
}