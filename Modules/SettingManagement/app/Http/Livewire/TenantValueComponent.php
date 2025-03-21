<?php

namespace Modules\SettingManagement\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Modules\SettingManagement\App\Models\Setting;
use Modules\SettingManagement\App\Models\SettingValue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Layout('admin.layout')]
class TenantValueComponent extends Component
{
    use WithFileUploads;

    public $settingId;
    public $value;
    public $useDefault = false;
    public $tempFile;

    public function mount($id)
    {
        $this->settingId = $id;
        
        $settingValue = SettingValue::where('setting_id', $id)->first();
            
        if ($settingValue) {
            $this->value = $settingValue->value;
        } else {
            $this->useDefault = true;
            $setting = Setting::find($id);
            $this->value = $setting->default_value;
        }
    }

    public function updatedTempFile()
    {
        if ($this->tempFile) {
            $this->value = $this->tempFile->store('settings', 'public');
        }
    }

    public function toggleDefault()
    {
        $this->useDefault = !$this->useDefault;
        
        if ($this->useDefault) {
            $setting = Setting::find($this->settingId);
            $this->value = $setting->default_value;
        }
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
            // Tenant veritabanında SettingValue güncelle veya oluştur
            $settingValue = SettingValue::updateOrCreate(
                ['setting_id' => $this->settingId],
                ['value' => $this->value]
            );
            
            log_activity(
                $setting,
                'değeri güncellendi',
                ['old' => $setting->value, 'new' => $this->value]
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