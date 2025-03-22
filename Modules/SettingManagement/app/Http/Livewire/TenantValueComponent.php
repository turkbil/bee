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
    public $temporaryMultipleImages = [];
    public $multipleImagesArray = [];
    
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
                
            case 'image_multiple':
                try {
                    if (is_string($this->value) && !empty($this->value)) {
                        $this->multipleImagesArray = json_decode($this->value, true) ?: [];
                    } elseif (is_array($this->value)) {
                        $this->multipleImagesArray = $this->value;
                    } else {
                        $this->multipleImagesArray = [];
                    }
                } catch (\Exception $e) {
                    $this->multipleImagesArray = [];
                }
                break;
        }
        
        // Dosya türünde ve geçerli bir dosya varsa, önizleme URL'sini hazırla
        if (($setting->type === 'file' || $setting->type === 'image') && $this->value) {
            $this->previewing = true;
            $this->previewUrl = cdn($this->value);
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
                    
                case 'image_multiple':
                    try {
                        if (is_string($this->value) && !empty($this->value)) {
                            $this->multipleImagesArray = json_decode($this->value, true) ?: [];
                        } else {
                            $this->multipleImagesArray = [];
                        }
                    } catch (\Exception $e) {
                        $this->multipleImagesArray = [];
                    }
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
                // Eğer eski değer storage/ ile başlıyorsa, yolu düzelt
                $oldPath = $this->value;
                if (Str::startsWith($oldPath, 'storage/')) {
                    $oldPath = substr($oldPath, 8); // "storage/" kısmını çıkar
                }
                
                // Dosya yolundan local path'i çıkart
                $localPath = $this->extractLocalPath($oldPath);
                
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
    
    // Çoklu resim için işleme
    public function updatedTemporaryMultipleImages($value, $index)
    {
        $this->validateOnly("temporaryMultipleImages.{$index}", [
            "temporaryMultipleImages.{$index}" => ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
        ]);
        
        // Değişen resim sıralanacak
        $this->useDefault = false;
    }
    
    // Çoklu resim ekle
    public function addMultipleImageField()
    {
        if (!isset($this->temporaryMultipleImages)) {
            $this->temporaryMultipleImages = [];
        }
        
        $this->temporaryMultipleImages[] = null;
    }
    
    // Çoklu resim sil
    public function removeMultipleImageField($index)
    {
        if (isset($this->temporaryMultipleImages[$index])) {
            unset($this->temporaryMultipleImages[$index]);
            // Boşlukları temizle
            $this->temporaryMultipleImages = array_values($this->temporaryMultipleImages);
        }
    }
    
    // Mevcut çoklu resim sil
    public function removeMultipleImage($index)
    {
        if (isset($this->multipleImagesArray[$index])) {
            // Dosya yolunu al
            $imagePath = $this->multipleImagesArray[$index];
            
            // Dosyayı sil
            \Modules\SettingManagement\App\Helpers\TenantStorageHelper::deleteFile($imagePath);
            
            // Diziden kaldır
            unset($this->multipleImagesArray[$index]);
            
            // Diziye yeniden sırala ve değeri güncelle
            $this->multipleImagesArray = array_values($this->multipleImagesArray);
            $this->value = !empty($this->multipleImagesArray) ? json_encode($this->multipleImagesArray) : null;
            
            // Değer değişti
            $this->useDefault = false;
        }
    }

    // URL'den yerel depolama yolunu çıkarır
    private function extractLocalPath($path)
    {
        if (empty($path)) {
            return '';
        }
        
        // storage/ ile başlıyorsa çıkar
        if (Str::startsWith($path, 'storage/')) {
            $path = substr($path, 8); // "storage/" kısmını çıkar
        }
        
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
                
            case 'image_multiple':
                try {
                    if (is_string($this->value) && !empty($this->value)) {
                        $this->multipleImagesArray = json_decode($this->value, true) ?: [];
                    } else {
                        $this->multipleImagesArray = [];
                    }
                } catch (\Exception $e) {
                    $this->multipleImagesArray = [];
                }
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
            
            // Eğer dosya varsa sil
            if ($this->value && ($setting->type === 'file' || $setting->type === 'image')) {
                \Modules\SettingManagement\App\Helpers\TenantStorageHelper::deleteFile($this->value);
            }
            
            // Eğer çoklu resim varsa hepsini sil
            if ($setting->type === 'image_multiple' && !empty($this->multipleImagesArray)) {
                foreach ($this->multipleImagesArray as $imagePath) {
                    if (!empty($imagePath)) {
                        \Modules\SettingManagement\App\Helpers\TenantStorageHelper::deleteFile($imagePath);
                    }
                }
            }
                
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
            } 
            elseif ($setting->type === 'image_multiple') {
                // Çoklu resim işleme
                try {
                    if (!empty($this->temporaryMultipleImages)) {
                        $newImages = [];
                        
                        // Eğer zaten bir dizi varsa, mevcut resimleri koru
                        if (!empty($this->multipleImagesArray)) {
                            $newImages = $this->multipleImagesArray;
                        }
                        
                        // Yeni yüklenen resimleri ekle
                        foreach ($this->temporaryMultipleImages as $index => $image) {
                            if ($image) {
                                // Tenant id belirleme - Central ise tenant1, değilse gerçek tenant ID
                                $tenantId = is_tenant() ? tenant_id() : 1;
                                
                                // Dosya adı oluşturma
                                $fileName = time() . '_' . Str::slug($setting->key) . '_' . $index . '.' . $image->getClientOriginalExtension();
                                
                                // YENİ: TenantStorageHelper ile doğru şekilde dosyayı yükle
                                $imagePath = \Modules\SettingManagement\App\Helpers\TenantStorageHelper::storeTenantFile(
                                    $image,
                                    "settings/images",
                                    $fileName,
                                    $tenantId
                                );
                                
                                $newImages[] = $imagePath;
                            }
                        }
                        
                        // Dizi varsa JSON'a çevir
                        if (!empty($newImages)) {
                            $valueToSave = json_encode($newImages);
                            $this->multipleImagesArray = $newImages;
                        } else {
                            $valueToSave = null;
                        }
                    } 
                    else {
                        // Eğer yeni resim yoksa mevcut dizinin JSON formatını kullan
                        if (!empty($this->multipleImagesArray)) {
                            $valueToSave = json_encode($this->multipleImagesArray);
                        } else {
                            $valueToSave = null;
                        }
                    }
                } catch (\Exception $e) {
                    $this->dispatch('toast', [
                        'title' => 'Hata!',
                        'message' => 'Çoklu resim yüklenirken bir hata oluştu: ' . $e->getMessage(),
                        'type' => 'error',
                    ]);
                    return;
                }
            }
            elseif (($setting->type === 'image' || $setting->type === 'file') && !empty($this->temporaryImages)) {
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
                        
                        // Eski dosyayı sil (eğer varsa)
                        if ($this->value) {
                            \Modules\SettingManagement\App\Helpers\TenantStorageHelper::deleteFile($this->value);
                        }
                        
                        // YENİ: TenantStorageHelper ile doğru şekilde dosyayı yükle
                        $valueToSave = \Modules\SettingManagement\App\Helpers\TenantStorageHelper::storeTenantFile(
                            $this->temporaryImages[$key],
                            "settings/{$folder}",
                            $fileName,
                            $tenantId
                        );
                        
                        $this->previewing = true;
                        $this->previewUrl = cdn($valueToSave);
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