<?php

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

    public function mount($group)
    {
        $this->groupId = $group;
        $this->group = SettingGroup::findOrFail($group);
        
        $settings = Setting::where('group_id', $this->groupId)->get();
        
        foreach ($settings as $setting) {
            $value = SettingValue::where('setting_id', $setting->id)->first();
                
            $this->values[$setting->id] = $value ? $value->value : $setting->default_value;
            $this->originalValues[$setting->id] = $this->values[$setting->id];
        }
    }

    public function resetToDefault($settingId)
    {
        $setting = Setting::find($settingId);
        $this->values[$settingId] = $setting->default_value;
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

    public function checkChanges()
    {
        $this->changes = [];
        foreach ($this->values as $id => $value) {
            if ($value == 'temp' || $value != $this->originalValues[$id]) {
                $this->changes[$id] = $value;
            }
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
                    $tenancy = app(\Stancl\Tenancy\Tenancy::class);
                    $tenantId = $tenancy->initialized ? $tenancy->tenant->id : 1;
                    
                    // Dosya adını oluştur
                    $fileName = Str::slug($setting->key) . '-' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                    $folder = $type === 'image' ? 'images' : 'files';
                    
                    // Dosya yolu
                    $path = "settings/{$folder}/{$fileName}";
                    
                    // Tenant için dosya yolu
                    $tenantPath = "tenant{$tenantId}/settings/{$folder}/{$fileName}";
                    
                    // Eski dosyayı sil (eğer varsa)
                    if ($oldValue) {
                        // Dosya yolundan local path'i çıkart
                        $localPath = $this->extractLocalPath($oldValue);
                        
                        if (Storage::disk('public')->exists($localPath)) {
                            Storage::disk('public')->delete($localPath);
                        }
                    }
                    
                    // Dosyayı doğru klasöre kaydet
                    if ($tenantId == 1) {
                        // Central için normal storage/app/public/ altına kaydet
                        Storage::disk('public')->putFileAs(
                            dirname($path),
                            $file,
                            basename($path)
                        );
                    } else {
                        // Tenant için storage/tenant{id}/app/public/ altına kaydet
                        $tenantStorage = storage_path("tenant{$tenantId}/app/public/" . dirname($path));
                        if (!file_exists($tenantStorage)) {
                            mkdir($tenantStorage, 0755, true);
                        }
                        
                        // Tenant klasörüne yükle
                        $file->storeAs(
                            dirname($path),
                            basename($path),
                            ['disk' => 'tenant']
                        );
                    }
                    
                    // Dosya yolunu değer olarak ata - tenant ID'li formatı kullan
                    $value = $tenantPath;
                    $this->values[$settingId] = $tenantPath;
                } catch (\Exception $e) {
                    $this->dispatch('toast', [
                        'title' => 'Hata!',
                        'message' => 'Dosya yüklenirken bir hata oluştu: ' . $e->getMessage(),
                        'type' => 'error',
                    ]);
                    continue;
                }
            }
            
            if ($value === $setting->default_value) {
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
                SettingValue::updateOrCreate(
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
    
        if ($redirect) {
            return redirect()->route('admin.settingmanagement.tenant.settings');
        }
    
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Değişiklikler kaydedildi.',
            'type' => 'success'
        ]);
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