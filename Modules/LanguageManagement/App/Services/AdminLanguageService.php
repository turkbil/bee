<?php

namespace Modules\LanguageManagement\app\Services;

use Modules\LanguageManagement\app\Models\AdminLanguage;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class AdminLanguageService
{
    /**
     * Mevcut admin locale'i al
     */
    public function getCurrentAdminLocale(): string
    {
        $currentLocale = App::getLocale();
        
        if ($this->isValidAdminLanguage($currentLocale)) {
            return $currentLocale;
        }
        
        return $this->getDefaultAdminLanguage();
    }
    
    /**
     * Admin için varsayılan dili al (Hiyerarşi: User > Tenant > System)
     */
    public function getDefaultAdminLanguage(): string
    {
        // 1. User locale (sadece auth user'lar için)
        if (auth()->check()) {
            $userLanguage = $this->getUserAdminLocale();
            if ($userLanguage && $this->isValidAdminLanguage($userLanguage)) {
                return $userLanguage;
            }
        }
        
        // 2. Tenant varsayılanı
        $tenantLanguage = $this->getTenantAdminLanguage();
        if ($tenantLanguage && $this->isValidAdminLanguage($tenantLanguage)) {
            return $tenantLanguage;
        }
        
        // 3. Sistem varsayılanı
        return 'tr';
    }
    
    /**
     * Kullanıcının admin locale'ini al
     */
    public function getUserAdminLocale(): ?string
    {
        $user = auth()->user();
        if (!$user) {
            return null;
        }
        
        return $user->admin_locale;
    }
    
    /**
     * Tenant'ın admin dilini al
     */
    public function getTenantAdminLanguage(): ?string
    {
        $tenant = $this->getCurrentTenant();
        if (!$tenant) {
            return null;
        }
        
        return $tenant->admin_default_locale;
    }
    
    /**
     * Aktif admin dillerini al (cache'li)
     */
    public function getActiveAdminLanguages(): array
    {
        return Cache::remember('admin_languages.active', 3600, function () {
            return AdminLanguage::active()->ordered()->get()->toArray();
        });
    }
    
    /**
     * Admin dili geçerli mi kontrol et
     */
    public function isValidAdminLanguage(string $languageCode): bool
    {
        $activeLanguages = $this->getActiveAdminLanguages();
        $codes = array_column($activeLanguages, 'code');
        return in_array($languageCode, $codes);
    }
    
    /**
     * Kullanıcının admin dil tercihini kaydet
     */
    public function setUserAdminLanguagePreference(string $languageCode): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        
        if (!$this->isValidAdminLanguage($languageCode)) {
            return false;
        }
        
        $user->admin_locale = $languageCode;
        return $user->save();
    }
    
    /**
     * Tenant admin dil ayarını güncelle
     */
    public function updateTenantAdminLanguage(string $languageCode): bool
    {
        $tenant = $this->getCurrentTenant();
        if (!$tenant) {
            return false;
        }
        
        if (!$this->isValidAdminLanguage($languageCode)) {
            return false;
        }
        
        $tenant->admin_default_locale = $languageCode;
        return $tenant->save();
    }
    
    /**
     * Admin dili koduna göre dil bilgisini al
     */
    public function getAdminLanguageByCode(string $code): ?AdminLanguage
    {
        return Cache::remember("admin_language.{$code}", 3600, function () use ($code) {
            return AdminLanguage::byCode($code)->first();
        });
    }
    
    /**
     * Admin dili oluştur/güncelle
     */
    public function createOrUpdateAdminLanguage(array $data): AdminLanguage
    {
        $language = AdminLanguage::updateOrCreate(
            ['code' => $data['code']],
            $data
        );
        
        $this->clearAdminLanguageCache();
        
        return $language;
    }
    
    /**
     * Admin dilini sil
     */
    public function deleteAdminLanguage(string $code): bool
    {
        // TR ve EN silinemez
        if (in_array($code, ['tr', 'en'])) {
            return false;
        }
        
        $language = AdminLanguage::byCode($code)->first();
        if (!$language) {
            return false;
        }
        
        $deleted = $language->delete();
        
        if ($deleted) {
            $this->clearAdminLanguageCache();
        }
        
        return $deleted;
    }
    
    /**
     * Admin locale'i set et
     */
    public function setAdminLocale(string $languageCode): bool
    {
        if (!$this->isValidAdminLanguage($languageCode)) {
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
        return $this->getCurrentAdminLocale();
    }
    
    /**
     * Mevcut dili set et (LanguageService için)
     */
    public function setCurrentLanguage(string $languageCode): bool
    {
        return $this->setAdminLocale($languageCode);
    }
    
    /**
     * Admin dil cache'ini temizle
     */
    public function clearAdminLanguageCache(): void
    {
        Cache::forget('admin_languages.active');
        
        $languages = AdminLanguage::all();
        foreach ($languages as $language) {
            Cache::forget("admin_language.{$language->code}");
        }
    }
    
    /**
     * Aktif admin dillerini collection olarak al (LanguageService için)
     */
    public function getAvailableLanguages()
    {
        return AdminLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }
    
    /**
     * Mevcut tenant'ı al
     */
    private function getCurrentTenant(): ?Tenant
    {
        if (function_exists('tenant')) {
            return tenant();
        }
        
        return null;
    }
}