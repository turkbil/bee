<?php
namespace Modules\Portfolio\App\Http\Livewire\Traits;

trait WithBulkActions
{
    public $selectedItems = [];
    public $selectAll = false;
    public $bulkActionsEnabled = false;

    protected function getModelClass()
    {
        return "";  // Alt sınıfta override edilecek
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
            $tenant = tenancy()->tenant;
    
            // Mevcut filtrelenmiş ve sayfalanmış kayıtları al
            $modelClass = $this->getModelClass();
            $primaryKey = (new $modelClass)->getKeyName();
            
            $this->selectedItems = $modelClass::where('tenant_id', $tenant->id)
                ->where(function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('slug', 'like', '%' . $this->search . '%');
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPortfolio)
                ->pluck($primaryKey)
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedItems = [];
        }
    
        $this->bulkActionsEnabled = count($this->selectedItems) > 0;
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
        $tenant = tenancy()->tenant;
        if (!$tenant || empty($this->selectedItems)) {
            return;
        }

        $modelClass = $this->getModelClass();
        $primaryKey = (new $modelClass)->getKeyName();
        
        $items = $modelClass::where('tenant_id', $tenant->id)
            ->whereIn($primaryKey, $this->selectedItems)
            ->get();

        foreach ($items as $item) {
            $item->is_active = $status;
            $item->save();

            $statusText = $status ? 'aktif' : 'pasif';
            log_activity(
                class_basename($modelClass),
                "\"{$item->title}\" toplu {$statusText} yapma işleminde {$statusText} yapıldı.",
                $item,
                [],
                $statusText
            );
        }

        $this->selectedItems = [];
        $this->selectAll = false;
        $this->bulkActionsEnabled = false;

        $statusText = $status ? 'aktif' : 'pasif';
        $this->dispatch('toast', [
            'title'   => 'Başarılı!',
            'message' => "Seçili kayıtlar {$statusText} yapıldı.",
            'type'    => 'success',
        ]);
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

        $module = strtolower(class_basename($this->getModelClass()));
        
        $this->dispatch('showBulkDeleteModal', [
            'module' => $module,
            'selectedItems' => $this->selectedItems
        ])->to('modals.bulk-delete-modal');
    }
}