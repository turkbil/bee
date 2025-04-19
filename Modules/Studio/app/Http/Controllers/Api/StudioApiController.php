<?php

namespace Modules\Studio\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Studio\App\Services\WidgetService;
use Modules\Studio\App\Services\StudioThemeService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StudioApiController extends Controller
{
    protected $widgetService;
    protected $themeService;
    
    public function __construct(WidgetService $widgetService, StudioThemeService $themeService)
    {
        $this->widgetService = $widgetService;
        $this->themeService = $themeService;
    }
    
    public function getWidgets()
    {
        try {
            $widgets = $this->widgetService->getWidgetsAsBlocks();
            $categories = $this->widgetService->getCategories();
            
            return response()->json([
                'success' => true,
                'widgets' => $widgets,
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            Log::error('Widget verileri alınırken hata: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Widget verileri alınırken hata: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getThemes()
    {
        try {
            $defaultTheme = $this->themeService->getDefaultTheme();
            $themeName = $defaultTheme ? $defaultTheme['folder_name'] : 'blank';
            
            return response()->json([
                'success' => true,
                'themes' => $this->themeService->getAllThemes(),
                'defaultTheme' => $defaultTheme,
                'templates' => $this->themeService->getHeaderFooterTemplates($themeName)
            ]);
        } catch (\Exception $e) {
            Log::error('Tema verileri alınırken hata: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Tema verileri alınırken hata: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function addWidget(Request $request)
    {
        $widgetId = $request->input('widget_id');
        
        try {
            $settings = $request->input('settings', []);
            $items = $request->input('items', []);
            
            $tenantWidget = $this->widgetService->createTenantWidget($widgetId, $settings, $items);
            
            if (!$tenantWidget) {
                return response()->json([
                    'success' => false,
                    'message' => 'Widget eklenemedi. Veritabanı hatası.'
                ], 500);
            }
            
            // Widget temel bilgilerini getir
            $widget = \Modules\WidgetManagement\App\Models\Widget::find($widgetId);
            
            $widgetData = [
                'id' => $tenantWidget->id,
                'widget_id' => $widgetId,
                'name' => $widget->name,
                'content_html' => $widget->content_html,
                'content_css' => $widget->content_css,
                'content_js' => $widget->content_js,
                'css_files' => $widget->css_files ?? [],
                'js_files' => $widget->js_files ?? [],
                'settings' => $tenantWidget->settings,
                'has_items' => $widget->has_items
            ];
            
            if ($widget->has_items) {
                $widgetData['items'] = $tenantWidget->items->map(function($item) {
                    return $item->content;
                })->toArray();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Widget başarıyla eklendi.',
                'widget' => $widgetData
            ]);
        } catch (\Exception $e) {
            Log::error('Widget eklenirken hata: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Widget eklenirken hata: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getWidgetContent($id)
    {
        try {
            if (class_exists('Modules\WidgetManagement\App\Models\Widget')) {
                $widget = \Modules\WidgetManagement\App\Models\Widget::find($id);
                
                if (!$widget) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Widget bulunamadı.'
                    ], 404);
                }
                
                return response()->json([
                    'success' => true,
                    'widget' => [
                        'id' => $widget->id,
                        'name' => $widget->name,
                        'type' => $widget->type,
                        'content_html' => $widget->content_html,
                        'content_css' => $widget->content_css,
                        'content_js' => $widget->content_js,
                        'css_files' => $widget->css_files ?? [],
                        'js_files' => $widget->js_files ?? [],
                        'has_items' => $widget->has_items,
                        'settings_schema' => $widget->settings_schema,
                        'item_schema' => $widget->item_schema
                    ]
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'WidgetManagement modülü bulunamadı.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Widget içeriği alınırken hata: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Widget içeriği alınırken hata: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function saveContent(Request $request)
    {
        $module = $request->input('module');
        $id = (int)$request->input('id');
        $content = $request->input('content');
        $css = $request->input('css');
        $js = $request->input('js');
        
        try {
            if ($module === 'page' && class_exists('Modules\Page\App\Models\Page')) {
                $page = \Modules\Page\App\Models\Page::findOrFail($id);
                $page->body = $content;
                $page->css = $css;
                $page->js = $js;
                $page->save();
                
                if (function_exists('log_activity')) {
                    log_activity($page, 'studio ile düzenlendi');
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Sayfa başarıyla kaydedildi.'
                ]);
            } elseif ($module === 'widget' && class_exists('Modules\WidgetManagement\App\Models\Widget')) {
                $result = $this->widgetService->updateWidgetContent($id, $content, $css, $js);
                
                return response()->json([
                    'success' => $result,
                    'message' => $result ? 'Widget başarıyla kaydedildi.' : 'Widget kaydedilemedi.'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Desteklenmeyen modül: ' . $module
            ], 400);
        } catch (\Exception $e) {
            Log::error('İçerik kaydedilirken hata: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'İçerik kaydedilirken hata: ' . $e->getMessage()
            ], 500);
        }
    }
}