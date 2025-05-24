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
            
            if ($schemaType === 'settings') {
                $schemaData = $widget->getSettingsSchema();
                $title = $widget->name . ' Ayarları';
            } elseif ($schemaType === 'items') {
                $schemaData = $widget->getItemSchema();
                $title = $widget->name . ' İçerik Yapısı';
            }
            
            if (is_null($schemaData)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Schema data not found',
                    'layout' => [
                        'title' => 'Widget Form Yapısı',
                        'elements' => []
                    ]
                ], 200);
            }
            
            $elements = [];
            
            if (!empty($schemaData)) {
                $elements = $this->parseSchemaToElements($schemaData);
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
            
            if ($schemaType === 'settings') {
                $schema = $this->ensureRequiredSettingsFields($schema);
                $widget->settings_schema = $schema;
            } elseif ($schemaType === 'items') {
                $schema = $this->ensureRequiredItemFields($schema);
                $widget->item_schema = $schema;
            }
            
            $widget->save();
            
            log_activity(
                $widget,
                'widget ' . ($schemaType === 'settings' ? 'ayar' : 'içerik') . ' form yapısı güncellendi'
            );
            
            return response()->json(['success' => true, 'message' => 'Form yapısı kaydedildi']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    
    private function ensureRequiredSettingsFields($schema)
    {
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
                'label' => 'Widget Başlığı',
                'type' => 'text',
                'required' => true,
                'system' => true,
                'protected' => true
            ]);
        }
        
        if (!$hasUniqueId) {
            $schema[] = [
                'name' => 'unique_id',
                'label' => 'Benzersiz ID',
                'type' => 'text',
                'required' => false,
                'system' => true,
                'hidden' => true,
                'protected' => true
            ];
        }
        
        return $schema;
    }
    
    private function ensureRequiredItemFields($schema)
    {
        $hasTitle = false;
        $hasActive = false;
        
        foreach ($schema as $field) {
            if (isset($field['name'])) {
                if ($field['name'] === 'title') $hasTitle = true;
                if ($field['name'] === 'is_active') $hasActive = true;
            }
        }
        
        if (!$hasTitle) {
            array_unshift($schema, [
                'name' => 'title',
                'label' => 'Başlık',
                'type' => 'text',
                'required' => true,
                'system' => true,
                'protected' => true
            ]);
        }
        
        if (!$hasActive) {
            $schema[] = [
                'name' => 'is_active',
                'label' => 'Durum',
                'type' => 'switch',
                'required' => false,
                'system' => true,
                'protected' => true,
                'properties' => [
                    'active_label' => 'Aktif',
                    'inactive_label' => 'Aktif Değil',
                    'default_value' => true
                ]
            ];
        }
        
        return $schema;
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
        if (!isset($field['type'])) {
            return null;
        }
        
        if ($field['type'] === 'row') {
            return [
                'type' => 'row',
                'properties' => []
            ];
        }
        
        if ($field['type'] === 'card') {
            $element = [
                'type' => 'card',
                'label' => $field['label'] ?? 'Kart',
                'properties' => [
                    'title' => $field['label'] ?? ($field['properties']['title'] ?? 'Kart'),
                    'content' => $field['properties']['content'] ?? null
                ]
            ];
            
            if (isset($field['elements']) && is_array($field['elements'])) {
                $element['elements'] = [];
                foreach ($field['elements'] as $childField) {
                    $childElement = $this->createElementFromField($childField);
                    if ($childElement) {
                        $element['elements'][] = $childElement;
                    }
                }
            }
            
            return $element;
        }
        
        if ($field['type'] === 'tab_group') {
            $element = [
                'type' => 'tab_group',
                'label' => $field['label'] ?? 'Sekme Grubu',
                'properties' => [
                    'tabs' => []
                ]
            ];
            
            if (isset($field['properties']['tabs']) && is_array($field['properties']['tabs'])) {
                foreach ($field['properties']['tabs'] as $tab) {
                    $tabData = [
                        'title' => $tab['title'] ?? 'Sekme',
                        'elements' => []
                    ];
                    
                    if (isset($tab['icon'])) {
                        $tabData['icon'] = $tab['icon'];
                    }
                    
                    if (isset($tab['content'])) {
                        $tabData['content'] = $tab['content'];
                    }
                    
                    if (isset($tab['elements']) && is_array($tab['elements'])) {
                        foreach ($tab['elements'] as $childField) {
                            $childElement = $this->createElementFromField($childField);
                            if ($childElement) {
                                $tabData['elements'][] = $childElement;
                            }
                        }
                    }
                    
                    $element['properties']['tabs'][] = $tabData;
                }
            } else {
                $element['properties']['tabs'][] = [
                    'title' => 'Varsayılan Sekme',
                    'elements' => []
                ];
            }
            
            return $element;
        }

        $element = [
            'type' => $field['type'],
            'properties' => [
                'name' => $field['name'] ?? '',
                'label' => $field['label'] ?? '',
                'required' => $field['required'] ?? false,
                'is_active' => true,
                'is_system' => $field['system'] ?? false,
                'is_protected' => $field['protected'] ?? false,
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
        
        if (isset($field['properties']) && is_array($field['properties'])) {
            foreach ($field['properties'] as $key => $value) {
                $element['properties'][$key] = $value;
            }
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

        if ($element['type'] === 'card') {
            $field = [
                'type' => 'card',
                'label' => $element['label'] ?? 'Kart',
                'properties' => []
            ];
            
            if (isset($element['properties']) && is_array($element['properties'])) {
                $field['properties'] = $element['properties'];
                
                if (isset($element['properties']['title']) && !empty($element['properties']['title'])) {
                    $field['label'] = $element['properties']['title'];
                }
            }
            
            if (isset($element['elements']) && is_array($element['elements'])) {
                $field['elements'] = [];
                foreach ($element['elements'] as $childElement) {
                    $childField = $this->createFieldFromElement($childElement);
                    if ($childField) {
                        $field['elements'][] = $childField;
                    }
                }
            }
            
            return $field;
        }
        
        if ($element['type'] === 'tab_group') {
            $field = [
                'type' => 'tab_group',
                'label' => $element['label'] ?? 'Sekme Grubu',
                'properties' => []
            ];
            
            if (isset($element['properties']) && is_array($element['properties'])) {
                $field['properties'] = $element['properties'];
                
                if (isset($field['properties']['tabs']) && is_array($field['properties']['tabs'])) {
                    foreach ($field['properties']['tabs'] as $tabIndex => $tab) {
                        if (isset($tab['elements']) && is_array($tab['elements'])) {
                            $tabElements = [];
                            foreach ($tab['elements'] as $childElement) {
                                $childField = $this->createFieldFromElement($childElement);
                                if ($childField) {
                                    $tabElements[] = $childField;
                                }
                            }
                            $field['properties']['tabs'][$tabIndex]['elements'] = $tabElements;
                        }
                    }
                }
            }
            
            return $field;
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
        
        if (isset($properties['active_label']) || isset($properties['inactive_label'])) {
            $field['properties'] = [];
            if (isset($properties['active_label'])) {
                $field['properties']['active_label'] = $properties['active_label'];
            }
            if (isset($properties['inactive_label'])) {
                $field['properties']['inactive_label'] = $properties['inactive_label'];
            }
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
        
        if (isset($properties['is_protected']) && $properties['is_protected']) {
            $field['protected'] = true;
        }

        return $field;
    }
}