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
    
    protected $listeners = ['languageChanged' => 'refreshComponent'];

    public function mount()
    {
        // İlk yüklemede kullanıcının DB tercihlerini session'a yükle
        if (auth()->check()) {
            if (auth()->user()->admin_locale && !session()->has('admin_locale')) {
                session(['admin_locale' => auth()->user()->admin_locale]);
            }
            if (auth()->user()->tenant_locale && !session()->has('tenant_locale')) {
                session(['tenant_locale' => auth()->user()->tenant_locale]);
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
        
        \Log::info('🎯 AdminLanguageSwitcher - switchLanguage çağrıldı', [
            'new_locale' => $languageCode,
            'current_locale' => $this->currentLanguage,
            'tenant_locale' => session('tenant_locale'),
            'user_id' => auth()->id()
        ]);
        
        // 3 Aşamalı Hibrit Sistem ile admin dil değiştir
        if (set_user_admin_language($languageCode)) {
            $this->isLoading = true;
            $this->currentLanguage = $languageCode;
            
            \Log::info('🎯 AdminLanguageSwitcher - set_user_admin_language başarılı', [
                'new_locale' => $languageCode,
                'session_admin_locale' => session('admin_locale'),
                'session_tenant_locale' => session('tenant_locale')
            ]);
            
            // Toast mesajı
            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('admin.admin_language_changed'),
                'type' => 'success'
            ]);
            
            // Admin panelinde kalarak sayfa yenileme
            $this->dispatch('refreshPage');
        }
    }

    public function switchSiteLanguage($languageCode)
    {
        // Zaten aktif site dili seçiliyse hiçbir şey yapma
        if ($this->currentSiteLanguage === $languageCode) {
            return;
        }
        
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
                'title' => __('admin.success'),
                'message' => __('languagemanagement::admin.data_language_changed'),
                'type' => 'success'
            ]);
            
            // Page component'lerini refresh et
            $this->dispatch('refreshPageData');
            
            // Admin panelinde kalarak sayfa yenileme
            $this->dispatch('refreshPage');
        }
    }



    public function refreshComponent()
    {
        // 3 Aşamalı Hibrit Sistem ile refresh
        $this->currentLanguage = current_admin_language();
        $this->currentSiteLanguage = current_tenant_language();
    }


    public function render()
    {
        // Admin admin_languages tablosundan aktif dilleri çek
        $adminLanguages = AdminLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        // Site tenant_languages tablosundan aktif dilleri çek
        $siteLanguages = DB::table('tenant_languages')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        // Mevcut admin dilini admin_languages tablosundan al
        $currentAdminLanguageData = AdminLanguage::where('code', $this->currentLanguage)->first();
        
        // Mevcut site dilini tenant_languages tablosundan al
        $currentSiteLanguageData = DB::table('tenant_languages')->where('code', $this->currentSiteLanguage)->first();
        
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