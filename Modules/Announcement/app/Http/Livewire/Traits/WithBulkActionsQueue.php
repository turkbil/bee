<?php

namespace Modules\Announcement\App\Http\Livewire\Traits;

use Illuminate\Support\Facades\Cache;

/**
 * 🚀 Queue-Based Bulk Actions Trait - Announcement Module
 * 
 * Announcement modülü için queue-based bulk işlemler:
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
        $this->selectedItems = array_filter($this->selectedItems, function($id) use ($itemId) {
            return $id != $itemId;
        });
        
        $this->bulkActionsEnabled = count($this->selectedItems) > 0;
        
        if (count($this->selectedItems) === 0) {
            $this->selectAll = false;
        }
    }

    /**
     * 🗑️ Queue-based bulk delete - Announcement specific
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
            
            \Modules\Announcement\App\Jobs\BulkDeleteAnnouncementsJob::dispatch(
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
                'progress_key' => "bulk_delete_announcements_{$tenantId}_{$userId}",
                'started_at' => now()->toISOString()
            ];
            
            $this->dispatch('toast', [
                'title' => 'Queue İşlemi Başlatıldı',
                'message' => "{$count} duyuru silme işlemi kuyruğa eklendi",
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
     * ✏️ Queue-based bulk update - Announcement specific
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
            
            \Modules\Announcement\App\Jobs\BulkUpdateAnnouncementsJob::dispatch(
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
                'progress_key' => "bulk_update_announcements_{$tenantId}_{$userId}",
                'started_at' => now()->toISOString()
            ];
            
            $this->dispatch('toast', [
                'title' => 'Queue İşlemi Başlatıldı',
                'message' => "{$count} duyuru güncelleme işlemi kuyruğa eklendi",
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
     * 🔥 Bulk toggle important status (Announcement specific)
     */
    public function bulkToggleImportant($status)
    {
        $this->bulkUpdate(['is_important' => $status]);
    }

    /**
     * ⭐ Bulk toggle featured status (Announcement specific)
     */
    public function bulkToggleFeatured($status)
    {
        $this->bulkUpdate(['is_featured' => $status]);
    }

    /**
     * 🎯 Bulk update announcement type (Announcement specific)
     */
    public function bulkUpdateType($type)
    {
        $allowedTypes = ['info', 'warning', 'success', 'danger', 'maintenance'];
        
        if (!in_array($type, $allowedTypes)) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Geçersiz duyuru tipi',
                'type' => 'error'
            ]);
            return;
        }
        
        $this->bulkUpdate(['type' => $type]);
    }

    /**
     * 👥 Bulk update target audience (Announcement specific)
     */
    public function bulkUpdateAudience($audience)
    {
        $allowedAudiences = ['admin', 'user', 'all'];
        
        if (!in_array($audience, $allowedAudiences)) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Geçersiz hedef kitle',
                'type' => 'error'
            ]);
            return;
        }
        
        $this->bulkUpdate(['target_audience' => $audience]);
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