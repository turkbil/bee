<?php 
namespace Modules\SettingManagement\App\Http\Livewire\Traits;

use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Modules\SettingManagement\App\Models\Setting;

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
                // Tenant id belirleme
                $tenantId = is_tenant() ? tenant_id() : 'central';
                
                // Benzersiz bir dosya adı oluştur
                $fileName = Str::slug($model->key) . '-' . Str::random(6) . '.' . $this->temporaryImages[$imageKey]->getClientOriginalExtension();
                
                // Koleksiyon adını belirle (klasör)
                $folder = $this->getCollectionName($imageKey);
                
                // Dosya yolu - dizin yapısını düzelt
                $path = "settings/{$folder}/{$fileName}";
                
                // Eski dosyayı sil (eğer varsa)
                if ($model->value && Storage::disk('public')->exists($model->value)) {
                    Storage::disk('public')->delete($model->value);
                }
                
                // Yeni dosyayı yükle
                $filePath = Storage::disk('public')->putFileAs(
                    dirname($path),
                    $this->temporaryImages[$imageKey],
                    basename($path)
                );
                
                // Modeli güncelle
                if ($filePath) {
                    $relativePath = $path;
                    
                    // Eğer bu bir TenantValueComponent veya ValuesComponent ise değeri güncelle
                    if (isset($this->value)) {
                        $this->value = $relativePath;
                    }
                    
                    log_activity(
                        $model,
                        'resim yüklendi',
                        ['path' => $relativePath, 'filename' => $fileName]
                    );
                    
                    return $relativePath;
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Resim yükleme hatası: ' . $e->getMessage(), [
                    'model_id' => $model->id,
                    'tenant' => is_tenant() ? tenant_id() : 'central'
                ]);
            }
        }
        
        return null;
    }

    public function removeImage($imageKey)
    {
        if (isset($this->settingId)) {
            $model = Setting::find($this->settingId);
            
            if ($model && isset($this->value) && Storage::disk('public')->exists($this->value)) {
                Storage::disk('public')->delete($this->value);
                
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