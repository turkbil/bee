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
    public $tenantWidget = null;
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
        
        $this->tenantWidget = TenantWidget::with('widget')->findOrFail($tenantWidgetId);
        $this->schema = $this->tenantWidget->widget->getItemSchema();
        
        // Statik bileşen kontrolü
        if ($this->tenantWidget->widget->type === 'static' && $this->items->isEmpty()) {
            // Statik bileşen için otomatik bir içerik öğesi oluştur
            $content = [
                'title' => $this->tenantWidget->settings['title'] ?? $this->tenantWidget->widget->name,
                'is_active' => true,
                'unique_id' => (string) Str::uuid()
            ];
            
            $this->itemService->addItem($this->tenantWidgetId, $content);
            $this->loadItems();
        }
        
        // Her zaman title ve is_active alanları olmalı - bunlar değiştirilemez
        $hasTitle = false;
        $hasActive = false;

        if (is_array($this->schema)) {
            foreach ($this->schema as $field) {
                if ($field['name'] === 'title') $hasTitle = true;
                if ($field['name'] === 'is_active') $hasActive = true;
            }
        } else {
            $this->schema = [];
        }
        
        // Title alanı yoksa ekle
        if (!$hasTitle) {
            $this->schema = array_merge([[
                'name' => 'title',
                'label' => 'Başlık',
                'type' => 'text',
                'required' => true,
                'system' => true // Sistem alanı olduğunu belirt
            ]], $this->schema ?? []);
        }
        
        // Aktif/Pasif alanı yoksa ekle
        if (!$hasActive) {
            $this->schema[] = [
                'name' => 'is_active',
                'label' => 'Aktif',
                'type' => 'checkbox',
                'required' => false,
                'system' => true // Sistem alanı olduğunu belirt
            ];
        }
        
        // formData'yı başlat
        $this->initFormData();
    }
    
    public function loadItems()
    {
        $this->items = $this->itemService->getItemsForWidget($this->tenantWidgetId);
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
    
    public function addItem()
    {
        $this->formMode = true;
        $this->currentItemId = null;
        $this->initFormData();
    }
    
    public function editItem($itemId)
    {
        $item = WidgetItem::findOrFail($itemId);
        $this->currentItemId = $item->id;
        $this->formData = $item->content;
        $this->formMode = true;
    }
    
    public function deleteItem($itemId)
    {
        try {
            // Statik bileşen kontrolü - tek öğe varsa silinemez
            if ($this->tenantWidget->widget->type === 'static' && $this->items->count() <= 1) {
                $this->dispatch('toast', [
                    'title' => 'Hata!',
                    'message' => 'Statik bileşenin tek içerik öğesi silinemez.',
                    'type' => 'error'
                ]);
                return;
            }
            
            $this->itemService->deleteItem($itemId);
            
            $this->loadItems();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Öğe başarıyla silindi.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Öğe silinirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    public function cancelForm()
    {
        $this->formMode = false;
        $this->currentItemId = null;
        $this->initFormData();
    }
    
    public function updateItemOrder($items)
    {
        // Sadece geçerli bir item dizisi ise devam et
        if (!is_array($items) || empty($items)) {
            return;
        }
        
        try {
            $this->itemService->reorderItems($this->tenantWidgetId, $items);
            
            $this->loadItems();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Öğeler başarıyla sıralandı.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Öğeler sıralanırken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
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
            // Benzersiz ID otomatik ekle - kullanıcı görmesin
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
    
    public function render()
    {
        return view('widgetmanagement::livewire.widget-item-component');
    }
}