<?php
namespace App\Livewire\Modals;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BulkDeleteModal extends Component
{
   public $showModal = false;
   public $module;
   public $selectedItems = [];

   protected $listeners = ['showBulkDeleteModal'];

   public function mount()
   {
       $this->showModal = false;
   }

   public function showBulkDeleteModal($params)
   {
       $this->module = $params['module'];
       $this->selectedItems = $params['selectedItems'];
       $this->showModal = true;
   }

   public function bulkDelete()
   {
       // Silme yetkisi kontrolü
       if (!auth()->user()->hasModulePermission($this->module, 'delete')) {
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

           if (empty($this->selectedItems)) {
               $this->dispatch('toast', [
                   'title' => 'Hata!',
                   'message' => 'İşlem yapılamadı.',
                   'type' => 'error',
               ]);
               return;
           }

           $modelClass = "Modules\\" . ucfirst($this->module) . "\\App\\Models\\" . ucfirst($this->module);
           $items = $modelClass::whereIn($this->module . '_id', $this->selectedItems)
               ->get();

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

               log_activity(
                   $item,
                   'silindi'
               );
           }

           $modelClass::whereIn($this->module . '_id', $this->selectedItems)
               ->delete();

           DB::commit();

           $this->showModal = false;

           $this->dispatch('toast', [
               'title' => 'Silindi!',
               'message' => count($this->selectedItems) . " adet kayıt silindi.",
               'type' => 'danger',
           ]);

           $this->dispatch('bulkItemsDeleted')->to($this->module . '-component');
           $this->dispatch('resetSelectAll')->to($this->module . '-component');

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
       return view('livewire.modals.bulk-delete-modal');
   }
}