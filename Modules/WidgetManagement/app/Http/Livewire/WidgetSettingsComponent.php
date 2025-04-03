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
        
        // Unique ID'yi gizle - otomatik olarak oluşturulacak, kullanıcının görüp düzenlemesine gerek yok
        $this->schema = array_filter($this->schema ?? [], function($field) {
            return $field['name'] !== 'unique_id' && $field['name'] !== 'id';
        });
        
        // Benzersiz ID yoksa otomatik ekle
        if (!isset($this->settings['unique_id'])) {
            $this->settings['unique_id'] = (string) Str::uuid();
        }
        
        // Title yoksa, widget adını kullan
        if (!isset($this->settings['title'])) {
            $this->settings['title'] = $this->tenantWidget->widget->name;
        }
    }
    
    public function save()
    {
        // Form alanlarını doğrula
        $rules = [];
        
        foreach ($this->schema as $field) {
            if (isset($field['required']) && $field['required'] && $field['name'] !== 'unique_id' && $field['name'] !== 'id') {
                $rules['settings.' . $field['name']] = 'required';
            }
        }
        
        $this->validate($rules);

        // Dosya yüklemeleri
        foreach ($this->temporaryUpload as $fieldName => $upload) {
            if ($upload) {
                $tenantId = tenant()->id ?? 'central';
                $filename = time() . '_' . Str::slug($fieldName) . '.' . $upload->getClientOriginalExtension();
                $path = $upload->storeAs("widgets/{$tenantId}/settings", $filename, 'public');
                $this->settings[$fieldName] = asset('storage/' . $path);
            }
        }

        // Eğer benzersiz ID yoksa, yeni bir tane oluştur
        if (!isset($this->settings['unique_id'])) {
            $this->settings['unique_id'] = (string) Str::uuid();
        }
        
        $this->tenantWidget->update([
            'settings' => $this->settings
        ]);
        
        // Widget önbelleğini temizle
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