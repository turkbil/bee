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
                    // Tenant ID belirleme
                    $tenantId = is_tenant() ? tenant_id() : 'central';
                    
                    // Dosya adını oluştur
                    $fileName = Str::slug($setting->key) . '-' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                    $folder = $type === 'image' ? 'images' : 'files';
                    
                    // Dosya yolu - tenant id sadece bir kez geçecek
                    $path = "settings/{$tenantId}/{$folder}/{$fileName}";
                    
                    // Eski dosyayı sil (eğer varsa)
                    if ($oldValue && Storage::disk('public')->exists($oldValue)) {
                        Storage::disk('public')->delete($oldValue);
                    }
                    
                    // Dosyayı storage/app/public/ altına kaydet
                    Storage::disk('public')->putFileAs(
                        dirname($path),
                        $file,
                        basename($path)
                    );
                    
                    // Dosya yolunu değer olarak ata
                    $value = $path;
                    $this->values[$settingId] = $path;
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
        
        if ($setting && $value && Storage::disk('public')->exists($value)) {
            Storage::disk('public')->delete($value);
            
            $this->values[$settingId] = null;
            $this->checkChanges();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Dosya silindi.',
                'type' => 'success'
            ]);
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