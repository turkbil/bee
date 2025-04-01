<?php

namespace Modules\WidgetManagement\app\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Services\WidgetService;
use Illuminate\Support\Str;

class WidgetSettingsComponent extends Component
{
    use WithFileUploads;
    
    public $tenantWidgetId;
    public $settings = [];
    public $schema = [];
    public $tenantWidget;
    public $temporaryUpload = [];
    
    protected $widgetService;
    
    public function boot(WidgetService $widgetService)
    {
        $this->widgetService = $widgetService;
    }
    
    public function mount($tenantWidgetId)
    {
        $this->tenantWidgetId = $tenantWidgetId;
        $this->tenantWidget = TenantWidget::with('widget')->findOrFail($tenantWidgetId);
        
        $this->schema = $this->tenantWidget->widget->getSettingsSchema();
        $this->settings = $this->tenantWidget->settings ?? [];
    }
    
    public function updatedTemporaryUpload()
    {
        $this->validate([
            'temporaryUpload.*' => 'image|max:1024',
        ]);
    }
    
    public function save()
    {
        // Form alanlarını doğrula
        $rules = [];
        
        foreach ($this->schema as $field) {
            if (isset($field['required']) && $field['required']) {
                $rules['settings.' . $field['name']] = 'required';
            }
        }
        
        $this->validate($rules);
        
        // Dosya yüklemeleri
        foreach ($this->temporaryUpload as $fieldName => $upload) {
            if ($upload) {
                $filename = time() . '_' . Str::slug($fieldName) . '.' . $upload->getClientOriginalExtension();
                $path = $upload->storeAs('widgets/' . tenant()->id . "/settings", $filename, 'public');
                $this->settings[$fieldName] = asset('storage/' . $path);
            }
        }
        
        $this->tenantWidget->update([
            'settings' => $this->settings
        ]);
        
        // Widget önbelleğini temizle
        $this->widgetService->clearWidgetCache(tenant()->id, $this->tenantWidgetId);
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Widget ayarları kaydedildi.',
            'type' => 'success'
        ]);
        
        $this->dispatch('widgetSettingsUpdated');
        $this->dispatch('closeModal');
    }
    
    public function render()
    {
        return view('widgetmanagement::livewire.widget-settings-component');
    }
}