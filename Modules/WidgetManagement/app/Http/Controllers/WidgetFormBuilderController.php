<?php

namespace Modules\WidgetManagement\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\WidgetManagement\App\Models\Widget;
use Illuminate\Support\Str;

class WidgetFormBuilderController extends Controller
{
    /**
     * Widget form layout yükle
     * @param int $widgetId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function load($widgetId, Request $request)
    {
        try {
            $widget = Widget::findOrFail($widgetId);
            $schemaType = $request->get('schema', 'item_schema');
            
            $schemaData = null;
            
            if ($schemaType === 'setting_schema' || $schemaType === 'settings_schema') {
                $schemaData = $widget->settings_schema;
                $title = $widget->name . ' Ayarları';
            } else {
                $schemaData = $widget->item_schema;
                $title = $widget->name . ' İçerik Yapısı';
            }
            
            $elements = [];
            
            if (!empty($schemaData)) {
                $schema = is_array($schemaData) ? $schemaData : json_decode($schemaData, true);
                
                if (json_last_error() === JSON_ERROR_NONE && is_array($schema)) {
                    foreach ($schema as $field) {
                        $element = [
                            'type' => $field['type'] ?? 'text',
                            'properties' => [
                                'name' => $field['name'] ?? '',
                                'label' => $field['label'] ?? '',
                                'required' => $field['required'] ?? false,
                                'is_active' => true,
                                'is_system' => $field['system'] ?? false,
                                'width' => 12
                            ]
                        ];
                        
                        if (isset($field['default'])) {
                            $element['properties']['default_value'] = $field['default'];
                        }
                        
                        if (isset($field['placeholder'])) {
                            $element['properties']['placeholder'] = $field['placeholder'];
                        }
                        
                        if (isset($field['help_text'])) {
                            $element['properties']['help_text'] = $field['help_text'];
                        }
                        
                        if (isset($field['options']) && is_array($field['options'])) {
                            $element['properties']['options'] = [];
                            foreach ($field['options'] as $key => $value) {
                                $element['properties']['options'][] = [
                                    'value' => $key,
                                    'label' => $value,
                                    'is_default' => false
                                ];
                            }
                        }
                        
                        if (isset($field['hidden']) && $field['hidden']) {
                            continue;
                        }
                        
                        $elements[] = $element;
                    }
                }
            }
            
            $layout = [
                'title' => $title,
                'elements' => $elements
            ];
                
            return response()->json([
                'success' => true,
                'layout' => $layout
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'layout' => [
                    'title' => 'Widget Form Yapısı',
                    'elements' => []
                ]
            ], 200);
        }
    }
    
    /**
     * Widget form layout kaydet
     * @param Request $request
     * @param int $widgetId
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request, $widgetId)
    {
        try {
            $widget = Widget::findOrFail($widgetId);
            $schemaType = $request->get('schema', 'item_schema');
            
            $formData = $request->input('layout');
            
            if (empty($formData)) {
                throw new \Exception("Form verisi boş olamaz.");
            }
            
            if (is_string($formData)) {
                $formData = json_decode($formData, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception("Geçersiz JSON formatı");
                }
            }
            
            $schema = [];
            
            if (isset($formData['elements']) && is_array($formData['elements'])) {
                foreach ($formData['elements'] as $element) {
                    if (!isset($element['type']) || !isset($element['properties'])) {
                        continue;
                    }
                    
                    $field = [
                        'name' => $element['properties']['name'] ?? '',
                        'label' => $element['properties']['label'] ?? '',
                        'type' => $element['type'],
                        'required' => $element['properties']['required'] ?? false
                    ];
                    
                    if (isset($element['properties']['default_value'])) {
                        $field['default'] = $element['properties']['default_value'];
                    }
                    
                    if (isset($element['properties']['placeholder'])) {
                        $field['placeholder'] = $element['properties']['placeholder'];
                    }
                    
                    if (isset($element['properties']['help_text'])) {
                        $field['help_text'] = $element['properties']['help_text'];
                    }
                    
                    if (isset($element['properties']['options']) && is_array($element['properties']['options'])) {
                        $options = [];
                        foreach ($element['properties']['options'] as $option) {
                            if (isset($option['value']) && isset($option['label'])) {
                                $options[$option['value']] = $option['label'];
                            }
                        }
                        if (!empty($options)) {
                            $field['options'] = $options;
                        }
                    }
                    
                    if (isset($element['properties']['is_system']) && $element['properties']['is_system']) {
                        $field['system'] = true;
                    }
                    
                    $schema[] = $field;
                }
            }
            
            if ($schemaType === 'setting_schema' || $schemaType === 'settings_schema') {
                $hasTitle = false;
                $hasUniqueId = false;
                
                foreach ($schema as $field) {
                    if ($field['name'] === 'title') $hasTitle = true;
                    if ($field['name'] === 'unique_id') $hasUniqueId = true;
                }
                
                if (!$hasTitle) {
                    array_unshift($schema, [
                        'name' => 'title',
                        'label' => 'Başlık',
                        'type' => 'text',
                        'required' => true,
                        'system' => true
                    ]);
                }
                
                if (!$hasUniqueId) {
                    $schema[] = [
                        'name' => 'unique_id',
                        'label' => 'Benzersiz ID',
                        'type' => 'text',
                        'required' => false,
                        'system' => true,
                        'hidden' => true
                    ];
                }
                
                $widget->settings_schema = $schema;
            } else {
                $hasTitle = false;
                $hasActive = false;
                $hasUniqueId = false;
                
                foreach ($schema as $field) {
                    if ($field['name'] === 'title') $hasTitle = true;
                    if ($field['name'] === 'is_active') $hasActive = true;
                    if ($field['name'] === 'unique_id') $hasUniqueId = true;
                }
                
                if (!$hasTitle) {
                    array_unshift($schema, [
                        'name' => 'title',
                        'label' => 'Başlık',
                        'type' => 'text',
                        'required' => true,
                        'system' => true
                    ]);
                }
                
                if (!$hasActive) {
                    $schema[] = [
                        'name' => 'is_active',
                        'label' => 'Aktif',
                        'type' => 'checkbox',
                        'required' => false,
                        'system' => true
                    ];
                }
                
                if (!$hasUniqueId) {
                    $schema[] = [
                        'name' => 'unique_id',
                        'label' => 'Benzersiz ID',
                        'type' => 'text',
                        'required' => false,
                        'system' => true,
                        'hidden' => true
                    ];
                }
                
                $widget->item_schema = $schema;
            }
            
            $widget->save();
            
            log_activity(
                $widget,
                'widget ' . ($schemaType === 'setting_schema' || $schemaType === 'settings_schema' ? 'ayar' : 'içerik') . ' form yapısı güncellendi'
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Widget form yapısı başarıyla kaydedildi.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Widget form yapısı kaydedilirken bir hata oluştu: ' . $e->getMessage()
            ]);
        }
    }
}