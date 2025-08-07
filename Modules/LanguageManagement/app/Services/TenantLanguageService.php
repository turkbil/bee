<?php

namespace Modules\LanguageManagement\app\Services;

use Modules\LanguageManagement\app\Models\TenantLanguage;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class TenantLanguageService
{
    /**
     * Mevcut tenant locale'i al
     */
    public function getCurrentTenantLocale(): string
    {
        $currentLocale = App::getLocale();
        
        if ($this->isValidTenantLanguage($currentLocale)) {
            return $currentLocale;
        }
        
        return $this->getDefaultTenantLanguage();
    }
    
    /**
     * Tenant için varsayılan dili al (Hiyerarşi: User > Tenant Default > First Active)
     */
    public function getDefaultTenantLanguage(): string
    {
        // 1. User locale (sadece auth user'lar için)
        if (auth()->check()) {
            $userLanguage = $this->getUserTenantLocale();
            if ($userLanguage && $this->isValidTenantLanguage($userLanguage)) {
                return $userLanguage;
            }
        }
        
        // 2. Tenant varsayılan dili
        $defaultLanguage = $this->getTenantDefaultLanguage();
        if ($defaultLanguage) {
            return $defaultLanguage->code;
        }
        
        // 3. İlk aktif dil
        $firstActive = TenantLanguage::active()->ordered()->first();
        if ($firstActive) {
            return $firstActive->code;
        }
        
        // 4. Fallback
        return 'tr';
    }
    
    /**
     * is_default kolonunu tenants.tenant_default_locale ile senkronize et
     */
    public function syncDefaultLanguageColumn(): void
    {
        try {
            // Önce tüm is_default'ları false yap
            TenantLanguage::query()->update(['is_default' => false]);
            
            // Tenant default locale'i bul
            $defaultCode = $this->getTenantDefaultLocale();
            
            if ($defaultCode) {
                // Varsayılan dili true yap
                TenantLanguage::where('code', $defaultCode)
                    ->update(['is_default' => true]);
            }
        } catch (\Exception $e) {
            \Log::warning('TenantLanguageService: is_default sync failed', [
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Kullanıcının tenant locale'ini al
     */
    public function getUserTenantLocale(): ?string
    {
        $user = auth()->user();
        if (!$user) {
            return null;
        }
        
        return $user->tenant_locale;
    }
    
    /**
     * Tenant varsayılan dilini al (artık tenants tablosundan)
     */
    public function getTenantDefaultLanguage(): ?TenantLanguage
    {
        // Artık tenants tablosundan alınıyor
        if (function_exists('tenant') && tenant()) {
            $defaultCode = tenant()->tenant_default_locale ?? 'tr';
            return TenantLanguage::byCode($defaultCode)->first();
        }
        
        return TenantLanguage::active()->ordered()->first();
    }
    
    /**
     * Aktif tenant dillerini al (cache'li)
     */
    public function getActiveTenantLanguages(): array
    {
        return Cache::remember('tenant_languages.active.' . $this->getTenantCacheKey(), 1800, function () {
            return TenantLanguage::active()->ordered()->get()->toArray();
        });
    }
    
    /**
     * Aktif tenant dillerini Collection olarak al (LanguageService için)
     */
    public function getAvailableLanguages()
    {
        return Cache::remember('tenant_languages.collection.' . $this->getTenantCacheKey(), 1800, function () {
            return TenantLanguage::active()->ordered()->get();
        });
    }
    
    /**
     * Tenant dili geçerli mi kontrol et
     */
    public function isValidTenantLanguage(string $languageCode): bool
    {
        $activeLanguages = $this->getActiveTenantLanguages();
        $codes = array_column($activeLanguages, 'code');
        return in_array($languageCode, $codes);
    }
    
    /**
     * Kullanıcının tenant dil tercihini kaydet
     */
    public function setUserTenantLanguagePreference(string $languageCode): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        
        if (!$this->isValidTenantLanguage($languageCode)) {
            return false;
        }
        
        $user->tenant_locale = $languageCode;
        return $user->save();
    }
    
    /**
     * Tenant dili koduna göre dil bilgisini al
     */
    public function getTenantLanguageByCode(string $code): ?TenantLanguage
    {
        return Cache::remember("tenant_language.{$code}." . $this->getTenantCacheKey(), 1800, function () use ($code) {
            return TenantLanguage::byCode($code)->first();
        });
    }
    
    /**
     * Tenant dili oluştur/güncelle
     */
    public function createOrUpdateTenantLanguage(array $data): TenantLanguage
    {
        $language = TenantLanguage::updateOrCreate(
            ['code' => $data['code']],
            $data
        );
        
        $this->clearTenantLanguageCache();
        
        return $language;
    }
    
    /**
     * Tenant dilini sil
     */
    public function deleteTenantLanguage(int $id): bool
    {
        $language = TenantLanguage::find($id);
        if (!$language) {
            return false;
        }
        
        $deleted = $language->delete();
        
        if ($deleted) {
            $this->clearTenantLanguageCache();
        }
        
        return $deleted;
    }
    
    /**
     * Varsayılan tenant dilini ayarla (artık tenants tablosunda)
     */
    public function setDefaultTenantLanguage(string $code): bool
    {
        if (function_exists('tenant') && tenant()) {
            tenant()->update(['tenant_default_locale' => $code]);
            $this->clearTenantLanguageCache();
            return true;
        }
        
        return false;
    }
    
    /**
     * Tenant locale'i set et
     */
    public function setTenantLocale(string $languageCode): bool
    {
        if (!$this->isValidTenantLanguage($languageCode)) {
            return false;
        }
        
        App::setLocale($languageCode);
        return true;
    }
    
    /**
     * Mevcut dili getir (LanguageService için)
     */
    public function getCurrentLanguage(): string
    {
        return $this->getCurrentTenantLocale();
    }
    
    /**
     * Mevcut dili set et (LanguageService için)
     */
    public function setCurrentLanguage(string $languageCode): bool
    {
        return $this->setTenantLocale($languageCode);
    }
    
    /**
     * Tenant'ın varsayılan locale'ini al (Dashboard için)
     */
    public function getTenantDefaultLocale(): string
    {
        $defaultLanguage = $this->getTenantDefaultLanguage();
        if ($defaultLanguage) {
            return $defaultLanguage->code;
        }
        
        // İlk aktif dil
        $firstActive = TenantLanguage::active()->ordered()->first();
        if ($firstActive) {
            return $firstActive->code;
        }
        
        return 'tr';
    }

    /**
     * Tenant dil cache'ini temizle
     */
    public function clearTenantLanguageCache(): void
    {
        Cache::forget('tenant_language.default');
        Cache::forget('tenant_languages.active.' . $this->getTenantCacheKey());
        Cache::forget('tenant_languages.collection.' . $this->getTenantCacheKey());
        
        $languages = TenantLanguage::all();
        foreach ($languages as $language) {
            Cache::forget("tenant_language.{$language->code}." . $this->getTenantCacheKey());
        }
        
        // Language regex cache'ini de temizle
        if (function_exists('clearLanguageRegexCache')) {
            clearLanguageRegexCache();
        }
        
        // URL prefix cache'ini de temizle
        if (class_exists('Modules\LanguageManagement\app\Services\UrlPrefixService')) {
            \Modules\LanguageManagement\app\Services\UrlPrefixService::clearCache();
        }
    }
    
    /**
     * Tenant cache key'i al
     */
    private function getTenantCacheKey(): string
    {
        if (function_exists('tenant') && tenant()) {
            return tenant()->id;
        }
        
        return 'central';
    }
}