<?php

namespace Modules\Page\App\Services;

class PageTabService
{
    /**
     * Tab konfigürasyon bilgilerini al
     */
    public static function getTabConfig(): array
    {
        return config('page.tabs', []);
    }

    /**
     * Form persistence ayarlarını al
     */
    public static function getFormConfig(): array
    {
        return config('page.form', []);
    }

    /**
     * Aktif tab key'ini localStorage'dan al
     */
    public static function getStorageKey(): string
    {
        $config = self::getFormConfig();
        return $config['persistence']['storage_key'] ?? 'page_active_tab';
    }

    /**
     * Tüm tab'ları listele - orijinal 3 tab yapısı
     */
    public static function getAllTabs(): array
    {
        return [
            [
                'key' => 'basic',
                'name' => __('admin.basic_information'),
                'icon' => 'ti ti-file-text',
                'required_fields' => ['title', 'slug', 'content']
            ],
            [
                'key' => 'seo',
                'name' => 'SEO',
                'icon' => 'ti ti-seo',
                'required_fields' => ['seo_title']
            ],
            [
                'key' => 'code',
                'name' => 'Code',
                'icon' => 'ti ti-code',
                'required_fields' => []
            ]
        ];
    }

    /**
     * Belirli bir tab'ın bilgilerini al
     */
    public static function getTabByKey(string $key): ?array
    {
        $tabs = self::getAllTabs();
        
        foreach ($tabs as $tab) {
            if ($tab['key'] === $key) {
                return $tab;
            }
        }
        
        return null;
    }

    /**
     * Tab'ın gerekli alanlarını al
     */
    public static function getRequiredFields(string $tabKey): array
    {
        $tab = self::getTabByKey($tabKey);
        return $tab['required_fields'] ?? [];
    }

    /**
     * Tab'ın validation kurallarını oluştur
     */
    public static function getTabValidationRules(string $tabKey): array
    {
        $requiredFields = self::getRequiredFields($tabKey);
        $rules = [];

        foreach ($requiredFields as $field) {
            $rules[$field] = 'required';
        }

        return $rules;
    }

    /**
     * Tüm gerekli alanları topla
     */
    public static function getAllRequiredFields(): array
    {
        $tabs = self::getAllTabs();
        $allRequired = [];

        foreach ($tabs as $tab) {
            $allRequired = array_merge($allRequired, $tab['required_fields'] ?? []);
        }

        return array_unique($allRequired);
    }

    /**
     * Tab'ın dolu olup olmadığını kontrol et
     */
    public static function isTabComplete(string $tabKey, array $data): bool
    {
        $requiredFields = self::getRequiredFields($tabKey);
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Tab completion durumlarını hesapla
     */
    public static function getTabCompletionStatus(array $data): array
    {
        $tabs = self::getAllTabs();
        $status = [];

        foreach ($tabs as $tab) {
            $isComplete = self::isTabComplete($tab['key'], $data);
            $requiredCount = count($tab['required_fields'] ?? []);
            $completedCount = 0;

            foreach ($tab['required_fields'] ?? [] as $field) {
                if (!empty($data[$field])) {
                    $completedCount++;
                }
            }

            $status[$tab['key']] = [
                'complete' => $isComplete,
                'progress' => $requiredCount > 0 ? round(($completedCount / $requiredCount) * 100) : 100,
                'required_count' => $requiredCount,
                'completed_count' => $completedCount
            ];
        }

        return $status;
    }

    /**
     * Tab navigation için JavaScript config oluştur
     */
    public static function getJavaScriptConfig(): array
    {
        $config = self::getFormConfig();
        
        return [
            'storage_key' => self::getStorageKey(),
            'save_active_tab' => $config['persistence']['save_active_tab'] ?? true,
            'restore_on_load' => $config['persistence']['restore_on_load'] ?? true,
            'real_time_validation' => $config['validation']['real_time'] ?? true,
            'submit_button_states' => $config['validation']['submit_button_states'] ?? true,
            'tabs' => self::getAllTabs()
        ];
    }

    /**
     * İlk tab'ın key'ini al (varsayılan)
     */
    public static function getDefaultTabKey(): string
    {
        $tabs = self::getAllTabs();
        return !empty($tabs) ? $tabs[0]['key'] : 'content';
    }

    /**
     * JavaScript için tab data attribute'larını oluştur
     */
    public static function getTabDataAttributes(string $tabKey): string
    {
        $tab = self::getTabByKey($tabKey);
        if (!$tab) return '';

        $attributes = [
            'data-tab-key' => $tabKey,
            'data-tab-name' => $tab['name'] ?? '',
            'data-tab-icon' => $tab['icon'] ?? '',
            'data-required-fields' => implode(',', $tab['required_fields'] ?? [])
        ];

        return implode(' ', array_map(function($key, $value) {
            return $key . '="' . htmlspecialchars($value) . '"';
        }, array_keys($attributes), $attributes));
    }
}