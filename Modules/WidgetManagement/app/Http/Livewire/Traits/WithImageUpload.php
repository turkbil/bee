<?php 
namespace Modules\WidgetManagement\app\Http\Livewire\Traits;

use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Helpers\WidgetStorageHelper;

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
                $tenantId = is_tenant() ? tenant_id() : 1;
                
                $fileName = time() . '_' . Str::slug($model->slug) . '_' . $this->temporaryImages[$imageKey]->getClientOriginalName();
                
                $folder = $this->getCollectionName($imageKey);
                
                $oldValue = isset($model->{$imageKey}) ? $model->{$imageKey} : null;
                if ($oldValue) {
                    WidgetStorageHelper::deleteWidgetFile($oldValue);
                }
                
                $urlPath = WidgetStorageHelper::storeWidgetFile(
                    $this->temporaryImages[$imageKey],
                    "widgets/{$folder}",
                    $fileName,
                    $tenantId
                );
                
                if (isset($this->widget) && isset($this->widget[$imageKey])) {
                    $this->widget[$imageKey] = $urlPath;
                }
                
                if ($imageKey === 'thumbnail') {
                    $this->imagePreview = $urlPath;
                }
                
                return $urlPath;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Widget resim yükleme hatası: ' . $e->getMessage(), [
                    'model_id' => $model->id,
                    'tenant' => is_tenant() ? tenant_id() : 1,
                    'exception' => $e
                ]);
            }
        }
        
        return null;
    }

    private function extractLocalPath($path)
    {
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
                WidgetStorageHelper::deleteWidgetFile($this->widget[$imageKey]);
                
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