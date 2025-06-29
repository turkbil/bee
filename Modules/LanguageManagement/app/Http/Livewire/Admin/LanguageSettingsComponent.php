<?php

namespace Modules\LanguageManagement\app\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Modules\LanguageManagement\app\Services\AdminLanguageService;
use Modules\LanguageManagement\app\Services\TenantLanguageService;
use Modules\LanguageManagement\app\Models\AdminLanguage;
use Modules\LanguageManagement\app\Models\TenantLanguage;

#[Layout('admin.layout')]
class LanguageSettingsComponent extends Component
{
    public $systemLanguagesCount = 0;
    public $siteLanguagesCount = 0;
    public $currentAdminLanguage = 'tr';
    public $currentTenantLanguage = 'tr';
    public $recentAdminLanguages = [];
    public $recentTenantLanguages = [];
    
    // URL Prefix Ayarları
    public $urlPrefixMode = 'except_default'; // none, except_default, all
    public $defaultLanguageCode = 'tr';
    public $availableLanguages = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $systemLanguageService = app(AdminLanguageService::class);
        $siteLanguageService = app(TenantLanguageService::class);

        // Sistem dilleri istatistikleri
        $this->systemLanguagesCount = AdminLanguage::where('is_active', true)->count();
        $this->currentAdminLanguage = $systemLanguageService->getTenantAdminLanguage() ?: 'tr';
        $this->recentAdminLanguages = AdminLanguage::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Site dilleri istatistikleri
        $this->siteLanguagesCount = TenantLanguage::where('is_active', true)->count();
        $this->currentTenantLanguage = $siteLanguageService->getTenantDefaultLocale() ?: 'tr';
        $this->recentTenantLanguages = TenantLanguage::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
            
        // URL Prefix ayarlarını yükle
        $this->loadUrlPrefixSettings();
    }
    
    public function loadUrlPrefixSettings()
    {
        // Site dillerini yükle
        $this->availableLanguages = TenantLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['code', 'name', 'native_name'])
            ->toArray();
        
        // Varsayılan dili al
        $defaultLang = TenantLanguage::where('is_default', true)->first();
        if ($defaultLang) {
            $this->urlPrefixMode = $defaultLang->url_prefix_mode ?? 'except_default';
            $this->defaultLanguageCode = $defaultLang->code;
        }
    }
    
    public function saveUrlPrefixSettings()
    {
        $this->validate([
            'urlPrefixMode' => 'required|in:none,except_default,all',
            'defaultLanguageCode' => 'required|exists:tenant_languages,code'
        ]);
        
        // Tüm site dillerini güncelle
        TenantLanguage::query()->update(['url_prefix_mode' => $this->urlPrefixMode]);
        
        // Önce tüm is_default'ları false yap
        TenantLanguage::query()->update(['is_default' => false]);
        
        // Sonra yeni varsayılanı belirle
        TenantLanguage::where('code', $this->defaultLanguageCode)
            ->update(['is_default' => true]);
        
        // Cache temizle
        if (class_exists('Modules\LanguageManagement\app\Services\UrlPrefixService')) {
            \Modules\LanguageManagement\app\Services\UrlPrefixService::clearCache();
        }
        \App\Services\ModuleSlugService::clearCache();
        
        session()->flash('success', 'URL Prefix ayarları başarıyla kaydedildi!');
        
        if (function_exists('log_activity')) {
            log_activity('url_prefix_ayarlar_kaydedildi');
        }
    }

    public function render()
    {
        return view('languagemanagement::admin.livewire.language-settings-component');
    }
}