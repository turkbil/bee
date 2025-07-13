<?php

namespace Modules\LanguageManagement\App\Http\Livewire;

use Livewire\Component;
use Modules\LanguageManagement\App\Models\AdminLanguage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class AdminLanguageSwitcher extends Component
{
    public $currentLanguage;
    public $currentSiteLanguage;
    public $isLoading = false;
    public $loadingLanguageCode = null;
    public $loadingLanguageFlag = null;
    
    protected $listeners = ['languageChanged' => 'refreshComponent'];

    public function mount()
    {
        // PERFORMANCE: Cache user data to avoid repeated auth() queries
        static $cachedUser = null;
        if ($cachedUser === null) {
            $cachedUser = auth()->user();
        }
        
        // İlk yüklemede kullanıcının DB tercihlerini session'a yükle
        if ($cachedUser) {
            if ($cachedUser->admin_locale && !session()->has('admin_locale')) {
                session(['admin_locale' => $cachedUser->admin_locale]);
            }
            if ($cachedUser->tenant_locale && !session()->has('tenant_locale')) {
                session(['tenant_locale' => $cachedUser->tenant_locale]);
            }
        }
        
        $this->currentLanguage = current_admin_language();
        $this->currentSiteLanguage = current_tenant_language();
    }

    public function switchLanguage($languageCode)
    {
        // Zaten aktif dil seçiliyse hiçbir şey yapma
        if ($this->currentLanguage === $languageCode) {
            return;
        }
        
        // Loading başladığında event dispatch
        $this->dispatch('languageStarted');
        
        // Loading state için dil bilgisini ayarla
        $this->loadingLanguageCode = $languageCode;
        $adminLanguages = cache()->remember('admin_languages_switcher', 600, function() {
            return \Modules\LanguageManagement\App\Models\AdminLanguage::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });
        $targetLanguage = $adminLanguages->firstWhere('code', $languageCode);
        $this->loadingLanguageFlag = $targetLanguage ? $targetLanguage->flag_icon : '🌐';
        
        \Log::info('🎯 AdminLanguageSwitcher - switchLanguage çağrıldı', [
            'new_locale' => $languageCode,
            'current_locale' => $this->currentLanguage,
            'tenant_locale' => session('tenant_locale'),
            'user_id' => auth()->id()
        ]);
        
        // 3 Aşamalı Hibrit Sistem ile admin dil değiştir
        if (set_user_admin_language($languageCode)) {
            $this->currentLanguage = $languageCode;
            
            \Log::info('🎯 AdminLanguageSwitcher - set_user_admin_language başarılı', [
                'new_locale' => $languageCode,
                'session_admin_locale' => session('admin_locale'),
                'session_tenant_locale' => session('tenant_locale')
            ]);
            
            // Toast mesajı
            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'Admin dili değiştirildi',
                'type' => 'success'
            ]);
            
            // Kısa delay ekle loading göstermek için
            usleep(300000); // 0.3 saniye
            
            // Admin panelinde kalarak sayfa yenileme
            $this->dispatch('refreshPage');
        }
        
        // Loading bittiğinde event dispatch
        $this->dispatch('languageFinished');
        
        // Loading state'i temizle
        $this->loadingLanguageCode = null;
        $this->loadingLanguageFlag = null;
    }

    public function switchSiteLanguage($languageCode)
    {
        // Zaten aktif site dili seçiliyse hiçbir şey yapma
        if ($this->currentSiteLanguage === $languageCode) {
            return;
        }
        
        // Loading başladığında event dispatch
        $this->dispatch('languageStarted');
        
        // Loading state için dil bilgisini ayarla
        $this->loadingLanguageCode = $languageCode;
        $siteLanguages = cache()->remember('tenant_languages_switcher', 600, function() {
            return \DB::table('tenant_languages')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });
        $targetLanguage = collect($siteLanguages)->firstWhere('code', $languageCode);
        $this->loadingLanguageFlag = $targetLanguage ? $targetLanguage->flag_icon : '🌐';
        
        \Log::info('🎯 AdminLanguageSwitcher - switchSiteLanguage çağrıldı', [
            'new_locale' => $languageCode,
            'current_locale' => $this->currentSiteLanguage,
            'admin_locale' => session('admin_locale'),
            'user_id' => auth()->id()
        ]);
        
        // 3 Aşamalı Hibrit Sistem ile tenant dil değiştir  
        if (set_user_tenant_language($languageCode)) {
            $this->currentSiteLanguage = $languageCode;
            
            \Log::info('🎯 AdminLanguageSwitcher - set_user_tenant_language başarılı', [
                'new_locale' => $languageCode,
                'session_admin_locale' => session('admin_locale'),
                'session_tenant_locale' => session('tenant_locale')
            ]);
            
            // Cache temizleme (tenant-aware)
            $tenantId = tenant('id');
            if ($tenantId) {
                cache()->tags(["tenant_{$tenantId}_response_cache"])->flush();
            }
            
            // Toast mesajı
            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'Veri dili değiştirildi',
                'type' => 'success'
            ]);
            
            // Page component'lerini refresh et
            $this->dispatch('refreshPageData');
            
            // Kısa delay ekle loading göstermek için
            usleep(300000); // 0.3 saniye
            
            // Admin panelinde kalarak sayfa yenileme
            $this->dispatch('refreshPage');
        }
        
        // Loading bittiğinde event dispatch
        $this->dispatch('languageFinished');
        
        // Loading state'i temizle
        $this->loadingLanguageCode = null;
        $this->loadingLanguageFlag = null;
    }



    public function refreshComponent()
    {
        // 3 Aşamalı Hibrit Sistem ile refresh
        $this->currentLanguage = current_admin_language();
        $this->currentSiteLanguage = current_tenant_language();
    }


    public function render()
    {
        // PERFORMANCE: Cache all language queries for 10 minutes
        $adminLanguages = cache()->remember('admin_languages_switcher', 600, function() {
            return AdminLanguage::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });
        
        $siteLanguages = cache()->remember('tenant_languages_switcher', 600, function() {
            return DB::table('tenant_languages')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });
        
        // Find current languages from cached collections
        $currentAdminLanguageData = $adminLanguages->firstWhere('code', $this->currentLanguage);
        $currentSiteLanguageData = collect($siteLanguages)->firstWhere('code', $this->currentSiteLanguage);
        
        // Başlangıçta admin dilini göster (admin flag icon'u)
        $currentLanguageObject = $currentAdminLanguageData;
        
        return view('languagemanagement::livewire.admin-language-switcher', [
            'adminLanguages' => $adminLanguages,
            'siteLanguages' => $siteLanguages,
            'currentAdminLanguage' => $currentAdminLanguageData,
            'currentSiteLanguage' => $currentSiteLanguageData,
            'currentLanguageObject' => $currentLanguageObject,
            'currentAdminLocale' => $this->currentLanguage,
            'currentSiteLocale' => $this->currentSiteLanguage,
            'isLoading' => $this->isLoading
        ]);
    }
}