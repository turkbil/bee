<?php

namespace Modules\MenuManagement\App\Http\Livewire\Traits;

use Illuminate\Support\Facades\Cache;

/**
 * 🚀 Queue-Based Bulk Actions Trait - MenuManagement Module
 * 
 * MenuManagement modülü için queue-based bulk işlemler:
 * - Bulk delete (queue)
 * - Bulk update (queue) 
 * - Progress tracking
 * - Page modülünden kopya alınmış template
 */
trait WithBulkActionsQueue
{
    // Note: selectedItems, selectAll, bulkActionsEnabled should be declared in the component
    public $bulkProgressVisible = false;
    public $bulkProgress = [];

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
            'removeFromSelected' => 'removeFromSelected',
            'checkBulkProgress' => 'checkBulkProgress',
            'hideBulkProgress' => 'hideBulkProgress'
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
            $primaryKey = (new $modelClass)->getKeyName();
            
            $this->selectedItems = $modelClass::query()
                ->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('location', 'like', '%' . $this->search . '%');
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
        $this->selectedItems = array_filter($this->selectedItems, function($id) use ($itemId) {
            return $id != $itemId;
        });
        
        $this->bulkActionsEnabled = count($this->selectedItems) > 0;
        
        if (count($this->selectedItems) === 0) {
            $this->selectAll = false;
        }
    }

    /**
     * 🗑️ Queue-based bulk delete - Menu specific
     */
    public function bulkDelete()
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('toast', [
                'title' => 'Uyarı',
                'message' => 'Silinecek öğe seçilmedi',
                'type' => 'warning'
            ]);
            return;
        }

        try {
            // Queue job ile bulk delete
            $tenantId = tenant('id') ?? 'central';
            $userId = (string) auth()->id();
            
            \Modules\MenuManagement\App\Jobs\BulkDeleteMenusJob::dispatch(
                $this->selectedItems,
                $tenantId,
                $userId,
                ['force_delete' => false]
            );
            
            $count = count($this->selectedItems);
            $this->selectedItems = [];
            $this->selectAll = false;
            $this->bulkActionsEnabled = false;
            
            // Progress tracking başlat
            $this->bulkProgressVisible = true;
            $this->bulkProgress = [
                'operation' => 'delete',
                'count' => $count,
                'progress_key' => "bulk_delete_menus_{$tenantId}_{$userId}",
                'started_at' => now()->toISOString()
            ];
            
            $this->dispatch('toast', [
                'title' => 'Queue İşlemi Başlatıldı',
                'message' => "{$count} menü silme işlemi kuyruğa eklendi",
                'type' => 'success'
            ]);
            
            // Progress check timer başlat
            $this->dispatch('startBulkProgressCheck', [
                'progress_key' => $this->bulkProgress['progress_key']
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Queue işlemi başarısız: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * ✏️ Queue-based bulk update - Menu specific
     */
    public function bulkUpdate($updateData)
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('toast', [
                'title' => 'Uyarı',
                'message' => 'Güncellenecek öğe seçilmedi',
                'type' => 'warning'
            ]);
            return;
        }

        try {
            // Queue job ile bulk update
            $tenantId = tenant('id') ?? 'central';
            $userId = (string) auth()->id();
            
            \Modules\MenuManagement\App\Jobs\BulkUpdateMenusJob::dispatch(
                $this->selectedItems,
                $updateData,
                $tenantId,
                $userId,
                ['validate' => true]
            );
            
            $count = count($this->selectedItems);
            $this->selectedItems = [];
            $this->selectAll = false;
            $this->bulkActionsEnabled = false;
            
            // Progress tracking başlat
            $this->bulkProgressVisible = true;
            $this->bulkProgress = [
                'operation' => 'update',
                'count' => $count,
                'progress_key' => "bulk_update_menus_{$tenantId}_{$userId}",
                'started_at' => now()->toISOString()
            ];
            
            $this->dispatch('toast', [
                'title' => 'Queue İşlemi Başlatıldı',
                'message' => "{$count} menü güncelleme işlemi kuyruğa eklendi",
                'type' => 'success'
            ]);
            
            // Progress check timer başlat
            $this->dispatch('startBulkProgressCheck', [
                'progress_key' => $this->bulkProgress['progress_key']
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Queue işlemi başarısız: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * 🔄 Bulk toggle active status (queue-based)
     */
    public function bulkToggleActive($status)
    {
        $this->bulkUpdate(['is_active' => $status]);
    }

    /**
     * 📍 Bulk update theme location (Menu specific)
     */
    public function bulkUpdateLocation($location)
    {
        $allowedLocations = ['header', 'footer', 'sidebar', 'primary', 'secondary'];
        
        if (!in_array($location, $allowedLocations)) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Geçersiz tema konumu',
                'type' => 'error'
            ]);
            return;
        }
        
        $this->bulkUpdate(['theme_location' => $location]);
    }

    /**
     * 🔢 Bulk update max depth (Menu specific)
     */
    public function bulkUpdateMaxDepth($depth)
    {
        if ($depth < 1 || $depth > 10) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Maksimum derinlik 1-10 arasında olmalıdır',
                'type' => 'error'
            ]);
            return;
        }
        
        $this->bulkUpdate(['max_depth' => $depth]);
    }

    /**
     * 📊 Progress tracking kontrolü
     */
    public function checkBulkProgress()
    {
        if (!$this->bulkProgressVisible || empty($this->bulkProgress['progress_key'])) {
            return;
        }

        try {
            $progress = Cache::get($this->bulkProgress['progress_key']);
            
            if (!$progress) {
                return;
            }

            // Progress data'yı güncelle
            $this->bulkProgress['current_progress'] = $progress['progress'] ?? 0;
            $this->bulkProgress['status'] = $progress['status'] ?? 'unknown';
            $this->bulkProgress['data'] = $progress['data'] ?? [];

            // İşlem tamamlandıysa veya hata olduysa
            if (in_array($progress['status'], ['completed', 'failed'])) {
                $this->bulkProgressVisible = false;
                
                if ($progress['status'] === 'completed') {
                    $processed = $progress['data']['processed'] ?? 0;
                    $errors = $progress['data']['errors'] ?? 0;
                    $duration = $progress['data']['duration'] ?? 0;
                    
                    $this->dispatch('toast', [
                        'title' => '✅ İşlem Tamamlandı',
                        'message' => "İşlenen: {$processed}, Hata: {$errors}, Süre: {$duration}s",
                        'type' => 'success'
                    ]);
                    
                    // Sayfa refresh
                    $this->dispatch('$refresh');
                    
                } else {
                    $error = $progress['data']['error'] ?? 'Bilinmeyen hata';
                    
                    $this->dispatch('toast', [
                        'title' => '❌ İşlem Başarısız',
                        'message' => $error,
                        'type' => 'error'
                    ]);
                }
                
                // Cache temizle
                Cache::forget($this->bulkProgress['progress_key']);
            }
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Progress Hatası',
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Progress modal'ı gizle
     */
    public function hideBulkProgress()
    {
        $this->bulkProgressVisible = false;
        $this->bulkProgress = [];
    }

    /**
     * Bulk delete confirmation
     */
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