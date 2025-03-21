<?php
namespace Modules\SettingManagement\App\Http\Livewire\Traits;

use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Modules\SettingManagement\App\Models\Setting;

trait WithImageUpload
{
    use WithFileUploads;

    public $tempImage = null;
    public $imagePreview = null;

    public function updatedTempImage()
    {
        $this->validateOnly('tempImage', [
            'tempImage' => ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
        ]);

        if ($this->tempImage) {
            $this->imagePreview = $this->tempImage->temporaryUrl();
        }
    }

    public function uploadImage($settingId = null)
    {
        if (!$this->tempImage) {
            return null;
        }

        $this->validate([
            'tempImage' => ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
        ]);

        // Eski resmi sil (eğer varsa)
        if ($settingId) {
            $setting = Setting::find($settingId);
            if ($setting && !empty($setting->default_value) && Storage::disk('public')->exists($setting->default_value)) {
                Storage::disk('public')->delete($setting->default_value);
            }
        }

        // Yeni resmi kaydet
        $fileName = time() . '_' . $this->tempImage->getClientOriginalName();
        $path = $this->tempImage->storeAs('settings/images', $fileName, 'public');
        
        // Resim yolunu döndür
        return $path;
    }

    public function deleteImage($imagePath)
    {
        if (empty($imagePath)) {
            return;
        }

        if (Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
            return true;
        }

        return false;
    }

    public function imagePreviewUrl($imagePath)
    {
        if (empty($imagePath)) {
            return null;
        }

        if (Storage::disk('public')->exists($imagePath)) {
            return Storage::url($imagePath);
        }

        return null;
    }
}