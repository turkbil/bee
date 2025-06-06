<?php

namespace Modules\WidgetManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Models\WidgetItem;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Modules\WidgetManagement\app\Services\WidgetService;

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
    
    public function showInstance(WidgetService $widgetService, $tenantWidgetId)
    {
        try {
            WidgetService::resetAssets(); // Assetleri temizle

            $tenantWidget = TenantWidget::with(['widget', 'items'])->findOrFail($tenantWidgetId);
            $widget = $tenantWidget->widget;

            if (!$widget || !$widget->is_active) {
                return response()->view('widgetmanagement::widget.error', [
                    'message' => 'Widget bulunamadı veya aktif değil.'
                ], 404);
            }

            // Sunucu taraflı render için ayarlar
            $widgetService->setHandlebarsUsage(false);
            $widgetService->setCacheUsage(false); // Önizlemeler için cache kullanma

            // Widget'ı işle ve render et
            $renderedHtml = $widgetService->processWidget($tenantWidget);

            if (empty(trim($renderedHtml))) {
                return response()->view('widgetmanagement::widget.error', [
                    'message' => 'Rendered HTML is empty or whitespace.'
                ], 500);
            }

            // Statik assetleri temizle (her önizleme için yeniden oluşturulsun)
            // Bu, WidgetService içinde statik array'leri sıfırlayan bir metoda ihtiyaç duyabilir
            // Şimdilik bu adımı atlıyoruz, ancak idealde olmalı.
            // Alternatif olarak, WidgetService'i her istekte yeniden oluşturabiliriz.
            // Veya WidgetService'teki statik array'leri public static yapıp burada sıfırlayabiliriz.
            // En temizi WidgetService'e bir resetAssets() metodu eklemek olurdu.
            // Şimdilik, aynı widget tekrar tekrar önizlenirse assetlerin birikebileceğini not edelim.

            return view('widgetmanagement::widget.preview', [
                'widget' => $widget, // Orijinal widget bilgisi hala gerekebilir
                'tenantWidget' => $tenantWidget, // TenantWidget bilgisi de gerekebilir
                'renderedHtml' => $renderedHtml,
                'widgetServiceClass' => WidgetService::class // Statik metotlar için
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
        $context = [
            'widget' => []
        ];
        
        if ($widget->settings_schema) {
            foreach ($widget->settings_schema as $field) {
                if (!isset($field['name'])) continue;
                
                $fieldName = $field['name'];
                $defaultValue = $field['default'] ?? ($field['properties']['default_value'] ?? '');
                
                $context['widget'][$fieldName] = $defaultValue;
                $context[$fieldName] = $defaultValue;
                
                if (strpos($fieldName, 'widget_') === 0) {
                    $context[substr($fieldName, 7)] = $defaultValue;
                } else {
                    $context['widget_' . $fieldName] = $defaultValue;
                }
            }
        }
        
        $context['items'] = [];
        
        return $context;
    }
    
    private function buildInstanceContext($widget, $tenantWidget)
    {
        $context = [
            'widget' => []
        ];
        
        $settings = $tenantWidget->settings ?? [];
        
        foreach ($settings as $key => $value) {
            $context['widget'][$key] = $value;
            $context[$key] = $value;
            
            if (strpos($key, 'widget_') === 0) {
                $context[substr($key, 7)] = $value;
            } else {
                $context['widget_' . $key] = $value;
            }
        }
        
        if ($widget->settings_schema) {
            foreach ($widget->settings_schema as $field) {
                if (!isset($field['name'])) continue;
                
                $fieldName = $field['name'];
                if (!isset($context['widget'][$fieldName])) {
                    $defaultValue = $field['default'] ?? ($field['properties']['default_value'] ?? '');
                    $context['widget'][$fieldName] = $defaultValue;
                    $context[$fieldName] = $defaultValue;
                    
                    if (strpos($fieldName, 'widget_') === 0) {
                        $context[substr($fieldName, 7)] = $defaultValue;
                    } else {
                        $context['widget_' . $fieldName] = $defaultValue;
                    }
                }
            }
        }
        
        $items = [];
        
        if ($widget->has_items) {
            $widgetItems = WidgetItem::where('tenant_widget_id', $tenantWidget->id)
                ->orderBy('order')
                ->get();
            
            foreach ($widgetItems as $widgetItem) {
                if ($widgetItem->content['is_active'] ?? true) {
                    $content = $widgetItem->content;
                    
                    foreach ($content as $key => $value) {
                        if (is_string($value) && !empty($value) && !preg_match('/^https?:\/\//', $value)) {
                            if (strpos($key, 'image') !== false || strpos($key, 'photo') !== false || strpos($key, 'picture') !== false) {
                                $content[$key] = cdn($value);
                            }
                        }
                    }
                    
                    $items[] = $content;
                }
            }
        }
        
        $context['items'] = $items;
        
        return $context;
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