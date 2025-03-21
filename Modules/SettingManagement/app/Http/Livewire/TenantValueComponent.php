<?php

namespace Modules\SettingManagement\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Modules\SettingManagement\App\Models\Setting;
use Modules\SettingManagement\App\Models\SettingValue;
use Modules\SettingManagement\App\Http\Livewire\Traits\WithImageUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Layout('admin.layout')]
class TenantValueComponent extends Component
{
    use WithFileUploads, WithImageUpload;

    public $settingId;
    public $value;
    public $useDefault = false;
    public $tempFile;
    public $previewing = false;
    public $previewUrl = null;
    public $datePickerFormat;
    public $timePickerFormat = 'h:i A';
    
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
        if (($setting->type === 'file' || $setting->type === 'image') && $this->value && Storage::disk('public')->exists($this->value)) {
            $this->previewing = true;
            $this->previewUrl = Storage::url($this->value);
        }
    }

    public function updatedTempFile()
    {
        if ($this->tempFile) {
            $this->validate([
                'tempFile' => 'file|max:2048|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,zip',
            ]);
            
            // Eski dosyayı sil (varsa)
            $setting = Setting::find($this->settingId);
            $oldValue = SettingValue::where('setting_id', $this->settingId)->first();
            
            if ($oldValue && !empty($oldValue->value) && Storage::disk('public')->exists($oldValue->value)) {
                Storage::disk('public')->delete($oldValue->value);
            }
            
            // Yeni dosyayı kaydet
            $filename = time() . '_' . $this->tempFile->getClientOriginalName();
            $path = $this->tempFile->storeAs('settings', $filename, 'public');
            
            $this->value = $path;
            $this->previewing = true;
            $this->previewUrl = Storage::url($path);
        }
    }
    
    // Renk değeri değiştiğinde ana değeri güncelle
    public function updatedColorValue()
    {
        $this->value = $this->colorValue;
    }
    
    // Tarih değeri değiştiğinde ana değeri güncelle
    public function updatedDateValue()
    {
        $this->value = $this->dateValue;
    }
    
    // Saat değeri değiştiğinde ana değeri güncelle
    public function updatedTimeValue()
    {
        $this->value = $this->timeValue;
    }
    
    // Checkbox değeri değiştiğinde ana değeri güncelle
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
            
            // Ayar türüne göre özel değişkenleri güncelle
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
    
    // Dosya silme fonksiyonu
    public function deleteFile()
    {
        if ($this->value && Storage::disk('public')->exists($this->value)) {
            Storage::disk('public')->delete($this->value);
        }
        
        $this->value = null;
        $this->previewing = false;
        $this->previewUrl = null;
        
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
            } elseif ($setting->type === 'image' && $this->tempImage) {
                $path = $this->uploadImage($this->settingId);
                if ($path) {
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
                ['old' => $setting->value, 'new' => $valueToSave]
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

    public function render()
    {
        $setting = Setting::find($this->settingId);
        
        return view('settingmanagement::livewire.tenant-value-component', [
            'setting' => $setting
        ]);
    }
}