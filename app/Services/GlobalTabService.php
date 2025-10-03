<?php

namespace App\Services;

class GlobalTabService
{
    /**
     * Modül-specific tab konfigürasyonunu al
     */
    public static function getTabConfig(string $module = 'default'): array
    {
        $moduleConfig = config("{$module}.tabs", []);
        $defaultConfig = config('tabs.default', []);
        
        return array_merge($defaultConfig, $moduleConfig);
    }

    /**
     * Modül-specific form konfigürasyonunu al
     */
    public static function getFormConfig(string $module = 'default'): array
    {
        $moduleConfig = config("{$module}.form", []);
        $defaultConfig = config('tabs.form', []);
        
        return array_merge($defaultConfig, $moduleConfig);
    }

    /**
     * Aktif tab key'ini localStorage'dan al
     */
    public static function getStorageKey(string $module = 'default'): string
    {
        $config = self::getFormConfig($module);
        $defaultKey = $module === 'default' ? 'active_tab' : "{$module}_active_tab";
        
        return $config['persistence']['storage_key'] ?? $defaultKey;
    }

    /**
     * Modül için tüm tab'ları listele
     */
    public static function getAllTabs(string $module = 'default'): array
    {
        // Direkt modül config'inden tabs al
        $tabs = config("{$module}.tabs", null);

        // Modül-specific tab'lar varsa onları kullan
        if (!empty($tabs)) {
            return $tabs;
        }

        // Varsayılan tab yapısı (Page modülü pattern'i)
        return [
            [
                'key' => 'basic',
                'name' => __('admin.basic_information'),
                'icon' => 'fas fa-file-text',
                'required_fields' => ['title', 'slug', 'content']
            ],
            [
                'key' => 'seo',
                'name' => 'SEO',
                'icon' => 'fas fa-search',
                'required_fields' => ['seo_title']
            ],
            [
                'key' => 'code',
                'name' => 'Code',
                'icon' => 'fas fa-code',
                'required_fields' => []
            ]
        ];
    }

    /**
     * Belirli bir tab'ın bilgilerini al
     */
    public static function getTabByKey(string $key, string $module = 'default'): ?array
    {
        $tabs = self::getAllTabs($module);
        
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
    public static function getRequiredFields(string $tabKey, string $module = 'default'): array
    {
        $tab = self::getTabByKey($tabKey, $module);
        return $tab['required_fields'] ?? [];
    }

    /**
     * Tab'ın validation kurallarını oluştur
     */
    public static function getTabValidationRules(string $tabKey, string $module = 'default'): array
    {
        $requiredFields = self::getRequiredFields($tabKey, $module);
        $rules = [];

        foreach ($requiredFields as $field) {
            $rules[$field] = 'required';
        }

        return $rules;
    }

    /**
     * Tüm gerekli alanları topla
     */
    public static function getAllRequiredFields(string $module = 'default'): array
    {
        $tabs = self::getAllTabs($module);
        $allRequired = [];

        foreach ($tabs as $tab) {
            $allRequired = array_merge($allRequired, $tab['required_fields'] ?? []);
        }

        return array_unique($allRequired);
    }

    /**
     * Tab'ın dolu olup olmadığını kontrol et
     */
    public static function isTabComplete(string $tabKey, array $data, string $module = 'default'): bool
    {
        $requiredFields = self::getRequiredFields($tabKey, $module);
        
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
    public static function getTabCompletionStatus(array $data, string $module = 'default'): array
    {
        $tabs = self::getAllTabs($module);
        $status = [];

        foreach ($tabs as $tab) {
            $isComplete = self::isTabComplete($tab['key'], $data, $module);
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
    public static function getJavaScriptConfig(string $module = 'default'): array
    {
        $config = self::getFormConfig($module);
        
        return [
            'storage_key' => self::getStorageKey($module),
            'save_active_tab' => $config['persistence']['save_active_tab'] ?? true,
            'restore_on_load' => $config['persistence']['restore_on_load'] ?? true,
            'real_time_validation' => $config['validation']['real_time'] ?? true,
            'submit_button_states' => $config['validation']['submit_button_states'] ?? true,
            'tabs' => self::getAllTabs($module),
            'module' => $module
        ];
    }

    /**
     * İlk tab'ın key'ini al (varsayılan)
     */
    public static function getDefaultTabKey(string $module = 'default'): string
    {
        $tabs = self::getAllTabs($module);
        return !empty($tabs) ? $tabs[0]['key'] : 'content';
    }

    /**
     * JavaScript için tab data attribute'larını oluştur
     */
    public static function getTabDataAttributes(string $tabKey, string $module = 'default'): string
    {
        $tab = self::getTabByKey($tabKey, $module);
        if (!$tab) return '';

        $attributes = [
            'data-tab-key' => $tabKey,
            'data-tab-name' => $tab['name'] ?? '',
            'data-tab-icon' => $tab['icon'] ?? '',
            'data-required-fields' => implode(',', $tab['required_fields'] ?? []),
            'data-module' => $module
        ];

        return implode(' ', array_map(function($key, $value) {
            return $key . '="' . htmlspecialchars($value) . '"';
        }, array_keys($attributes), $attributes));
    }

    /**
     * Modül için varsayılan tab konfigürasyonunu oluştur
     */
    public static function getDefaultTabsForModule(string $module): array
    {
        $moduleDisplayName = ucfirst($module);
        
        return [
            [
                'key' => 'basic',
                'name' => __('admin.basic_information'),
                'icon' => 'fas fa-file-text',
                'required_fields' => ['title']
            ],
            [
                'key' => 'seo',
                'name' => 'SEO',
                'icon' => 'fas fa-search',
                'required_fields' => ['seo_title']
            ]
        ];
    }

    /**
     * Modül konfigürasyonunu kontrol et ve eksikleri tamamla
     */
    public static function ensureModuleTabConfig(string $module): array
    {
        $existingConfig = config("{$module}.tabs", []);
        
        // Konfigürasyon yoksa varsayılanları oluştur
        if (empty($existingConfig)) {
            return self::getDefaultTabsForModule($module);
        }
        
        return $existingConfig;
    }
}