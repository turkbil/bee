<?php

namespace Modules\WidgetManagement\app\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Models\WidgetItem;
use Modules\WidgetManagement\app\Services\WidgetItemService;

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
        
        // Widget şemasını al
        $tenantWidget = TenantWidget::with('widget')->findOrFail($tenantWidgetId);
        $this->schema = $tenantWidget->widget->getItemSchema();
        
        // formData'yı başlat - her alan için boş değer
        $this->initFormData();
    }
    
    // Yeni metod: formData için tüm alanları başlat
    protected function initFormData()
    {
        $this->formData = [];
        
        if (!empty($this->schema)) {
            foreach ($this->schema as $field) {
                $this->formData[$field['name']] = '';
            }
        }
    }
    
    public function loadItems()
    {
        $this->items = $this->itemService->getItemsForWidget($this->tenantWidgetId);
    }
    
    public function addNew()
    {
        $this->formMode = true;
        $this->currentItemId = null;
        $this->initFormData(); // Formu sıfırla
    }
    
    public function editItem($itemId)
    {
        $item = WidgetItem::findOrFail($itemId);
        $this->formMode = true;
        $this->currentItemId = $itemId;
        
        // Önce formu sıfırla
        $this->initFormData();
        
        // Sonra mevcut verileri yükle
        if (!empty($item->content) && is_array($item->content)) {
            foreach ($item->content as $key => $value) {
                $this->formData[$key] = $value;
            }
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
    
    public function deleteItem($itemId)
    {
        try {
            $this->itemService->deleteItem($itemId);
            $this->loadItems();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Öğe silindi.',
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
        $this->initFormData(); // Formu temizle
    }
    
    public function updateItemOrder($orderedIds)
    {
        try {
            $this->itemService->reorderItems($this->tenantWidgetId, $orderedIds);
            $this->loadItems();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Öğe sıralaması güncellendi.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Sıralama güncellenirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    public function render()
    {
        $tenantWidget = TenantWidget::with('widget')->findOrFail($this->tenantWidgetId);
        
        return view('widgetmanagement::livewire.widget-item-component', [
            'tenantWidget' => $tenantWidget
        ]);
    }
}