<?php
namespace Modules\ThemeManagement\App\Http\Livewire\Traits;

use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Modules\ThemeManagement\App\Models\Theme;

trait WithImageUpload
{
    use WithFileUploads;

    public $temporaryImages = [];

    public function updatedTemporaryImages($value, $key)
    {
        $this->validateOnly("temporaryImages.{$key}", [
            "temporaryImages.{$key}" => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($this->temporaryImages[$key]) {
            $this->uploadImage($key);
        }
    }

    private function uploadImage($imageKey, $model = null)
    {
        if (!$model && isset($this->themeId)) {
            $model = Theme::find($this->themeId);
        }
    
        if ($model && isset($this->temporaryImages[$imageKey])) {
            $fileName = Str::slug($this->inputs['title']) . '-' . Str::random(6) . '.' . $this->temporaryImages[$imageKey]->getClientOriginalExtension();
    
            $collectionName = $this->getCollectionName($imageKey);
    
            // Mevcut medyayı temizle
            $model->clearMediaCollection($collectionName);
            
            // Yeni medyayı ekle
            $media = $model
                ->addMedia($this->temporaryImages[$imageKey]->getRealPath())
                ->preservingOriginal()
                ->usingFileName($fileName)
                ->withCustomProperties([
                    'image_type' => $imageKey,
                ])
                ->toMediaCollection($collectionName, 'public');
    
            if ($media) {
                // Medya yükleme işlemini logla
                log_activity(
                    $model,
                    'resim yüklendi',
                    ['collection' => $collectionName, 'filename' => $fileName]
                );
                
                // Yükleme tamamlandığında event tetikle
                $this->dispatch('fileUploaded');
            }
            
            // Geçici resmi temizle
            unset($this->temporaryImages[$imageKey]);
        }
    }

    public function removeImage($imageKey)
    {
        // Geçici resim varsa sil
        if (isset($this->temporaryImages[$imageKey])) {
            unset($this->temporaryImages[$imageKey]);
            $this->dispatch('fileUploaded');
            return;
        }
        
        // Kaydedilmiş resmi sil
        if (isset($this->themeId)) {
            $model = Theme::find($this->themeId);
            $collectionName = $this->getCollectionName($imageKey);
            
            if ($model && $model->getFirstMedia($collectionName)) {
                $media = $model->getFirstMedia($collectionName);
                $fileName = $media->file_name;
                
                $model->clearMediaCollection($collectionName);
                
                log_activity(
                    $model,
                    'resim silindi',
                    ['collection' => $collectionName, 'filename' => $fileName]
                );
                
                $this->dispatch('fileUploaded');
            }
        }
    }

    private function getCollectionName($imageKey)
    {
        // Eğer imageKey boşsa veya "thumbnail" ise artık "images" koleksiyonunu kullan
        if (empty($imageKey) || $imageKey === 'thumbnail') {
            return 'images';
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