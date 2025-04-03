<?php

namespace Modules\WidgetManagement\app\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Models\WidgetItem;
use Modules\WidgetManagement\app\Services\WidgetItemService;
use Illuminate\Support\Str;

#[Layout('admin.layout')]
class WidgetItemComponent extends Component
{
    use WithFileUploads;
    
    public $tenantWidgetId;
    public $items = [];
    public $formMode = false;
    public $formData = [];
    public $currentItemId = null;
    public $schema = [];
    
    protected $itemService;
    
    protected $listeners = [
        'updateItemOrder',
        'itemOrderUpdated' => 'updateItemOrder',
        'addWidgetItem' => 'addItem',
        'editWidgetItem' => 'editItem',
        'deleteWidgetItem' => 'deleteItem'
    ];
    
    public function boot(WidgetItemService $itemService)
    {
        $this->itemService = $itemService;
    }
    
    public function mount($tenantWidgetId)
    {
        $this->tenantWidgetId = $tenantWidgetId;
        $this->loadItems();
        
        $tenantWidget = TenantWidget::with('widget')->findOrFail($tenantWidgetId);
        $this->schema = $tenantWidget->widget->getItemSchema();
        
        // Şema boşsa veya title alanı yoksa, standart alanları ekle
        if (empty($this->schema) || !$this->hasField('title')) {
            $this->schema = array_merge($this->schema ?? [], [
                [
                    'name' => 'title',
                    'label' => 'Başlık',
                    'type' => 'text',
                    'required' => true
                ]
            ]);
        }
        
        // Aktif/Pasif alanı yoksa ekle
        if (!$this->hasField('is_active')) {
            $this->schema = array_merge($this->schema ?? [], [
                [
                    'name' => 'is_active',
                    'label' => 'Aktif',
                    'type' => 'checkbox',
                    'required' => false
                ]
            ]);
        }
        
        // formData'yı başlat
        $this->initFormData();
    }
    
    // Yardımcı metod
    private function hasField($fieldName)
    {
        if (empty($this->schema)) return false;
        
        foreach ($this->schema as $field) {
            if ($field['name'] === $fieldName) {
                return true;
            }
        }
        
        return false;
    }
    
    // Yeni metod: formData için tüm alanları başlat
    protected function initFormData()
    {
        $this->formData = [];
        
        if (!empty($this->schema)) {
            foreach ($this->schema as $field) {
                if ($field['type'] === 'checkbox') {
                    $this->formData[$field['name']] = false;
                } else {
                    $this->formData[$field['name']] = '';
                }
            }
        }
        
        // Varsayılan olarak aktif
        if (isset($this->formData['is_active'])) {
            $this->formData['is_active'] = true;
        }
    }
    
    public function loadItems()
    {
        $this->items = $this->itemService->getItemsForWidget($this->tenantWidgetId);
    }
    
    // Diğer fonksiyonlar korundu...
    
    public function saveItem()
    {
        // Form verilerini doğrula
        $rules = [];
        
        foreach ($this->schema as $field) {
            if (isset($field['required']) && $field['required']) {
                $rules['formData.' . $field['name']] = 'required';
            }
            
            if ($field['type'] === 'image' && isset($this->formData[$field['name']]) && !is_string($this->formData[$field['name']])) {
                $rules['formData.' . $field['name']] = 'image|max:1024';
            }
        }
        
        $this->validate($rules);
        
        try {
            // Benzersiz ID ekle
            if (!isset($this->formData['unique_id'])) {
                $this->formData['unique_id'] = (string) Str::uuid();
            }
            
            // Dosyaları yükle
            foreach ($this->schema as $field) {
                if ($field['type'] === 'image' && isset($this->formData[$field['name']]) && !is_string($this->formData[$field['name']])) {
                    $file = $this->formData[$field['name']];
                    $filename = time() . '_' . $file->getClientOriginalName();
                    
                    // Tenant kontrolü
                    $path = '';
                    if (function_exists('tenant') && tenant()) {
                        $path = $file->storeAs('widgets/' . tenant()->id, $filename, 'public');
                    } else {
                        $path = $file->storeAs('widgets/central', $filename, 'public');
                    }
                    
                    $this->formData[$field['name']] = asset('storage/' . $path);
                }
            }
            
            if ($this->currentItemId) {
                // Mevcut öğeyi güncelle
                $this->itemService->updateItem($this->currentItemId, $this->formData);
                $successMessage = 'Öğe güncellendi.';
            } else {
                // Yeni öğe ekle
                $this->itemService->addItem($this->tenantWidgetId, $this->formData);
                $successMessage = 'Yeni öğe eklendi.';
            }
            
            $this->formMode = false;
            $this->currentItemId = null;
            $this->initFormData(); // Formu temizle
            
            $this->loadItems();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => $successMessage,
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Öğe kaydedilirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
}