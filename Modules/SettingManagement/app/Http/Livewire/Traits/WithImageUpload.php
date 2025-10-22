<?php 
namespace Modules\SettingManagement\App\Http\Livewire\Traits;

use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Modules\SettingManagement\App\Models\Setting;
use Modules\SettingManagement\App\Helpers\TenantStorageHelper;

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
        if (!$model && isset($this->settingId)) {
            $model = Setting::find($this->settingId);
        }
    
        if ($model && isset($this->temporaryImages[$imageKey])) {
            try {
                // Tenant id belirleme - Central ise tenant1, değilse gerçek tenant ID
                $tenantId = is_tenant() ? tenant_id() : 1;
                
                // Benzersiz bir dosya adı oluştur
                $fileName = Str::slug($model->key) . '-' . Str::random(6) . '.' . $this->temporaryImages[$imageKey]->getClientOriginalExtension();
                
                // Koleksiyon adını belirle (klasör)
                $folder = $this->getCollectionName($imageKey);
                
                // Eski dosyayı kontrol et ve sil
                $oldValue = $model->value;
                if ($oldValue) {
                    TenantStorageHelper::deleteFile($oldValue);
                }
                
                // YENİ: TenantStorageHelper ile doğru şekilde dosyayı yükle
                $urlPath = TenantStorageHelper::storeTenantFile(
                    $this->temporaryImages[$imageKey],
                    "settings/{$folder}",
                    $fileName,
                    $tenantId
                );
                
                // Eğer bu bir TenantValueComponent veya ValuesComponent ise değeri güncelle
                if (isset($this->value)) {
                    $this->value = $urlPath;
                }
                
                log_activity(
                    $model,
                    'resim yüklendi',
                    ['path' => $urlPath, 'filename' => $fileName]
                );
                
                return $urlPath;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Resim yükleme hatası: ' . $e->getMessage(), [
                    'model_id' => $model->id,
                    'tenant' => is_tenant() ? tenant_id() : 'central',
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
        if (isset($this->settingId)) {
            $model = Setting::find($this->settingId);
            
            if ($model && isset($this->value)) {
                // Dosyayı sil
                TenantStorageHelper::deleteFile($this->value);
                
                log_activity(
                    $model,
                    'resim silindi',
                    ['path' => $this->value]
                );
                
                $this->value = null;
            }
        }
        
        unset($this->temporaryImages[$imageKey]);
    }

    private function getCollectionName($imageKey)
    {
        // Eğer imageKey boşsa veya "image" ise direkt "images" klasörünü kullan
        if (empty($imageKey) || $imageKey === 'image') {
            return 'images';
        }
        
        // Diğer keyler için kendi adını kullan
        return $imageKey;
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