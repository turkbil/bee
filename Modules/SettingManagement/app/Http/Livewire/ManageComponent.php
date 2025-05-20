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
use Illuminate\Support\Facades\Log;

#[Layout('admin.layout')]
class ManageComponent extends Component
{
    use WithFileUploads, WithImageUpload;

    public $settingId;
    public $redirect = false;
    public $temporaryImages = [];
    public $temporaryMultipleImages = [];
    public $imagePreview = null;
    public $optionFormat = 'key-value'; // 'key-value' veya 'text'
    
    // Varsayılan değer için izleme
    public $selectedDefaultValue = null;
    public $inputs = [
        'group_id' => '',
        'label' => '',
        'key' => '',  // Arayüzden kaldırıldı, arka planda otomatik oluşturulacak
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
        'tel' => 'Telefon',
        'url' => 'URL',
        'time' => 'Saat',
    ];

    protected function rules()
    {
        return [
            'inputs.group_id' => 'required|exists:settings_groups,id',
            'inputs.label' => 'required|min:3|max:255',
            // inputs.key alanı UI'dan kaldırıldı, arka planda otomatik oluşturulacak
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
                $this->inputs['options_array'] = [];
                foreach ($this->inputs['options'] as $key => $value) {
                    $this->inputs['options_array'][Str::random(6)] = [
                        'key' => $key,
                        'value' => $value
                    ];
                }
                
                // Metin formatı için kullanılacak string formatında options
                $optionsText = [];
                foreach ($this->inputs['options'] as $key => $value) {
                    $optionsText[] = $key . '=' . $value;
                }
                $this->inputs['options'] = implode("\n", $optionsText);
            }

            // Mevcut resim için önizleme ayarla
            if ($this->inputs['type'] === 'image' && !empty($this->inputs['default_value'])) {
                $this->imagePreview = cdn($this->inputs['default_value']);
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

    // İnput değişimi izleme - Başlık değiştiğinde otomatik olarak anahtar (slug) oluşturma
    public function updatedInputsLabel()
    {
        // Bu metod artık anahtar oluşturmayacak. Anahtar save() içinde oluşturulacak.
        // İsteğe bağlı olarak loglama bırakılabilir.
        Log::info('updatedInputsLabel triggered (Key generation moved to save method).');
    }
    
    // Format değişikliğinde verilerin sağlıklı aktarımı
    public function updatedOptionFormat($value)
    {
        if ($value === 'text') {
            // options_array'den options'a dönüştür - string olarak ayarla
            if (empty($this->inputs['options_array'])) {
                $this->inputs['options'] = '';
                return;
            }
            
            $options = [];
            foreach ($this->inputs['options_array'] as $option) {
                if (isset($option['key']) && isset($option['value'])) {
                    $options[] = $option['key'] . '=' . $option['value'];
                }
            }
            
            // String formatında ayarla
            $this->inputs['options'] = implode("\n", $options);
        } 
        elseif ($value === 'key-value') {        
            // Eğer options bir string değilse veya boşsa, boş bir string olarak ayarla
            if (empty($this->inputs['options']) || !is_string($this->inputs['options'])) {
                $this->inputs['options'] = '';
                $this->inputs['options_array'] = []; // options boşsa, array'i de temizle
                return; 
            }
            
            // options'dan options_array'e dönüştür
            $parsedOptionsArray = []; // Geçici dizi
            $lines = explode("\n", $this->inputs['options']);
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $id = Str::random(6); // Yeni ID oluştur
                    
                    // Anahtar=Değer formatında mı kontrol et
                    if (strpos($line, '=') !== false) {
                        list($key, $value) = explode('=', $line, 2);
                        $parsedOptionsArray[$id] = [
                            'key' => trim($key),
                            'value' => trim($value)
                        ];
                    } else {
                        // Sadece değer varsa, anahtar olarak slugını al
                        $parsedOptionsArray[$id] = [
                            'key' => Str::slug($line, '_'),
                            'value' => $line
                        ];
                    }
                }
            }
            
            // Parse edilen array ile güncelle
            $this->inputs['options_array'] = $parsedOptionsArray;
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
        $id = Str::random(6);
        $this->inputs['options_array'][$id] = [
            'key' => '',
            'value' => ''
        ];
    }
    
    // Select option'ı sil
    public function removeSelectOption($key)
    {
        if (isset($this->inputs['options_array'][$key])) {
            unset($this->inputs['options_array'][$key]);
        }
    }
    
    // Seçenek değerini otomatik slug yapma
    public function slugifyOptionKey($id, $value)
    {
        if (isset($this->inputs['options_array'][$id])) {
            $this->inputs['options_array'][$id]['key'] = Str::slug($value, '_');
        }
    }
    
    // Varsayılan değer seçimini güncelle
    public function updateDefaultValue($value)
    {
        $this->inputs['default_value'] = $value;
        $this->selectedDefaultValue = $value;
    }

    // Geçici resim yüklendiğinde önizleme oluştur
    public function updatedTemporaryImages($value, $key)
    {
        if ($key === 'image' && $this->temporaryImages[$key]) {
            $this->imagePreview = $this->temporaryImages[$key]->temporaryUrl();
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
        // --- Anahtar (key) Oluşturma --- START ---
        // save() içinde, validate() öncesinde anahtarı oluşturmak daha güvenilir.
        $labelSlug = Str::slug($this->inputs['label'] ?? '', '_');
        $groupSlug = '';
        if (!empty($this->inputs['group_id'])) {
            $group = SettingGroup::find($this->inputs['group_id']);
            if ($group && !empty($group->slug)) {
                $groupSlug = $group->slug;
            } elseif ($group && !empty($group->name)) {
                $groupSlug = Str::slug($group->name, '_');
            }
        }
        $finalKey = $labelSlug;
        if (!empty($groupSlug)) {
            $finalKey = $groupSlug . '_' . $labelSlug;
        }
        // inputs.key alanını güncelle
        $this->inputs['key'] = $finalKey;
        Log::info('[Save Method] Generated Key: ' . $finalKey . ' for Label: ' . ($this->inputs['label'] ?? '') . ' and Group ID: ' . ($this->inputs['group_id'] ?? 'null'));
        // --- Anahtar (key) Oluşturma --- END ---
        
        // Eğer select tipiyse, options_array'i options'a dönüştür
        if ($this->inputs['type'] === 'select') {
            $options = [];
            
            if (!empty($this->inputs['options_array']) && is_array($this->inputs['options_array'])) {
                foreach ($this->inputs['options_array'] as $id => $option) {
                    // Yeni format (key-value nesnesi)
                    if (isset($option['key']) && !empty($option['key']) && isset($option['value'])) {
                        $options[$option['key']] = $option['value'];
                    } 
                    // Eski format (string değer)
                    else if (!is_array($option) && !empty($option)) {
                        $options[Str::slug($option)] = $option;
                    }
                }
                
                $this->inputs['options'] = $options;
            }
            // Eğer string olarak geldiyse parse edelim
            else if (is_string($this->inputs['options'])) {
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
            
            // Eğer options hala boşsa, hatanın önüne geçmek için boş array koyalım
            if (empty($this->inputs['options'])) {
                $this->inputs['options'] = [];
            }
        }

        $this->validate();
        
        // Eğer mevcut bir ayarı düzenliyorsak ve sistem ayarı ise bazı alanları değiştirmeye izin verme
        if ($this->settingId) {
            $existingSetting = Setting::find($this->settingId);
            if ($existingSetting && $existingSetting->is_system) {
                // Sistem ayarlarının değiştirilmemesi gereken özellikleri
                $this->inputs['key'] = $existingSetting->key;
                $this->inputs['type'] = $existingSetting->type;
                $this->inputs['is_system'] = true; // Sistem ayarı özelliğini koruyalım
                
                // !!! Sistem ayarının select options'larının değiştirilmesini ENGELLEMEK doğru olmayabilir. 
                // !!! Kullanıcı belki sadece varsayılan değeri değiştirmek istiyordur.
                // !!! Şimdilik bu kısmı yorum satırı yapıyorum, gerekirse tekrar aktif edilebilir.
                // if ($existingSetting->type === 'select') {
                //     $this->inputs['options'] = $existingSetting->options;
                //     $this->inputs['options_array'] = $existingSetting->options;
                // }
            }
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
            
            // Güvenli bir şekilde değişiklikleri bul
            $changes = [];
            $newData = $setting->toArray();
            
            // Her iki dizide de bulunan anahtarları karşılaştır ve değişenleri belirle
            foreach ($newData as $key => $newValue) {
                // Sadece eski veri ile karşılaştırılabilecek anahtarları işle
                if (isset($oldData[$key])) {
                    $oldValue = $oldData[$key];
                    
                    // Değişiklik olup olmadığını kontrol et
                    // Karmaşık yapılar (array veya object) için json_encode karşılaştırması yap
                    if (is_array($newValue) || is_object($newValue) || is_array($oldValue) || is_object($oldValue)) {
                        $newValueJson = json_encode($newValue, JSON_UNESCAPED_UNICODE);
                        $oldValueJson = json_encode($oldValue, JSON_UNESCAPED_UNICODE);
                        
                        if ($newValueJson !== $oldValueJson) {
                            // Değişiklik var, insan okunabilir formatta kaydet
                            $changes[$key] = "Önceki: ".$oldValueJson." => Yeni: ".$newValueJson;
                        }
                    } elseif ($newValue !== $oldValue) {
                        // Basit değerler için direkt karşılaştırma
                        $changes[$key] = $newValue;
                    }
                }
            }
            
            log_activity(
                $setting,
                'güncellendi',
                $changes
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
            $this->reset('inputs', 'temporaryImages', 'temporaryMultipleImages', 'imagePreview');
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