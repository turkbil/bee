<?php
namespace Modules\UserManagement\App\Http\Livewire\Traits;

use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\User;

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
        if (!$model && isset($this->userId)) {
            $model = User::find($this->userId);
        }
    
        if ($model && isset($this->temporaryImages[$imageKey])) {
            $fileName = Str::slug($this->inputs['name'] ?? 'profile') . '-' . Str::random(6) . '.' . $this->temporaryImages[$imageKey]->getClientOriginalExtension();
    
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
        if (isset($this->userId)) {
            $model = User::find($this->userId);
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
        // Avatar için özel koleksiyon adı
        if (empty($imageKey) || $imageKey === 'avatar') {
            return 'avatar';
        }
        
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