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
      // Permission kontrolü için modül adını belirle
      $permissionModule = $this->module;
      if (str_starts_with($this->module, 'shop-')) {
          $permissionModule = 'shop';
      }

      // Silme yetkisi kontrolü
      if (!auth()->user()->hasModulePermission($permissionModule, 'delete')) {
          $this->dispatch('toast', [
              'title' => 'Yetkisiz İşlem!',
              'message' => 'Bu işlem için gerekli yetkiniz bulunmamaktadır.',
              'type' => 'error',
          ]);
          $this->showModal = false;
          return;
      }
      
      try {
          DB::beginTransaction();

          // Modül-Model mapping
          $moduleModelMap = [
              'module' => [
                  'class' => "Modules\\ModuleManagement\\App\\Models\\Module",
                  'key' => 'module_id'
              ],
              'shop-product' => [
                  'class' => "Modules\\Shop\\App\\Models\\ShopProduct",
                  'key' => 'product_id'
              ],
              'shop-category' => [
                  'class' => "Modules\\Shop\\App\\Models\\ShopCategory",
                  'key' => 'category_id'
              ],
              'shop-brand' => [
                  'class' => "Modules\\Shop\\App\\Models\\ShopBrand",
                  'key' => 'brand_id'
              ],
              'blog' => [
                  'class' => "Modules\\Blog\\App\\Models\\Blog",
                  'key' => 'blog_id'
              ],
              'portfolio' => [
                  'class' => "Modules\\Portfolio\\App\\Models\\Portfolio",
                  'key' => 'portfolio_id'
              ],
              'page' => [
                  'class' => "Modules\\Page\\App\\Models\\Page",
                  'key' => 'page_id'
              ],
          ];

          // Modül mapping'de var mı kontrol et
          if (isset($moduleModelMap[$this->module])) {
              $modelClass = $moduleModelMap[$this->module]['class'];
              $primaryKey = $moduleModelMap[$this->module]['key'];
          } else {
              // Default pattern (eski sistem)
              $modelClass = "Modules\\" . ucfirst($this->module) . "Management\\App\\Models\\" . ucfirst($this->module);
              $primaryKey = $this->module . '_id';
          }

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