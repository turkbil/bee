<?php

namespace Modules\UserManagement\App\Http\Livewire\Traits;

trait WithBulkActions
{
    public $selectedItems = [];
    public $selectAll = false;
    public $bulkActionsEnabled = false;

    protected function getModelClass()
    {
        return "";  // Alt sınıfta override edilecek
    }
    
    protected function getPrimaryKeyName()
    {
        // Alt sınıfta override edilebilir, varsayılan olarak ID kullanılır
        return (new ($this->getModelClass()))->getKeyName();
    }

    protected function getListeners()
    {
        return [
            'itemDeleted' => '$refresh',
            'bulkItemsDeleted' => '$refresh',
            'resetSelectAll' => 'resetSelectAll',
            'removeFromSelected' => 'removeFromSelected'
        ];
    }

    public function updatedSelectedItems()
    {
        $this->bulkActionsEnabled = count($this->selectedItems) > 0;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $modelClass = $this->getModelClass();
            $primaryKey = $this->getPrimaryKeyName();
            
            $this->selectedItems = $this->applySearchFilters($modelClass::query())
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
                ->pluck($primaryKey)
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedItems = [];
        }
    
        $this->bulkActionsEnabled = count($this->selectedItems) > 0;
    }
    
    protected function applySearchFilters($query)
    {
        // Bu metod alt sınıflarda override edilmelidir
        return $query;
    }

    public function refreshSelectedItems()
    {
        $this->selectedItems = [];
        $this->selectAll = false;
        $this->bulkActionsEnabled = false;
    }

    public function resetSelectAll()
    {
        $this->selectAll = false;
        $this->selectedItems = [];
        $this->bulkActionsEnabled = false;
    }

    public function removeFromSelected($itemId)
    {
        $this->selectedItems = array_filter($this->selectedItems, function($id) use ($itemId) {
            return $id != $itemId;
        });
        
        $this->bulkActionsEnabled = count($this->selectedItems) > 0;
        
        if (count($this->selectedItems) === 0) {
            $this->selectAll = false;
        }
    }

    public function bulkToggleActive($status)
    {
        if (empty($this->selectedItems)) {
            return;
        }

        $modelClass = $this->getModelClass();
        $primaryKey = $this->getPrimaryKeyName();
        
        // Önce mevcut durumları tek sorguda al
        $items = $modelClass::query()
            ->whereIn($primaryKey, $this->selectedItems)
            ->where('is_active', !$status) // Sadece durumu değişecek olanları al
            ->get();

        $processedCount = $items->count();

        // Eğer hiç değişiklik yapılacak kayıt yoksa
        if ($processedCount === 0) {
            $statusText = $status ? 'aktif' : 'pasif';
            $this->dispatch('toast', [
                'title'   => 'Bilgi',
                'message' => "Seçili kayıtlar zaten {$statusText} durumda.",
                'type'    => 'info',
            ]);
        } else {
            // Toplu güncelleme yap
            $modelClass::query()
                ->whereIn($primaryKey, $items->pluck($primaryKey))
                ->update([
                    'is_active' => $status,
                    'updated_at' => now()
                ]);

            // Log kayıtlarını oluştur
            foreach ($items as $item) {
                $statusText = $status ? 'aktif' : 'pasif';
                log_activity(
                    $item,
                    "{$statusText} edildi",
                    [
                        'bulk_update' => true
                    ]
                );
            }

            $statusText = $status ? 'aktif' : 'pasif';
            $toastType = $status ? 'success' : 'warning';
            
            $this->dispatch('toast', [
                'title'   => 'Başarılı!',
                'message' => "{$processedCount} kayıt {$statusText} yapıldı.",
                'type'    => $toastType,
            ]);
        }

        $this->selectedItems = [];
        $this->selectAll = false;
        $this->bulkActionsEnabled = false;
    }

    public function confirmBulkDelete()
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('toast', [
                'title'   => 'Uyarı!',
                'message' => 'Lütfen silmek istediğiniz öğeleri seçin.',
                'type'    => 'warning',
            ]);
            return;
        }

        if (!auth()->user()->isRoot()) {
            $this->dispatch('toast', [
                'title'   => 'Yetkisiz İşlem!',
                'message' => 'Toplu silme işlemi için gerekli yetkiniz bulunmuyor.',
                'type'    => 'error',
            ]);
            return;
        }

        $module = strtolower(class_basename($this->getModelClass()));
        
        $this->dispatch('showBulkDeleteModal', [
            'module' => $module,
            'selectedItems' => $this->selectedItems
        ])->to('modals.bulk-delete-modal');
    }
}