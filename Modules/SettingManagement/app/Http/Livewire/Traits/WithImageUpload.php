<?php
namespace Modules\SettingManagement\App\Http\Livewire\Traits;

use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Modules\SettingManagement\App\Models\Setting;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

trait WithImageUpload
{
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
            $fileName = Str::slug($model->key) . '-' . Str::random(6) . '.' . $this->temporaryImages[$imageKey]->getClientOriginalExtension();
    
            $collectionName = $this->getCollectionName($imageKey);
            
            // Eğer tenant ortamındaysa dosyayı tenant diskine yükleyelim
            if (is_tenant()) {
                // Tenant veritabanına kayıt yapacak şekilde direkt dosya yükleme işlemi
                $tenantPath = 'tenant' . tenant_id() . '/settings/' . $collectionName;
                $path = $this->temporaryImages[$imageKey]->storeAs($tenantPath, $fileName, 'public');
                
                // Media kaydı tenant medya tablosuna eklenecek
                $this->handleTenantMediaRecord($model, $imageKey, $path, $fileName, $collectionName);
                return;
            }
    
            // Central ortamı için normal işlem
            $model->clearMediaCollection($collectionName);
            $media = $model
                ->addMedia($this->temporaryImages[$imageKey]->getRealPath())
                ->preservingOriginal()
                ->usingFileName($fileName)
                ->withCustomProperties([
                    'uploaded_by' => auth()->id(),
                    'image_type' => $imageKey,
                ])
                ->toMediaCollection($collectionName, 'public');
    
            if ($media) {
                log_activity(
                    $model,
                    'resim yüklendi',
                    ['collection' => $collectionName, 'filename' => $fileName]
                );
            }
        }
    }
    
    // Tenant veritabanına medya kaydı ekleme
    private function handleTenantMediaRecord($model, $imageKey, $path, $fileName, $collectionName)
    {
        // Tenant medya tablosuna kayıt ekleme işlemi
        $mediaClass = config('media-library.media_model');
        $media = new $mediaClass();
        $media->model_type = get_class($model);
        $media->model_id = $model->id;
        $media->uuid = (string) Str::uuid();
        $media->collection_name = $collectionName;
        $media->name = pathinfo($fileName, PATHINFO_FILENAME);
        $media->file_name = $fileName;
        $media->mime_type = $this->temporaryImages[$imageKey]->getMimeType();
        $media->disk = 'public';
        $media->conversions_disk = 'public';
        $media->size = $this->temporaryImages[$imageKey]->getSize();
        $media->manipulations = json_encode([]);
        $media->custom_properties = json_encode([
            'uploaded_by' => auth()->id(),
            'image_type' => $imageKey,
        ]);
        $media->generated_conversions = json_encode([]);
        $media->responsive_images = json_encode([]);
        $media->save();
        
        log_activity(
            $model,
            'tenant resim yüklendi',
            ['collection' => $collectionName, 'filename' => $fileName, 'path' => $path]
        );
    }

    public function removeImage($imageKey)
    {
        if (isset($this->settingId)) {
            $model = Setting::find($this->settingId);
            $collectionName = $this->getCollectionName($imageKey);
            
            if ($model) {
                if (is_tenant()) {
                    // Tenant için medya silme işlemi
                    $this->removeTenantMedia($model, $collectionName);
                } else {
                    // Central için medya silme işlemi
                    if ($model->getFirstMedia($collectionName)) {
                        $media = $model->getFirstMedia($collectionName);
                        $fileName = $media->file_name;
                        
                        $model->clearMediaCollection($collectionName);
                        
                        log_activity(
                            $model,
                            'resim silindi',
                            ['collection' => $collectionName, 'filename' => $fileName]
                        );
                    }
                }
            }
        }
        unset($this->temporaryImages[$imageKey]);
    }
    
    private function removeTenantMedia($model, $collectionName)
    {
        // Tenant medya kaydını bul ve sil
        $mediaClass = config('media-library.media_model');
        $media = $mediaClass::where('model_type', get_class($model))
            ->where('model_id', $model->id)
            ->where('collection_name', $collectionName)
            ->first();
            
        if ($media) {
            $fileName = $media->file_name;
            $path = 'tenant' . tenant_id() . '/settings/' . $collectionName . '/' . $fileName;
            
            // Dosyayı diskten sil
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            
            // Medya kaydını sil
            $media->delete();
            
            log_activity(
                $model,
                'tenant resim silindi',
                ['collection' => $collectionName, 'filename' => $fileName]
            );
        }
    }

    private function getCollectionName($imageKey)
    {
        // Eğer imageKey boşsa veya "image" ise direkt "image" döndür
        if (empty($imageKey) || $imageKey === 'image') {
            return 'image';
        }
        
        // Diğer keyler için "image_" öneki ekle
        return 'image_' . $imageKey;
    }
    
    public function handleImageUpload($model)
    {
        if (!empty($this->temporaryImages)) {
            foreach ($this->temporaryImages as $imageKey => $image) {
                if ($image) {
                    $this->uploadImage($imageKey, $model);
                }
            }
        }
    }
}