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
        
        // Tenant varsayılan dilini al
        $currentTenant = null;
        if (app(\Stancl\Tenancy\Tenancy::class)->initialized) {
            $currentTenant = tenant();
        } else {
            // Central context'teyse domain'den çözümle
            $host = request()->getHost();
            $domain = \Stancl\Tenancy\Database\Models\Domain::with('tenant')
                ->where('domain', $host)
                ->first();
            $currentTenant = $domain?->tenant;
        }
        
        $defaultLanguageCode = $currentTenant ? $currentTenant->tenant_default_locale : 'tr';
        
        // is_default kolonunu senkronize et
        app(\Modules\LanguageManagement\app\Services\TenantLanguageService::class)->syncDefaultLanguageColumn();
            
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
        
        // Varsayılan dili tenants tablosundan al
        $currentTenant = null;
        if (app(\Stancl\Tenancy\Tenancy::class)->initialized) {
            $currentTenant = tenant();
        } else {
            // Central context'teyse domain'den çözümle
            $host = request()->getHost();
            $domain = \Stancl\Tenancy\Database\Models\Domain::with('tenant')
                ->where('domain', $host)
                ->first();
            $currentTenant = $domain?->tenant;
        }
        
        if ($currentTenant) {
            $this->urlPrefixMode = 'except_default'; // Default value
            $this->defaultLanguageCode = $currentTenant->tenant_default_locale ?? 'tr';
        } else {
            $this->urlPrefixMode = 'except_default';
            $this->defaultLanguageCode = 'tr';
        }
    }
    
    public function saveUrlPrefixSettings()
    {
        $this->validate([
            'urlPrefixMode' => 'required|in:none,except_default,all',
            'defaultLanguageCode' => 'required|exists:tenant_languages,code'
        ]);
        
        // Varsayılan dili tenants tablosunda güncelle
        $currentTenant = null;
        if (app(\Stancl\Tenancy\Tenancy::class)->initialized) {
            $currentTenant = tenant();
        } else {
            // Central context'teyse domain'den çözümle
            $host = request()->getHost();
            $domain = \Stancl\Tenancy\Database\Models\Domain::with('tenant')
                ->where('domain', $host)
                ->first();
            $currentTenant = $domain?->tenant;
        }
        
        if ($currentTenant) {
            $currentTenant->update([
                'tenant_default_locale' => $this->defaultLanguageCode
            ]);
        }
        
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