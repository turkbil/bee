<?php
namespace App\Livewire\Modals;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
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

          $modelClass = $this->module === 'module' 
              ? "Modules\\ModuleManagement\\App\\Models\\Module"
              : "Modules\\" . ucfirst($this->module) . "Management\\App\\Models\\" . ucfirst($this->module);

          $primaryKey = $this->module === 'module' ? 'module_id' : $this->module . '_id';

          $item = $modelClass::where($primaryKey, $this->itemId)->first();

          if (!$item) {
              $this->dispatch('toast', [
                  'title' => 'Hata!',
                  'message' => 'Silinmek istenen kayıt bulunamadı.',
                  'type' => 'error',
              ]);
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

          $this->dispatch('itemDeleted')->to($this->module . '-component');

      } catch (\Exception $e) {
          DB::rollBack();

          $this->dispatch('toast', [
              'title' => 'Hata!',
              'message' => 'Silme işlemi sırasında bir hata oluştu.',
              'type' => 'error',
          ]);
      }
  }

  public function render()
  {
      return view('livewire.modals.delete-modal');
  }
}