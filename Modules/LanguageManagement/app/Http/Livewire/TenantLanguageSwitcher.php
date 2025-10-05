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
        $this->availableLanguages = \App\Services\TenantLanguageProvider::getActiveLanguages();
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

            // 🧹 DİL DEĞİŞİMİ CACHE TEMİZLEME - Eski dil içerikleri gözükmesin
            try {
                // 1. ResponseCache tamamen temizle (eski dil içerikleri için)
                if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
                    \Spatie\ResponseCache\Facades\ResponseCache::clear();
                }

                // 2. Tenant-specific cache temizle
                $tenantId = tenant('id');
                if ($tenantId) {
                    // Redis'ten tenant cache'leri temizle
                    $redis = \Illuminate\Support\Facades\Redis::connection();
                    $pattern = "*tenant_{$tenantId}_*";
                    $keys = $redis->keys($pattern);

                    if (!empty($keys)) {
                        foreach ($keys as $key) {
                            $redis->del($key);
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Language switch cache clear error: ' . $e->getMessage());
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
        $this->availableLanguages = \App\Services\TenantLanguageProvider::getActiveLanguages();
        
        return view('languagemanagement::livewire.tenant-language-switcher');
    }
}