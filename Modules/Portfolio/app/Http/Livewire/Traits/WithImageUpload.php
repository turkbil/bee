<?php
namespace Modules\Portfolio\App\Http\Livewire\Traits;

use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Modules\Portfolio\App\Models\Portfolio;

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
        if (!$model && isset($this->portfolioId)) {
            $model = Portfolio::find($this->portfolioId);
        }
    
        if ($model && isset($this->temporaryImages[$imageKey])) {
            $fileName = Str::slug($this->inputs['title']) . '-' . Str::random(6) . '.' . $this->temporaryImages[$imageKey]->getClientOriginalExtension();
    
            $collectionName = $this->getCollectionName($imageKey);
    
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

    public function removeImage($imageKey)
    {
        if (isset($this->portfolioId)) {
            $model = Portfolio::find($this->portfolioId);
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
            }
        }
        unset($this->temporaryImages[$imageKey]);
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