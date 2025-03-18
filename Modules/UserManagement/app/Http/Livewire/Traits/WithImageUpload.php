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
        if (!$model && $this->userId) {
            $model = User::find($this->userId);
        }

        if ($model) {
            $fileName = ($model->name ?? 'image') . '-' . Str::random(6) . '.' . $this->temporaryImages[$imageKey]->getClientOriginalExtension();
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
                log_activity($model, 'resim yüklendi', [
                    'collection' => $collectionName,
                    'filename' => $fileName
                ]);
            }
        }
    }

    public function removeImage($imageKey)
    {
        if ($this->userId) {
            $model = User::find($this->userId);
            $collectionName = $this->getCollectionName($imageKey);
            
            if ($model && $model->getFirstMedia($collectionName)) {
                $fileName = $model->getFirstMedia($collectionName)->file_name;
                $model->clearMediaCollection($collectionName);
                
                log_activity($model, 'resim silindi', [
                    'collection' => $collectionName,
                    'filename' => $fileName
                ]);
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