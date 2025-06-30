<?php

namespace Modules\LanguageManagement\app\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Modules\LanguageManagement\app\Services\AdminLanguageService;
use Modules\LanguageManagement\app\Services\TenantLanguageService;

class LanguageService
{
    protected $adminLanguageService;
    protected $tenantLanguageService;

    public function __construct(
        AdminLanguageService $adminLanguageService,
        TenantLanguageService $tenantLanguageService
    ) {
        $this->adminLanguageService = $adminLanguageService;
        $this->tenantLanguageService = $tenantLanguageService;
    }

    /**
     * Belirtilen context için dil geçerli mi kontrol et
     */
    public function isValidLanguageForContext(string $languageCode, string $context = 'admin'): bool
    {
        if ($context === 'admin') {
            $availableLanguages = $this->adminLanguageService->getAvailableLanguages();
        } else {
            $availableLanguages = $this->tenantLanguageService->getAvailableLanguages();
        }

        return $availableLanguages->contains('code', $languageCode);
    }

    /**
     * Locale ayarla - SADECE belirtilen context'i değiştirir
     */
    public function setLocale(string $languageCode, string $context = 'admin'): void
    {
        \Log::info('🔧 LanguageService setLocale çağrıldı', [
            'language_code' => $languageCode,
            'context' => $context,
            'before_admin_locale' => session('admin_locale'),
            'before_site_locale' => session('site_locale')
        ]);
        
        // Laravel locale'ini ayarla (sadece admin context'te)
        if ($context === 'admin') {
            App::setLocale($languageCode);
        }
        
        // Session'a kaydet - SADECE belirtilen context
        Session::put($context . '_locale', $languageCode);
        
        // Context'e göre service'i güncelle
        if ($context === 'admin') {
            $this->adminLanguageService->setCurrentLanguage($languageCode);
        } else {
            $this->tenantLanguageService->setCurrentLanguage($languageCode);
        }
        
        \Log::info('✅ LanguageService setLocale tamamlandı', [
            'context' => $context,
            'new_language' => $languageCode,
            'after_admin_locale' => session('admin_locale'),
            'after_site_locale' => session('site_locale')
        ]);
    }

    /**
     * Kullanıcı dil tercihini kaydet
     */
    public function setUserLanguagePreference(string $languageCode, string $context = 'admin'): void
    {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();
        
        if ($context === 'admin') {
            // Admin language tercihi
            if ($user && method_exists($user, 'update')) {
                $user->update(['admin_locale' => $languageCode]);
            }
        } else {
            // Site language tercihi  
            if ($user && method_exists($user, 'update')) {
                $user->update(['tenant_locale' => $languageCode]);
            }
        }
    }

    /**
     * Context için varsayılan dili getir
     */
    public function getDefaultLanguage(string $context = 'admin'): string
    {
        if ($context === 'admin') {
            return $this->adminLanguageService->getDefaultAdminLanguage();
        } else {
            return $this->tenantLanguageService->getDefaultTenantLanguage();
        }
    }

    /**
     * Mevcut locale'i getir
     */
    public function getCurrentLocale(string $context = 'admin'): string
    {
        // Session'dan kontrol et - domain-specific key desteği ile
        if ($context === 'site') {
            // Site context için domain-specific key kontrol et
            $domain = request()->getHost();
            $domainSessionKey = 'site_locale_' . str_replace('.', '_', $domain);
            
            // Önce domain-specific key'i kontrol et
            if (Session::has($domainSessionKey)) {
                $domainLocale = Session::get($domainSessionKey);
                
                // Generic key'i de güncelle (sync için)
                Session::put('site_locale', $domainLocale);
                
                \Log::info('✅ LanguageService: Domain-specific locale bulundu', [
                    'domain' => $domain,
                    'domain_key' => $domainSessionKey,
                    'locale' => $domainLocale
                ]);
                
                return $domainLocale;
            }
            
            // Fallback: Generic key kontrol et
            if (Session::has('site_locale')) {
                return Session::get('site_locale');
            }
        } else {
            // Admin context için normal key
            $sessionKey = $context . '_locale';
            if (Session::has($sessionKey)) {
                return Session::get($sessionKey);
            }
        }

        // Kullanıcı tercihini kontrol et
        if (Auth::check()) {
            $user = Auth::user();
            $preferenceField = $context === 'admin' ? 'admin_locale' : 'tenant_locale';
            
            if ($user && isset($user->$preferenceField) && $user->$preferenceField) {
                return $user->$preferenceField;
            }
        }

        // Varsayılan dili döndür
        return $this->getDefaultLanguage($context);
    }

    /**
     * Context için mevcut dilleri getir
     */
    public function getAvailableLanguages(string $context = 'admin')
    {
        if ($context === 'admin') {
            return $this->adminLanguageService->getAvailableLanguages();
        } else {
            return $this->tenantLanguageService->getAvailableLanguages();
        }
    }
    
    /**
     * Site dilini al - Session veya kullanıcı tercihinden
     */
    public function getTenantLanguage(): string
    {
        return $this->getCurrentLocale('site');
    }
    
    /**
     * Admin dilini al - Session veya kullanıcı tercihinden  
     */
    public function getAdminLanguage(): string
    {
        return $this->getCurrentLocale('admin');
    }
}