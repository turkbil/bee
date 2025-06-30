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
    public $availableLanguages = [];
    public $showDropdown = false;
    public $isLoading = false;
    
    protected $listeners = ['languageChanged' => 'refreshComponent'];

    public function mount()
    {
        // Admin dilleri al - admin_languages tablosundan
        $this->availableLanguages = AdminLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->toArray();
        
        // Admin dil öncelik sırası: 1. Session 2. User locale 3. Default
        $this->currentLanguage = Session::get('admin_locale');
        if (!$this->currentLanguage && auth()->check()) {
            $this->currentLanguage = auth()->user()->admin_locale;
        }
        if (!$this->currentLanguage) {
            $this->currentLanguage = config('app.locale', 'tr');
        }

        // Site dili de al
        $this->currentSiteLanguage = Session::get('site_locale', 'tr');
    }

    public function switchLanguage($languageCode)
    {
        // Zaten aktif dil seçiliyse hiçbir şey yapma
        if ($this->currentLanguage === $languageCode) {
            return;
        }
        
        if (in_array($languageCode, $this->availableLanguages)) {
            $this->isLoading = true;
            
            // Admin context - route ile yönlendir
            return redirect()->route('admin.language.switch', $languageCode);
        }
    }

    public function switchSiteLanguage($languageCode)
    {
        \Log::info('🔄 VERI DİL DEĞİŞİMİ BAŞLADI', [
            'requested_language' => $languageCode,
            'current_site_language' => $this->currentSiteLanguage,
            'current_admin_language' => $this->currentLanguage,
            'current_url' => request()->url(),
            'session_before' => [
                'admin_locale' => session('admin_locale'),
                'site_locale' => session('site_locale')
            ]
        ]);
        
        // Zaten aktif site dili seçiliyse hiçbir şey yapma
        if ($this->currentSiteLanguage === $languageCode) {
            \Log::info('❌ Aynı dil seçildi, işlem iptal', ['language' => $languageCode]);
            return;
        }
        
        // Site language kodlarını kontrol et (tenant_languages tablosundan)
        $validSiteLanguages = DB::table('tenant_languages')->where('is_active', true)->pluck('code')->toArray();
        \Log::info('✅ Geçerli site dilleri kontrol edildi', ['valid_languages' => $validSiteLanguages]);
        
        if (!in_array($languageCode, $validSiteLanguages)) {
            \Log::warning('❌ Geçersiz site dil kodu', ['requested' => $languageCode, 'valid' => $validSiteLanguages]);
            return;
        }
        
        // Site locale session'ını güncelle (sadece site_locale değişir, admin_locale değişmez)
        session(['site_locale' => $languageCode]);
        $this->currentSiteLanguage = $languageCode;
        
        // Force session save ve regenerate
        session()->save();
        session()->migrate(true);
        
        // Laravel cache'i de temizle 
        app('session.store')->put('site_locale', $languageCode);
        app('session.store')->save();
        
        \Log::info('🔧 Session force save yapıldı', [
            'session_id' => session()->getId(),
            'site_locale_after_save' => session('site_locale'),
            'admin_locale_after_save' => session('admin_locale')
        ]);
        
        \Log::info('✅ Site locale session güncellendi', [
            'new_site_locale' => $languageCode,
            'admin_locale_unchanged' => session('admin_locale'),
            'session_after' => [
                'admin_locale' => session('admin_locale'),
                'site_locale' => session('site_locale')
            ]
        ]);

        // Cache temizleme (tenant-aware + aggressive)
        $tenantId = tenant('id');
        if ($tenantId) {
            // Response cache temizle
            cache()->tags(["tenant_{$tenantId}_response_cache"])->flush();
            
            // Laravel cache temizle
            cache()->forget("pages_list_{$tenantId}");
            cache()->forget("site_languages_{$tenantId}");
            
            // Livewire cache temizle
            if (class_exists('\\Livewire\\Mechanisms\\ComponentRegistry')) {
                cache()->forget('livewire:component-registry');
            }
            
            \Log::info('🧹 Aggressive cache temizlendi', ['tenant_id' => $tenantId]);
        }

        // Toast mesajı
        $this->dispatch('toast', [
            'title' => __('admin.success'), 
            'message' => __('languagemanagement::admin.data_language_changed'),
            'type' => 'success',
        ]);
        
        // Page component'lerini refresh et
        $this->dispatch('refreshPageData');

        // Admin panelinde kalacak şekilde refresh - mevcut admin sayfasında kal
        $currentUrl = request()->url();
        $refererUrl = request()->header('referer', '');
        $redirectUrl = '';
        
        // Livewire istekleri için referer URL'ini kullan
        if (str_contains($currentUrl, '/livewire/') && str_contains($refererUrl, '/admin/')) {
            // Referer URL'deki eski data_lang_changed parametresini temizle
            $cleanUrl = preg_replace('/[&?]data_lang_changed=[^&]*/', '', $refererUrl);
            $cleanUrl = preg_replace('/[&?]_=[^&]*/', '', $cleanUrl);
            
            // Yeni parametreleri ekle
            $redirectUrl = $cleanUrl . (str_contains($cleanUrl, '?') ? '&' : '?') . '_=' . time() . '&data_lang_changed=' . $languageCode;
            \Log::info('🔄 Livewire isteği - referer admin panelinde kalıyor', ['referer' => $refererUrl, 'clean_url' => $cleanUrl, 'redirect_to' => $redirectUrl]);
        } elseif (str_contains($currentUrl, '/admin/')) {
            // Normal admin paneli isteği
            $redirectUrl = $currentUrl . '?_=' . time() . '&data_lang_changed=' . $languageCode;
            \Log::info('🔄 Admin panelinde kalıyor', ['redirect_to' => $redirectUrl]);
        } else {
            // Admin panelinde değilsek dashboard'a git
            $redirectUrl = route('admin.dashboard') . '?_=' . time() . '&data_lang_changed=' . $languageCode;
            \Log::info('🔄 Admin dashboard\'a yönlendiriliyor', ['redirect_to' => $redirectUrl]);
        }
        
        \Log::info('🎯 VERI DİL DEĞİŞİMİ TAMAMLANDI', [
            'final_redirect_url' => $redirectUrl,
            'final_sessions' => [
                'admin_locale' => session('admin_locale'),
                'site_locale' => session('site_locale')
            ]
        ]);
        
        // Normal redirect ile yönlendir
        $this->redirect($redirectUrl);
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function refreshComponent()
    {
        // Admin dil öncelik sırası: 1. Session 2. User locale 3. Default
        $this->currentLanguage = Session::get('admin_locale');
        if (!$this->currentLanguage && auth()->check()) {
            $this->currentLanguage = auth()->user()->admin_locale;
        }
        if (!$this->currentLanguage) {
            $this->currentLanguage = config('app.locale', 'tr');
        }
    }

    protected function getLanguageName($languageCode)
    {
        $names = [
            'tr' => 'Türkçe',
            'en' => 'English'
        ];
        
        return $names[$languageCode] ?? $languageCode;
    }
    
    protected function getLanguageFlag($languageCode)
    {
        $flags = [
            'tr' => '🇹🇷',
            'en' => '🇺🇸'
        ];
        
        return $flags[$languageCode] ?? '🌐';
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