<?php

namespace App\Livewire;

use Livewire\Component;
use Modules\LanguageManagement\App\Models\SystemLanguage;

class LanguageSwitcher extends Component
{
    public $currentLocale;
    public $currentLanguage;
    public $systemLanguages;
    public $isLoading = false;

    public function mount()
    {
        $this->isLoading = false; // Açıkça false yap
        $this->loadLanguageData();
    }

    public function loadLanguageData()
    {
        // Dil seçim mantığı: 1. User admin dili 2. Tenant admin dili 3. tr
        $this->currentLocale = 'tr'; // Varsayılan

        // 1. Kullanıcının seçtiği admin dili
        if (auth()->check() && auth()->user()->language) {
            $this->currentLocale = auth()->user()->language;
        }
        // 2. Tenant'ın seçtiği admin dili (varsa)
        elseif (function_exists('tenant') && tenant() && isset(tenant()->admin_language)) {
            $this->currentLocale = tenant()->admin_language;
        }

        // Sistem dillerini al
        if (class_exists('Modules\LanguageManagement\App\Models\SystemLanguage')) {
            $this->systemLanguages = SystemLanguage::where('is_active', true)
                ->orderBy('id')
                ->get();
        } else {
            $this->systemLanguages = collect();
        }

        // Mevcut dil bilgisi
        $this->currentLanguage = $this->systemLanguages->firstWhere('code', $this->currentLocale);
    }

    public function switchLanguage($languageCode)
    {
        // Zaten aktif dil seçiliyse hiçbir şey yapma
        if ($this->currentLocale === $languageCode) {
            return;
        }

        // Loading durumunu aktif et
        $this->isLoading = true;

        // Dil değiştirme route'una yönlendir
        return redirect()->route('admin.language.switch', $languageCode);
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }
}