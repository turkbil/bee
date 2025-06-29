<?php

namespace Modules\LanguageManagement\App\Http\Livewire;

use Livewire\Component;
use Modules\LanguageManagement\App\Models\AdminLanguage;
use Illuminate\Support\Facades\Session;

class AdminLanguageSwitcher extends Component
{
    public $currentLanguage;
    public $availableLanguages = [];
    public $showDropdown = false;
    public $isLoading = false;
    
    protected $listeners = ['languageChanged' => 'refreshComponent'];

    public function mount()
    {
        // Admin dilleri al - sadece system_languages tablosundan
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
        // Admin system_languages tablosundan aktif dilleri çek
        $systemLanguages = AdminLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        // Mevcut dili system_languages tablosundan al
        $currentLanguageData = AdminLanguage::where('code', $this->currentLanguage)->first();
        $currentLocale = $this->currentLanguage;
        
        return view('languagemanagement::livewire.admin-language-switcher', [
            'systemLanguages' => $systemLanguages,
            'currentLanguageObject' => $currentLanguageData, // Obje olarak gönder
            'currentLanguage' => $this->currentLanguage, // String olarak gönder
            'currentLocale' => $currentLocale,
            'isLoading' => $this->isLoading
        ]);
    }
}