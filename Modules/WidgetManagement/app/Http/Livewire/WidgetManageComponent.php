<?php

namespace Modules\WidgetManagement\app\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Modules\WidgetManagement\app\Models\Widget;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

#[Layout('admin.layout')]
class WidgetManageComponent extends Component
{
    use WithFileUploads;
    
    public $widgetId;
    public $formMode = 'base'; // base, items, settings, design, preview
    public $thumbnail;
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
        'options' => []
    ];
    
    public $newOption = [
        'key' => '',
        'value' => ''
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
        'thumbnail' => 'nullable|image|max:1024'
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
    
    public function addItemSchemaField()
    {
        $this->validate([
            'newField.name' => 'required|regex:/^[a-zA-Z0-9_]+$/i',
            'newField.label' => 'required',
            'newField.type' => 'required|in:text,textarea,number,select,checkbox,image,url'
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
        
        if ($this->newField['type'] === 'select' && !empty($this->newField['options'])) {
            $field['options'] = $this->newField['options'];
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
            'options' => []
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
            'newField.type' => 'required|in:text,textarea,number,select,checkbox,image,url,color'
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
        
        if ($this->newField['type'] === 'select' && !empty($this->newField['options'])) {
            $field['options'] = $this->newField['options'];
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
            'options' => []
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
    
    public function addFieldOption()
    {
        $this->validate([
            'newOption.key' => 'required',
            'newOption.value' => 'required'
        ]);
        
        $options = $this->newField['options'] ?? [];
        $options[$this->newOption['key']] = $this->newOption['value'];
        
        $this->newField['options'] = $options;
        
        // Temizle
        $this->newOption = [
            'key' => '',
            'value' => ''
        ];
    }
    
    public function removeFieldOption($key)
    {
        $options = $this->newField['options'] ?? [];
        
        if (isset($options[$key])) {
            unset($options[$key]);
            $this->newField['options'] = $options;
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
                
                if (is_array($this->widget['item_schema'])) {
                    foreach ($this->widget['item_schema'] as $field) {
                        if ($field['name'] === 'title') $hasTitle = true;
                        if ($field['name'] === 'is_active') $hasActive = true;
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
                    'required' => true
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
            
            // Thumbnail yükleme
            if ($this->thumbnail) {
                $filename = $widget->slug . '.' . $this->thumbnail->extension();
                $path = $this->thumbnail->storeAs("widgets/{$widget->slug}", $filename, 'public');
                
                $widget->update([
                    'thumbnail' => $filename
                ]);
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