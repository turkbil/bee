<?php

namespace Modules\WidgetManagement\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\WidgetManagement\App\Models\Widget;
use Illuminate\Support\Str;

class WidgetFormBuilderController extends Controller
{
    public function load($widgetId, $schemaType, Request $request)
    {
        try {
            $widget = Widget::findOrFail($widgetId);
            
            $schemaData = null;
            
            if ($schemaType === 'settings_schema') {
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
                    $elements = $this->parseSchemaToElements($schema);
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
    
    public function save(Request $request, $widgetId, $schemaType)
    {
        try {
            $widget = Widget::findOrFail($widgetId);
            
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
                $schema = $this->parseElementsToSchema($formData['elements']);
            }
            
            if ($schemaType === 'settings_schema') {
                $hasTitle = false;
                $hasUniqueId = false;
                
                foreach ($schema as $field) {
                    if (isset($field['name'])) {
                        if ($field['name'] === 'title') $hasTitle = true;
                        if ($field['name'] === 'unique_id') $hasUniqueId = true;
                    }
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
                    if (isset($field['name'])) {
                        if ($field['name'] === 'title') $hasTitle = true;
                        if ($field['name'] === 'is_active') $hasActive = true;
                        if ($field['name'] === 'unique_id') $hasUniqueId = true;
                    }
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
                'widget ' . ($schemaType === 'settings_schema' ? 'ayar' : 'içerik') . ' form yapısı güncellendi'
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

    private function parseSchemaToElements($schema)
    {
        $elements = [];
        
        foreach ($schema as $field) {
            if (isset($field['hidden']) && $field['hidden']) {
                continue;
            }

            if (isset($field['type']) && $field['type'] === 'row' && isset($field['columns'])) {
                $element = [
                    'type' => 'row',
                    'properties' => []
                ];

                $columnData = [];
                foreach ($field['columns'] as $column) {
                    $columnItem = [
                        'width' => $column['width'] ?? 6,
                        'elements' => []
                    ];

                    if (isset($column['elements']) && is_array($column['elements'])) {
                        foreach ($column['elements'] as $columnField) {
                            $columnElement = $this->createElementFromField($columnField);
                            if ($columnElement) {
                                $columnItem['elements'][] = $columnElement;
                            }
                        }
                    }

                    $columnData[] = $columnItem;
                }
                
                $element['columns'] = $columnData;
                $elements[] = $element;
            } else {
                $element = $this->createElementFromField($field);
                if ($element) {
                    $elements[] = $element;
                }
            }
        }
        
        return $elements;
    }

    private function createElementFromField($field)
    {
        // Temel alan kontrolleri
        if (!isset($field['type'])) {
            return null;
        }
        
        // Satır tipi elementler için özel işlem
        if ($field['type'] === 'row') {
            return [
                'type' => 'row',
                'properties' => []
            ];
        }

        $element = [
            'type' => $field['type'],
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

        return $element;
    }

    private function parseElementsToSchema($elements)
    {
        $schema = [];
        
        foreach ($elements as $element) {
            if (!isset($element['type'])) {
                continue;
            }

            if ($element['type'] === 'row' && isset($element['columns'])) {
                $rowField = [
                    'type' => 'row',
                    'columns' => []
                ];

                foreach ($element['columns'] as $column) {
                    $columnData = [
                        'width' => $column['width'] ?? 6,
                        'elements' => []
                    ];

                    if (isset($column['elements']) && is_array($column['elements'])) {
                        foreach ($column['elements'] as $columnElement) {
                            $columnField = $this->createFieldFromElement($columnElement);
                            if ($columnField) {
                                $columnData['elements'][] = $columnField;
                            }
                        }
                    }

                    $rowField['columns'][] = $columnData;
                }

                $schema[] = $rowField;
            } else {
                $field = $this->createFieldFromElement($element);
                if ($field) {
                    $schema[] = $field;
                }
            }
        }
        
        return $schema;
    }

    private function createFieldFromElement($element)
    {
        if (!isset($element['type'])) {
            return null;
        }

        $properties = $element['properties'] ?? [];
        
        $field = [
            'name' => $properties['name'] ?? '',
            'label' => $properties['label'] ?? '',
            'type' => $element['type'],
            'required' => $properties['required'] ?? false
        ];
        
        if (isset($properties['default_value'])) {
            $field['default'] = $properties['default_value'];
        }
        
        if (isset($properties['placeholder'])) {
            $field['placeholder'] = $properties['placeholder'];
        }
        
        if (isset($properties['help_text'])) {
            $field['help_text'] = $properties['help_text'];
        }
        
        if (isset($properties['options']) && is_array($properties['options'])) {
            $options = [];
            foreach ($properties['options'] as $option) {
                if (isset($option['value']) && isset($option['label'])) {
                    $options[$option['value']] = $option['label'];
                }
            }
            if (!empty($options)) {
                $field['options'] = $options;
            }
        }
        
        if (isset($properties['is_system']) && $properties['is_system']) {
            $field['system'] = true;
        }

        return $field;
    }
}