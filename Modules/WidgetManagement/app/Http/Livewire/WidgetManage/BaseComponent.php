<?php

namespace Modules\WidgetManagement\app\Http\Livewire\WidgetManage;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\WidgetCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

#[Layout('admin.layout')]
class BaseComponent extends Component
{
    use WithFileUploads;
    
    public $widgetId;
    public $isNewWidget = true;
    public $thumbnail;
    public $imagePreview = null;
    public $isSubmitting = false;
    public $categories = [];
    public $currentMode = 'basic';
    public $isLoading = false;
    public $originalFilePath = '';
    
    public $widget = [
        'name' => '',
        'slug' => '',
        'description' => '',
        'widget_category_id' => null,
        'type' => 'static',
        'content_html' => '',
        'content_css' => '',
        'content_js' => '',
        'css_files' => [],
        'js_files' => [],
        'has_items' => false,
        'item_schema' => [],
        'settings_schema' => [],
        'is_active' => true,
        'is_core' => false,
        'file_path' => ''
    ];
    
    protected $rules = [
        'widget.name' => 'required|min:3|max:255',
        'widget.slug' => 'required|regex:/^[a-z0-9\-_]+$/i|max:255',
        'widget.description' => 'nullable|max:1000',
        'widget.widget_category_id' => 'nullable|exists:widget_categories,widget_category_id',
        'widget.type' => 'required|in:static,dynamic,module,file',
        'widget.content_html' => 'nullable',
        'widget.content_css' => 'nullable',
        'widget.content_js' => 'nullable',
        'widget.has_items' => 'boolean',
        'widget.is_active' => 'boolean',
        'widget.is_core' => 'boolean',
        'widget.file_path' => 'nullable|string|max:255',
        'thumbnail' => 'nullable|image|max:3072',
    ];
    
    protected $messages = [
        'widget.name.required' => 'Widget adı zorunludur.',
        'widget.name.min' => 'Widget adı en az 3 karakter olmalıdır.',
        'widget.name.max' => 'Widget adı en fazla 255 karakter olabilir.',
        'widget.slug.required' => 'Benzersiz tanımlayıcı (slug) zorunludur.',
        'widget.slug.regex' => 'Benzersiz tanımlayıcı (slug) sadece harf, rakam, tire ve alt çizgi içerebilir.',
        'widget.slug.max' => 'Benzersiz tanımlayıcı (slug) en fazla 255 karakter olabilir.',
        'widget.description.max' => 'Açıklama en fazla 1000 karakter olabilir.',
        'widget.widget_category_id.exists' => 'Seçilen kategori geçerli değil.',
        'widget.type.required' => 'Widget tipi zorunludur.',
        'widget.type.in' => 'Geçersiz widget tipi seçildi.',
        'widget.file_path.max' => 'Dosya yolu en fazla 255 karakter olabilir.',
        'thumbnail.image' => 'Yüklenen dosya bir görsel olmalıdır.',
        'thumbnail.max' => 'Görsel boyutu en fazla 3MB olabilir.',
    ];
    
    public function mount($id = null)
    {
        $this->categories = WidgetCategory::where('is_active', true)
            ->orderBy('title')
            ->get();
            
        $this->widgetId = $id;
        $this->isNewWidget = !$id;
        
        if ($id) {
            $widget = Widget::findOrFail($id);
            
            $this->originalFilePath = $widget->file_path;
            
            $this->widget = [
                'name' => $widget->name,
                'slug' => $widget->slug,
                'description' => $widget->description,
                'widget_category_id' => $widget->widget_category_id,
                'type' => $widget->type,
                'content_html' => $widget->content_html,
                'content_css' => $widget->content_css,
                'content_js' => $widget->content_js,
                'css_files' => $widget->css_files ?? [],
                'js_files' => $widget->js_files ?? [],
                'has_items' => $widget->has_items,
                'item_schema' => $widget->item_schema ?? [],
                'settings_schema' => $widget->settings_schema ?? [],
                'is_active' => $widget->is_active,
                'is_core' => $widget->is_core,
                'file_path' => $widget->file_path
            ];
            
            if ($widget->thumbnail) {
                $this->imagePreview = $widget->getThumbnailUrl();
            }
        }
    }
    
    public function getModuleFiles()
    {
        $modulePath = module_path('WidgetManagement', 'resources/views/blocks/modules');
        $files = [];
        
        if (!File::exists($modulePath)) {
            return $files;
        }
        
        $usedPaths = Widget::where('type', 'module')
            ->whereNotNull('file_path')
            ->where('file_path', '!=', '')
            ->when($this->widgetId, function($query) {
                $query->where('id', '!=', $this->widgetId);
            })
            ->pluck('file_path')
            ->toArray();
        
        $moduleFiles = File::allFiles($modulePath);
        
        foreach ($moduleFiles as $file) {
            if ($file->getExtension() === 'php' && str_ends_with($file->getFilename(), '.blade.php')) {
                $relativePath = str_replace($modulePath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                $relativePath = str_replace('.blade.php', '', $relativePath);
                $relativePath = str_replace('\\', '/', $relativePath);
                $relativePath = 'modules/' . $relativePath;
                
                if (!in_array($relativePath, $usedPaths)) {
                    $displayName = str_replace('/', ' / ', str_replace('modules/', '', $relativePath));
                    $displayName = str_replace('/view', '', $displayName);
                    $files[$relativePath] = ucwords(str_replace(['-', '_'], ' ', $displayName));
                }
            }
        }
        
        ksort($files);
        return $files;
    }
    
    public function getViewFiles()
    {
        $blocksPath = module_path('WidgetManagement', 'resources/views/blocks');
        $files = [];
        
        if (!File::exists($blocksPath)) {
            return $files;
        }
        
        $usedPaths = Widget::where('type', 'file')
            ->whereNotNull('file_path')
            ->where('file_path', '!=', '')
            ->when($this->widgetId, function($query) {
                $query->where('id', '!=', $this->widgetId);
            })
            ->pluck('file_path')
            ->toArray();
        
        $viewFiles = File::allFiles($blocksPath);
        
        foreach ($viewFiles as $file) {
            if ($file->getExtension() === 'php' && str_ends_with($file->getFilename(), '.blade.php')) {
                $relativePath = str_replace($blocksPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                $relativePath = str_replace('.blade.php', '', $relativePath);
                $relativePath = str_replace('\\', '/', $relativePath);
                
                if (!str_starts_with($relativePath, 'modules/') && !in_array($relativePath, $usedPaths)) {
                    $displayName = str_replace('/', ' / ', $relativePath);
                    $displayName = str_replace('/view', '', $displayName);
                    $files[$relativePath] = ucwords(str_replace(['-', '_'], ' ', $displayName));
                }
            }
        }
        
        ksort($files);
        return $files;
    }
    
    public function hasAvailableModuleFiles()
    {
        return count($this->getModuleFiles()) > 0;
    }
    
    public function hasAvailableViewFiles()
    {
        return count($this->getViewFiles()) > 0;
    }
    
    public function updatedWidgetType()
    {
        $this->widget['file_path'] = '';
    }
    
    public function updatedWidgetFilePath()
    {
        $this->dispatch('$refresh');
    }
    
    public function updatedThumbnail()
    {
        $this->validateOnly('thumbnail', [
            'thumbnail' => 'image|max:3072'
        ]);
        
        if ($this->thumbnail) {
            $this->imagePreview = $this->thumbnail->temporaryUrl();
        }
    }
    
    public function setMode($mode)
    {
        $this->isLoading = true;
        $this->currentMode = $mode;
        
        $this->dispatch('$refresh');
        
        $this->js('setTimeout(() => { $wire.set("isLoading", false); }, 300);');
    }
    
    public function addCssFile()
    {
        $cssFiles = $this->widget['css_files'] ?? [];
        $cssFiles[] = '';
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

    public function removeJsFile($index)
    {
        $jsFiles = $this->widget['js_files'];
        unset($jsFiles[$index]);
        $this->widget['js_files'] = array_values($jsFiles);
    }
    
    public function getAvailableVariables()
    {
        $variables = [];
        
        if (!empty($this->widget['settings_schema'])) {
            $variables['settings'] = $this->extractFieldsFromSchema($this->widget['settings_schema']);
        }
        
        if ($this->widget['has_items'] && !empty($this->widget['item_schema'])) {
            $variables['items'] = $this->extractFieldsFromSchema($this->widget['item_schema']);
        }
        
        return $variables;
    }
    
    private function extractFieldsFromSchema($schema)
    {
        $fields = [];
        
        foreach ($schema as $field) {
            if (!isset($field['type'])) {
                continue;
            }
            
            $designElements = ['row', 'card', 'tab_group'];
            if (in_array($field['type'], $designElements)) {
                if (isset($field['elements']) && is_array($field['elements'])) {
                    $nestedFields = $this->extractFieldsFromSchema($field['elements']);
                    $fields = array_merge($fields, $nestedFields);
                }
                
                if ($field['type'] === 'tab_group' && isset($field['properties']['tabs'])) {
                    foreach ($field['properties']['tabs'] as $tab) {
                        if (isset($tab['elements']) && is_array($tab['elements'])) {
                            $tabFields = $this->extractFieldsFromSchema($tab['elements']);
                            $fields = array_merge($fields, $tabFields);
                        }
                    }
                }
                
                if ($field['type'] === 'row' && isset($field['columns'])) {
                    foreach ($field['columns'] as $column) {
                        if (isset($column['elements']) && is_array($column['elements'])) {
                            $columnFields = $this->extractFieldsFromSchema($column['elements']);
                            $fields = array_merge($fields, $columnFields);
                        }
                    }
                }
                
                continue;
            }
            
            if (isset($field['name']) && !empty($field['name']) && 
                (!isset($field['hidden']) || !$field['hidden'])) {
                $fields[] = [
                    'name' => $field['name'],
                    'label' => $field['label'] ?? 'Tanımsız',
                    'type' => $field['type'] ?? 'text'
                ];
            }
        }
        
        return $fields;
    }

    public function save($redirect = false, $resetForm = false)
    {
        $this->isSubmitting = true;
        
        if (empty($this->widget['slug'])) {
            $this->widget['slug'] = Str::slug($this->widget['name']);
        }
        
        $rules = [
            'widget.name' => 'required|min:3|max:255',
            'widget.slug' => 'required|regex:/^[a-z0-9\-_]+$/i|max:255',
            'widget.description' => 'nullable|max:1000',
            'widget.widget_category_id' => 'nullable|exists:widget_categories,widget_category_id',
            'widget.type' => 'required|in:static,dynamic,module,file',
            'widget.has_items' => 'boolean',
            'widget.is_active' => 'boolean',
            'widget.is_core' => 'boolean',
            'thumbnail' => 'nullable|image|max:3072',
        ];
        
        if (in_array($this->widget['type'], ['module', 'file'])) {
            $rules['widget.file_path'] = 'required|string|max:255';
        }
        
        $this->validate($rules);
        
        try {
            if ($this->widgetId) {
                $widget = Widget::findOrFail($this->widgetId);
                $widget->update([
                    'name' => $this->widget['name'],
                    'slug' => $this->widget['slug'],
                    'description' => $this->widget['description'],
                    'widget_category_id' => $this->widget['widget_category_id'],
                    'type' => $this->widget['type'],
                    'has_items' => $this->widget['has_items'],
                    'is_active' => $this->widget['is_active'],
                    'is_core' => $this->widget['is_core'],
                    'file_path' => $this->widget['file_path'],
                ]);
            } else {
                $widget = Widget::create([
                    'name' => $this->widget['name'],
                    'slug' => $this->widget['slug'],
                    'description' => $this->widget['description'],
                    'widget_category_id' => $this->widget['widget_category_id'],
                    'type' => $this->widget['type'],
                    'has_items' => $this->widget['has_items'],
                    'is_active' => $this->widget['is_active'],
                    'is_core' => $this->widget['is_core'],
                    'file_path' => $this->widget['file_path'],
                    'content_html' => '',
                    'content_css' => '',
                    'content_js' => '',
                    'css_files' => [],
                    'js_files' => [],
                    'item_schema' => [],
                    'settings_schema' => []
                ]);
                
                $this->widgetId = $widget->id;
                $this->isNewWidget = false;
            }
            
            if ($this->thumbnail) {
                $tenantId = is_tenant() ? tenant_id() : 1;
                $filename = 'image-' . $widget->slug . '-' . Str::random(6) . '.' . $this->thumbnail->extension();
                
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
                    
                    $this->thumbnail = null;
                } catch (\Exception $e) {
                    Log::error('Thumbnail yükleme hatası: ' . $e->getMessage());
                }
            }
            
            if ($redirect) {
                session()->flash('toast', [
                    'title' => 'Başarılı!',
                    'message' => 'Widget başarıyla kaydedildi.',
                    'type' => 'success'
                ]);
                
                if ($this->isNewWidget) {
                    return redirect()->route('admin.widgetmanagement.index');
                } else {
                    return redirect()->route('admin.widgetmanagement.index');
                }
            }
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Widget başarıyla kaydedildi.',
                'type' => 'success'
            ]);
            
            if ($resetForm && $this->isNewWidget) {
                $this->reset(['widget', 'thumbnail', 'imagePreview']);
                $this->widget = [
                    'name' => '',
                    'slug' => '',
                    'description' => '',
                    'widget_category_id' => null,
                    'type' => 'static',
                    'content_html' => '',
                    'content_css' => '',
                    'content_js' => '',
                    'css_files' => [],
                    'js_files' => [],
                    'has_items' => false,
                    'item_schema' => [],
                    'settings_schema' => [],
                    'is_active' => true,
                    'is_core' => false,
                    'file_path' => ''
                ];
            }
            
            if ($this->isNewWidget) {
                return redirect()->route('admin.widgetmanagement.manage', $widget->id);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Widget kaydedilirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
        
        $this->isSubmitting = false;
    }
    
    public function saveBasicInfo()
    {
        $this->save(false, false);
    }
    
    public function saveDesign()
    {
        $this->isSubmitting = true;
        
        $this->validate([
            'widget.content_html' => 'nullable',
            'widget.content_css' => 'nullable',
            'widget.content_js' => 'nullable',
        ]);
        
        try {
            $widget = Widget::findOrFail($this->widgetId);
            $widget->update([
                'content_html' => $this->widget['content_html'],
                'content_css' => $this->widget['content_css'],
                'content_js' => $this->widget['content_js'],
                'css_files' => $this->widget['css_files'],
                'js_files' => $this->widget['js_files'],
            ]);
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Widget tasarımı kaydedildi.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Widget tasarımı kaydedilirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
        
        $this->isSubmitting = false;
    }
    
    public function render()
    {
        return view('widgetmanagement::livewire.widget-manage.index');
    }
}
