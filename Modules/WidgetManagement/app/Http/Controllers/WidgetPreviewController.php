<?php

namespace Modules\WidgetManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class WidgetPreviewController extends Controller
{
    public function showTemplate($widgetId)
    {
        try {
            $widget = Widget::findOrFail($widgetId);
            
            if ($widget->type === 'file') {
                return $this->handleFileWidget($widget);
            }
            
            if ($widget->type === 'module') {
                return $this->handleModuleWidget($widget);
            }
            
            $context = $this->buildTemplateContext($widget);
            
            return view('widgetmanagement::widget.preview', [
                'widget' => $widget,
                'context' => $context,
                'useHandlebars' => true
            ]);
            
        } catch (\Exception $e) {
            return response()->view('widgetmanagement::widget.error', [
                'message' => 'Widget şablonu yüklenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function showInstance($tenantWidgetId)
    {
        try {
            $tenantWidget = TenantWidget::with(['widget', 'items'])->findOrFail($tenantWidgetId);
            $widget = $tenantWidget->widget;
            
            if (!$widget || !$widget->is_active) {
                return response()->view('widgetmanagement::widget.error', [
                    'message' => 'Widget bulunamadı veya aktif değil.'
                ], 404);
            }
            
            if ($widget->type === 'file') {
                return $this->handleFileWidget($widget, $tenantWidget);
            }
            
            if ($widget->type === 'module') {
                return $this->handleModuleWidget($widget, $tenantWidget);
            }
            
            $context = $this->buildInstanceContext($widget, $tenantWidget);
            
            return view('widgetmanagement::widget.preview', [
                'widget' => $widget,
                'context' => $context,
                'useHandlebars' => true
            ]);
            
        } catch (\Exception $e) {
            return response()->view('widgetmanagement::widget.error', [
                'message' => 'Widget instance yüklenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function handleFileWidget($widget, $tenantWidget = null)
    {
        if (empty($widget->file_path)) {
            return response()->view('widgetmanagement::widget.error', [
                'message' => 'Bu dosya widget\'ı için dosya yolu tanımlanmamış.'
            ], 404);
        }
        
        $viewPath = 'widgetmanagement::blocks.' . $widget->file_path;
        
        if (!View::exists($viewPath)) {
            $fullPath = base_path() . '/Modules/WidgetManagement/resources/views/blocks/' . $widget->file_path . '.blade.php';
            
            if (!File::exists($fullPath)) {
                return response()->view('widgetmanagement::widget.error', [
                    'message' => 'Belirtilen dosya bulunamadı: ' . $viewPath . 
                        '<br><br>Dosya yolu: <code>Modules/WidgetManagement/resources/views/blocks/' . 
                        $widget->file_path . '.blade.php</code>'
                ], 404);
            }
        }
        
        $settings = $this->getFileWidgetSettings($widget, $tenantWidget);
        
        return view('widgetmanagement::widget.file-preview', [
            'widget' => $widget,
            'viewPath' => $viewPath,
            'settings' => $settings
        ]);
    }
    
    private function handleModuleWidget($widget, $tenantWidget = null)
    {
        if (empty($widget->file_path)) {
            return response()->view('widgetmanagement::widget.error', [
                'message' => 'Bu modül widget\'ı için dosya yolu tanımlanmamış.'
            ], 404);
        }
        
        $viewPath = 'widgetmanagement::blocks.' . $widget->file_path;
        
        if (!View::exists($viewPath)) {
            $fullPath = base_path() . '/Modules/WidgetManagement/resources/views/blocks/' . $widget->file_path . '.blade.php';
            
            if (!File::exists($fullPath)) {
                return response()->view('widgetmanagement::widget.error', [
                    'message' => 'Belirtilen modül dosyası bulunamadı: ' . $viewPath . 
                        '<br><br>Dosya yolu: <code>Modules/WidgetManagement/resources/views/blocks/' . 
                        $widget->file_path . '.blade.php</code>'
                ], 404);
            }
        }
        
        $settings = $this->getModuleWidgetSettings($widget, $tenantWidget);
        
        return view('widgetmanagement::widget.file-preview', [
            'widget' => $widget,
            'viewPath' => $viewPath,
            'settings' => $settings
        ]);
    }
    
    private function buildTemplateContext($widget)
    {
        $context = [];
        
        if ($widget->type === 'static') {
            $context = $this->getDefaultSettingsContext($widget);
        } elseif ($widget->type === 'dynamic') {
            $context = $this->getDefaultSettingsContext($widget);
            $context['items'] = $this->getDefaultItemsContext($widget);
        }
        
        return $context;
    }
    
    private function buildInstanceContext($widget, $tenantWidget)
    {
        $context = [];
        
        if ($widget->type === 'static') {
            $context = $this->getInstanceSettingsContext($widget, $tenantWidget);
        } elseif ($widget->type === 'dynamic') {
            $context = $this->getInstanceSettingsContext($widget, $tenantWidget);
            $context['items'] = $this->getInstanceItemsContext($widget, $tenantWidget);
        }
        
        return $context;
    }
    
    private function getDefaultSettingsContext($widget)
    {
        $context = [
            'widget' => [
                'title' => $widget->name,
                'unique_id' => Str::random(),
            ]
        ];
        
        if (!empty($widget->settings_schema) && is_array($widget->settings_schema)) {
            foreach ($widget->settings_schema as $schema) {
                if (!isset($schema['name'])) continue;
                
                $key = $schema['name'];
                if (isset($schema['default'])) {
                    $context['widget'][$key] = $schema['default'];
                } elseif (isset($schema['properties']['default_value'])) {
                    $context['widget'][$key] = $schema['properties']['default_value'];
                } else {
                    switch ($schema['type']) {
                        case 'checkbox':
                        case 'switch':
                            $context['widget'][$key] = false;
                            break;
                        case 'number':
                            $context['widget'][$key] = 0;
                            break;
                        default:
                            $context['widget'][$key] = '';
                            break;
                    }
                }
                
                if (strpos($key, 'widget.') !== 0) {
                    $context[$key] = $context['widget'][$key];
                }
            }
        }
        
        return $context;
    }
    
    private function getInstanceSettingsContext($widget, $tenantWidget)
    {
        $context = [
            'widget' => [
                'title' => $tenantWidget->settings['title'] ?? $widget->name,
                'unique_id' => $tenantWidget->settings['unique_id'] ?? Str::random(),
            ]
        ];
        
        $tenantSettings = $tenantWidget->settings ?? [];
        
        if (!empty($widget->settings_schema) && is_array($widget->settings_schema)) {
            foreach ($widget->settings_schema as $schema) {
                if (!isset($schema['name'])) continue;
                
                $key = $schema['name'];
                if (array_key_exists($key, $tenantSettings)) {
                    $context['widget'][$key] = $tenantSettings[$key];
                } elseif (isset($schema['default'])) {
                    $context['widget'][$key] = $schema['default'];
                } elseif (isset($schema['properties']['default_value'])) {
                    $context['widget'][$key] = $schema['properties']['default_value'];
                } else {
                    switch ($schema['type']) {
                        case 'checkbox':
                        case 'switch':
                            $context['widget'][$key] = false;
                            break;
                        case 'number':
                            $context['widget'][$key] = 0;
                            break;
                        default:
                            $context['widget'][$key] = '';
                            break;
                    }
                }
                
                if (strpos($key, 'widget.') !== 0) {
                    $context[$key] = $context['widget'][$key];
                }
            }
        }
        
        return $context;
    }
    
    private function getDefaultItemsContext($widget)
    {
        if (!$widget->has_items || empty($widget->item_schema)) {
            return [];
        }
        
        $defaultItem = [];
        foreach ($widget->item_schema as $schema) {
            if (!isset($schema['name'])) continue;
            
            $key = $schema['name'];
            if (isset($schema['default']) && $schema['default'] !== '') {
                $value = $schema['default'];
            } else {
                if ($key === 'image') {
                    $value = 'https://placehold.co/800x400?text=Placeholder';
                } elseif ($key === 'title') {
                    $value = 'Örnek Başlık';
                } elseif ($key === 'description') {
                    $value = 'Bu bir örnek açıklama metnidir.';
                } else {
                    $value = $schema['label'] ?? ucfirst(str_replace('_', ' ', $key));
                }
            }
            $defaultItem[$key] = $value;
        }
        
        $defaultItem['is_active'] = true;
        $defaultItem['unique_id'] = Str::uuid()->toString();
        
        return [$defaultItem];
    }
    
    private function getInstanceItemsContext($widget, $tenantWidget)
    {
        if (!$widget->has_items) {
            return [];
        }
        
        $itemsData = $tenantWidget->items
            ->filter(function ($item) {
                return !isset($item->content['is_active']) || $item->content['is_active'] === true;
            })
            ->map(function ($item) {
                $content = $item->content;
                if (isset($content['image']) && !preg_match('/^https?:\/\//', $content['image'])) {
                    $content['image'] = cdn($content['image']);
                }
                return $content;
            })->toArray();
        
        if (empty($itemsData)) {
            return $this->getDefaultItemsContext($widget);
        }
        
        return $itemsData;
    }
    
    private function getFileWidgetSettings($widget, $tenantWidget = null)
    {
        $settings = [
            'title' => $widget->name,
            'unique_id' => Str::random(),
            'show_description' => true
        ];
        
        if ($tenantWidget && !empty($tenantWidget->settings)) {
            $settings = array_merge($settings, $tenantWidget->settings);
        }
        
        if (!empty($widget->settings_schema) && is_array($widget->settings_schema)) {
            foreach ($widget->settings_schema as $schema) {
                if (!isset($schema['name'])) continue;
                
                $key = $schema['name'];
                if (!isset($settings[$key])) {
                    if (isset($schema['default'])) {
                        $settings[$key] = $schema['default'];
                    } elseif (isset($schema['properties']['default_value'])) {
                        $settings[$key] = $schema['properties']['default_value'];
                    }
                }
            }
        }
        
        return $settings;
    }
    
    private function getModuleWidgetSettings($widget, $tenantWidget = null)
    {
        $settings = [
            'title' => $widget->name,
            'unique_id' => Str::random(),
            'show_description' => true
        ];
        
        if ($tenantWidget && !empty($tenantWidget->settings)) {
            $settings = array_merge($settings, $tenantWidget->settings);
        }
        
        if (!empty($widget->settings_schema) && is_array($widget->settings_schema)) {
            foreach ($widget->settings_schema as $schema) {
                if (!isset($schema['name'])) continue;
                
                $key = $schema['name'];
                if (!isset($settings[$key])) {
                    if (isset($schema['default'])) {
                        $settings[$key] = $schema['default'];
                    } elseif (isset($schema['properties']['default_value'])) {
                        $settings[$key] = $schema['properties']['default_value'];
                    }
                }
            }
        }
        
        return $settings;
    }
    
    public function embed($tenantWidgetId)
    {
        try {
            $tenantWidget = TenantWidget::with('items')->findOrFail($tenantWidgetId);
            $widget = $tenantWidget->widget;
            $context = $this->buildInstanceContext($widget, $tenantWidget);
            
            return view('widgetmanagement::widget.embed', [
                'widget' => $widget,
                'context' => $context,
                'tenantWidgetId' => $tenantWidgetId,
                'useHandlebars' => true
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function embedJson($tenantWidgetId)
    {
        try {
            $tenantWidget = TenantWidget::with('items')->findOrFail($tenantWidgetId);
            $widget = $tenantWidget->widget;
            $context = $this->buildInstanceContext($widget, $tenantWidget);
            
            return response()->json([
                'content_html' => $widget->content_html,
                'context'      => $context,
                'content_css'  => $widget->content_css ?? '',
                'content_js'   => $widget->content_js ?? '',
                'useHandlebars'=> true
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function moduleJson($moduleId)
    {
        try {
            $widget = Widget::findOrFail($moduleId);
            
            if ($widget->type !== 'module') {
                return response()->json(['error' => 'Bu widget bir module tipi değil.'], 400);
            }
            
            $viewPath = 'widgetmanagement::blocks.' . $widget->file_path;
            
            if (!View::exists($viewPath)) {
                return response()->json([
                    'error' => 'Belirtilen modül dosyası bulunamadı: ' . $viewPath,
                    'html' => '<div class="alert alert-danger">Modül dosyası bulunamadı: ' . $viewPath . '</div>'
                ], 404);
            }
            
            $settings = [
                'title' => $widget->name,
                'unique_id' => Str::random(),
                'show_description' => true
            ];
            
            if (!empty($widget->settings_schema) && is_array($widget->settings_schema)) {
                $settings = array_merge($settings, $widget->settings_schema);
            }
            
            try {
                $html = view($viewPath, ['settings' => $settings])->render();
                
                return response()->json([
                    'success' => true, 
                    'html' => $html,
                    'content_html' => $html,
                    'css' => $widget->content_css ?? '',
                    'js' => $widget->content_js ?? '',
                    'context' => $settings,
                    'useHandlebars' => false,
                    'widget_id' => $widget->id,
                    'widget_name' => $widget->name,
                    'widget_type' => 'module',
                    'file_path' => $widget->file_path
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Modül render hatası: ' . $e->getMessage(),
                    'html' => '<div class="alert alert-danger">Modül render hatası: ' . $e->getMessage() . '</div>'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Modül widget yüklenirken bir hata oluştu: ' . $e->getMessage(),
                'html' => '<div class="alert alert-danger">Modül widget yüklenemedi: ' . $e->getMessage() . '</div>'
            ], 500);
        }
    }
}