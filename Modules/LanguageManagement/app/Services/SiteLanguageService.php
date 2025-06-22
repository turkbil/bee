<?php

namespace Modules\LanguageManagement\app\Services;

use Modules\LanguageManagement\app\Models\SiteLanguage;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class SiteLanguageService
{
    /**
     * Mevcut site locale'i al
     */
    public function getCurrentSiteLocale(): string
    {
        $currentLocale = App::getLocale();
        
        if ($this->isValidSiteLanguage($currentLocale)) {
            return $currentLocale;
        }
        
        return $this->getDefaultSiteLanguage();
    }
    
    /**
     * Site için varsayılan dili al (Hiyerarşi: User > Site Default > First Active)
     */
    public function getDefaultSiteLanguage(): string
    {
        // 1. User tercihi (sadece auth user'lar için)
        if (auth()->check()) {
            $userLanguage = $this->getUserSiteLanguagePreference();
            if ($userLanguage && $this->isValidSiteLanguage($userLanguage)) {
                return $userLanguage;
            }
        }
        
        // 2. Site varsayılan dili
        $defaultLanguage = $this->getSiteDefaultLanguage();
        if ($defaultLanguage) {
            return $defaultLanguage->code;
        }
        
        // 3. İlk aktif dil
        $firstActive = SiteLanguage::active()->ordered()->first();
        if ($firstActive) {
            return $firstActive->code;
        }
        
        // 4. Fallback
        return 'tr';
    }
    
    /**
     * Kullanıcının site dil tercihini al
     */
    public function getUserSiteLanguagePreference(): ?string
    {
        $user = auth()->user();
        if (!$user) {
            return null;
        }
        
        return $user->site_language_preference;
    }
    
    /**
     * Site varsayılan dilini al
     */
    public function getSiteDefaultLanguage(): ?SiteLanguage
    {
        return Cache::remember('site_language.default', 1800, function () {
            return SiteLanguage::default()->first();
        });
    }
    
    /**
     * Aktif site dillerini al (cache'li)
     */
    public function getActiveSiteLanguages(): array
    {
        return Cache::remember('site_languages.active.' . $this->getTenantCacheKey(), 1800, function () {
            return SiteLanguage::active()->ordered()->get()->toArray();
        });
    }
    
    /**
     * Site dili geçerli mi kontrol et
     */
    public function isValidSiteLanguage(string $languageCode): bool
    {
        $activeLanguages = $this->getActiveSiteLanguages();
        $codes = array_column($activeLanguages, 'code');
        return in_array($languageCode, $codes);
    }
    
    /**
     * Kullanıcının site dil tercihini kaydet
     */
    public function setUserSiteLanguagePreference(string $languageCode): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        
        if (!$this->isValidSiteLanguage($languageCode)) {
            return false;
        }
        
        $user->site_language_preference = $languageCode;
        return $user->save();
    }
    
    /**
     * Site dili koduna göre dil bilgisini al
     */
    public function getSiteLanguageByCode(string $code): ?SiteLanguage
    {
        return Cache::remember("site_language.{$code}." . $this->getTenantCacheKey(), 1800, function () use ($code) {
            return SiteLanguage::byCode($code)->first();
        });
    }
    
    /**
     * Site dili oluştur/güncelle
     */
    public function createOrUpdateSiteLanguage(array $data): SiteLanguage
    {
        // Eğer varsayılan dil olarak işaretlenmişse, diğerlerini false yap
        if (isset($data['is_default']) && $data['is_default']) {
            SiteLanguage::query()->update(['is_default' => false]);
        }
        
        $language = SiteLanguage::updateOrCreate(
            ['code' => $data['code']],
            $data
        );
        
        $this->clearSiteLanguageCache();
        
        return $language;
    }
    
    /**
     * Site dilini sil
     */
    public function deleteSiteLanguage(int $id): bool
    {
        $language = SiteLanguage::find($id);
        if (!$language) {
            return false;
        }
        
        // Varsayılan dil siliniyorsa, başka bir dili varsayılan yap
        if ($language->is_default) {
            $newDefault = SiteLanguage::where('id', '!=', $id)->active()->first();
            if ($newDefault) {
                $newDefault->setAsDefault();
            }
        }
        
        $deleted = $language->delete();
        
        if ($deleted) {
            $this->clearSiteLanguageCache();
        }
        
        return $deleted;
    }
    
    /**
     * Varsayılan site dilini ayarla
     */
    public function setDefaultSiteLanguage(string $code): bool
    {
        $language = SiteLanguage::byCode($code)->first();
        if (!$language) {
            return false;
        }
        
        $language->setAsDefault();
        $this->clearSiteLanguageCache();
        
        return true;
    }
    
    /**
     * Site locale'i set et
     */
    public function setSiteLocale(string $languageCode): bool
    {
        if (!$this->isValidSiteLanguage($languageCode)) {
            return false;
        }
        
        App::setLocale($languageCode);
        return true;
    }
    
    /**
     * Tenant'ın varsayılan site dilini al (Dashboard için)
     */
    public function getTenantDefaultSiteLanguage(): string
    {
        $defaultLanguage = $this->getSiteDefaultLanguage();
        if ($defaultLanguage) {
            return $defaultLanguage->code;
        }
        
        // İlk aktif dil
        $firstActive = SiteLanguage::active()->ordered()->first();
        if ($firstActive) {
            return $firstActive->code;
        }
        
        return 'tr';
    }

    /**
     * Site dil cache'ini temizle
     */
    public function clearSiteLanguageCache(): void
    {
        Cache::forget('site_language.default');
        Cache::forget('site_languages.active.' . $this->getTenantCacheKey());
        
        $languages = SiteLanguage::all();
        foreach ($languages as $language) {
            Cache::forget("site_language.{$language->code}." . $this->getTenantCacheKey());
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