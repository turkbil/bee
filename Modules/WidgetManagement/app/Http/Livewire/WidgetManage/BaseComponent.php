<?php

namespace Modules\WidgetManagement\app\Http\Livewire\WidgetManage;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\WidgetCategory;
use Modules\WidgetManagement\app\Http\Livewire\Traits\WithImageUpload;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

#[Layout('admin.layout')]
class BaseComponent extends Component
{
    use WithFileUploads, WithImageUpload;
    use DesignTrait, ItemsTrait, SettingsTrait, ImageHandlerTrait;
    
    public $widgetId;
    public $formMode = 'base'; // base, items, settings, design, preview
    public $thumbnail;
    public $temporaryImages = [];
    public $temporaryMultipleImages = [];
    public $imagePreview = null;
    public $isSubmitting = false;
    public $categories = [];
    
    public $widget = [
        'name' => '',
        'slug' => '',
        'description' => '',
        'widget_category_id' => null,
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
        'is_core' => false,
        'file_path' => ''
    ];
    
    protected $rules = [
        'widget.name' => 'required|min:3|max:255',
        'widget.slug' => 'required|regex:/^[a-z0-9\-_]+$/i|max:255',
        'widget.description' => 'nullable|max:1000',
        'widget.widget_category_id' => 'nullable|exists:widget_categories,widget_category_id',
        'widget.type' => 'required|in:static,dynamic,module,file',
        'widget.module_ids' => 'nullable|array',
        'widget.content_html' => 'nullable',
        'widget.content_css' => 'nullable',
        'widget.content_js' => 'nullable',
        'widget.has_items' => 'boolean',
        'widget.item_schema' => 'nullable|array',
        'widget.settings_schema' => 'nullable|array',
        'widget.is_active' => 'boolean',
        'widget.is_core' => 'boolean',
        'widget.file_path' => 'nullable|string|max:255',
        'thumbnail' => 'nullable|image|max:3072', // 3MB = 3072KB
        'temporaryImages.*' => 'nullable|image|max:3072',
        'temporaryMultipleImages.*' => 'nullable|image|max:3072',
    ];
    
    // Lint hatalarını düzeltmek için özel değişkenler
    protected $casts = [
        'widget.item_schema' => 'array',
        'widget.settings_schema' => 'array',
    ];

    // Özel doğrulama mesajları
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
        'temporaryImages.*.image' => 'Yüklenen dosya bir görsel olmalıdır.',
        'temporaryImages.*.max' => 'Görsel boyutu en fazla 3MB olabilir.',
        'temporaryMultipleImages.*.image' => 'Yüklenen dosya bir görsel olmalıdır.',
        'temporaryMultipleImages.*.max' => 'Görsel boyutu en fazla 3MB olabilir.',
    ];
    
    public function mount($id = null)
    {
        // Kategorileri yükle
        $this->categories = WidgetCategory::where('is_active', true)
            ->orderBy('title')
            ->get();
            
        $this->widgetId = $id;
        
        if ($id) {
            $widget = Widget::findOrFail($id);
            
            $this->widget = [
                'name' => $widget->name,
                'slug' => $widget->slug,
                'description' => $widget->description,
                'widget_category_id' => $widget->widget_category_id,
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
                'is_core' => $widget->is_core,
                'file_path' => $widget->file_path
            ];
            
            // 'title' ve 'is_active' her zaman item_schema'da olmalı
            if ($widget->has_items) {
                $hasTitle = false;
                $hasActive = false;
                $hasUniqueId = false;
                
                if (is_array($this->widget['item_schema'])) {
                    foreach ($this->widget['item_schema'] as $field) {
                        if ($field['name'] === 'title') $hasTitle = true;
                        if ($field['name'] === 'is_active') $hasActive = true;
                        if ($field['name'] === 'unique_id') $hasUniqueId = true;
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
                
                if (!$hasUniqueId) {
                    $this->widget['item_schema'][] = [
                        'name' => 'unique_id',
                        'label' => 'Benzersiz ID',
                        'type' => 'text',
                        'required' => false,
                        'system' => true,
                        'hidden' => true
                    ];
                }
            }
            
            // Mevcut resim için önizleme ayarla
            if ($widget->thumbnail) {
                $this->imagePreview = $widget->getThumbnailUrl();
            }
        }
    }
    
    public function updatedWidgetName()
    {
        // Widget adı değiştiğinde otomatik olarak slug oluştur (zaten varsa değiştirme)
        if (empty($this->widget['slug'])) {
            $this->widget['slug'] = Str::slug($this->widget['name']);
        }
    }
    
    public function setFormMode($mode)
    {
        $this->formMode = $mode;
    }
    
    public function save()
    {
        $this->isSubmitting = true;
        
        // Widget adından slug oluştur (kaydetmeden önce)
        if (empty($this->widget['slug'])) {
            $this->widget['slug'] = Str::slug($this->widget['name']);
        }
        
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
                        'system' => true,
                        'hidden' => true
                    ];
                }
            }
            
            // Settings Schema'ya title ekle
            $hasTitle = false;
            $hasUniqueId = false;
            
            if (is_array($this->widget['settings_schema'])) {
                foreach ($this->widget['settings_schema'] as $field) {
                    if ($field['name'] === 'widget.title') $hasTitle = true;
                    if ($field['name'] === 'widget.unique_id') $hasUniqueId = true;
                }
            } else {
                $this->widget['settings_schema'] = [];
            }
            
            if (!$hasTitle) {
                $this->widget['settings_schema'] = array_merge([[
                    'name' => 'widget.title',
                    'label' => 'Başlık',
                    'type' => 'text',
                    'required' => true,
                    'system' => true
                ]], $this->widget['settings_schema']);
            }
            
            if (!$hasUniqueId) {
                $this->widget['settings_schema'][] = [
                    'name' => 'widget.unique_id',
                    'label' => 'Benzersiz ID',
                    'type' => 'text',
                    'required' => false,
                    'system' => true,
                    'hidden' => true
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
        
        return view('widgetmanagement::livewire.widget-manage.index', [
            'modules' => $modules
        ]);
    }
}