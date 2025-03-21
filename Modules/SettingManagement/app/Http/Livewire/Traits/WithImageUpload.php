<?php
namespace Modules\SettingManagement\App\Http\Livewire\Traits;

use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Modules\SettingManagement\App\Models\Setting;

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
        if (!$model && isset($this->settingId)) {
            $model = Setting::find($this->settingId);
        }
    
        if ($model && isset($this->temporaryImages[$imageKey])) {
            $fileName = Str::slug($model->key) . '-' . Str::random(6) . '.' . $this->temporaryImages[$imageKey]->getClientOriginalExtension();
    
            $path = $this->temporaryImages[$imageKey]->storeAs('settings/images', $fileName, 'public');
            
            if ($model->type === 'image' || $model->type === 'file') {
                // Eğer daha önce dosya varsa sil
                if ($model->default_value && Storage::disk('public')->exists($model->default_value)) {
                    Storage::disk('public')->delete($model->default_value);
                }
                
                $model->default_value = $path;
                $model->save();
                
                log_activity(
                    $model,
                    'dosya yüklendi',
                    ['path' => $path]
                );
            }
            
            return $path;
        }
        
        return null;
    }

    public function removeImage($imageKey)
    {
        if (isset($this->settingId)) {
            $model = Setting::find($this->settingId);
            
            if ($model && $model->default_value && Storage::disk('public')->exists($model->default_value)) {
                Storage::disk('public')->delete($model->default_value);
                
                $model->default_value = null;
                $model->save();
                
                log_activity(
                    $model,
                    'dosya silindi'
                );
            }
        }
        
        unset($this->temporaryImages[$imageKey]);
    }
}