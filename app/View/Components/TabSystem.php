<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class TabSystem extends Component
{
    public array $tabs;
    public array $tabCompletion;
    public array $tabConfig;
    public string $activeTab;
    public string $storageKey;
    
    /**
     * Global Tab System Component
     */
    public function __construct(
        array $tabs = [],
        array $tabCompletion = [],
        array $tabConfig = [],
        string $activeTab = '',
        string $storageKey = 'active_tab'
    ) {
        $this->tabs = $tabs;
        $this->tabCompletion = $tabCompletion;
        $this->tabConfig = $tabConfig;
        $this->activeTab = $activeTab ?: ($tabs[0]['key'] ?? 'content');
        $this->storageKey = $storageKey;
    }

    /**
     * Tab completion durumunu hesapla
     */
    public function getTabCompletionStatus(array $data): array
    {
        $status = [];

        foreach ($this->tabs as $tab) {
            $requiredFields = $tab['required_fields'] ?? [];
            $isComplete = true;
            $completedCount = 0;
            $requiredCount = count($requiredFields);

            foreach ($requiredFields as $field) {
                if (!empty($data[$field])) {
                    $completedCount++;
                } else {
                    $isComplete = false;
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
     * JavaScript configuration'ını oluştur
     */
    public function getJavaScriptConfig(): array
    {
        return [
            'storage_key' => $this->storageKey,
            'save_active_tab' => true,
            'restore_on_load' => true,
            'real_time_validation' => true,
            'tabs' => $this->tabs
        ];
    }

    /**
     * Tab data attribute'larını oluştur
     */
    public function getTabDataAttributes(string $tabKey): string
    {
        $tab = collect($this->tabs)->firstWhere('key', $tabKey);
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

    /**
     * Component view'ını render et
     */
    public function render(): View
    {
        return view('components.tab-system', [
            'tabs' => $this->tabs,
            'tabCompletion' => $this->tabCompletion,
            'activeTab' => $this->activeTab,
            'storageKey' => $this->storageKey,
            'jsConfig' => $this->getJavaScriptConfig()
        ]);
    }
}