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
    }
    
    public function updatedTemporaryUpload()
    {
        $this->validate([
            'temporaryUpload.*' => 'image|max:1024',
        ]);
    }
    
    /**
     * Benzersiz ID üretir
     */
    private function generateUniqueId(): string
    {
        return (string) Str::uuid();
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

        // Tenant ID'yi erken al
        $tenantId = tenant()->id; 
        if (!$tenantId) {
            // Eğer tenant ID hala null ise hata ver veya logla
             activity()->log('Tenant ID could not be determined in WidgetSettingsComponent save method.');
             $this->dispatch('toast', [
                 'title' => 'Hata!',
                 'message' => 'Kiracı bilgisi alınamadı. Lütfen tekrar deneyin.',
                 'type' => 'error'
             ]);
             return;
        }
        
        // Dosya yüklemeleri
        foreach ($this->temporaryUpload as $fieldName => $upload) {
            if ($upload) {
                // Dosya yüklerken de $tenantId değişkenini kullanalım
                $filename = time() . '_' . Str::slug($fieldName) . '.' . $upload->getClientOriginalExtension();
                $path = $upload->storeAs('widgets/' . $tenantId . "/settings", $filename, 'public');
                $this->settings[$fieldName] = asset('storage/' . $path);
            }
        }

        // Eğer benzersiz ID yoksa, yeni bir tane oluştur
        if (!isset($this->settings['unique_id'])) {
            $this->settings['unique_id'] = $this->generateUniqueId();
        }
        
        $this->tenantWidget->update([
            'settings' => $this->settings
        ]);
        
        // Widget önbelleğini temizle - Kaydedilen tenantId'yi kullan
        $this->widgetService->clearWidgetCache($tenantId, $this->tenantWidgetId);
        
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