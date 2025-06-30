<?php

namespace Modules\LanguageManagement\App\Http\Livewire;

use Livewire\Component;
use Modules\LanguageManagement\App\Models\TenantLanguage;

class TenantLanguageSwitcher extends Component
{
    public $currentLanguage;
    public $availableLanguages;

    public function mount()
    {
        $this->currentLanguage = session('site_locale', 'tr');
        $this->loadAvailableLanguages();
    }

    protected function loadAvailableLanguages()
    {
        $this->availableLanguages = TenantLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function switchLanguage($locale)
    {
        // Site locale session'ını güncelle
        session(['site_locale' => $locale]);

        $this->currentLanguage = $locale;

        // Cache temizleme (tenant-aware)
        $tenantId = tenant('id');
        if ($tenantId) {
            cache()->tags(["tenant_{$tenantId}_response_cache"])->flush();
        }

        // Toast mesajı
        $this->dispatch('toast', [
            'title' => __('admin.success'),
            'message' => __('languagemanagement::admin.data_language_changed'),
            'type' => 'success',
        ]);

        // Sayfayı yenile (sayfa içeriklerini güncellemek için)
        $this->redirect(request()->url() . '?_=' . time() . '&data_lang_changed=' . $locale);
    }

    public function render()
    {
        return view('languagemanagement::livewire.tenant-language-switcher');
    }
}