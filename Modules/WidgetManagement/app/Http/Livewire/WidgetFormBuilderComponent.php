<?php

namespace Modules\WidgetManagement\App\Http\Livewire;

use Livewire\Component;
use Modules\WidgetManagement\App\Models\Widget;

class WidgetFormBuilderComponent extends Component
{
    public $widgetId;
    public $widget;
    public $schemaType = 'item_schema';
    
    public function mount($widgetId, $schemaType = 'item_schema')
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
                throw new \Exception('Eksik parametreler: widgetId veya formData');
            }
            
            $widget = Widget::findOrFail($widgetId);
            
            if (is_string($formData)) {
                $formData = json_decode($formData, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Geçersiz JSON formatı');
                }
            }
            
            // Karmaşık form elemanlarını işle
            $processedFormData = $this->processFormElements($formData);
            
            if ($schemaType === 'settings_schema') {
                $widget->settings_schema = $processedFormData;
            } else {
                $widget->item_schema = $processedFormData;
            }
            
            $widget->save();
            
            log_activity(
                $widget,
                'widget ' . ($schemaType === 'settings_schema' ? 'ayar' : 'içerik') . ' form yapısı güncellendi'
            );
            
            return ['success' => true, 'message' => 'Widget form yapısı başarıyla kaydedildi'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Karmaşık form elemanlarını işle
     *
     * @param array $formData
     * @return array
     */
    protected function processFormElements($formData)
    {
        if (!is_array($formData)) {
            return $formData;
        }
        
        foreach ($formData as $key => $element) {
            // Card ve tab_group için özel işlem
            if (isset($element['type']) && in_array($element['type'], ['card', 'tab_group'])) {
                // Eğer properties boşsa, başlat
                if (!isset($element['properties']) || !is_array($element['properties'])) {
                    $formData[$key]['properties'] = [];
                }
                
                // Card için özel işlem
                if ($element['type'] === 'card') {
                    // Kart başlığı ve içeriği ayarla
                    if (!isset($formData[$key]['properties']['title'])) {
                        $formData[$key]['properties']['title'] = $element['label'] ?? 'Kart';
                    }
                    
                    // Eğer içerik elemanları varsa, onları da işle
                    if (isset($element['elements']) && is_array($element['elements'])) {
                        $formData[$key]['elements'] = $this->processFormElements($element['elements']);
                    } else {
                        $formData[$key]['elements'] = [];
                    }
                }
                
                // Tab group için özel işlem
                if ($element['type'] === 'tab_group') {
                    // Sekme verileri yoksa boş bir dizi ekle
                    if (!isset($formData[$key]['properties']['tabs']) || !is_array($formData[$key]['properties']['tabs'])) {
                        $formData[$key]['properties']['tabs'] = [
                            [
                                'title' => 'Varsayılan Sekme',
                                'elements' => []
                            ]
                        ];
                    } else {
                        // Her sekmeyi kontrol et ve işle
                        foreach ($formData[$key]['properties']['tabs'] as $tabIndex => $tab) {
                            // Sekme başlığı kontrol et
                            if (!isset($tab['title'])) {
                                $formData[$key]['properties']['tabs'][$tabIndex]['title'] = 'Sekme ' . ($tabIndex + 1);
                            }
                            
                            // Sekme içerik elemanlarını işle
                            if (isset($tab['elements']) && is_array($tab['elements'])) {
                                $formData[$key]['properties']['tabs'][$tabIndex]['elements'] = 
                                    $this->processFormElements($tab['elements']);
                            } else {
                                $formData[$key]['properties']['tabs'][$tabIndex]['elements'] = [];
                            }
                        }
                    }
                }
            }
        }
        
        return $formData;
    }
    
    public function render()
    {
        return view('widgetmanagement::form-builder.edit')
            ->layout('widgetmanagement::form-builder.layout');
    }
}
