<?php

namespace Modules\LanguageManagement\app\Services;

use Modules\LanguageManagement\app\Models\SystemLanguage;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class SystemLanguageService
{
    /**
     * Mevcut admin locale'i al
     */
    public function getCurrentAdminLocale(): string
    {
        $currentLocale = App::getLocale();
        
        if ($this->isValidSystemLanguage($currentLocale)) {
            return $currentLocale;
        }
        
        return $this->getDefaultAdminLanguage();
    }
    
    /**
     * Admin için varsayılan dili al (Hiyerarşi: User > Tenant > System)
     */
    public function getDefaultAdminLanguage(): string
    {
        // 1. User tercihi (sadece auth user'lar için)
        if (auth()->check()) {
            $userLanguage = $this->getUserAdminLanguagePreference();
            if ($userLanguage && $this->isValidSystemLanguage($userLanguage)) {
                return $userLanguage;
            }
        }
        
        // 2. Tenant varsayılanı
        $tenantLanguage = $this->getTenantAdminLanguage();
        if ($tenantLanguage && $this->isValidSystemLanguage($tenantLanguage)) {
            return $tenantLanguage;
        }
        
        // 3. Sistem varsayılanı
        return 'tr';
    }
    
    /**
     * Kullanıcının admin dil tercihini al
     */
    public function getUserAdminLanguagePreference(): ?string
    {
        $user = auth()->user();
        if (!$user) {
            return null;
        }
        
        return $user->admin_language_preference;
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
        
        return $tenant->admin_default_language;
    }
    
    /**
     * Aktif sistem dillerini al (cache'li)
     */
    public function getActiveSystemLanguages(): array
    {
        return Cache::remember('system_languages.active', 3600, function () {
            return SystemLanguage::active()->ordered()->get()->toArray();
        });
    }
    
    /**
     * Sistem dili geçerli mi kontrol et
     */
    public function isValidSystemLanguage(string $languageCode): bool
    {
        $activeLanguages = $this->getActiveSystemLanguages();
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
        
        if (!$this->isValidSystemLanguage($languageCode)) {
            return false;
        }
        
        $user->admin_language_preference = $languageCode;
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
        
        if (!$this->isValidSystemLanguage($languageCode)) {
            return false;
        }
        
        $tenant->admin_default_language = $languageCode;
        return $tenant->save();
    }
    
    /**
     * Sistem dili koduna göre dil bilgisini al
     */
    public function getSystemLanguageByCode(string $code): ?SystemLanguage
    {
        return Cache::remember("system_language.{$code}", 3600, function () use ($code) {
            return SystemLanguage::byCode($code)->first();
        });
    }
    
    /**
     * Sistem dili oluştur/güncelle
     */
    public function createOrUpdateSystemLanguage(array $data): SystemLanguage
    {
        $language = SystemLanguage::updateOrCreate(
            ['code' => $data['code']],
            $data
        );
        
        $this->clearSystemLanguageCache();
        
        return $language;
    }
    
    /**
     * Sistem dilini sil
     */
    public function deleteSystemLanguage(string $code): bool
    {
        // TR ve EN silinemez
        if (in_array($code, ['tr', 'en'])) {
            return false;
        }
        
        $language = SystemLanguage::byCode($code)->first();
        if (!$language) {
            return false;
        }
        
        $deleted = $language->delete();
        
        if ($deleted) {
            $this->clearSystemLanguageCache();
        }
        
        return $deleted;
    }
    
    /**
     * Admin locale'i set et
     */
    public function setAdminLocale(string $languageCode): bool
    {
        if (!$this->isValidSystemLanguage($languageCode)) {
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
     * Sistem dil cache'ini temizle
     */
    public function clearSystemLanguageCache(): void
    {
        Cache::forget('system_languages.active');
        
        $languages = SystemLanguage::all();
        foreach ($languages as $language) {
            Cache::forget("system_language.{$language->code}");
        }
    }
    
    /**
     * Aktif sistem dillerini collection olarak al (LanguageService için)
     */
    public function getAvailableLanguages()
    {
        return SystemLanguage::where('is_active', true)
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