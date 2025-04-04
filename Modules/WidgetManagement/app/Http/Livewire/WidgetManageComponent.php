<?php
namespace Modules\WidgetManagement\app\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Http\Livewire\Traits\WithImageUpload;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

#[Layout('admin.layout')]
class WidgetManageComponent extends Component
{
    use WithFileUploads, WithImageUpload;
    
    public $widgetId;
    public $formMode = 'base'; // base, items, settings, design, preview
    public $thumbnail;
    public $temporaryImages = [];
    public $temporaryMultipleImages = [];
    public $imagePreview = null;
    public $isSubmitting = false;
    
    public $widget = [
        'name' => '',
        'slug' => '',
        'description' => '',
        'type' => 'static',
        'module_ids' => [],
        'content_html' => '',
        'content_css' => '',
        'content_js' => '',
        'css_files' => [],
        'js_files' => [],
        'has_items' => false,
        'item_schema' => [],
        'settings_schema' => [],
        'is_active' => true,
        'is_core' => false
    ];
    
    // Yeni şema alanı için form
    public $newField = [
        'name' => '',
        'label' => '',
        'type' => 'text',
        'required' => false,
        'options' => [],
        'options_array' => []
    ];
    
    public $newOption = [
        'key' => '',
        'value' => ''
    ];
    
    public $optionFormat = 'key-value'; // 'key-value' veya 'text'
    
    // Kullanılabilir alan tipleri
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
    
    protected $rules = [
        'widget.name' => 'required|min:3|max:255',
        'widget.slug' => 'required|regex:/^[a-z0-9\-_]+$/i|max:255',
        'widget.description' => 'nullable|max:1000',
        'widget.type' => 'required|in:static,dynamic,module,content',
        'widget.module_ids' => 'nullable|array',
        'widget.content_html' => 'nullable',
        'widget.content_css' => 'nullable',
        'widget.content_js' => 'nullable',
        'widget.has_items' => 'boolean',
        'widget.item_schema' => 'nullable|array',
        'widget.settings_schema' => 'nullable|array',
        'widget.is_active' => 'boolean',
        'widget.is_core' => 'boolean',
        'thumbnail' => 'nullable|image|max:1024',
        'temporaryImages.*' => 'nullable|image|max:2048',
        'temporaryMultipleImages.*' => 'nullable|image|max:2048',
    ];
    
    // Lint hatalarını düzeltmek için özel değişkenler
    protected $casts = [
        'widget.item_schema' => 'array',
        'widget.settings_schema' => 'array',
    ];
    
    public function mount($id = null)
    {
        $this->widgetId = $id;
        
        if ($id) {
            $widget = Widget::findOrFail($id);
            
            $this->widget = [
                'name' => $widget->name,
                'slug' => $widget->slug,
                'description' => $widget->description,
                'type' => $widget->type,
                'module_ids' => $widget->module_ids ?? [],
                'content_html' => $widget->content_html,
                'content_css' => $widget->content_css,
                'content_js' => $widget->content_js,
                'css_files' => $widget->css_files ?? [],
                'js_files' => $widget->js_files ?? [],
                'has_items' => $widget->has_items,
                'item_schema' => $widget->item_schema ?? [],
                'settings_schema' => $widget->settings_schema ?? [],
                'is_active' => $widget->is_active,
                'is_core' => $widget->is_core
            ];
            
            // 'title' ve 'is_active' her zaman item_schema'da olmalı
            if ($widget->has_items) {
                $hasTitle = false;
                $hasActive = false;
                
                if (is_array($this->widget['item_schema'])) {
                    foreach ($this->widget['item_schema'] as $field) {
                        if ($field['name'] === 'title') $hasTitle = true;
                        if ($field['name'] === 'is_active') $hasActive = true;
                    }
                }
                
                if (!$hasTitle) {
                    $this->widget['item_schema'] = array_merge([[
                        'name' => 'title',
                        'label' => 'Başlık',
                        'type' => 'text',
                        'required' => true,
                        'system' => true
                    ]], $this->widget['item_schema'] ?? []);
                }
                
                if (!$hasActive) {
                    $this->widget['item_schema'][] = [
                        'name' => 'is_active',
                        'label' => 'Aktif',
                        'type' => 'checkbox',
                        'required' => false,
                        'system' => true
                    ];
                }
            }
            
            // Mevcut resim için önizleme ayarla
            if ($widget->thumbnail) {
                $this->imagePreview = $widget->getThumbnailUrl();
            }
        }
    }

    public function addCssFile()
    {
        $cssFiles = $this->widget['css_files'] ?? [];
        $cssFiles[] = '';
        $this->widget['css_files'] = $cssFiles;
    }

    public function updateCssFile($index, $value)
    {
        $cssFiles = $this->widget['css_files'];
        $cssFiles[$index] = $value;
        $this->widget['css_files'] = $cssFiles;
    }

    public function removeCssFile($index)
    {
        $cssFiles = $this->widget['css_files'];
        unset($cssFiles[$index]);
        $this->widget['css_files'] = array_values($cssFiles);
    }

    public function addJsFile()
    {
        $jsFiles = $this->widget['js_files'] ?? [];
        $jsFiles[] = '';
        $this->widget['js_files'] = $jsFiles;
    }

    public function updateJsFile($index, $value)
    {
        $jsFiles = $this->widget['js_files'];
        $jsFiles[$index] = $value;
        $this->widget['js_files'] = $jsFiles;
    }

    public function removeJsFile($index)
    {
        $jsFiles = $this->widget['js_files'];
        unset($jsFiles[$index]);
        $this->widget['js_files'] = array_values($jsFiles);
    }
  
    public function updatedWidgetName()
    {
        if (empty($this->widget['slug'])) {
            $this->widget['slug'] = Str::slug($this->widget['name']);
        }
    }
    
    public function setFormMode($mode)
    {
        $this->formMode = $mode;
    }
    
    // Format değişikliğinde verilerin sağlıklı aktarımı
    public function updatedOptionFormat($value)
    {
        if ($value === 'text') {
            // options_array'den options'a dönüştür - string olarak ayarla
            if (empty($this->newField['options_array'])) {
                $this->newField['options'] = '';
                return;
            }
            
            $options = [];
            foreach ($this->newField['options_array'] as $option) {
                if (isset($option['key']) && isset($option['value'])) {
                    $options[] = $option['key'] . '=' . $option['value'];
                }
            }
            
            // String formatında ayarla
            $this->newField['options'] = implode("\n", $options);
        } 
        elseif ($value === 'key-value') {        
            // Eğer options bir string değilse veya boşsa, boş bir string olarak ayarla
            if (empty($this->newField['options']) || !is_string($this->newField['options'])) {
                $this->newField['options'] = '';
                $this->newField['options_array'] = []; // options boşsa, array'i de temizle
                return; 
            }
            
            // options'dan options_array'e dönüştür
            $parsedOptionsArray = []; // Geçici dizi
            $lines = explode("\n", $this->newField['options']);
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
            $this->newField['options_array'] = $parsedOptionsArray;
        }
    }
    
    public function addItemSchemaField()
    {
        $this->validate([
            'newField.name' => 'required|regex:/^[a-zA-Z0-9_]+$/i',
            'newField.label' => 'required',
            'newField.type' => 'required'
        ]);
        
        // Sistem alanı ise düzenlenemez
        $systemSpecialFields = ['title', 'is_active', 'unique_id'];
        if (in_array($this->newField['name'], $systemSpecialFields)) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Bu alan ismi sistem tarafından ayrılmıştır ve kullanılamaz.',
                'type' => 'error'
            ]);
            return;
        }
        
        $field = [
            'name' => $this->newField['name'],
            'label' => $this->newField['label'],
            'type' => $this->newField['type'],
            'required' => $this->newField['required'] ?? false
        ];
        
        if ($this->newField['type'] === 'select') {
            // Seçim kutusu için options
            if ($this->optionFormat === 'key-value') {
                $options = [];
                if (!empty($this->newField['options_array'])) {
                    foreach ($this->newField['options_array'] as $option) {
                        if (isset($option['key']) && !empty($option['key']) && isset($option['value'])) {
                            $options[$option['key']] = $option['value'];
                        }
                    }
                }
                $field['options'] = $options;
            } else {
                // Text formatından dönüştür
                $options = [];
                $lines = explode("\n", $this->newField['options']);
                
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
                
                $field['options'] = $options;
            }
        }
        
        $itemSchema = $this->widget['item_schema'] ?? [];
        $itemSchema[] = $field;
        
        $this->widget['item_schema'] = $itemSchema;
        
        // Temizle
        $this->newField = [
            'name' => '',
            'label' => '',
            'type' => 'text',
            'required' => false,
            'options' => '',
            'options_array' => []
        ];
    }
    
    public function removeItemSchemaField($index)
    {
        $itemSchema = $this->widget['item_schema'] ?? [];
        
        // Sistem alanları silinemez
        if (isset($itemSchema[$index]) && isset($itemSchema[$index]['system']) && $itemSchema[$index]['system']) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Sistem alanları silinemez.',
                'type' => 'error'
            ]);
            return;
        }
        
        if (isset($itemSchema[$index])) {
            unset($itemSchema[$index]);
            $this->widget['item_schema'] = array_values($itemSchema);
        }
    }
    
    public function addSettingsSchemaField()
    {
        $this->validate([
            'newField.name' => 'required|regex:/^[a-zA-Z0-9_]+$/i',
            'newField.label' => 'required',
            'newField.type' => 'required'
        ]);
        
        // Sistem alanı ise düzenlenemez
        $systemSpecialFields = ['title', 'unique_id'];
        if (in_array($this->newField['name'], $systemSpecialFields)) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Bu alan ismi sistem tarafından ayrılmıştır ve kullanılamaz.',
                'type' => 'error'
            ]);
            return;
        }
        
        $field = [
            'name' => $this->newField['name'],
            'label' => $this->newField['label'],
            'type' => $this->newField['type'],
            'required' => $this->newField['required'] ?? false
        ];
        
        if ($this->newField['type'] === 'select') {
            // Seçim kutusu için options
            if ($this->optionFormat === 'key-value') {
                $options = [];
                if (!empty($this->newField['options_array'])) {
                    foreach ($this->newField['options_array'] as $option) {
                        if (isset($option['key']) && !empty($option['key']) && isset($option['value'])) {
                            $options[$option['key']] = $option['value'];
                        }
                    }
                }
                $field['options'] = $options;
            } else {
                // Text formatından dönüştür
                $options = [];
                $lines = explode("\n", $this->newField['options']);
                
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
                
                $field['options'] = $options;
            }
        }
        
        $settingsSchema = $this->widget['settings_schema'] ?? [];
        $settingsSchema[] = $field;
        
        $this->widget['settings_schema'] = $settingsSchema;
        
        // Temizle
        $this->newField = [
            'name' => '',
            'label' => '',
            'type' => 'text',
            'required' => false,
            'options' => '',
            'options_array' => []
        ];
    }
    
    public function removeSettingsSchemaField($index)
    {
        $settingsSchema = $this->widget['settings_schema'] ?? [];
        
        // Sistem alanları silinemez
        if (isset($settingsSchema[$index]) && isset($settingsSchema[$index]['system']) && $settingsSchema[$index]['system']) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Sistem alanları silinemez.',
                'type' => 'error'
            ]);
            return;
        }
        
        if (isset($settingsSchema[$index])) {
            unset($settingsSchema[$index]);
            $this->widget['settings_schema'] = array_values($settingsSchema);
        }
    }
    
    // Select için option ekle
    public function addFieldOption()
    {
        $id = Str::random(6);
        $this->newField['options_array'][$id] = [
            'key' => '',
            'value' => ''
        ];
    }
    
    // Select option'ı sil
    public function removeFieldOption($key)
    {
        if (isset($this->newField['options_array'][$key])) {
            unset($this->newField['options_array'][$key]);
        }
    }
    
    // Seçenek değerini otomatik slug yapma
    public function slugifyOptionKey($id, $value)
    {
        if (isset($this->newField['options_array'][$id])) {
            $this->newField['options_array'][$id]['key'] = Str::slug($value, '_');
        }
    }
    
    // Geçici resim yüklendiğinde önizleme oluştur
    public function updatedThumbnail()
    {
        $this->validateOnly('thumbnail', [
            'thumbnail' => 'image|max:1024'
        ]);
        
        if ($this->thumbnail) {
            $this->imagePreview = $this->thumbnail->temporaryUrl();
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
    
    // Çoklu resim için güncelleme metodu
    public function updatedTemporaryMultipleImages($value, $key)
    {
        // Doğrulama yap
        $this->validateOnly("temporaryMultipleImages.{$key}", [
            "temporaryMultipleImages.{$key}" => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        
        // Değişiklik olduğunu işaretle
        $this->isSubmitting = false;
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
    
    public function save()
    {
        $this->isSubmitting = true;
        
        $this->validate();
        
        try {
            // Her zaman title ve is_active alanlarını ekleyin (itemSchema için)
            if ($this->widget['has_items']) {
                $hasTitle = false;
                $hasActive = false;
                $hasUniqueId = false;
                
                if (is_array($this->widget['item_schema'])) {
                    foreach ($this->widget['item_schema'] as $field) {
                        if ($field['name'] === 'title') $hasTitle = true;
                        if ($field['name'] === 'is_active') $hasActive = true;
                        if ($field['name'] === 'unique_id') $hasUniqueId = true;
                    }
                } else {
                    $this->widget['item_schema'] = [];
                }
                
                if (!$hasTitle) {
                    $this->widget['item_schema'] = array_merge([[
                        'name' => 'title',
                        'label' => 'Başlık',
                        'type' => 'text',
                        'required' => true,
                        'system' => true
                    ]], $this->widget['item_schema']);
                }
                
                if (!$hasActive) {
                    $this->widget['item_schema'][] = [
                        'name' => 'is_active',
                        'label' => 'Aktif',
                        'type' => 'checkbox',
                        'required' => false,
                        'system' => true
                    ];
                }
                
                if (!$hasUniqueId) {
                    $this->widget['item_schema'][] = [
                        'name' => 'unique_id',
                        'label' => 'Benzersiz ID',
                        'type' => 'text',
                        'required' => false,
                        'system' => true
                    ];
                }
            }
            
            // Settings Schema'ya title ekle
            $hasTitle = false;
            $hasUniqueId = false;
            
            if (is_array($this->widget['settings_schema'])) {
                foreach ($this->widget['settings_schema'] as $field) {
                    if ($field['name'] === 'title') $hasTitle = true;
                    if ($field['name'] === 'unique_id') $hasUniqueId = true;
                }
            } else {
                $this->widget['settings_schema'] = [];
            }
            
            if (!$hasTitle) {
                $this->widget['settings_schema'] = array_merge([[
                    'name' => 'title',
                    'label' => 'Başlık',
                    'type' => 'text',
                    'required' => true,
                    'system' => true
                ]], $this->widget['settings_schema']);
            }
            
            if (!$hasUniqueId) {
                $this->widget['settings_schema'][] = [
                    'name' => 'unique_id',
                    'label' => 'Benzersiz ID',
                    'type' => 'text',
                    'required' => false,
                    'system' => true
                ];
            }
            
            if ($this->widgetId) {
                $widget = Widget::findOrFail($this->widgetId);
                $widget->update($this->widget);
            } else {
                $widget = Widget::create($this->widget);
                $this->widgetId = $widget->id;
            }
            
            // Thumbnail yükleme - SettingManagement ile uyumlu şekilde
            if ($this->thumbnail) {
                $tenantId = is_tenant() ? tenant_id() : 1;
                $filename = 'image-' . $widget->slug . '-' . Str::random(6) . '.' . $this->thumbnail->extension();
                
                // SettingManagement'taki TenantStorageHelper ile yükleme yap
                try {
                    $path = \Modules\SettingManagement\App\Helpers\TenantStorageHelper::storeTenantFile(
                        $this->thumbnail,
                        "widgets/images",
                        $filename,
                        $tenantId
                    );
                    
                    $widget->update([
                        'thumbnail' => $path
                    ]);
                } catch (\Exception $e) {
                    Log::error('Thumbnail yükleme hatası: ' . $e->getMessage());
                }
            }
            
            // Çoklu resim yükleme işlemi - SettingManagement ile uyumlu şekilde
            if (!empty($this->temporaryMultipleImages)) {
                try {
                    foreach ($this->temporaryMultipleImages as $index => $image) {
                        if ($image) {
                            // Tenant id belirleme
                            $tenantId = is_tenant() ? tenant_id() : 1;
                            
                            // Dosya adını oluştur
                            $fileName = 'image-' . $widget->slug . '-' . time() . '-' . Str::random(6) . '.' . $image->getClientOriginalExtension();
                            
                            // SettingManagement'taki TenantStorageHelper ile yükleme yap
                            $imagePath = \Modules\SettingManagement\App\Helpers\TenantStorageHelper::storeTenantFile(
                                $image,
                                "widgets/images",
                                $fileName,
                                $tenantId
                            );
                            
                            // Burada çoklu resim bilgilerini widget'a ekleyebilirsiniz
                            // Örneğin: $widget->images[] = $imagePath;
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Çoklu resim yükleme hatası: ' . $e->getMessage());
                }
            }
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Widget kaydedildi.',
                'type' => 'success'
            ]);
            
            return redirect()->route('admin.widgetmanagement.index');
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Widget kaydedilirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
        
        $this->isSubmitting = false;
    }
    
    public function render()
    {
        try {
            // Module.manager servisini güvenli bir şekilde almaya çalış
            $modules = collect();
            if (app()->bound('module.manager')) {
                $modules = app('module.manager')->all();
            }
        } catch (\Exception $e) {
            // Hata oluşursa boş bir koleksiyon kullan
            $modules = collect();
        }
        
        return view('widgetmanagement::livewire.widget-manage-component', [
            'modules' => $modules
        ]);
    }
}