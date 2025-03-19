<?php

namespace Modules\ModuleManagement\App\Http\Livewire\Modals;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DeleteModal extends Component
{
    public $showModal = false;
    public $module;
    public $itemId;
    public $title;

    protected $listeners = ['showDeleteModal'];

    // Parametre olarak data yerine doğrudan $data['module'], $data['id'], $data['title'] olarak alacak şekilde düzeltelim
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

            $modelClass = $this->module === 'module' 
                ? "Modules\\ModuleManagement\\App\\Models\\Module"
                : "Modules\\" . ucfirst($this->module) . "\\App\\Models\\" . ucfirst($this->module);

            $primaryKey = $this->module === 'module' ? 'module_id' : $this->module . '_id';

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
                $item->tenants()->detach();
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