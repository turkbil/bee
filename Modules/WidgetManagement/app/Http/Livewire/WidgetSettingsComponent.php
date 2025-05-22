<?php

namespace Modules\WidgetManagement\app\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Services\WidgetService;
use Illuminate\Support\Str;

#[Layout('admin.layout')]
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
        
        $this->processSchema();
        
        if (!isset($this->settings['unique_id'])) {
            $this->settings['unique_id'] = (string) Str::uuid();
        }
        
        if (!isset($this->settings['title'])) {
            $this->settings['title'] = $this->tenantWidget->widget->name;
        }
    }
    
    protected function processSchema()
    {
        if (!is_array($this->schema)) {
            $this->schema = [];
        }
        
        $this->schema = array_filter($this->schema ?? [], function($field) {
            if (!isset($field['name'])) return true;
            return $field['name'] !== 'unique_id' && $field['name'] !== 'id';
        });
        
        $hasTitle = false;
        $hasUniqueId = false;
        
        foreach ($this->schema as $field) {
            if (!isset($field['name'])) continue;
            
            if ($field['name'] === 'title') $hasTitle = true;
            if ($field['name'] === 'unique_id') $hasUniqueId = true;
        }
        
        if (!$hasTitle) {
            array_unshift($this->schema, [
                'name' => 'title',
                'label' => 'Widget Başlığı',
                'type' => 'text',
                'required' => true,
                'system' => true,
                'properties' => [
                    'width' => 12,
                    'placeholder' => 'Widget başlığını giriniz',
                    'default_value' => $this->tenantWidget->widget->name
                ]
            ]);
        }
        
        if (!$hasUniqueId) {
            $this->schema[] = [
                'name' => 'unique_id',
                'label' => 'Benzersiz ID',
                'type' => 'text',
                'required' => false,
                'system' => true,
                'hidden' => true,
                'properties' => [
                    'width' => 12
                ]
            ];
        }
    }
    
    public function save()
    {
        $rules = [];
        
        foreach ($this->schema as $field) {
            if (!isset($field['name']) || !isset($field['type'])) continue;
            if ($field['type'] === 'row') continue;
            
            if (isset($field['required']) && $field['required'] && $field['name'] !== 'unique_id' && $field['name'] !== 'id') {
                $rules['settings.' . $field['name']] = 'required';
            }
        }
        
        $this->validate($rules);

        foreach ($this->temporaryUpload as $fieldName => $upload) {
            if ($upload) {
                $tenantId = tenant()->id ?? 'central';
                $filename = time() . '_' . Str::slug($fieldName) . '.' . $upload->getClientOriginalExtension();
                $path = $upload->storeAs("widgets/{$tenantId}/settings", $filename, 'public');
                $this->settings[$fieldName] = asset('storage/' . $path);
            }
        }

        if (!isset($this->settings['unique_id'])) {
            $this->settings['unique_id'] = (string) Str::uuid();
        }
        
        $this->tenantWidget->update([
            'settings' => $this->settings
        ]);
        
        $this->widgetService->clearWidgetCache(tenant()->id ?? null, $this->tenantWidgetId);
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Widget ayarları kaydedildi.',
            'type' => 'success'
        ]);
        
        $this->dispatch('widgetSettingsUpdated');
    }
    
    public function render()
    {
        return view('widgetmanagement::livewire.widget-settings-component');
    }
}