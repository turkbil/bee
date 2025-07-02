<?php

namespace Modules\LanguageManagement\App\Http\Livewire;

use Livewire\Component;

class TenantLanguageSwitcher extends Component
{
    public $currentLanguage;
    public $availableLanguages;

    public function mount()
    {
        // 3 Aşamalı Hibrit Sistem ile tenant dili al
        $this->currentLanguage = current_tenant_language();
        $this->availableLanguages = available_tenant_languages();
    }

    public function switchLanguage($locale)
    {
        // Zaten aktif dil seçiliyse hiçbir şey yapma
        if ($this->currentLanguage === $locale) {
            return;
        }

        // 3 Aşamalı Hibrit Sistem ile tenant dil değiştir
        if (set_user_tenant_language($locale)) {
            $this->currentLanguage = $locale;

            // Cache temizleme (tenant-aware)
            $tenantId = tenant('id');
            if ($tenantId) {
                cache()->tags(["tenant_{$tenantId}_response_cache"])->flush();
            }

            // Toast mesajı
            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('languagemanagement::admin.tenant_language_changed'),
                'type' => 'success',
            ]);

            // Sayfayı yenile (sayfa içeriklerini güncellemek için)
            $this->redirect(request()->url() . '?_=' . time() . '&tenant_lang_changed=' . $locale);
        }
    }

    public function render()
    {
        // En güncel dil listesi al
        $this->availableLanguages = available_tenant_languages();
        
        return view('languagemanagement::livewire.tenant-language-switcher');
    }
}