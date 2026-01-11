<?php

namespace Modules\UserManagement\app\Http\Livewire\Traits;

use Illuminate\Support\Facades\Cache;
use Modules\UserManagement\app\Jobs\BulkDeleteUsersJob;
use Modules\UserManagement\app\Jobs\BulkUpdateUsersJob;

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
    public $bulkUpdateName = '';
    public $bulkUpdateEmail = '';
    public $bulkUpdateStatus = '';
    public $bulkUpdateActive = '';

    protected function getListeners()
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
        $this->bulkModalContent = count($this->selectedItems) . ' kullanıcıyı silmek istediğinizden emin misiniz?';
        $this->showBulkModal = true;
    }

    public function confirmBulkDelete()
    {
        if (empty($this->selectedItems)) {
            $this->addError('selectedItems', 'Silinecek kullanıcı seçiniz.');
            return;
        }

        $cacheKey = 'bulk_delete_users_' . auth()->id() . '_' . time();
        $tenantId = tenant('id') ?? 'central';

        Cache::put($cacheKey, [
            'progress' => 0,
            'status' => 'processing',
            'message' => 'Toplu silme işlemi başlatılıyor...'
        ], 300);

        BulkDeleteUsersJob::dispatch(
            $this->selectedItems,
            $tenantId,
            auth()->id(),
            $cacheKey
        )->onQueue('tenant_isolated_' . $tenantId);

        $this->currentBulkJob = $cacheKey;
        $this->bulkProgress = ['progress' => 0, 'status' => 'processing'];
        $this->closeBulkModal();

        // Livewire 3: emit() -> dispatch() oldu
        $this->dispatch('bulkJobStarted', cacheKey: $cacheKey);
        session()->flash('message', 'Toplu silme işlemi başlatıldı.');
    }

    // Bulk Update Status
    public function bulkUpdateStatus()
    {
        $this->bulkModalTitle = 'Toplu Durum Güncelleme';
        $this->bulkModalContent = 'Seçili kullanıcıların durumunu güncellemek için yeni durumu seçin:';
        $this->showBulkModal = true;
    }

    public function confirmBulkUpdateStatus()
    {
        if (empty($this->selectedItems)) {
            $this->addError('selectedItems', 'Güncellenecek kullanıcı seçiniz.');
            return;
        }

        if (empty($this->bulkUpdateStatus)) {
            $this->addError('bulkUpdateStatus', 'Durum seçiniz.');
            return;
        }

        $updateData = ['status' => $this->bulkUpdateStatus];
        $this->executeBulkUpdate($updateData, 'durum güncelleme');
    }

    // Bulk Update Active Status
    public function bulkUpdateActive()
    {
        $this->bulkModalTitle = 'Toplu Aktiflik Durumu Güncelleme';
        $this->bulkModalContent = 'Seçili kullanıcıların aktiflik durumunu güncellemek için seçim yapın:';
        $this->showBulkModal = true;
    }

    public function confirmBulkUpdateActive()
    {
        if (empty($this->selectedItems)) {
            $this->addError('selectedItems', 'Güncellenecek kullanıcı seçiniz.');
            return;
        }

        if ($this->bulkUpdateActive === '') {
            $this->addError('bulkUpdateActive', 'Aktiflik durumu seçiniz.');
            return;
        }

        $updateData = ['is_active' => (bool) $this->bulkUpdateActive];
        $this->executeBulkUpdate($updateData, 'aktiflik durumu güncelleme');
    }

    // Bulk Update Name
    public function bulkUpdateName()
    {
        $this->bulkModalTitle = 'Toplu İsim Güncelleme';
        $this->bulkModalContent = 'Seçili kullanıcıların isimlerini güncellemek için yeni isim girin:';
        $this->showBulkModal = true;
    }

    public function confirmBulkUpdateName()
    {
        if (empty($this->selectedItems)) {
            $this->addError('selectedItems', 'Güncellenecek kullanıcı seçiniz.');
            return;
        }

        if (empty($this->bulkUpdateName)) {
            $this->addError('bulkUpdateName', 'Yeni isim giriniz.');
            return;
        }

        $updateData = ['name' => $this->bulkUpdateName];
        $this->executeBulkUpdate($updateData, 'isim güncelleme');
    }

    // Common bulk update execution
    private function executeBulkUpdate(array $updateData, string $operation)
    {
        $cacheKey = 'bulk_update_users_' . auth()->id() . '_' . time();
        $tenantId = tenant('id') ?? 'central';

        Cache::put($cacheKey, [
            'progress' => 0,
            'status' => 'processing',
            'message' => "Toplu {$operation} işlemi başlatılıyor..."
        ], 300);

        BulkUpdateUsersJob::dispatch(
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

        // Livewire 3: emit() -> dispatch() oldu
        $this->dispatch('bulkJobStarted', cacheKey: $cacheKey);
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
        $this->bulkUpdateName = '';
        $this->bulkUpdateEmail = '';
        $this->bulkUpdateStatus = '';
        $this->bulkUpdateActive = '';
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
        // Livewire 3: Self refresh, event'leri kaldır
        // Component'in render method'u otomatik çağrılacak
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