<?php

namespace App\Http\Livewire\Components;

use Livewire\Component;
use App\Services\GlobalTabService;
use Illuminate\Support\Facades\Log;

/**
 * UNIVERSAL TAB SYSTEM COMPONENT
 * Pattern: A1 CMS Universal System
 *
 * Tüm modüller için ortak Tab System Component'i
 * Tab management, tab completion status, GlobalTabService entegrasyonu
 *
 * Kullanım:
 * <livewire:universal-tab-system
 *     module="page"
 *     :active-tab="$activeTab"
 *     :data="$allFormData"
 * />
 */
class UniversalTabSystemComponent extends Component
{
    // Tab yönetimi
    public $module = 'default';
    public $activeTab;
    public $tabConfig = [];
    public $tabCompletionStatus = [];

    // Form data (tab completion için)
    public $data = [];

    // Listeners
    protected $listeners = [
        'tab-changed' => 'handleTabChange',
        'update-tab-completion' => 'updateTabCompletionStatus',
        'refresh-tabs' => '$refresh',
    ];

    public function mount($module = 'default', $activeTab = null, $data = [])
    {
        $this->module = $module;
        $this->data = $data;

        // Tab konfigürasyonunu yükle
        $this->loadTabConfiguration();

        // Active tab belirle
        if ($activeTab && $this->isValidTab($activeTab)) {
            $this->activeTab = $activeTab;
        } else {
            $this->activeTab = GlobalTabService::getDefaultTabKey($this->module);
        }

        // Tab completion durumunu hesapla
        $this->updateTabCompletionStatus();

        Log::info('📑 UniversalTabSystem mounted', [
            'module' => $this->module,
            'active_tab' => $this->activeTab,
            'tab_count' => count($this->tabConfig)
        ]);
    }

    /**
     * Tab konfigürasyonunu yükle
     */
    protected function loadTabConfiguration()
    {
        $this->tabConfig = GlobalTabService::getAllTabs($this->module);
    }

    /**
     * Tab geçerli mi kontrol et
     */
    protected function isValidTab($tabKey): bool
    {
        foreach ($this->tabConfig as $tab) {
            if ($tab['key'] === $tabKey) {
                return true;
            }
        }
        return false;
    }

    /**
     * Tab değişikliğini handle et
     */
    public function handleTabChange($tabData)
    {
        $newTabKey = $tabData['tab'] ?? $tabData;

        if (!$this->isValidTab($newTabKey)) {
            Log::warning('⚠️ Geçersiz tab değiştirme talebi', [
                'requested_tab' => $newTabKey,
                'valid_tabs' => array_column($this->tabConfig, 'key')
            ]);
            return;
        }

        $oldTab = $this->activeTab;
        $this->activeTab = $newTabKey;

        Log::info('🔄 Tab değiştirildi', [
            'old_tab' => $oldTab,
            'new_tab' => $newTabKey,
            'module' => $this->module
        ]);

        // Parent component'e tab değişikliğini bildir
        $this->dispatch('tabSwitched', [
            'oldTab' => $oldTab,
            'newTab' => $newTabKey,
            'module' => $this->module
        ]);
    }

    /**
     * Tab completion durumunu güncelle
     */
    public function updateTabCompletionStatus($data = null)
    {
        if ($data !== null) {
            $this->data = is_array($data) ? $data : [];
        }

        $this->tabCompletionStatus = GlobalTabService::getTabCompletionStatus($this->data, $this->module);

        Log::info('📊 Tab completion güncellendi', [
            'module' => $this->module,
            'completed_tabs' => count(array_filter($this->tabCompletionStatus, fn($status) => $status['complete']))
        ]);

        // Parent component'e güncellemeyi bildir
        $this->dispatch('tabCompletionUpdated', [
            'status' => $this->tabCompletionStatus,
            'module' => $this->module
        ]);
    }

    /**
     * Belirli bir tab'ın completion durumunu al
     */
    public function getTabCompletion($tabKey): array
    {
        return $this->tabCompletionStatus[$tabKey] ?? [
            'complete' => false,
            'progress' => 0,
            'required_count' => 0,
            'completed_count' => 0
        ];
    }

    /**
     * Tüm tab'lar dolu mu kontrol et
     */
    public function areAllTabsComplete(): bool
    {
        foreach ($this->tabCompletionStatus as $status) {
            if (!$status['complete']) {
                return false;
            }
        }
        return true;
    }

    /**
     * JavaScript config'i al
     */
    public function getJavaScriptConfig(): array
    {
        return GlobalTabService::getJavaScriptConfig($this->module);
    }

    /**
     * Active tab'ı al (parent component için)
     */
    public function getActiveTab(): string
    {
        return $this->activeTab;
    }

    /**
     * Tab config'i al (parent component için)
     */
    public function getTabConfig(): array
    {
        return $this->tabConfig;
    }

    /**
     * Tab completion status'u al (parent component için)
     */
    public function getTabCompletionStatus(): array
    {
        return $this->tabCompletionStatus;
    }

    public function render()
    {
        return view('components.universal-tab-system-component', [
            'module' => $this->module,
            'activeTab' => $this->activeTab,
            'tabConfig' => $this->tabConfig,
            'tabCompletionStatus' => $this->tabCompletionStatus,
            'jsConfig' => $this->getJavaScriptConfig()
        ]);
    }
}