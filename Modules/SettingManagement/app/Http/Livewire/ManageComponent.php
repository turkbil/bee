<?php

namespace Modules\SettingManagement\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Modules\SettingManagement\App\Http\Livewire\Traits\WithImageUpload;
use Modules\SettingManagement\App\Models\Setting;
use Modules\SettingManagement\App\Models\SettingGroup;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

#[Layout('admin.layout')]
class ManageComponent extends Component
{
    use WithFileUploads, WithImageUpload;

    public $settingId;
    public $redirect = false;
    public $temporaryImages = [];
    public $temporaryMultipleImages = [];
    public $inputs = [
        'group_id' => '',
        'label' => '',
        'key' => '',
        'type' => 'text',
        'options' => null,
        'default_value' => null,
        'sort_order' => 0,
        'is_active' => true,
        'is_system' => false,
        'options_array' => [],
    ];
    

    // Kullanılabilir ayar tipleri
    public $availableTypes = [
        'text' => 'Metin',
        'textarea' => 'Uzun Metin',
        'number' => 'Sayı',
        'select' => 'Seçim Kutusu',
        'checkbox' => 'Onay Kutusu',
        'file' => 'Dosya',
        'image' => 'Resim',
        'image_multiple' => 'Çoklu Resim',
        'color' => 'Renk',
        'date' => 'Tarih',
        'email' => 'E-posta',
        'password' => 'Şifre',
        'tel' => 'Telefon',
        'url' => 'URL',
        'time' => 'Saat',
    ];

    protected function rules()
    {
        return [
            'inputs.group_id' => 'required|exists:settings_groups,id',
            'inputs.label' => 'required|min:3|max:255',
            'inputs.key' => 'required|regex:/^[a-zA-Z0-9_]+$/|max:255|unique:settings,key,' . $this->settingId,
            'inputs.type' => 'required|in:' . implode(',', array_keys($this->availableTypes)),
            'inputs.options' => 'nullable|required_if:inputs.type,select',
            'inputs.default_value' => 'nullable',
            'inputs.is_active' => 'boolean',
            'temporaryImages.*' => 'nullable|file|max:2048',
            'temporaryMultipleImages.*' => 'nullable|image|max:2048',
        ];
    }

    protected $messages = [
        'inputs.group_id.required' => 'Grup seçimi zorunludur',
        'inputs.label.required' => 'Başlık alanı zorunludur',
        'inputs.key.required' => 'Anahtar alanı zorunludur',
        'inputs.key.regex' => 'Anahtar sadece harf, rakam ve alt çizgi içerebilir',
        'inputs.type.required' => 'Tip seçimi zorunludur',
        'inputs.options.required_if' => 'Seçim kutusu için seçenekler belirtmelisiniz',
        'temporaryImages.*.max' => 'Dosya boyutu en fazla 2MB olabilir',
        'temporaryMultipleImages.*.max' => 'Resim boyutu en fazla 2MB olabilir',
    ];

    public function mount($id = null)
    {
        $this->settingId = $id;
        
        if ($id) {
            $setting = Setting::findOrFail($id);
            $this->inputs = $setting->only([
                'group_id', 'label', 'key', 'type', 'options', 
                'default_value', 'sort_order', 'is_active', 'is_system'
            ]);
            
            // Options alanını options_array'e dönüştür
            if (!empty($this->inputs['options']) && is_array($this->inputs['options'])) {
                $this->inputs['options_array'] = $this->inputs['options'];
            }
        } else {
            // Yeni kayıt için sort_order değerini en sona al
            $this->inputs['sort_order'] = Setting::max('sort_order') + 1;
            $this->inputs['is_system'] = false; // Varsayılan olarak sistem ayarı değil
            
            // URL'den group_id parametresi varsa inputs'a ekle
            if (request()->has('group_id')) {
                $this->inputs['group_id'] = request()->get('group_id');
            }
        }
    }

    // İnput değişimi izleme
    public function updatedInputsLabel()
    {
        if (empty($this->inputs['key']) && !empty($this->inputs['label'])) {
            $selectedGroup = null;
            if (!empty($this->inputs['group_id'])) {
                $selectedGroup = SettingGroup::find($this->inputs['group_id']);
            }
            
            $prefix = '';
            if ($selectedGroup) {
                $prefix = Str::slug($selectedGroup->name, '_');
            }
            
            $this->inputs['key'] = $prefix . '_' . Str::slug($this->inputs['label'], '_');
        }
    }

    public function updatedInputsGroupId()
    {
        if (!empty($this->inputs['label']) && !empty($this->inputs['group_id'])) {
            $selectedGroup = SettingGroup::find($this->inputs['group_id']);
            if ($selectedGroup) {
                $prefix = Str::slug($selectedGroup->name, '_');
                $key = Str::slug($this->inputs['label'], '_');
                $this->inputs['key'] = $prefix . '_' . $key;
            }
        }
    }
    
    // Select için option ekle
    public function addSelectOption()
    {
        if (!isset($this->inputs['options_array'])) {
            $this->inputs['options_array'] = [];
        }
        
        $this->inputs['options_array'][Str::random(6)] = '';
    }
    
    // Select option'ı sil
    public function removeSelectOption($key)
    {
        if (isset($this->inputs['options_array'][$key])) {
            unset($this->inputs['options_array'][$key]);
        }
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

    public function save($redirect = false, $resetForm = false)
    {
        $this->validate();
        
        // Eğer mevcut bir ayarı düzenliyorsak ve sistem ayarı ise bazı alanları değiştirmeye izin verme
        if ($this->settingId) {
            $existingSetting = Setting::find($this->settingId);
            if ($existingSetting && $existingSetting->is_system) {
                // Sistem ayarlarının değiştirilmemesi gereken özellikleri
                $this->inputs['key'] = $existingSetting->key;
                $this->inputs['type'] = $existingSetting->type;
                $this->inputs['is_system'] = true; // Sistem ayarı özelliğini koruyalım
                
                // Eğer select tipiyse options değerlerini değiştirmeyelim
                if ($existingSetting->type === 'select') {
                    $this->inputs['options'] = $existingSetting->options;
                    $this->inputs['options_array'] = $existingSetting->options;
                }
            }
        }
        
        // Eğer select tipiyse, options_array'i options'a dönüştür
        if ($this->inputs['type'] === 'select' && !empty($this->inputs['options_array'])) {
            $options = [];
            foreach ($this->inputs['options_array'] as $key => $value) {
                if (!empty($value)) {
                    $options[$key] = $value;
                }
            }
            $this->inputs['options'] = $options;
        } 
        // Eğer hala string olarak geldiyse parse edelim
        elseif ($this->inputs['type'] === 'select' && is_string($this->inputs['options'])) {
            $options = [];
            $lines = explode("\n", $this->inputs['options']);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $options[trim($key)] = trim($value);
                } else {
                    $options[Str::slug($line)] = $line;
                }
            }
            $this->inputs['options'] = $options;
        }
        
        // Çoklu resim için işleme
        if ($this->inputs['type'] === 'image_multiple' && !empty($this->temporaryMultipleImages)) {
            // Eğer default_value varsa, JSON formatına çevir
            $multipleImagePaths = [];
            
            foreach ($this->temporaryMultipleImages as $index => $image) {
                if ($image) {
                    $tenantId = is_tenant() ? tenant_id() : 1;
                    $fileName = time() . '_' . Str::slug($this->inputs['key']) . '_' . $index . '.' . $image->getClientOriginalExtension();
                    $folder = 'images';
                    
                    // YENİ: TenantStorageHelper ile doğru şekilde dosyayı yükle
                    $imagePath = \Modules\SettingManagement\App\Helpers\TenantStorageHelper::storeTenantFile(
                        $image,
                        "settings/{$folder}",
                        $fileName,
                        $tenantId
                    );
                    
                    $multipleImagePaths[] = $imagePath;
                }
            }
            
            if (!empty($multipleImagePaths)) {
                $this->inputs['default_value'] = json_encode($multipleImagePaths);
            }
        }
    
        if ($this->settingId) {
            $setting = Setting::findOrFail($this->settingId);
            $oldData = $setting->toArray();
            $setting->update($this->inputs);
            
            // Dosya/resim yüklemesi varsa
            if (!empty($this->temporaryImages)) {
                foreach ($this->temporaryImages as $key => $image) {
                    if ($image) {
                        $this->uploadImage($key, $setting);
                    }
                }
            }
            
            log_activity(
                $setting,
                'güncellendi',
                array_diff_assoc($setting->toArray(), $oldData)
            );
            
            $message = 'Ayar güncellendi';
        } else {
            $setting = Setting::create($this->inputs);
            
            // Dosya/resim yüklemesi varsa
            if (!empty($this->temporaryImages)) {
                foreach ($this->temporaryImages as $key => $image) {
                    if ($image) {
                        $this->uploadImage($key, $setting);
                    }
                }
            }
            
            log_activity(
                $setting,
                'oluşturuldu'
            );
            
            $message = 'Ayar oluşturuldu';
        }
    
        if ($redirect) {
            return redirect()->route('admin.settingmanagement.items', $setting->group_id);
        }
    
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => $message,
            'type' => 'success'
        ]);
        
        if ($resetForm && !$this->settingId) {
            $this->reset('inputs', 'temporaryImages', 'temporaryMultipleImages');
            $this->inputs['sort_order'] = Setting::max('sort_order') + 1;
            $this->inputs['is_active'] = true;
        }
    }

    public function render()
    {
        // Grupları hiyerarşik şekilde getir
        $groups = SettingGroup::all();
        $parentGroups = $groups->where('parent_id', null);
        
        return view('settingmanagement::livewire.manage-component', [
            'groups' => $groups,
            'parentGroups' => $parentGroups,
            'model' => $this->settingId ? Setting::find($this->settingId) : null
        ]);
    }
}