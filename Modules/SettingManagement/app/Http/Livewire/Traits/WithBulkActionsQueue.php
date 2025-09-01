<?php

namespace Modules\SettingManagement\app\Http\Livewire\Traits;

use Illuminate\Support\Facades\Cache;
use Modules\SettingManagement\app\Jobs\BulkDeleteSettingsJob;
use Modules\SettingManagement\app\Jobs\BulkUpdateSettingsJob;

trait WithBulkActionsQueue
{
    public $selectedItems = [];
    public $selectAll = false;
    public $bulkActionsEnabled = false;
    public $bulkProgress = null;
    public $showBulkModal = false;
    public $bulkModalTitle = '';
    public $bulkModalContent = '';
    public $currentBulkJob = null;

    // Bulk update fields
    public $bulkUpdateValue = '';
    public $bulkUpdateType = '';
    public $bulkUpdateActive = '';
    public $bulkUpdatePublic = '';
    public $bulkUpdateGroupId = '';

    // Remove $listeners property to avoid conflicts - use getListeners() method instead in component
    
    protected function getBulkListeners()
    {
        return [
            'bulkProgressUpdate' => 'refreshBulkProgress',
            'bulkJobCompleted' => 'onBulkJobCompleted',
            'closeBulkModal' => 'closeBulkModal'
        ];
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedItems = $this->getModelClass()::pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
        $this->updateBulkActionsState();
    }

    public function toggleSelect($itemId)
    {
        if (in_array($itemId, $this->selectedItems)) {
            $this->selectedItems = array_filter($this->selectedItems, fn($id) => $id != $itemId);
        } else {
            $this->selectedItems[] = $itemId;
        }
        
        $this->selectAll = count($this->selectedItems) === $this->getModelClass()::count();
        $this->updateBulkActionsState();
    }

    public function updateBulkActionsState()
    {
        $this->bulkActionsEnabled = count($this->selectedItems) > 0;
    }

    // Bulk Delete
    public function bulkDelete()
    {
        $this->bulkModalTitle = 'Toplu Silme Onayı';
        $this->bulkModalContent = count($this->selectedItems) . ' ayarı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.';
        $this->showBulkModal = true;
    }

    public function confirmBulkDelete()
    {
        if (empty($this->selectedItems)) {
            $this->addError('selectedItems', 'Silinecek ayar seçiniz.');
            return;
        }

        $cacheKey = 'bulk_delete_settings_' . auth()->id() . '_' . time();
        $tenantId = tenant('id') ?? 'central';

        Cache::put($cacheKey, [
            'progress' => 0,
            'status' => 'processing',
            'message' => 'Toplu silme işlemi başlatılıyor...'
        ], 300);

        BulkDeleteSettingsJob::dispatch(
            $this->selectedItems,
            $tenantId,
            auth()->id(),
            $cacheKey
        )->onQueue('tenant_isolated_' . $tenantId);

        $this->currentBulkJob = $cacheKey;
        $this->bulkProgress = ['progress' => 0, 'status' => 'processing'];
        $this->closeBulkModal();
        
        $this->emit('bulkJobStarted', $cacheKey);
        session()->flash('message', 'Toplu silme işlemi başlatıldı.');
    }

    // Bulk Update Type
    public function bulkUpdateType()
    {
        $this->bulkModalTitle = 'Toplu Tip Güncelleme';
        $this->bulkModalContent = 'Seçili ayarların tipini güncellemek için yeni tip seçin:';
        $this->showBulkModal = true;
    }

    public function confirmBulkUpdateType()
    {
        if (empty($this->selectedItems)) {
            $this->addError('selectedItems', 'Güncellenecek ayar seçiniz.');
            return;
        }

        if (empty($this->bulkUpdateType)) {
            $this->addError('bulkUpdateType', 'Tip seçiniz.');
            return;
        }

        $updateData = ['type' => $this->bulkUpdateType];
        $this->executeBulkUpdate($updateData, 'tip güncelleme');
    }

    // Bulk Update Active Status
    public function bulkUpdateActive()
    {
        $this->bulkModalTitle = 'Toplu Aktiflik Durumu Güncelleme';
        $this->bulkModalContent = 'Seçili ayarların aktiflik durumunu güncellemek için seçim yapın:';
        $this->showBulkModal = true;
    }

    public function confirmBulkUpdateActive()
    {
        if (empty($this->selectedItems)) {
            $this->addError('selectedItems', 'Güncellenecek ayar seçiniz.');
            return;
        }

        if ($this->bulkUpdateActive === '') {
            $this->addError('bulkUpdateActive', 'Aktiflik durumu seçiniz.');
            return;
        }

        $updateData = ['is_active' => (bool) $this->bulkUpdateActive];
        $this->executeBulkUpdate($updateData, 'aktiflik durumu güncelleme');
    }

    // Bulk Update Public Status
    public function bulkUpdatePublic()
    {
        $this->bulkModalTitle = 'Toplu Genel Erişim Güncelleme';
        $this->bulkModalContent = 'Seçili ayarların genel erişim durumunu güncellemek için seçim yapın:';
        $this->showBulkModal = true;
    }

    public function confirmBulkUpdatePublic()
    {
        if (empty($this->selectedItems)) {
            $this->addError('selectedItems', 'Güncellenecek ayar seçiniz.');
            return;
        }

        if ($this->bulkUpdatePublic === '') {
            $this->addError('bulkUpdatePublic', 'Genel erişim durumu seçiniz.');
            return;
        }

        $updateData = ['is_public' => (bool) $this->bulkUpdatePublic];
        $this->executeBulkUpdate($updateData, 'genel erişim güncelleme');
    }

    // Bulk Update Group
    public function bulkUpdateGroup()
    {
        $this->bulkModalTitle = 'Toplu Grup Güncelleme';
        $this->bulkModalContent = 'Seçili ayarları yeni gruba taşımak için grup seçin:';
        $this->showBulkModal = true;
    }

    public function confirmBulkUpdateGroup()
    {
        if (empty($this->selectedItems)) {
            $this->addError('selectedItems', 'Güncellenecek ayar seçiniz.');
            return;
        }

        if (empty($this->bulkUpdateGroupId)) {
            $this->addError('bulkUpdateGroupId', 'Grup seçiniz.');
            return;
        }

        $updateData = ['group_id' => $this->bulkUpdateGroupId];
        $this->executeBulkUpdate($updateData, 'grup güncelleme');
    }

    // Bulk Update Value
    public function bulkUpdateValue()
    {
        $this->bulkModalTitle = 'Toplu Değer Güncelleme';
        $this->bulkModalContent = 'Seçili ayarların değerini güncellemek için yeni değer girin:';
        $this->showBulkModal = true;
    }

    public function confirmBulkUpdateValue()
    {
        if (empty($this->selectedItems)) {
            $this->addError('selectedItems', 'Güncellenecek ayar seçiniz.');
            return;
        }

        if ($this->bulkUpdateValue === '') {
            $this->addError('bulkUpdateValue', 'Değer giriniz.');
            return;
        }

        $updateData = ['value' => $this->bulkUpdateValue];
        $this->executeBulkUpdate($updateData, 'değer güncelleme');
    }

    // Common bulk update execution
    private function executeBulkUpdate(array $updateData, string $operation)
    {
        $cacheKey = 'bulk_update_settings_' . auth()->id() . '_' . time();
        $tenantId = tenant('id') ?? 'central';

        Cache::put($cacheKey, [
            'progress' => 0,
            'status' => 'processing',
            'message' => "Toplu {$operation} işlemi başlatılıyor..."
        ], 300);

        BulkUpdateSettingsJob::dispatch(
            $this->selectedItems,
            $updateData,
            $tenantId,
            auth()->id(),
            $cacheKey
        )->onQueue('tenant_isolated_' . $tenantId);

        $this->currentBulkJob = $cacheKey;
        $this->bulkProgress = ['progress' => 0, 'status' => 'processing'];
        $this->closeBulkModal();
        $this->resetBulkFields();

        $this->emit('bulkJobStarted', $cacheKey);
        session()->flash('message', "Toplu {$operation} işlemi başlatıldı.");
    }

    // Modal management
    public function closeBulkModal()
    {
        $this->showBulkModal = false;
        $this->bulkModalTitle = '';
        $this->bulkModalContent = '';
        $this->resetBulkFields();
    }

    private function resetBulkFields()
    {
        $this->bulkUpdateValue = '';
        $this->bulkUpdateType = '';
        $this->bulkUpdateActive = '';
        $this->bulkUpdatePublic = '';
        $this->bulkUpdateGroupId = '';
    }

    // Progress tracking
    public function refreshBulkProgress()
    {
        if ($this->currentBulkJob) {
            $progress = Cache::get($this->currentBulkJob);
            if ($progress) {
                $this->bulkProgress = $progress;
                
                if (in_array($progress['status'] ?? '', ['completed', 'failed'])) {
                    $this->onBulkJobCompleted();
                }
            }
        }
    }

    public function onBulkJobCompleted()
    {
        $this->selectedItems = [];
        $this->selectAll = false;
        $this->bulkActionsEnabled = false;
        $this->currentBulkJob = null;
        
        // Refresh the component data
        $this->emit('refreshComponent');
        $this->render();
    }

    // Clear progress manually
    public function clearBulkProgress()
    {
        $this->bulkProgress = null;
        $this->currentBulkJob = null;
    }

    // Helper method - should be implemented in the component
    abstract protected function getModelClass();
}