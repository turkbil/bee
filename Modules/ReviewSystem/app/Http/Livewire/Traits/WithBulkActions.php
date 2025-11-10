<?php

namespace Modules\ReviewSystem\App\Http\Livewire\Traits;

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

        // Tüm kayıtlar manuel seçildi mi kontrol et
        $modelClass = $this->getModelClass();
        if (!empty($modelClass)) {
            $totalVisible = $modelClass::query()
                ->where(function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('slug', 'like', '%' . $this->search . '%');
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
                ->count();

            // Tüm görünür kayıtlar seçiliyse selectAll = true
            if (count($this->selectedItems) === $totalVisible && $totalVisible > 0) {
                $this->selectAll = true;
            }
            // Hiç seçili yoksa selectAll = false
            elseif (count($this->selectedItems) === 0) {
                $this->selectAll = false;
            }
            // Kısmi seçim varsa selectAll = false (indeterminate UI'da gösterilecek)
            else {
                $this->selectAll = false;
            }
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $modelClass = $this->getModelClass();
            $primaryKey = (new $modelClass)->getKeyName();

            $this->selectedItems = $modelClass::query()
                ->where(function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('slug', 'like', '%' . $this->search . '%');
                })
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
        $this->selectedItems = array_filter($this->selectedItems, function ($id) use ($itemId) {
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
        $primaryKey = (new $modelClass)->getKeyName();

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
                    "{$statusText} edildi"
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

        $module = strtolower(class_basename($this->getModelClass()));

        $this->dispatch('showBulkDeleteModal', [
            'module' => $module,
            'selectedItems' => $this->selectedItems
        ])->to('modals.bulk-delete-modal');
    }

    public function bulkDelete()
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('toast', [
                'title'   => 'Uyarı!',
                'message' => 'Lütfen silmek istediğiniz öğeleri seçin.',
                'type'    => 'warning',
            ]);
            return;
        }

        try {
            \DB::beginTransaction();

            $modelClass = $this->getModelClass();
            $primaryKey = (new $modelClass)->getKeyName();

            $items = $modelClass::whereIn($primaryKey, $this->selectedItems)->get();

            // Media temizliği (Spatie Media Library varsa)
            foreach ($items as $item) {
                if (method_exists($item, 'getMedia')) {
                    $collections = ['image'];
                    for ($i = 1; $i <= 10; $i++) {
                        $collections[] = 'image_' . $i;
                    }

                    foreach ($collections as $collection) {
                        if ($item->hasMedia($collection)) {
                            $item->clearMediaCollection($collection);
                        }
                    }
                }

                log_activity($item, 'silindi');
            }

            // Silme işlemi
            $modelClass::whereIn($primaryKey, $this->selectedItems)->delete();

            \DB::commit();

            $this->dispatch('toast', [
                'title'   => 'Silindi!',
                'message' => count($this->selectedItems) . ' adet kayıt silindi.',
                'type'    => 'success',
            ]);

            // State temizle
            $this->selectedItems = [];
            $this->selectAll = false;
            $this->bulkActionsEnabled = false;

        } catch (\Exception $e) {
            \DB::rollBack();

            $this->dispatch('toast', [
                'title'   => 'Hata!',
                'message' => 'Silme işlemi sırasında bir hata oluştu: ' . $e->getMessage(),
                'type'    => 'error',
            ]);
        }
    }
}
