<?php

namespace Modules\WidgetManagement\app\Http\Livewire\Traits;

use Illuminate\Support\Facades\Cache;
use Modules\WidgetManagement\app\Jobs\BulkDeleteWidgetsJob;
use Modules\WidgetManagement\app\Jobs\BulkUpdateWidgetsJob;

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
    public $bulkUpdateTitle = '';
    public $bulkUpdateStatus = '';
    public $bulkUpdateActive = '';
    public $bulkUpdateWidgetArea = '';
    public $bulkUpdatePosition = '';

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
        $this->bulkModalContent = count($this->selectedItems) . ' widget\'i silmek istediğinizden emin misiniz?';
        $this->showBulkModal = true;
    }

    public function confirmBulkDelete()
    {
        if (empty($this->selectedItems)) {
            $this->addError('selectedItems', 'Silinecek widget seçiniz.');
            return;
        }

        $cacheKey = 'bulk_delete_widgets_' . auth()->id() . '_' . time();
        $tenantId = tenant('id') ?? 'central';

        Cache::put($cacheKey, [
            'progress' => 0,
            'status' => 'processing',
            'message' => 'Toplu silme işlemi başlatılıyor...'
        ], 300);

        BulkDeleteWidgetsJob::dispatch(
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

    // Bulk Update Status
    public function bulkUpdateStatus()
    {
        $this->bulkModalTitle = 'Toplu Durum Güncelleme';
        $this->bulkModalContent = 'Seçili widget\'ların durumunu güncellemek için yeni durumu seçin:';
        $this->showBulkModal = true;
    }

    public function confirmBulkUpdateStatus()
    {
        if (empty($this->selectedItems)) {
            $this->addError('selectedItems', 'Güncellenecek widget seçiniz.');
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
        $this->bulkModalContent = 'Seçili widget\'ların aktiflik durumunu güncellemek için seçim yapın:';
        $this->showBulkModal = true;
    }

    public function confirmBulkUpdateActive()
    {
        if (empty($this->selectedItems)) {
            $this->addError('selectedItems', 'Güncellenecek widget seçiniz.');
            return;
        }

        if ($this->bulkUpdateActive === '') {
            $this->addError('bulkUpdateActive', 'Aktiflik durumu seçiniz.');
            return;
        }

        $updateData = ['is_active' => (bool) $this->bulkUpdateActive];
        $this->executeBulkUpdate($updateData, 'aktiflik durumu güncelleme');
    }

    // Bulk Update Widget Area
    public function bulkUpdateWidgetArea()
    {
        $this->bulkModalTitle = 'Toplu Widget Alanı Güncelleme';
        $this->bulkModalContent = 'Seçili widget\'ları yeni alana taşımak için alan seçin:';
        $this->showBulkModal = true;
    }

    public function confirmBulkUpdateWidgetArea()
    {
        if (empty($this->selectedItems)) {
            $this->addError('selectedItems', 'Güncellenecek widget seçiniz.');
            return;
        }

        if (empty($this->bulkUpdateWidgetArea)) {
            $this->addError('bulkUpdateWidgetArea', 'Widget alanı seçiniz.');
            return;
        }

        $updateData = ['widget_area' => $this->bulkUpdateWidgetArea];
        $this->executeBulkUpdate($updateData, 'widget alanı güncelleme');
    }

    // Bulk Update Position
    public function bulkUpdatePosition()
    {
        $this->bulkModalTitle = 'Toplu Pozisyon Güncelleme';
        $this->bulkModalContent = 'Seçili widget\'ların pozisyonunu güncellemek için yeni pozisyon girin:';
        $this->showBulkModal = true;
    }

    public function confirmBulkUpdatePosition()
    {
        if (empty($this->selectedItems)) {
            $this->addError('selectedItems', 'Güncellenecek widget seçiniz.');
            return;
        }

        if ($this->bulkUpdatePosition === '') {
            $this->addError('bulkUpdatePosition', 'Pozisyon değeri giriniz.');
            return;
        }

        $updateData = ['position' => (int) $this->bulkUpdatePosition];
        $this->executeBulkUpdate($updateData, 'pozisyon güncelleme');
    }

    // Common bulk update execution
    private function executeBulkUpdate(array $updateData, string $operation)
    {
        $cacheKey = 'bulk_update_widgets_' . auth()->id() . '_' . time();
        $tenantId = tenant('id') ?? 'central';

        Cache::put($cacheKey, [
            'progress' => 0,
            'status' => 'processing',
            'message' => "Toplu {$operation} işlemi başlatılıyor..."
        ], 300);

        BulkUpdateWidgetsJob::dispatch(
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
        $this->bulkUpdateTitle = '';
        $this->bulkUpdateStatus = '';
        $this->bulkUpdateActive = '';
        $this->bulkUpdateWidgetArea = '';
        $this->bulkUpdatePosition = '';
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