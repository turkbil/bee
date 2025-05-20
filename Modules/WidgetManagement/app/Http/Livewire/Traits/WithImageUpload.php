<?php 
namespace Modules\WidgetManagement\app\Http\Livewire\Traits;

use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Modules\WidgetManagement\app\Models\Widget;
// SettingManagement'taki TenantStorageHelper kullanılacak

trait WithImageUpload {
    use WithFileUploads;

    public $temporaryImages = [];

    public function updatedTemporaryImages($value, $key)
    {
        $this->validateOnly("temporaryImages.{$key}", [
            "temporaryImages.{$key}" => ['image', 'mimes:jpg,jpeg,png,webp,ico', 'max:2048'],
        ]);

        if ($this->temporaryImages[$key]) {
            $this->uploadImage($key);
        }
    }

    private function uploadImage($imageKey, $model = null)
    {
        if (!$model && isset($this->widgetId)) {
            $model = Widget::find($this->widgetId);
        }
    
        if ($model && isset($this->temporaryImages[$imageKey])) {
            try {
                // Tenant id belirleme - Central ise tenant1, değilse gerçek tenant ID
                $tenantId = is_tenant() ? tenant_id() : 1;
                
                // Benzersiz bir dosya adı oluştur
                $fileName = time() . '_' . Str::slug($model->slug) . '_' . $this->temporaryImages[$imageKey]->getClientOriginalName();
                
                // Koleksiyon adını belirle (klasör)
                $folder = $this->getCollectionName($imageKey);
                
                // Eski dosyayı kontrol et ve sil
                $oldValue = isset($model->{$imageKey}) ? $model->{$imageKey} : null;
                if ($oldValue) {
                    // SettingManagement'taki TenantStorageHelper kullan
                    \Modules\SettingManagement\App\Helpers\TenantStorageHelper::deleteFile($oldValue);
                }
                
                // SettingManagement'taki TenantStorageHelper ile doğru şekilde dosyayı yükle
                $urlPath = \Modules\SettingManagement\App\Helpers\TenantStorageHelper::storeTenantFile(
                    $this->temporaryImages[$imageKey],
                    "widgets/{$folder}",
                    $fileName,
                    $tenantId
                );
                
                // Eğer bu bir modele ait değer ise güncelle
                if (isset($this->widget) && isset($this->widget[$imageKey])) {
                    $this->widget[$imageKey] = $urlPath;
                }
                
                // Önizleme güncelle
                if ($imageKey === 'thumbnail') {
                    $this->imagePreview = $urlPath;
                }
                
                return $urlPath;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Resim yükleme hatası: ' . $e->getMessage(), [
                    'model_id' => $model->id,
                    'tenant' => is_tenant() ? tenant_id() : 1,
                    'exception' => $e
                ]);
            }
        }
        
        return null;
    }

    // URL'den yerel depolama yolunu çıkarır
    private function extractLocalPath($path)
    {
        // tenant{id}/ ifadesini ara ve kaldır
        if (preg_match('/^tenant\d+\/(.*)$/', $path, $matches)) {
            return $matches[1];
        }
        return $path;
    }

    public function removeImage($imageKey)
    {
        if (isset($this->widgetId)) {
            $model = Widget::find($this->widgetId);
            
            if ($model && isset($this->widget[$imageKey])) {
                // SettingManagement'taki TenantStorageHelper ile dosyayı sil
                \Modules\SettingManagement\App\Helpers\TenantStorageHelper::deleteFile($this->widget[$imageKey]);
                
                // Model değerini temizle
                $this->widget[$imageKey] = null;
                
                if ($imageKey === 'thumbnail') {
                    $this->imagePreview = null;
                }
            }
        }
        
        unset($this->temporaryImages[$imageKey]);
    }

    private function getCollectionName($imageKey)
    {
        // Tüm resimler için standart olarak "images" klasörünü kullan
        // Bu SettingManagement ile uyumlu olacak
        return 'images';
    }
    
    public function handleImageUpload($model)
    {
        $paths = [];
        
        if (!empty($this->temporaryImages)) {
            foreach ($this->temporaryImages as $imageKey => $image) {
                if ($image) {
                    $path = $this->uploadImage($imageKey, $model);
                    if ($path) {
                        $paths[$imageKey] = $path;
                    }
                }
            }
        }
        
        return $paths;
    }
}