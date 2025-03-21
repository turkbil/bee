<?php

namespace Modules\SettingManagement\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Modules\SettingManagement\App\Models\Setting;
use Modules\SettingManagement\App\Models\SettingValue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

#[Layout('admin.layout')]
class TenantValueComponent extends Component
{
    use WithFileUploads;

    public $settingId;
    public $value;
    public $useDefault = false;
    public $previewing = false;
    public $previewUrl = null;
    public $datePickerFormat;
    public $timePickerFormat = 'h:i A';
    public $temporaryImages = [];
    
    // Ayar türüne özgü alanlar
    public $colorValue = '#ffffff';
    public $dateValue = null;
    public $timeValue = null;
    public $checkboxValue = false;

    public function mount($id)
    {
        $this->settingId = $id;
        
        $setting = Setting::find($id);
        $settingValue = SettingValue::where('setting_id', $id)->first();
        
        // Şu anki değeri al veya varsayılan değeri kullan
        if ($settingValue) {
            $this->value = $settingValue->value;
            $this->useDefault = false;
        } else {
            $this->useDefault = true;
            $this->value = $setting->default_value;
        }
        
        // Ayar türüne göre özel değişkenleri ayarla
        switch ($setting->type) {
            case 'color':
                $this->colorValue = $this->value ?: '#ffffff';
                break;
                
            case 'date':
                $this->dateValue = $this->value ?: date('Y-m-d');
                break;
                
            case 'time':
                $this->timeValue = $this->value ?: date('H:i');
                break;
                
            case 'checkbox':
                $this->checkboxValue = (bool) $this->value;
                break;
        }
        
        // Dosya türünde ve geçerli bir dosya varsa, önizleme URL'sini hazırla
        // Burada MediaLibrary değil, doğrudan settings_values tablosundaki değeri kullanıyoruz
        if (($setting->type === 'file' || $setting->type === 'image') && $this->value && Storage::disk('public')->exists($this->value)) {
            $this->previewing = true;
            $this->previewUrl = Storage::url($this->value);
        }
    }
    

    public function updatedTemporaryImages($value, $key)
    {
        $this->validateOnly("temporaryImages.{$key}", [
            "temporaryImages.{$key}" => $key === 'image' 
                ? ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048']
                : ['file', 'max:2048'],
        ]);

        if ($this->temporaryImages[$key]) {
            // Eski dosyayı sil
            $setting = Setting::find($this->settingId);
            $settingValue = SettingValue::where('setting_id', $this->settingId)->first();
            
            $oldValue = $settingValue ? $settingValue->value : null;
            
            if ($oldValue && Storage::disk('public')->exists($oldValue)) {
                Storage::disk('public')->delete($oldValue);
            }
            
            // Yeni dosya için geçici URL ve ad hazırla
            if ($key === 'image') {
                $this->previewing = true;
                $this->previewUrl = $this->temporaryImages[$key]->temporaryUrl();
            } else {
                $this->previewing = true;
            }
        }
    }
    
    public function updatedColorValue()
    {
        $this->value = $this->colorValue;
    }
    
    public function updatedDateValue()
    {
        $this->value = $this->dateValue;
    }
    
    public function updatedTimeValue()
    {
        $this->value = $this->timeValue;
    }
    
    public function updatedCheckboxValue()
    {
        $this->value = $this->checkboxValue ? '1' : '0';
    }

    public function toggleDefault()
    {
        $this->useDefault = !$this->useDefault;
        
        if ($this->useDefault) {
            $setting = Setting::find($this->settingId);
            $this->value = $setting->default_value;
            
            switch ($setting->type) {
                case 'color':
                    $this->colorValue = $this->value ?: '#ffffff';
                    break;
                    
                case 'date':
                    $this->dateValue = $this->value ?: date('Y-m-d');
                    break;
                    
                case 'time':
                    $this->timeValue = $this->value ?: date('H:i');
                    break;
                    
                case 'checkbox':
                    $this->checkboxValue = (bool) $this->value;
                    break;
            }
        }
    }
    
    public function deleteFile()
    {
        if ($this->value && Storage::disk('public')->exists($this->value)) {
            Storage::disk('public')->delete($this->value);
        }
        
        $this->value = null;
        $this->previewing = false;
        $this->previewUrl = null;
        
        SettingValue::updateOrCreate(
            ['setting_id' => $this->settingId],
            ['value' => null]
        );
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Dosya silindi.',
            'type' => 'success',
        ]);
    }


    public function save($redirect = false)
    {
        $setting = Setting::find($this->settingId);
    
        if ($this->useDefault) {
            // Tenant veritabanında SettingValue kaydı varsa sil
            SettingValue::where('setting_id', $this->settingId)->delete();
                
            log_activity(
                $setting,
                'varsayılan değere döndürüldü'
            );
                
            $message = 'Ayar varsayılan değere döndürüldü.';
        } else {
            // Ayar tipine göre değeri işle
            $valueToSave = $this->value;
            
            if ($setting->type === 'checkbox') {
                $valueToSave = $this->checkboxValue ? '1' : '0';
            } elseif (($setting->type === 'image' || $setting->type === 'file') && !empty($this->temporaryImages)) {
                // Dosya yüklemesi var, işle
                $type = $setting->type;
                $key = $type === 'image' ? 'image' : 'file';
                
                if (isset($this->temporaryImages[$key])) {
                    // Tenant ID'ye göre prefix oluştur
                    $tenantPrefix = is_tenant() ? 'tenant' . tenant_id() : '';
                    
                    $fileName = time() . '_' . Str::slug($setting->key) . '.' . $this->temporaryImages[$key]->getClientOriginalExtension();
                    $folder = $type === 'image' ? 'images' : 'files';
                    
                    // Yeni dosyayı kaydet - hem spatie'ye hem de doğrudan diske
                    $path = "{$tenantPrefix}/settings/{$folder}/{$fileName}";
                    
                    // Spatie MediaLibrary ile dosyayı ekle
                    $setting->clearMediaCollection($type === 'image' ? 'images' : 'files');
                    $media = $setting->addMedia($this->temporaryImages[$key]->getRealPath())
                        ->usingFileName($fileName)
                        ->withCustomProperties([
                            'setting_id' => $this->settingId,
                            'tenant_id' => is_tenant() ? tenant_id() : 'central'
                        ])
                        ->toMediaCollection($type === 'image' ? 'images' : 'files', 'public');
                    
                    // Aynı zamanda doğrudan Storage üzerinden dosyayı kaydet
                    Storage::disk('public')->putFileAs("{$tenantPrefix}/settings/{$folder}", $this->temporaryImages[$key], $fileName);
                    
                    // Dosya yolunu sakla - bunu setting_values tablosuna kaydedeceğiz
                    $valueToSave = $path;
                }
            }
            
            // Tenant veritabanında SettingValue güncelle veya oluştur
            $settingValue = SettingValue::updateOrCreate(
                ['setting_id' => $this->settingId],
                ['value' => $valueToSave]
            );
            
            log_activity(
                $setting,
                'değeri güncellendi',
                ['old' => $setting->default_value, 'new' => $valueToSave]
            );
            
            $message = 'Ayar değeri güncellendi.';
        }
    
        if ($redirect) {
            return redirect()->route('admin.settingmanagement.tenant.settings');
        }
    
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => $message,
            'type' => 'success',
        ]);
    }
    
    
    
    
    // Tenant veritabanına doğrudan medya ekleyen fonksiyon
    private function addMediaToTenantDB($setting, $fileName, $path, $type)
    {
        // Central connection'dan tenant connection'a geçiş yap
        $mediaModel = config('media-library.media_model');
        
        // Tenant DB'sine manuel media kaydı ekle
        DB::table('media')->insert([
            'model_type' => get_class($setting),
            'model_id' => $setting->id,
            'uuid' => (string) Str::uuid(),
            'collection_name' => $type === 'image' ? 'images' : 'files',
            'name' => pathinfo($fileName, PATHINFO_FILENAME),
            'file_name' => $fileName,
            'mime_type' => $this->temporaryImages[$type === 'image' ? 'image' : 'file']->getMimeType(),
            'disk' => 'public',
            'conversions_disk' => 'public',
            'size' => $this->temporaryImages[$type === 'image' ? 'image' : 'file']->getSize(),
            'manipulations' => '[]',
            'custom_properties' => '{"setting_id":"' . $setting->id . '"}',
            'generated_conversions' => '[]',
            'responsive_images' => '[]',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function render()
    {
        $setting = Setting::find($this->settingId);
        
        return view('settingmanagement::livewire.tenant-value-component', [
            'setting' => $setting
        ]);
    }
}