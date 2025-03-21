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
            
            // MediaLibrary ile dosya/resim yüklemelerini işle
            if (isset($this->temporaryImages[$settingId])) {
                $file = $this->temporaryImages[$settingId];
                $type = $setting->type;
                $collectionName = $type === 'image' ? 'images' : 'files';
                
                // Önceki medyayı temizle
                $setting->clearMediaCollection($collectionName);
                
                // Yeni medyayı ekle
                $fileName = Str::slug($setting->key) . '-' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                $media = $setting->addMedia($file->getRealPath())
                    ->usingFileName($fileName)
                    ->withCustomProperties([
                        'setting_id' => $settingId,
                        'type' => $type
                    ])
                    ->toMediaCollection($collectionName);
                
                // MediaLibrary'den alınan URL'yi value olarak kaydet
                $mediaUrl = $media->getUrl();
                $value = $mediaUrl;
                $this->values[$settingId] = $mediaUrl;
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
        
        if ($setting) {
            $type = $setting->type;
            $collectionName = $type === 'image' ? 'images' : 'files';
            
            $setting->clearMediaCollection($collectionName);
            
            // SettingValue'da medya URL'sini temizle
            SettingValue::updateOrCreate(
                ['setting_id' => $settingId],
                ['value' => null]
            );
            
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