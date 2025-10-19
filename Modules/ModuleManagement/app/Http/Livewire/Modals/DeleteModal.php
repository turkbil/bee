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

            // Modül-Model mapping (tam liste)
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
                'settingmanagement' => [
                    'class' => "Modules\\Settingmanagement\\App\\Models\\Setting",
                    'key' => 'id'
                ],
                'thememanagement' => [
                    'class' => "Modules\\Thememanagement\\App\\Models\\Theme",
                    'key' => 'theme_id'
                ],
            ];

            // Modül mapping'de var mı kontrol et
            if (isset($moduleModelMap[$this->module])) {
                $modelClass = $moduleModelMap[$this->module]['class'];
                $primaryKey = $moduleModelMap[$this->module]['key'];
            } else {
                // Default pattern (eski sistem)
                $moduleName = ucfirst(strtolower($this->module));
                $modelName = ucfirst($this->module);
                $modelClass = "Modules\\" . $moduleName . "\\App\\Models\\" . $modelName;
                $primaryKey = $modelName . '_id';
            }

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