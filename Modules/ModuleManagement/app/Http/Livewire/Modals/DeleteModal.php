<?php

namespace Modules\ModuleManagement\App\Http\Livewire\Modals;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DeleteModal extends Component
{
    public $showModal = false;
    public $module;
    public $itemId;
    public $title;

    protected $listeners = ['showDeleteModal'];

    public function showDeleteModal($module, $id, $title)
    {
        $this->module = $module;
        $this->itemId = $id;
        $this->title = $title;
        $this->showModal = true;
    }

    public function delete()
    {
        try {
            DB::beginTransaction();

            // Özel durumlarda model isimlerini düzelt
            $modelMapping = [
                'settingmanagement' => 'Setting',
                'modulemanagement' => 'Module',
                // Diğer modüller için gerekirse buraya ekle
            ];
            
            $modelName = isset($modelMapping[$this->module]) 
                ? $modelMapping[$this->module] 
                : ucfirst($this->module);
            
            $modelClass = $this->module === 'module' 
                ? "Modules\\ModuleManagement\\App\\Models\\Module"
                : "Modules\\" . ucfirst($this->module) . "\\App\\Models\\" . $modelName;

            // Özel durumlarda model birincil anahtar sütun isimlerini düzelt
            $primaryKeyMapping = [
                'settingmanagement' => 'id',
                // Diğer modüller için gerekirse buraya ekle
            ];
            
            $primaryKey = $this->module === 'module' 
                ? 'module_id' 
                : (isset($primaryKeyMapping[$this->module]) 
                    ? $primaryKeyMapping[$this->module] 
                    : $this->module . '_id');

            $item = $modelClass::where($primaryKey, $this->itemId)->first();

            if (!$item) {
                $this->dispatch('toast', [
                    'title' => 'Hata!',
                    'message' => 'Silinmek istenen kayıt bulunamadı.',
                    'type' => 'error',
                ]);
                DB::rollBack();
                return;
            }

            if (method_exists($item, 'getMedia')) {
                $collections = ['image'];
                for ($i = 1; $i <= 10; $i++) {
                    $collections[] = 'image_' . $i;
                }

                foreach ($collections as $collection) {
                    if ($item->hasMedia($collection)) {
                        $mediaItems = $item->getMedia($collection);
                        foreach ($mediaItems as $media) {
                            log_activity(
                                $item,
                                'resim silindi',
                                [
                                    'collection' => $collection,
                                    'filename' => $media->file_name,
                                    'uuid' => $media->uuid
                                ]
                            );
                        }
                        $item->clearMediaCollection($collection);
                    }
                }
            }

            $oldData = $item->toArray();
            
            // Module tenants bağlantılarını sil
            if ($this->module === 'module') {
                // İlişkili tenantları al ve cache temizliği için sakla
                $tenantIds = $item->tenants()->pluck('tenant_id')->toArray();
                
                // İlişkileri sil
                $item->tenants()->detach();
                
                // Her tenant için önbelleği temizle
                foreach ($tenantIds as $tenantId) {
                    Cache::forget("modules_tenant_" . $tenantId);
                }
                
                // Central cache'i temizle
                Cache::forget("modules_tenant_central");
            }
            
            $item->delete();
            
            log_activity(
                $item,
                'silindi',
                $oldData
            );

            DB::commit();

            $this->showModal = false;

            $this->dispatch('toast', [
                'title' => 'Silindi!',
                'message' => 'Kayıt başarıyla silindi.',
                'type' => 'danger',
            ]);

            $this->dispatch('itemDeleted');
            $this->dispatch('moduleDeleted');

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Silme işlemi sırasında bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error',
            ]);
        }
    }

    public function render()
    {
        return view('modulemanagement::modals.delete-modal');
    }
}