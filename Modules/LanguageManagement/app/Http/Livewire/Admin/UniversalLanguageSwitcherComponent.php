<?php

namespace Modules\LanguageManagement\App\Http\Livewire\Admin;

use Livewire\Component;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Illuminate\Support\Facades\Log;

/**
 * UNIVERSAL LANGUAGE SWITCHER COMPONENT
 * Pattern: A1 CMS Universal System
 *
 * Tüm modüller için ortak Dil Değiştirme Component'i
 * JavaScript senkronizasyonu, session yönetimi ve dil state management
 *
 * Kullanım:
 * <livewire:languagemanagement::universal-language-switcher
 *     :current-language="$currentLanguage"
 *     :available-languages="$availableLanguages"
 *     storage-key="page_manage_language"
 * />
 */
class UniversalLanguageSwitcherComponent extends Component
{
    // Dil yönetimi
    public $currentLanguage;
    public $availableLanguages = [];
    public $storageKey = 'manage_language'; // Session storage key

    // Listeners
    protected $listeners = [
        'switchLanguage' => 'switchLanguage',
        'js-language-sync' => 'handleJavaScriptLanguageSync',
        'set-js-language' => 'setJavaScriptLanguage',
        'set-continue-mode' => 'setContinueMode',
    ];

    public function mount($currentLanguage = null, $availableLanguages = [], $storageKey = 'manage_language')
    {
        $this->storageKey = $storageKey;

        // Site dillerini yükle
        $this->loadAvailableLanguages();

        // Current language belirle
        if ($currentLanguage && in_array($currentLanguage, $this->availableLanguages)) {
            $this->currentLanguage = $currentLanguage;
        } else {
            $this->currentLanguage = $this->determinePreferredLanguage();
        }

        Log::info('🌍 UniversalLanguageSwitcher mounted', [
            'current_language' => $this->currentLanguage,
            'available_languages' => $this->availableLanguages,
            'storage_key' => $this->storageKey
        ]);
    }

    /**
     * Site dillerini yükle
     */
    protected function loadAvailableLanguages()
    {
        $languages = TenantLanguage::where('is_active', true)
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->toArray();

        $this->availableLanguages = !empty($languages) ? $languages : ['tr'];
    }

    /**
     * Tercih edilen dili belirle
     */
    protected function determinePreferredLanguage(): string
    {
        if (empty($this->availableLanguages)) {
            return 'tr';
        }

        // 1. Kaydet ve Devam Et modu kontrolü
        if (session($this->storageKey . '_continue_mode') && session('js_saved_language')) {
            $language = session('js_saved_language');
            session()->forget([$this->storageKey . '_continue_mode', 'js_saved_language']);
            if ($language && in_array($language, $this->availableLanguages, true)) {
                Log::info('🔄 Kaydet ve Devam Et - dil korundu', ['language' => $language]);
                return $language;
            }
        }

        // 2. JavaScript session kontrolü
        $sessionLanguage = session('js_current_language');
        if ($sessionLanguage && in_array($sessionLanguage, $this->availableLanguages, true)) {
            Log::info('🔄 JS dili korundu', ['language' => $sessionLanguage]);
            return $sessionLanguage;
        }

        // 3. Default language
        $defaultLanguage = session('site_default_language');
        if ($defaultLanguage && in_array($defaultLanguage, $this->availableLanguages, true)) {
            return $defaultLanguage;
        }

        // 4. Tenant default
        try {
            $tenantDefault = \App\Services\TenantLanguageProvider::getDefaultLanguageCode();
            if ($tenantDefault && in_array($tenantDefault, $this->availableLanguages, true)) {
                return $tenantDefault;
            }
        } catch (\Throwable $e) {
            // Silent catch
        }

        // 5. Fallback - ilk dil
        return $this->availableLanguages[0] ?? 'tr';
    }

    /**
     * Dil sekmesi değiştir
     */
    public function switchLanguage($language)
    {
        if (!in_array($language, $this->availableLanguages)) {
            Log::warning('⚠️ Geçersiz dil değiştirme talebi', [
                'requested_language' => $language,
                'available_languages' => $this->availableLanguages
            ]);
            return;
        }

        $oldLanguage = $this->currentLanguage;
        $this->currentLanguage = $language;

        // Session'a kaydet - save sonrası dil koruması için
        session([
            $this->storageKey => $language,
            'js_current_language' => $language
        ]);

        Log::info('🎯 UniversalLanguageSwitcher dil değiştirildi', [
            'old_language' => $oldLanguage,
            'new_language' => $language,
            'storage_key' => $this->storageKey
        ]);

        // Parent component'e dil değişikliğini bildir
        $this->dispatch('languageChanged', $language);

        // JavaScript'e dil değişikliğini bildir (TinyMCE, vb. için)
        $this->dispatch('language-switched', [
            'language' => $language,
            'oldLanguage' => $oldLanguage
        ]);
    }

    /**
     * JavaScript Language Sync Handler
     */
    public function handleJavaScriptLanguageSync($data)
    {
        $jsLanguage = $data['language'] ?? '';
        $oldLanguage = $this->currentLanguage;

        Log::info('🚨 handleJavaScriptLanguageSync çağrıldı', [
            'js_language' => $jsLanguage,
            'current_language' => $this->currentLanguage,
            'will_change' => in_array($jsLanguage, $this->availableLanguages) && $jsLanguage !== $this->currentLanguage
        ]);

        if (in_array($jsLanguage, $this->availableLanguages) && $jsLanguage !== $this->currentLanguage) {
            $this->currentLanguage = $jsLanguage;

            // Session'a kaydet
            session(['js_current_language' => $jsLanguage]);

            // Parent component'e bildir
            $this->dispatch('languageChanged', $jsLanguage);

            // JavaScript'e confirmation gönder
            $this->dispatch('language-sync-completed', [
                'language' => $jsLanguage,
                'oldLanguage' => $oldLanguage,
                'success' => true
            ]);

            Log::info('🔄 JavaScript Language Sync başarılı', [
                'old_language' => $oldLanguage,
                'new_language' => $jsLanguage
            ]);
        }
    }

    /**
     * JavaScript Language Session Handler
     */
    public function setJavaScriptLanguage($data)
    {
        $jsLanguage = $data['language'] ?? '';

        // Session'a JavaScript currentLanguage'i kaydet
        session(['js_current_language' => $jsLanguage]);

        Log::info('📝 JavaScript language session\'a kaydedildi', [
            'js_language' => $jsLanguage,
            'current_livewire_language' => $this->currentLanguage
        ]);
    }

    /**
     * Kaydet ve Devam Et Handler
     */
    public function setContinueMode($data)
    {
        session([
            $this->storageKey . '_continue_mode' => $data['continue_mode'] ?? false,
            'js_saved_language' => $data['saved_language'] ?? 'tr'
        ]);

        Log::info('✅ Kaydet ve Devam Et - session kaydedildi', [
            'continue_mode' => $data['continue_mode'] ?? false,
            'saved_language' => $data['saved_language'] ?? 'tr'
        ]);
    }

    /**
     * Current language'i al (parent component için)
     */
    public function getCurrentLanguage(): string
    {
        return $this->currentLanguage;
    }

    public function render()
    {
        return view('languagemanagement::admin.livewire.universal-language-switcher-component', [
            'currentLanguage' => $this->currentLanguage,
            'availableLanguages' => $this->availableLanguages,
            'tenantLanguages' => TenantLanguage::where('is_active', true)
                ->where('is_visible', true)
                ->orderBy('sort_order')
                ->get()
        ]);
    }
}