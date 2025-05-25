<?php

namespace Modules\WidgetManagement\app\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Models\WidgetItem;
use Modules\WidgetManagement\app\Services\WidgetItemService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

#[Layout('admin.layout')]
class WidgetItemManageComponent extends Component
{
    use WithFileUploads;
    
    public $tenantWidgetId;
    public $itemId = null;
    public $tenantWidget = null;
    public $formData = [];
    public $schema = [];
    public $isStaticWidget = false;
    
    public $temporaryImages = [];
    
    public $tempPhoto;
    public $photoField;
    public $photos = [];
    
    protected $itemService;
    
    public function boot(WidgetItemService $itemService)
    {
        $this->itemService = $itemService;
    }
    
    public function mount($tenantWidgetId = null, $itemId = null)
    {
        Log::info("WidgetItemManageComponent::mount", [
            'tenantWidgetId' => $tenantWidgetId,
            'itemId' => $itemId
        ]);
        
        $this->tenantWidgetId = $tenantWidgetId;
        $this->itemId = $itemId;
        
        try {
            if ($tenantWidgetId) {
                $this->tenantWidget = TenantWidget::with('widget')->findOrFail($tenantWidgetId);
                
                if ($this->tenantWidget && $this->tenantWidget->widget) {
                    $this->schema = $this->tenantWidget->widget->getItemSchema();
                    $this->isStaticWidget = $this->tenantWidget->widget->type === 'static';
                } else {
                    $this->schema = [];
                    $this->isStaticWidget = false;
                    
                    Log::error("Widget bulunamadı veya TenantWidget'a bağlı değil: {$tenantWidgetId}");
                    session()->flash('error', 'Widget bulunamadı veya TenantWidget\'a bağlı değil.');
                    return redirect()->route('admin.widgetmanagement.index');
                }
            }
            
            if ($itemId) {
                $item = WidgetItem::findOrFail($itemId);
                Log::info("Item bulundu:", ['item' => $item->toArray()]);
                
                if (!$this->tenantWidget || !$this->tenantWidget->widget) {
                    $this->tenantWidgetId = $item->tenant_widget_id;
                    $this->tenantWidget = TenantWidget::with('widget')->findOrFail($item->tenant_widget_id);
                    
                    if (!$this->tenantWidget->widget) {
                        Log::error("Widget bulunamadı: TenantWidget ID {$item->tenant_widget_id}");
                        session()->flash('error', 'Widget bulunamadı.');
                        return redirect()->route('admin.widgetmanagement.index');
                    }
                    
                    $this->schema = $this->tenantWidget->widget->getItemSchema();
                    $this->isStaticWidget = $this->tenantWidget->widget->type === 'static';
                }
                
                $this->formData = $item->content;
                Log::info("Form verileri yüklendi:", ['formData' => $this->formData]);
            }
            
            $this->processSchema();
            
            if (empty($this->formData)) {
                $this->initFormData();
            }
            
            if ($this->isStaticWidget && !$this->itemId) {
                $items = $this->itemService->getItemsForWidget($this->tenantWidgetId);
                
                if ($items->isNotEmpty()) {
                    $this->itemId = $items->first()->id;
                    $this->formData = $items->first()->content;
                }
            }
        } catch (\Exception $e) {
            Log::error("WidgetItemManageComponent mount hatası: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'İçerik yüklenirken bir hata oluştu: ' . $e->getMessage());
        }
    }
    
    protected function processSchema()
    {
        if (!is_array($this->schema)) {
            $this->schema = [];
        }
        
        $hasTitle = false;
        $hasActive = false;

        foreach ($this->schema as $field) {
            if (!isset($field['name'])) continue;
            
            if ($field['name'] === 'title') $hasTitle = true;
            if ($field['name'] === 'is_active') $hasActive = true;
        }
        
        if (!$hasTitle) {
            array_unshift($this->schema, [
                'name' => 'title',
                'label' => 'Başlık',
                'type' => 'text',
                'required' => true,
                'system' => true,
                'properties' => [
                    'width' => 12,
                    'placeholder' => 'İçerik başlığını giriniz'
                ]
            ]);
        }
        
        if (!$hasActive) {
            $this->schema[] = [
                'name' => 'is_active',
                'label' => 'Durum',
                'type' => 'switch',
                'required' => false,
                'system' => true,
                'properties' => [
                    'width' => 12,
                    'active_label' => 'Aktif',
                    'inactive_label' => 'Aktif Değil',
                    'default_value' => true
                ]
            ];
        }
    }
    
    public function updatedTempPhoto()
    {
        if ($this->tempPhoto && $this->photoField) {
            $photosToProcess = is_array($this->tempPhoto) ? $this->tempPhoto : [$this->tempPhoto];
            
            foreach ($photosToProcess as $photo) {
                $this->validate([
                    'tempPhoto.*' => 'image|max:3072',
                ]);
                
                if (!isset($this->photos[$this->photoField])) {
                    $this->photos[$this->photoField] = [];
                }
                
                $this->photos[$this->photoField][] = $photo;
            }
            
            $this->tempPhoto = null;
        }
    }
    
    public function setPhotoField($fieldName)
    {
        $this->photoField = $fieldName;
    }
    
    public function removePhoto($fieldName, $index)
    {
        if (isset($this->photos[$fieldName]) && isset($this->photos[$fieldName][$index])) {
            unset($this->photos[$fieldName][$index]);
            $this->photos[$fieldName] = array_values($this->photos[$fieldName]);
        }
    }
    
    public function removeExistingMultipleImage($fieldName, $index)
    {
        if (isset($this->formData[$fieldName]) && is_array($this->formData[$fieldName]) && isset($this->formData[$fieldName][$index])) {
            if (function_exists('is_tenant') && is_tenant()) {
                \Modules\SettingManagement\App\Helpers\TenantStorageHelper::deleteFile($this->formData[$fieldName][$index]);
            }
            
            unset($this->formData[$fieldName][$index]);
            $this->formData[$fieldName] = array_values($this->formData[$fieldName]);
        }
    }
    
    public function deleteMedia($fieldName)
    {
        if (isset($this->formData[$fieldName]) && !empty($this->formData[$fieldName])) {
            if (function_exists('is_tenant') && is_tenant()) {
                \Modules\SettingManagement\App\Helpers\TenantStorageHelper::deleteFile($this->formData[$fieldName]);
            }
            
            $this->formData[$fieldName] = null;
        }
    }
    
    public function removeImage($imageKey)
    {
        unset($this->temporaryImages[$imageKey]);
    }
    
    protected function initFormData()
    {
        $this->formData = [];
        $this->temporaryImages = [];
        $this->photos = [];
        $this->tempPhoto = null;
        $this->photoField = null;
        
        if (!empty($this->schema)) {
            foreach ($this->schema as $field) {
                if (!isset($field['name']) || !isset($field['type'])) continue;
                if ($field['type'] === 'row') continue;
                
                if ($field['type'] === 'checkbox' || $field['type'] === 'switch') {
                    $defaultValue = isset($field['properties']['default_value']) ? $field['properties']['default_value'] : false;
                    $this->formData[$field['name']] = $defaultValue;
                } elseif ($field['type'] === 'image_multiple') {
                    $this->formData[$field['name']] = [];
                } else {
                    $this->formData[$field['name']] = isset($field['properties']['default_value']) ? $field['properties']['default_value'] : '';
                }
            }
        }
        
        if (isset($this->formData['is_active'])) {
            $this->formData['is_active'] = true;
        }
    }
    
    public function save($redirect = false, $resetForm = false)
    {
        $rules = [];
        
        foreach ($this->schema as $field) {
            if (!isset($field['name']) || !isset($field['type'])) continue;
            if ($field['type'] === 'row') continue;
            
            if (isset($field['required']) && $field['required'] && $field['name'] !== 'unique_id' && $field['type'] !== 'image') {
                $rules['formData.' . $field['name']] = 'required';
            }
            
            if ($field['type'] === 'image' && isset($this->temporaryImages[$field['name']])) {
                $rules['temporaryImages.' . $field['name']] = 'image|max:3072';
            }
        }
        
        $this->validate($rules);

        try {
            if (!isset($this->formData['unique_id'])) {
                $this->formData['unique_id'] = (string) Str::uuid();
            }
            
            foreach ($this->schema as $field) {
                if (!isset($field['name']) || !isset($field['type'])) continue;
                if ($field['type'] === 'row') continue;
                
                if (($field['type'] === 'image' || $field['type'] === 'file') && 
                    isset($this->temporaryImages[$field['name']]) && 
                    $this->temporaryImages[$field['name']]) {
                    
                    $file = $this->temporaryImages[$field['name']];
                    $filename = time() . '_' . Str::slug($field['name']) . '_' . $file->getClientOriginalName();
                    
                    $tenantId = is_tenant() ? tenant_id() : 1;
                    
                    $folder = $field['type'] === 'image' ? 'images' : 'files';
                    
                    try {
                        $urlPath = \Modules\SettingManagement\App\Helpers\TenantStorageHelper::storeTenantFile(
                            $file,
                            "widgets/{$folder}",
                            $filename,
                            $tenantId
                        );
                        
                        $this->formData[$field['name']] = $urlPath;
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Dosya yükleme hatası: ' . $e->getMessage(), [
                            'field' => $field['name'],
                            'type' => $field['type'],
                            'tenant' => $tenantId,
                            'exception' => $e
                        ]);
                    }
                }
                
                if (isset($field['type']) && $field['type'] === 'image_multiple' && isset($field['name']) && isset($this->photos[$field['name']]) && count($this->photos[$field['name']]) > 0) {
                    $fieldName = $field['name'];
                    
                    if (!isset($this->formData[$fieldName]) || !is_array($this->formData[$fieldName])) {
                        $this->formData[$fieldName] = [];
                    }
                    
                    foreach ($this->photos[$fieldName] as $index => $photo) {
                        $tenantId = is_tenant() ? tenant_id() : 1;
                        $filename = time() . '_' . Str::slug($fieldName) . '_' . $index . '_' . $photo->getClientOriginalName();
                        
                        try {
                            $urlPath = \Modules\SettingManagement\App\Helpers\TenantStorageHelper::storeTenantFile(
                                $photo,
                                "widgets/images",
                                $filename,
                                $tenantId
                            );
                            
                            $this->formData[$fieldName][] = $urlPath;
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('Çoklu resim yükleme hatası: ' . $e->getMessage(), [
                                'field' => $fieldName,
                                'index' => $index,
                                'tenant' => $tenantId,
                                'exception' => $e
                            ]);
                        }
                    }
                }
            }
            
            if ($this->itemId) {
                $this->itemService->updateItem($this->itemId, $this->formData);
                $successMessage = 'İçerik güncellendi.';
            } else {
                $item = $this->itemService->addItem($this->tenantWidgetId, $this->formData);
                $this->itemId = $item->id;
                $successMessage = 'Yeni içerik eklendi.';
            }
            
            $this->photos = [];
            $this->temporaryImages = [];
            
            if ($redirect) {
                session()->flash('toast', [
                    'title' => 'Başarılı!',
                    'message' => $successMessage,
                    'type' => 'success'
                ]);
                return redirect()->route('admin.widgetmanagement.items', $this->tenantWidgetId);
            }
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => $successMessage,
                'type' => 'success'
            ]);
            
            if ($resetForm && !$this->itemId) {
                $this->reset(['formData', 'temporaryImages', 'photos']);
                $this->initFormData();
            }
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'İçerik kaydedilirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    public function render()
    {
        return view('widgetmanagement::livewire.widget-item-manage-component');
    }
}