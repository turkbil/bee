<?php

namespace Modules\LanguageManagement\App\Http\Livewire;

use Livewire\Component;
use Modules\LanguageManagement\App\Services\LanguageService;
use Modules\LanguageManagement\App\Models\SiteLanguage;
use Modules\LanguageManagement\App\Models\SystemLanguage;
use Illuminate\Support\Facades\Session;

class LanguageSwitcher extends Component
{
    public $currentLanguage;
    public $availableLanguages = [];
    public $showDropdown = false;
    public $showFlags = true;
    public $showText = true;
    public $style = 'dropdown'; // dropdown, buttons, links
    public $context = 'site'; // site or admin
    
    protected $listeners = ['languageChanged' => 'refreshComponent'];

    public function mount($context = 'site')
    {
        $this->context = $context;
        
        // Context'e göre dilleri çek
        if ($this->context === 'admin') {
            $this->availableLanguages = SystemLanguage::where('is_active', true)
                ->orderBy('sort_order')
                ->pluck('code')
                ->toArray();
            
            // Admin dil öncelik sırası: 1. Session 2. User preference 3. Default
            $this->currentLanguage = Session::get('admin_locale');
            if (!$this->currentLanguage && auth()->check()) {
                $this->currentLanguage = auth()->user()->admin_language_preference ?? auth()->user()->language;
            }
            if (!$this->currentLanguage) {
                $this->currentLanguage = config('app.locale', 'tr');
            }
        } else {
            $this->availableLanguages = SiteLanguage::where('is_active', true)
                ->orderBy('sort_order')
                ->pluck('code')
                ->toArray();
                
            // Site dil öncelik sırası: 1. App locale (en güncel) 2. Session 3. User preference 4. Default
            $this->currentLanguage = app()->getLocale();
            if (!$this->currentLanguage) {
                $this->currentLanguage = Session::get('site_locale') ?? Session::get('locale');
            }
            if (!$this->currentLanguage && auth()->check()) {
                $this->currentLanguage = auth()->user()->site_language_preference;
            }
            if (!$this->currentLanguage) {
                $this->currentLanguage = config('app.locale', 'tr');
            }
        }
        
    }

    public function switchLanguage($languageCode)
    {
        // Debug log
        \Log::info('LanguageSwitcher::switchLanguage called', [
            'languageCode' => $languageCode,
            'currentLanguage' => $this->currentLanguage,
            'context' => $this->context,
            'availableLanguages' => $this->availableLanguages
        ]);
        
        // Zaten aktif dil seçiliyse hiçbir şey yapma
        if ($this->currentLanguage === $languageCode) {
            \Log::info('LanguageSwitcher: Same language selected, skipping');
            return;
        }
        
        if (in_array($languageCode, $this->availableLanguages)) {
            if ($this->context === 'admin') {
                // Admin context - route ile yönlendir (A21 tarzı)
                \Log::info('LanguageSwitcher: Redirecting to admin route');
                return redirect()->route('admin.language.switch', $languageCode);
            } else {
                // Site context - basit route ile yönlendir
                \Log::info('LanguageSwitcher: Redirecting to site route');
                return redirect()->route('site.language.switch', $languageCode);
            }
        } else {
            \Log::warning('LanguageSwitcher: Language not available', ['languageCode' => $languageCode]);
        }
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function refreshComponent()
    {
        if ($this->context === 'admin') {
            // Admin dil öncelik sırası: 1. Session 2. User preference 3. Default
            $this->currentLanguage = Session::get('admin_locale');
            if (!$this->currentLanguage && auth()->check()) {
                $this->currentLanguage = auth()->user()->admin_language_preference ?? auth()->user()->language;
            }
            if (!$this->currentLanguage) {
                $this->currentLanguage = config('app.locale', 'tr');
            }
        } else {
            // Site dil öncelik sırası: 1. App locale (en güncel) 2. Session 3. User preference 4. Default
            $this->currentLanguage = app()->getLocale();
            if (!$this->currentLanguage) {
                $this->currentLanguage = Session::get('site_locale') ?? Session::get('locale');
            }
            if (!$this->currentLanguage && auth()->check()) {
                $this->currentLanguage = auth()->user()->site_language_preference;
            }
            if (!$this->currentLanguage) {
                $this->currentLanguage = config('app.locale', 'tr');
            }
        }
    }

    protected function getLanguageName($languageCode)
    {
        $names = [
            'tr' => 'Türkçe',
            'en' => 'English', 
            'ar' => 'العربية'
        ];
        
        return $names[$languageCode] ?? $languageCode;
    }
    
    protected function getLanguageFlag($languageCode)
    {
        $flags = [
            'tr' => '🇹🇷',
            'en' => '🇺🇸',
            'ar' => '🇸🇦'
        ];
        
        return $flags[$languageCode] ?? '🌐';
    }
    
    protected function generateLanguageUrl($languageCode)
    {
        // Helper function kullan
        if (function_exists('current_url_for_locale')) {
            return current_url_for_locale($languageCode);
        }
        
        // Fallback
        return url('/');
    }
    

    public function render()
    {
        // Context'e göre veritabanından aktif dilleri çek
        if ($this->context === 'admin') {
            $languages = SystemLanguage::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        } else {
            $languages = SiteLanguage::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        }
        
        // Mevcut dili ilgili tablodan al
        $currentLanguageData = null;
        if ($this->context === 'admin') {
            $currentLanguageData = SystemLanguage::where('code', $this->currentLanguage)->first();
        } else {
            $currentLanguageData = SiteLanguage::where('code', $this->currentLanguage)->first();
        }
        
        return view('languagemanagement::livewire.language-switcher', [
            'currentLanguage' => $this->currentLanguage,
            'currentLanguageName' => $currentLanguageData ? $currentLanguageData->native_name : $this->getLanguageName($this->currentLanguage),
            'currentLanguageFlag' => $currentLanguageData ? $currentLanguageData->flag_icon : $this->getLanguageFlag($this->currentLanguage),
            'languages' => $languages->map(function ($lang) {
                return [
                    'code' => $lang->code,
                    'name' => $lang->native_name,
                    'flag' => $lang->flag_icon,
                ];
            })->toArray(),
            'context' => $this->context
        ]);
    }
}