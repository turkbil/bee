<?php

namespace Modules\LanguageManagement\app\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Modules\LanguageManagement\app\Services\SystemLanguageService;
use Modules\LanguageManagement\app\Services\SiteLanguageService;

class LanguageService
{
    protected $systemLanguageService;
    protected $siteLanguageService;

    public function __construct(
        SystemLanguageService $systemLanguageService,
        SiteLanguageService $siteLanguageService
    ) {
        $this->systemLanguageService = $systemLanguageService;
        $this->siteLanguageService = $siteLanguageService;
    }

    /**
     * Belirtilen context için dil geçerli mi kontrol et
     */
    public function isValidLanguageForContext(string $languageCode, string $context = 'admin'): bool
    {
        if ($context === 'admin') {
            $availableLanguages = $this->systemLanguageService->getAvailableLanguages();
        } else {
            $availableLanguages = $this->siteLanguageService->getAvailableLanguages();
        }

        return $availableLanguages->contains('code', $languageCode);
    }

    /**
     * Locale ayarla
     */
    public function setLocale(string $languageCode, string $context = 'admin'): void
    {
        // Laravel locale'ini ayarla
        App::setLocale($languageCode);
        
        // Session'a kaydet
        Session::put($context . '_locale', $languageCode);
        
        // Context'e göre service'i güncelle
        if ($context === 'admin') {
            $this->systemLanguageService->setCurrentLanguage($languageCode);
        } else {
            $this->siteLanguageService->setCurrentLanguage($languageCode);
        }
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
                $user->update(['admin_language_preference' => $languageCode]);
            }
        } else {
            // Site language tercihi  
            if ($user && method_exists($user, 'update')) {
                $user->update(['site_language_preference' => $languageCode]);
            }
        }
    }

    /**
     * Context için varsayılan dili getir
     */
    public function getDefaultLanguage(string $context = 'admin'): string
    {
        if ($context === 'admin') {
            return $this->systemLanguageService->getDefaultAdminLanguage();
        } else {
            return $this->siteLanguageService->getDefaultSiteLanguage();
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
            $preferenceField = $context === 'admin' ? 'admin_language_preference' : 'site_language_preference';
            
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
            return $this->systemLanguageService->getAvailableLanguages();
        } else {
            return $this->siteLanguageService->getAvailableLanguages();
        }
    }
    
    /**
     * Site dilini al - Session veya kullanıcı tercihinden
     */
    public function getSiteLanguage(): string
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