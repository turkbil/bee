<?php

namespace Modules\WidgetManagement\App\Http\Livewire;

use Livewire\Component;
use Modules\WidgetManagement\App\Models\Widget;

class WidgetFormBuilderComponent extends Component
{
    public $widgetId;
    public $widget;
    public $schemaType = 'item'; // item veya settings
    
    public function mount($widgetId, $schemaType = 'item')
    {
        $this->widgetId = $widgetId;
        $this->schemaType = $schemaType;
        
        $this->widget = Widget::findOrFail($widgetId);
    }
    
    public function saveLayout($data)
    {
        try {
            $widgetId = $data['widgetId'] ?? $this->widgetId;
            $formData = $data['formData'] ?? null;
            $schemaType = $data['schemaType'] ?? $this->schemaType;
            
            if (!$widgetId || !$formData) {
                throw new \Exception("Eksik parametreler: widgetId veya formData");
            }
            
            $widget = Widget::findOrFail($widgetId);
            
            if (is_string($formData)) {
                $formData = json_decode($formData, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception("Geçersiz JSON formatı");
                }
            }
            
            if ($schemaType === 'settings') {
                $widget->settings_schema = $formData;
            } else {
                $widget->item_schema = $formData;
            }
            
            $widget->save();
            
            log_activity(
                $widget,
                'widget ' . ($schemaType === 'settings' ? 'ayar' : 'içerik') . ' form yapısı güncellendi'
            );
            
            return ['success' => true, 'message' => 'Widget form yapısı başarıyla kaydedildi'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function render()
    {
        return view('widgetmanagement::form-builder.edit')
            ->layout('widgetmanagement::form-builder.layout');
    }
}