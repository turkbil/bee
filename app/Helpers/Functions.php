<?php

use App\Helpers\TenantHelpers;

// Global helper fonksiyonları

if (!function_exists('cdn')) {
    /**
     * Ortak public klasöründen asset URL'i oluşturur
     * Tüm tenant'lar aynı public klasörünü paylaşır
     *
     * @param string $path
     * @return string
     */
    function cdn($path)
    {
        // Boş path kontrolü
        if (empty($path)) {
            return '';
        }

        // Zaten tam URL ise olduğu gibi döndür
        if (preg_match('#^https?://#', $path)) {
            return $path;
        }

        // Path temizleme
        $path = ltrim($path, '/');

        // Mevcut domain üzerinden asset URL oluştur
        // (Tüm tenant'lar domain alias olarak aynı public'i kullanır)
        return url($path);
    }
}

if (!function_exists('tenant_id')) {
    /**
     * Aktif tenant ID'sini döndürür, tenant yoksa null döner
     * 
     * @return int|null
     */
    function tenant_id()
    {
        return TenantHelpers::getCurrentTenantId();
    }
}

if (!function_exists('resolve_tenant_id')) {
    /**
     * Hızlı tenant ID çözümleme (admin paneli desteği ile)
     * Öncelik: tenant() -> auth()->user()->tenant_id -> latest tenant
     * 
     * @param bool $fallbackToLatest En son tenant'a fallback yap (default: true)
     * @return int|null
     */
    function resolve_tenant_id(bool $fallbackToLatest = true): ?int
    {
        return TenantHelpers::resolveCurrentTenantId($fallbackToLatest);
    }
}

if (!function_exists('get_tenant_info')) {
    /**
     * Tenant bilgilerini hızlı al (cache'li)
     * 
     * @param int|null $tenantId
     * @return \App\Models\Tenant|null
     */
    function get_tenant_info(?int $tenantId = null): ?\App\Models\Tenant
    {
        return TenantHelpers::getTenantById($tenantId);
    }
}

if (!function_exists('get_tenant_domain')) {
    /**
     * Tenant domain'ini al
     * 
     * @param int|null $tenantId
     * @return string|null
     */
    function get_tenant_domain(?int $tenantId = null): ?string
    {
        return TenantHelpers::getTenantDomain($tenantId);
    }
}

if (!function_exists('tenant_db_type')) {
    /**
     * Tenant'ın database tipini al ('central' veya 'tenant')
     * 
     * @param int|null $tenantId
     * @return string
     */
    function tenant_db_type(?int $tenantId = null): string
    {
        return TenantHelpers::getTenantDatabaseType($tenantId);
    }
}

if (!function_exists('is_tenant')) {
    /**
     * İşlemin tenant veritabanında olup olmadığını kontrol eder
     * 
     * @return bool
     */
    function is_tenant()
    {
        return TenantHelpers::isTenant();
    }
}

if (!function_exists('is_central')) {
    /**
     * İşlemin central veritabanında olup olmadığını kontrol eder
     * 
     * @return bool
     */
    function is_central()
    {
        return TenantHelpers::isCentral();
    }
}

if (!function_exists('tenant_name')) {
    /**
     * Aktif tenant'ın adını döndürür, tenant yoksa null döner
     * 
     * @return string|null
     */
    function tenant_name()
    {
        if (is_tenant()) {
            return tenant()->name ?? tenant()->id;
        }
        
        return null;
    }
}

if (!function_exists('tenant_domain')) {
    /**
     * Aktif tenant'ın domain adresini döndürür, tenant yoksa null döner
     * 
     * @return string|null
     */
    function tenant_domain()
    {
        if (is_tenant()) {
            return tenant()->domains->first()->domain ?? null;
        }
        
        return null;
    }
}

if (!function_exists('tenant_disk')) {
    /**
     * Aktif tenant için disk adını döndürür
     * Central için 'public', tenant için 'tenant' döndürür ve yapılandırır
     *
     * @return string
     */
    function tenant_disk()
    {
        return TenantHelpers::getTenantDiskConfig();
    }
}

if (!function_exists('tenant_storage_path')) {
    /**
     * Tenant için depolama yolunu oluşturur
     * Tenant1 için normal storage yolu, diğer tenant'lar için tenant{id} yolu kullanır
     *
     * @param int|null $tenantId
     * @param string $path
     * @return string
     */
    function tenant_storage_path($path = '', $tenantId = null)
    {
        if ($tenantId === null) {
            $tenantId = tenant_id() ?? 1;
        }
        
        // Central için normal storage yolu
        if ($tenantId == 1) {
            return storage_path('app/public/' . ltrim($path, '/'));
        }
        
        // Tenant için tenant{id} yolu
        return storage_path('tenant' . $tenantId . '/app/public/' . ltrim($path, '/'));
    }
}

if (!function_exists('tenant_storage_url')) {
    /**
     * Tenant için depolama URL'ini oluşturur
     * tenant{id} formatında URL döndürür
     *
     * @param string $path
     * @param int|null $tenantId
     * @return string
     */
    function tenant_storage_url($path = '', $tenantId = null)
    {
        if ($tenantId === null) {
            $tenantId = tenant_id() ?? 1;
        }
        
        // Tenant ID'li URL formatı
        return url('/storage/tenant' . $tenantId . '/' . ltrim($path, '/'));
    }
}

if (!function_exists('widget')) {
    /**
     * Widget render helper
     * 
     * @param string $position Widget pozisyonu
     * @param int|null $pageId Sayfa ID
     * @param string|null $module Modül adı
     * @return string
     */
    function widget($position, $pageId = null, $module = null)
    {
        $service = app('widget.service');
        return $service->renderWidgetsInPosition($position, $pageId, $module);
    }
}

if (!function_exists('href')) {
    /**
     * Module dynamic URL helper with locale prefix support
     * 
     * @param string $module Module name (portfolio, page, announcement)
     * @param string $action Action name (index, show, category)
     * @param string|null $slug Item slug for show actions
     * @param string|null $locale Locale for URL prefix
     * @return string
     */
    function href($module, $action, $slug = null, $locale = null)
    {
        // 🎯 PAGE MODÜLÜ ÖZEL DURUMU: Prefix'siz direkt slug
        if (strtolower($module) === 'page' && $action === 'show' && $slug) {
            $url = '/' . $slug;

            // Locale prefix desteği ekle
            $currentLocale = $locale ?: app()->getLocale();
            if (function_exists('needs_locale_prefix') && needs_locale_prefix($currentLocale)) {
                $url = '/' . $currentLocale . $url;
            }

            return $url;
        }

        // 📋 DİĞER MODÜLLER: Normal prefix'li yapı
        $slugService = app(\App\Services\ModuleSlugService::class);
        $moduleSlug = $slugService->getSlug($module, $action);

        $url = '/' . $moduleSlug;

        if ($slug) {
            $url .= '/' . $slug;
        }

        // Locale prefix desteği ekle
        $currentLocale = $locale ?: app()->getLocale();

        // UrlPrefixService kullanarak prefix gerekli mi kontrol et
        if (function_exists('needs_locale_prefix') && needs_locale_prefix($currentLocale)) {
            $url = '/' . $currentLocale . $url;
        }

        return $url;
    }
}

if (!function_exists('user_initials')) {
    /**
     * Kullanıcı adından avatar harfleri çıkarır (Türkçe karakter destekli)
     * 
     * @param string $name
     * @param int $length
     * @return string
     */
    function user_initials($name, $length = 2)
    {
        // Türkçe karakterleri temizle ve normalize et
        $name = trim($name);
        if (empty($name)) {
            return 'U'; // Default User
        }
        
        // İsimleri böl
        $parts = explode(' ', $name);
        $initials = '';
        
        // Her kelimeden ilk harfi al
        foreach ($parts as $part) {
            if (!empty($part)) {
                // mb_substr kullanarak Türkçe karakter desteği
                $initial = mb_strtoupper(mb_substr($part, 0, 1, 'UTF-8'), 'UTF-8');
                $initials .= $initial;
                
                // İstenilen uzunluka ulaştık mı?
                if (mb_strlen($initials, 'UTF-8') >= $length) {
                    break;
                }
            }
        }
        
        // Eğer yeterli harf yoksa, ilk kelimeden daha fazla harf al
        if (mb_strlen($initials, 'UTF-8') < $length && !empty($parts[0])) {
            $needed = $length - mb_strlen($initials, 'UTF-8');
            $firstWord = $parts[0];
            for ($i = 1; $i <= $needed && $i < mb_strlen($firstWord, 'UTF-8'); $i++) {
                $initials .= mb_strtoupper(mb_substr($firstWord, $i, 1, 'UTF-8'), 'UTF-8');
            }
        }
        
        return mb_substr($initials, 0, $length, 'UTF-8');
    }
}

// =====================================================
// LOG HELPER FUNCTIONS (eski LogHelper.php'den)
// =====================================================

if (!function_exists('log_activity')) {
    function log_activity(
        \Illuminate\Database\Eloquent\Model $model,
        string $event,
        ?array $degisenler = null
    ): void {
        // Multi-language JSON alanları için title extraction
        $rawTitle = $model->title ?? $model->name ?? 'Bilinmeyen';

        // Eğer title array/object ise (JSON decode edilmiş), ilk değeri al
        if (is_array($rawTitle) || is_object($rawTitle)) {
            $titleArray = (array) $rawTitle;
            if (!empty($titleArray)) {
                // Önce varsayılan dili dene, sonra ilk değeri al
                $defaultLang = session('site_default_language', 'tr');
                $baslik = $titleArray[$defaultLang] ?? reset($titleArray);
            } else {
                $baslik = 'Bilinmeyen';
            }
        } else {
            $baslik = $rawTitle;
        }

        // Güvenlik için string'e çevir
        $baslik = (string) $baslik;

        // Event string'ini çevir (activity.php dil dosyasından)
        $translatedEvent = __('activity.' . $event);
        // Çeviri bulunamazsa orijinal event'i kullan
        if ($translatedEvent === 'activity.' . $event) {
            $translatedEvent = $event;
        }

        $modelName = class_basename($model);
        $batchUuid = \Illuminate\Support\Str::uuid();

        activity()
            ->performedOn($model)
            ->causedBy(auth()->check() ? auth()->user() : null)
            ->inLog($modelName)
            ->withProperties([
                'baslik' => $baslik,
                'modul' => $modelName,
                'degisenler' => $degisenler ?: [],
                'event_key' => $event, // Orijinal key'i de sakla
            ])
            ->tap(function (\Spatie\Activitylog\Models\Activity $activity) use ($batchUuid, $event) {
                $activity->batch_uuid = $batchUuid;
                $activity->event = $event; // DB'ye orijinal key kaydedilir
            })
            ->log("{$baslik} {$translatedEvent}"); // Gösterim için çevrilmiş metin
    }
}

// =====================================================
// SETTINGS HELPER FUNCTIONS (eski SettingsHelper.php'den)
// =====================================================

if (!function_exists('settings')) {
    function settings($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('settings');
        }

        try {
            if (function_exists('is_tenant') && is_tenant()) {
                config(['database.connections.tenant.driver' => 'mysql']);
                \Illuminate\Support\Facades\DB::purge('tenant');
            }
            
            return app('settings')->get($key, $default);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Settings helper error: " . $e->getMessage());
            return $default;
        }
    }
}

if (!function_exists('settings_id')) {
    function settings_id($id = null, $default = null)
    {
        if (is_null($id)) {
            return app('settings');
        }

        try {
            if (function_exists('is_tenant') && is_tenant()) {
                config(['database.connections.tenant.driver' => 'mysql']);
                \Illuminate\Support\Facades\DB::purge('tenant');
            }

            return app('settings')->getById($id, $default);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Settings ID helper error: " . $e->getMessage());
            return $default;
        }
    }
}

// Alias: setting() -> settings() (tenant-aware)
if (!function_exists('setting')) {
    /**
     * Tenant-aware settings helper (alias for settings())
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key = null, $default = null)
    {
        return settings($key, $default);
    }
}

if (!function_exists('ai_format_response')) {
    /**
     * AI yanıtını modern template ile formatla
     * Global kullanım - tüm modüllerde kullanılabilir
     * 
     * @param string $text AI yanıt metni
     * @param string|null $featureSlug AI feature slug'ı
     * @return string Formatted HTML template
     */
    function ai_format_response(string $text, ?string $featureSlug = null): string
    {
        try {
            $templateService = app(\App\Services\AITemplateService::class);
            return $templateService->buildModernTemplate($text, $featureSlug);
        } catch (\Exception $e) {
            \Log::error('AI Template Helper Error: ' . $e->getMessage());
            return '
            <div class="ai-response-template">
                <div class="ai-error-message">
                    <p><i class="fas fa-exclamation-triangle me-2"></i>AI yanıt formatlanamadı: ' . $e->getMessage() . '</p>
                </div>
            </div>';
        }
    }
}

if (!function_exists('getFlagForLanguage')) {
    /**
     * Dil koduna göre flag emoji döndürür
     * 
     * @param string $languageCode
     * @return string Flag emoji
     */
    function getFlagForLanguage(string $languageCode): string
    {
        $flags = [
            'tr' => '🇹🇷',
            'en' => '🇬🇧', 
            'ar' => '🇸🇦',
            'da' => '🇩🇰',
            'sq' => '🇦🇱',
            'de' => '🇩🇪',
            'fr' => '🇫🇷',
            'es' => '🇪🇸',
            'it' => '🇮🇹',
            'pt' => '🇵🇹',
            'ru' => '🇷🇺',
            'zh' => '🇨🇳',
            'ja' => '🇯🇵',
            'ko' => '🇰🇷',
            'nl' => '🇳🇱',
            'pl' => '🇵🇱',
            'cs' => '🇨🇿',
            'hu' => '🇭🇺',
            'ro' => '🇷🇴',
            'bg' => '🇧🇬',
            'hr' => '🇭🇷',
            'sk' => '🇸🇰',
            'sl' => '🇸🇮',
            'et' => '🇪🇪',
            'lv' => '🇱🇻',
            'lt' => '🇱🇹',
            'fi' => '🇫🇮',
            'sv' => '🇸🇪',
            'no' => '🇳🇴',
            'is' => '🇮🇸',
            'ga' => '🇮🇪',
            'mt' => '🇲🇹',
            'cy' => '🏴󠁧󠁢󠁷󠁬󠁳󠁿',
            'eu' => '🇪🇸', // Basque
            'ca' => '🇪🇸', // Catalan
            'gl' => '🇪🇸', // Galician
            'he' => '🇮🇱',
            'hi' => '🇮🇳',
            'bn' => '🇧🇩',
            'ur' => '🇵🇰',
            'fa' => '🇮🇷',
            'th' => '🇹🇭',
            'vi' => '🇻🇳',
            'id' => '🇮🇩',
            'ms' => '🇲🇾',
            'tl' => '🇵🇭', // Filipino
            'sw' => '🇰🇪', // Swahili
            'am' => '🇪🇹', // Amharic
            'yo' => '🇳🇬', // Yoruba
            'ig' => '🇳🇬', // Igbo
            'ha' => '🇳🇬', // Hausa
            'zu' => '🇿🇦', // Zulu
            'af' => '🇿🇦', // Afrikaans
            'xh' => '🇿🇦', // Xhosa
        ];
        
        return $flags[strtolower($languageCode)] ?? '🌐';
    }
}

// 💰 CLAUDE_AI.MD UYUMLU KREDİ SİSTEMİ
if (!function_exists('ai_deduct_credits_properly')) {
    /**
     * Doğru kredi düşürme fonksiyonu - claude_ai.md tam uyumlu
     */
    function ai_deduct_credits_properly(
        int $inputTokens,
        int $outputTokens, 
        string $providerName,
        string $model,
        array $metadata = []
    ): float {
        try {
            // Calculate credits first
            $credits = ai_calculate_model_credits($inputTokens, $outputTokens, $providerName, $model);
            
            // Get tenant with fallback
            $tenant = tenant();
            if (!$tenant) {
                // Fallback to tenant ID 1 for CLI/testing context
                $tenant = \App\Models\Tenant::find(1);
                if (!$tenant) {
                    Log::warning('No tenant found for credit deduction');
                    return 0;
                }
            }
            
            // Check if tenant has enough credits
            if ($tenant->ai_credits_balance < $credits) {
                Log::warning('Insufficient credits', [
                    'required' => $credits,
                    'available' => $tenant->ai_credits_balance
                ]);
                // Still process but log warning
            }

            // Deduct credits
            $tenant->ai_credits_balance -= $credits;
            $tenant->save();
            
            // Record transaction in new ai_credit_transactions table
            DB::table('ai_credit_transactions')->insert([
                'tenant_id' => $tenant->id,
                'user_id' => auth()->id() ?? 1,
                'provider' => $providerName,
                'model' => $model,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'total_tokens' => $inputTokens + $outputTokens,
                'credits_used' => $credits,
                'cost_per_token' => $credits / ($inputTokens + $outputTokens),
                'transaction_type' => $metadata['transaction_type'] ?? 'ai_usage',
                'feature_name' => $metadata['feature'] ?? $metadata['source'] ?? 'ai_feature',
                'metadata' => json_encode($metadata),
                'processed_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            Log::info('💰 Credits deducted successfully', [
                'tenant_id' => $tenant->id,
                'credits_used' => $credits,
                'remaining_credits' => $tenant->ai_credits_balance,
                'provider' => $providerName,
                'model' => $model,
                'tokens' => $inputTokens + $outputTokens
            ]);
            
            return $credits;
            
        } catch (\Exception $e) {
            Log::error('❌ Credit deduction failed', [
                'error' => $e->getMessage(),
                'provider' => $providerName,
                'model' => $model,
                'tokens' => $inputTokens + $outputTokens
            ]);
            return 0;
        }
    }
}
