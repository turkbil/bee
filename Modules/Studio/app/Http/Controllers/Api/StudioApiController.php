<?php

namespace Modules\Studio\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Studio\App\Services\WidgetService;
use Modules\Studio\App\Services\StudioThemeService;

class StudioApiController extends Controller
{
    protected $widgetService;
    protected $themeService;
    
    public function __construct(WidgetService $widgetService, StudioThemeService $themeService)
    {
        $this->widgetService = $widgetService;
        $this->themeService = $themeService;
    }
    
    /**
     * Widgetları getir
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWidgets()
    {
        return response()->json([
            'widgets' => $this->widgetService->getWidgetsAsBlocks(),
            'categories' => $this->widgetService->getCategories()
        ]);
    }
    
    /**
     * Temaları getir
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getThemes()
    {
        $defaultTheme = $this->themeService->getDefaultTheme();
        $themeName = $defaultTheme ? $defaultTheme['folder_name'] : 'blank';
        
        return response()->json([
            'themes' => $this->themeService->getAllThemes(),
            'defaultTheme' => $defaultTheme,
            'templates' => $this->themeService->getHeaderFooterTemplates($themeName)
        ]);
    }
    
    /**
     * Widget ekle
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addWidget(Request $request)
    {
        $widgetId = $request->input('widget_id');
        
        try {
            if (class_exists('Modules\WidgetManagement\App\Models\Widget') && 
                class_exists('Modules\WidgetManagement\App\Models\TenantWidget')) {
                $widget = \Modules\WidgetManagement\App\Models\Widget::findOrFail($widgetId);
                
                // Tenant Widget oluştur
                $tenantWidget = new \Modules\WidgetManagement\App\Models\TenantWidget();
                $tenantWidget->widget_id = $widgetId;
                $tenantWidget->position = 'content';
                $tenantWidget->order = \Modules\WidgetManagement\App\Models\TenantWidget::max('order') + 1;
                $tenantWidget->settings = [
                    'unique_id' => (string) \Illuminate\Support\Str::uuid(),
                    'title' => $widget->name
                ];
                $tenantWidget->save();
                
                // Önbelleği temizle
                Cache::forget('studio_widgets_' . (function_exists('tenant_id') ? tenant_id() : 'default'));
                
                return response()->json([
                    'success' => true,
                    'message' => 'Widget başarıyla eklendi.',
                    'widget' => [
                        'id' => $tenantWidget->id,
                        'name' => $widget->name
                    ]
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'WidgetManagement modülü bulunamadı.'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hata: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * İçerik kaydetme
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveContent(Request $request)
    {
        $module = $request->input('module');
        $id = (int)$request->input('id'); // ID'yi integer'a çevir
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
                
                log_activity($page, 'studio ile düzenlendi');
                
                return response()->json([
                    'success' => true,
                    'message' => 'Sayfa başarıyla kaydedildi.'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Desteklenmeyen modül: ' . $module
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hata: ' . $e->getMessage()
            ], 500);
        }
    }
}