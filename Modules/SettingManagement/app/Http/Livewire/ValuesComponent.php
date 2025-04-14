<?php
// Modules/SettingManagement/app/Http/Livewire/ValuesComponent.php

namespace Modules\SettingManagement\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Modules\SettingManagement\App\Models\Setting;
use Modules\SettingManagement\App\Models\SettingValue;
use Modules\SettingManagement\App\Models\SettingGroup;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

#[Layout('admin.layout')]
class ValuesComponent extends Component
{
    use WithPagination, WithFileUploads;
    
    public $groupId;
    public $values = [];
    public $originalValues = [];
    public $changes = [];
    public $group;
    public $temporaryImages = [];
    public $temporaryMultipleImages = [];
    public $multipleImagesArrays = [];
    
    // Çoklu resim seçimi için yeni değişkenler
    public $tempPhoto;
    public $photoField; // Hangi alan için yüklüyoruz

    public function mount($group)
    {
        $this->groupId = $group;
        $this->group = SettingGroup::findOrFail($group);
        
        $settings = Setting::where('group_id', $this->groupId)->get();
        
        foreach ($settings as $setting) {
            $value = SettingValue::where('setting_id', $setting->id)->first();
            
            // Normal değerler için
            $this->values[$setting->id] = $value ? $value->value : $setting->default_value;
            $this->originalValues[$setting->id] = $this->values[$setting->id];
            
            // Çoklu resim için JSON'ı diziye çevir
            if ($setting->type === 'image_multiple') {
                try {
                    if (isset($this->values[$setting->id]) && !empty($this->values[$setting->id])) {
                        $this->multipleImagesArrays[$setting->id] = json_decode($this->values[$setting->id], true) ?: [];
                    } else {
                        $this->multipleImagesArrays[$setting->id] = [];
                    }
                } catch (\Exception $e) {
                    $this->multipleImagesArrays[$setting->id] = [];
                }
            }
        }
    }

    public function resetToDefault($settingId)
    {
        $setting = Setting::find($settingId);
        $this->values[$settingId] = $setting->default_value;
        
        // Eğer ayar türü çoklu resim ise
        if ($setting->type === 'image_multiple') {
            try {
                if (!empty($setting->default_value)) {
                    $this->multipleImagesArrays[$settingId] = json_decode($setting->default_value, true) ?: [];
                } else {
                    $this->multipleImagesArrays[$settingId] = [];
                }
            } catch (\Exception $e) {
                $this->multipleImagesArrays[$settingId] = [];
            }
        }
        
        $this->checkChanges();
    }

    public function updatedValues()
    {
        $this->checkChanges();
    }

    public function updatedTemporaryImages($value, $key)
    {
        $parts = explode('.', $key);
        $settingId = $parts[0] ?? null;
        
        if ($settingId && isset($this->temporaryImages[$settingId])) {
            $setting = Setting::find($settingId);
            
            if ($setting) {
                $this->values[$settingId] = 'temp'; // Geçici değer, dosya yüklendiğinde gerçek path ile değiştirilecek
                $this->checkChanges();
            }
        }
    }
    
    // Çoklu resim için güncelleme - toplu seçim yapmayı destekler
    public function updatedTempPhoto()
    {
        if ($this->tempPhoto && $this->photoField) {
            // Çoklu fotoğraf yükleme için dizi kontrolü
            $photosToProcess = is_array($this->tempPhoto) ? $this->tempPhoto : [$this->tempPhoto];
            
            foreach ($photosToProcess as $photo) {
                $this->validate([
                    'tempPhoto.*' => 'image|max:2048', // 2MB Max
                ]);
                
                if (!isset($this->temporaryMultipleImages[$this->photoField])) {
                    $this->temporaryMultipleImages[$this->photoField] = [];
                }
                
                $this->temporaryMultipleImages[$this->photoField][] = $photo;
            }
            
            $this->tempPhoto = null;
            
            // Değişiklik var olarak işaretle
            if (!isset($this->changes[$this->photoField])) {
                $this->changes[$this->photoField] = true;
            }
        }
    }
    
    public function setPhotoField($fieldName)
    {
        $this->photoField = $fieldName;
    }
    
    // Çoklu resim ekle
    public function addMultipleImageField($settingId)
    {
        if (!isset($this->temporaryMultipleImages[$settingId])) {
            $this->temporaryMultipleImages[$settingId] = [];
        }
        
        // Değişiklik olarak kaydet
        if (!isset($this->changes[$settingId])) {
            $this->changes[$settingId] = true;
        }
        
        $this->checkChanges();
    }
    
    // Çoklu resim sil (yeni eklenenler için)
    public function removeMultipleImageField($settingId, $index)
    {
        if (isset($this->temporaryMultipleImages[$settingId][$index])) {
            unset($this->temporaryMultipleImages[$settingId][$index]);
            // Boşlukları temizle
            $this->temporaryMultipleImages[$settingId] = array_values($this->temporaryMultipleImages[$settingId]);
        }
        $this->checkChanges();
    }
    
    // Çoklu resim sil (mevcut resimler için)
    public function removeMultipleImage($settingId, $index)
    {
        if (isset($this->multipleImagesArrays[$settingId][$index])) {
            // Dosyayı sil
            $imagePath = $this->multipleImagesArrays[$settingId][$index];
            \Modules\SettingManagement\App\Helpers\TenantStorageHelper::deleteFile($imagePath);
            
            // Diziden çıkar
            unset($this->multipleImagesArrays[$settingId][$index]);
            // Diziye yeniden sırala
            $this->multipleImagesArrays[$settingId] = array_values($this->multipleImagesArrays[$settingId]);
            
            // Eğer dizide hiç eleman kalmazsa varsayılan değer kullan
            if (empty($this->multipleImagesArrays[$settingId])) {
                $this->values[$settingId] = null;
            } else {
                // JSON'a çevir
                $this->values[$settingId] = json_encode($this->multipleImagesArrays[$settingId]);
            }
            
            // Değişikliği kaydet
            $this->checkChanges();
        }
    }
    
    public function checkChanges()
    {
        $this->changes = [];
        foreach ($this->values as $id => $value) {
            if ($value == 'temp' || $value != $this->originalValues[$id]) {
                $this->changes[$id] = $value;
            }
        }
        
        // Çoklu resim için de kontrol et
        foreach ($this->temporaryMultipleImages as $settingId => $images) {
            if (!empty($images)) {
                $this->changes[$settingId] = true;
            }
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

    public function save($redirect = false)
    {
        foreach ($this->values as $settingId => $value) {
            $setting = Setting::find($settingId);
            $oldValue = $this->originalValues[$settingId];
            
            // File/Image dosya yüklemelerini işle
            if (isset($this->temporaryImages[$settingId])) {
                $file = $this->temporaryImages[$settingId];
                $type = $setting->type;
                
                try {
                    // Tenant id belirleme - Central ise tenant1, değilse gerçek tenant ID
                    $tenantId = is_tenant() ? tenant_id() : 1;
                    
                    // Dosya adını oluştur
                    $fileName = Str::slug($setting->key) . '-' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                    $folder = $type === 'image' ? 'images' : 'files';
                    
                    // Eski dosyayı sil (eğer varsa)
                    if ($oldValue) {
                        \Modules\SettingManagement\App\Helpers\TenantStorageHelper::deleteFile($oldValue);
                    }
                    
                    // TenantStorageHelper ile doğru şekilde dosyayı yükle
                    $value = \Modules\SettingManagement\App\Helpers\TenantStorageHelper::storeTenantFile(
                        $file,
                        "settings/{$folder}",
                        $fileName,
                        $tenantId
                    );
                    
                    $this->values[$settingId] = $value;
                } catch (\Exception $e) {
                    $this->dispatch('toast', [
                        'title' => 'Hata!',
                        'message' => 'Dosya yüklenirken bir hata oluştu: ' . $e->getMessage(),
                        'type' => 'error',
                    ]);
                    continue;
                }
            }
            
            // Çoklu resim tipi için işlem
            if ($setting->type === 'image_multiple' && isset($this->temporaryMultipleImages[$settingId]) && count($this->temporaryMultipleImages[$settingId]) > 0) {
                try {
                    $newImages = [];
                    
                    // Eğer mevcut resimler varsa, onları koru
                    if (isset($this->multipleImagesArrays[$settingId]) && !empty($this->multipleImagesArrays[$settingId])) {
                        $newImages = $this->multipleImagesArrays[$settingId];
                    }
                    
                    // Yeni resimleri ekle
                    foreach ($this->temporaryMultipleImages[$settingId] as $index => $photo) {
                        if ($photo) {
                            // Tenant id belirleme - Central ise tenant1, değilse gerçek tenant ID
                            $tenantId = is_tenant() ? tenant_id() : 1;
                            
                            // Dosya adını oluştur
                            $fileName = time() . '_' . Str::slug($setting->key) . '_' . Str::random(6) . '.' . $photo->getClientOriginalExtension();
                            
                            // TenantStorageHelper ile doğru şekilde dosyayı yükle
                            $imagePath = \Modules\SettingManagement\App\Helpers\TenantStorageHelper::storeTenantFile(
                                $photo,
                                "settings/images",
                                $fileName,
                                $tenantId
                            );
                            
                            $newImages[] = $imagePath;
                        }
                    }
                    
                    // Yeni değeri JSON olarak ata
                    if (!empty($newImages)) {
                        $value = json_encode($newImages);
                        $this->values[$settingId] = $value;
                        $this->multipleImagesArrays[$settingId] = $newImages;
                    } else {
                        // Tüm resimler silindi
                        $value = null;
                        $this->values[$settingId] = null;
                        $this->multipleImagesArrays[$settingId] = [];
                    }
                } catch (\Exception $e) {
                    $this->dispatch('toast', [
                        'title' => 'Hata!',
                        'message' => 'Çoklu resim yüklenirken bir hata oluştu: ' . $e->getMessage(),
                        'type' => 'error',
                    ]);
                    continue;
                }
            }
            
            if ($value === $setting->default_value) {
                // Eğer dosya varsa sil
                if ($oldValue && ($setting->type === 'file' || $setting->type === 'image')) {
                    \Modules\SettingManagement\App\Helpers\TenantStorageHelper::deleteFile($oldValue);
                }
                
                SettingValue::where('setting_id', $settingId)->delete();
                
                if ($oldValue !== $value) {
                    log_activity(
                        $setting,
                        'varsayılan değere döndürüldü',
                        ['old' => $oldValue, 'new' => $value]
                    );
                }
            } 
            else if ($oldValue !== $value) {
                $settingValue = SettingValue::updateOrCreate(
                    ['setting_id' => $settingId],
                    ['value' => $value]
                );
                
                log_activity(
                    $setting,
                    'değeri güncellendi',
                    ['old' => $oldValue, 'new' => $value]
                );
            }
        }
    
        $this->originalValues = $this->values;
        $this->changes = [];
        $this->temporaryImages = [];
        $this->temporaryMultipleImages = [];
    
        if ($redirect) {
            return redirect()->route('admin.settingmanagement.tenant.settings');
        }
    
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Değişiklikler kaydedildi.',
            'type' => 'success'
        ]);
    }
    
    public function removeImage($settingId)
    {
        if (isset($this->temporaryImages[$settingId])) {
            unset($this->temporaryImages[$settingId]);
            $this->checkChanges();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Dosya kaldırıldı.',
                'type' => 'success'
            ]);
        }
    }
    
    public function deleteMedia($settingId)
    {
        $setting = Setting::find($settingId);
        $value = $this->values[$settingId] ?? null;
        
        if ($setting && $value) {
            // Dosya yolundan local path'i çıkart
            $localPath = $this->extractLocalPath($value);
            
            if (Storage::disk('public')->exists($localPath)) {
                Storage::disk('public')->delete($localPath);
                
                $this->values[$settingId] = null;
                $this->checkChanges();
                
                $this->dispatch('toast', [
                    'title' => 'Başarılı!',
                    'message' => 'Dosya silindi.',
                    'type' => 'success'
                ]);
            }
        }
    }
    
    public function render()
    {
        $settings = Setting::where('group_id', $this->groupId)
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('settingmanagement::livewire.values-component', [
            'settings' => $settings
        ]);
    }
}