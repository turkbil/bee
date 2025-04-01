<?php

namespace Modules\WidgetManagement\app\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Models\WidgetItem;
use Modules\WidgetManagement\app\Services\WidgetItemService;

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
    }
    
    public function loadItems()
    {
        $this->items = $this->itemService->getItemsForWidget($this->tenantWidgetId);
    }
    
    public function addNew()
    {
        $this->formMode = true;
        $this->currentItemId = null;
        $this->formData = [];
    }
    
    public function editItem($itemId)
    {
        $item = WidgetItem::findOrFail($itemId);
        $this->formMode = true;
        $this->currentItemId = $itemId;
        $this->formData = $item->content;
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
        
        // Dosyaları yükle
        foreach ($this->schema as $field) {
            if ($field['type'] === 'image' && isset($this->formData[$field['name']]) && !is_string($this->formData[$field['name']])) {
                $file = $this->formData[$field['name']];
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('widgets/' . tenant()->id, $filename, 'public');
                $this->formData[$field['name']] = asset('storage/' . $path);
            }
        }
        
        if ($this->currentItemId) {
            // Mevcut öğeyi güncelle
            $this->itemService->updateItem($this->currentItemId, $this->formData);
        } else {
            // Yeni öğe ekle
            $this->itemService->addItem($this->tenantWidgetId, $this->formData);
        }
        
        $this->formMode = false;
        $this->currentItemId = null;
        $this->formData = [];
        
        $this->loadItems();
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Öğe kaydedildi.',
            'type' => 'success'
        ]);
    }
    
    public function deleteItem($itemId)
    {
        $this->itemService->deleteItem($itemId);
        $this->loadItems();
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Öğe silindi.',
            'type' => 'success'
        ]);
    }
    
    public function cancelForm()
    {
        $this->formMode = false;
        $this->currentItemId = null;
        $this->formData = [];
    }
    
    public function updateItemOrder($orderedIds)
    {
        $this->itemService->reorderItems($this->tenantWidgetId, $orderedIds);
        $this->loadItems();
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Öğe sıralaması güncellendi.',
            'type' => 'success'
        ]);
    }
    
    public function render()
    {
        $tenantWidget = TenantWidget::with('widget')->findOrFail($this->tenantWidgetId);
        
        return view('widgetmanagement::livewire.widget-item-component', [
            'tenantWidget' => $tenantWidget
        ]);
    }
}