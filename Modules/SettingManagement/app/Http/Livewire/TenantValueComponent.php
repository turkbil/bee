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
    public $originalValue;
    public $defaultValue;
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
        $this->defaultValue = $setting->default_value;
        
        $settingValue = SettingValue::where('setting_id', $id)->first();
        
        // Şu anki değeri al veya varsayılan değeri kullan
        if ($settingValue) {
            $this->value = $settingValue->value;
            $this->originalValue = $this->value;
            $this->useDefault = false;
        } else {
            $this->useDefault = true;
            $this->value = $setting->default_value;
            $this->originalValue = null;
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
        if (($setting->type === 'file' || $setting->type === 'image') && $this->value) {
            $this->previewing = true;
            $this->previewUrl = url('/storage/' . $this->value);
        }
    }
    
    // Değer değiştiğinde otomatik olarak varsayılan değer ile karşılaştır
    public function updatedValue()
    {
        $this->checkValueStatus();
    }
    
    public function updatedColorValue()
    {
        $this->value = $this->colorValue;
        $this->checkValueStatus();
    }
    
    public function updatedDateValue()
    {
        $this->value = $this->dateValue;
        $this->checkValueStatus();
    }
    
    public function updatedTimeValue()
    {
        $this->value = $this->timeValue;
        $this->checkValueStatus();
    }
    
    public function updatedCheckboxValue()
    {
        $this->value = $this->checkboxValue ? '1' : '0';
        $this->checkValueStatus();
    }

    // Varsayılan değer kullan değiştiğinde
    public function updatedUseDefault()
    {
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
    
    // Değeri varsayılan değer ile karşılaştır ve useDefault'u otomatik güncelle
    private function checkValueStatus()
    {
        $setting = Setting::find($this->settingId);
        // Değer varsayılan değerden farklıysa useDefault false olmalı
        if ($this->value != $setting->default_value) {
            $this->useDefault = false;
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
            // Tenant id belirleme - Central ise tenant1, değilse gerçek tenant ID
            $tenantId = is_tenant() ? tenant_id() : 1;
            
            // Eski dosyayı kontrol et ve sil
            if ($this->value) {
                // Dosya yolundan local path'i çıkart
                $localPath = $this->extractLocalPath($this->value);
                
                if (Storage::disk('public')->exists($localPath)) {
                    Storage::disk('public')->delete($localPath);
                }
            }
            
            // Yeni dosya için geçici URL hazırla
            if ($key === 'image') {
                $this->previewing = true;
                $this->previewUrl = $this->temporaryImages[$key]->temporaryUrl();
            } else {
                $this->previewing = true;
            }
            
            // Dosya değiştiğinde varsayılan değere eşit olamaz
            $this->useDefault = false;
        }
    }
    
    // URL'den yerel depolama yolunu çıkarır
    private function extractLocalPath($path)
    {
        // tenant{id}/ ifadesini ara ve kaldır
        if (preg_match('/^tenant\d+\/(.*)$/', $path, $matches)) {
            return $matches[1];
        }
        return $path;
    }
    
    // Varsayılan değere dön butonu için 
    public function resetToDefault()
    {
        $setting = Setting::find($this->settingId);
        $this->value = $setting->default_value;
        $this->useDefault = true;
        
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
        
        $this->dispatch('toast', [
            'title' => 'Bilgi',
            'message' => 'Varsayılan değere dönüldü.',
            'type' => 'info',
        ]);
    }
    
    public function deleteFile()
    {
        if ($this->value) {
            // Dosya yolundan local path'i çıkart
            $localPath = $this->extractLocalPath($this->value);
            
            if (Storage::disk('public')->exists($localPath)) {
                Storage::disk('public')->delete($localPath);
            }
        }
        
        $this->value = null;
        $this->previewing = false;
        $this->previewUrl = null;
        $this->useDefault = true;
        
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
                try {
                    // Dosya yüklemesi var, işle
                    $type = $setting->type;
                    $key = $type === 'image' ? 'image' : 'file';
                    
                    if (isset($this->temporaryImages[$key])) {
                        $folder = $type === 'image' ? 'images' : 'files';
                        
                        // Tenant id belirleme - Central ise tenant1, değilse gerçek tenant ID
                        $tenantId = is_tenant() ? tenant_id() : 1;
                        
                        // Dosya adı oluşturma
                        $fileName = time() . '_' . Str::slug($setting->key) . '.' . $this->temporaryImages[$key]->getClientOriginalExtension();
                        
                        // Tenant için dosya yolu
                        $tenantPath = "tenant{$tenantId}/settings/{$folder}/{$fileName}";
                        
                        // Normal dosya yolu - veritabanı tenant ID'si içermez
                        $path = "settings/{$folder}/{$fileName}";
                        
                        // Eski dosyayı sil (eğer varsa)
                        if ($this->value) {
                            // Dosya yolundan local path'i çıkart
                            $localPath = $this->extractLocalPath($this->value);
                            
                            if (Storage::disk('public')->exists($localPath)) {
                                Storage::disk('public')->delete($localPath);
                            }
                        }
                        
                        // Dosyayı doğru klasöre kaydet
                        if ($tenantId == 1) {
                            // Central için normal public disk kullan
                            Storage::disk('public')->putFileAs(
                                dirname($path),
                                $this->temporaryImages[$key],
                                basename($path)
                            );
                        } else {
                            // Tenant için tenant{id} klasörünü kullan
                            $tenantStorage = storage_path("tenant{$tenantId}/app/public/" . dirname($path));
                            if (!file_exists($tenantStorage)) {
                                mkdir($tenantStorage, 0755, true);
                            }
                            
                            // Tenant klasörüne yükle
                            $this->temporaryImages[$key]->storeAs(
                                dirname($path),
                                basename($path),
                                ['disk' => 'tenant']
                            );
                        }
                        
                        // Dosya yolunu sakla - tenant ID'li formatı kullan
                        $valueToSave = $tenantPath;
                        $this->previewing = true;
                        $this->previewUrl = url('/storage/' . $tenantPath);
                    }
                } catch (\Exception $e) {
                    $this->dispatch('toast', [
                        'title' => 'Hata!',
                        'message' => 'Dosya yüklenirken bir hata oluştu: ' . $e->getMessage(),
                        'type' => 'error',
                    ]);
                    return;
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
                ['old' => $this->originalValue, 'new' => $valueToSave]
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