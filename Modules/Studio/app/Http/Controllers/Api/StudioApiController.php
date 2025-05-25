<?php

namespace Modules\Studio\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Studio\App\Services\BlockService;
use Modules\Studio\App\Services\WidgetService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class StudioApiController extends Controller
{
    protected $widgetService;
    protected $blockService;
    
    public function __construct(WidgetService $widgetService, BlockService $blockService)
    {
        $this->widgetService = $widgetService;
        $this->blockService = $blockService;
    }
    
    /**
     * Widget'ları getir
     *
     * @return JsonResponse
     */
    public function getWidgets(): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'widgets' => $this->widgetService->getWidgetsAsBlocks(),
                'tenant_widgets' => $this->widgetService->getTenantWidgetsAsBlocks(),
                'categories' => $this->widgetService->getCategories()
            ]);
        } catch (\Exception $e) {
            Log::error('Widget verileri alınırken hata: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Widget verileri alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Blokları getir
     *
     * @return JsonResponse
     */
    public function getBlocks(): JsonResponse
    {
        try {
            $blocks = $this->blockService->getAllBlocks();
            
            return response()->json([
                'success' => true,
                'blocks' => $blocks,
                'categories' => $this->blockService->getCategories()
            ]);
        } catch (\Exception $e) {
            Log::error('Blok verileri alınırken hata: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Blok verileri alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Widget içeriğini getir
     *
     * @param int $tenantWidgetId
     * @return JsonResponse
     */
    public function getWidgetContent($tenantWidgetId): JsonResponse
    {
        try {
            $html = $this->widgetService->renderTenantWidget($tenantWidgetId);
            
            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        } catch (\Exception $e) {
            Log::error('Widget içeriği alınırken hata: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Widget içeriği alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Module widget içeriğini getir
     *
     * @param int $moduleId
     * @return JsonResponse
     */
    public function getModuleWidget($moduleId): JsonResponse
    {
        try {
            if (!class_exists('Modules\WidgetManagement\App\Models\Widget')) {
                return response()->json([
                    'success' => false,
                    'message' => 'WidgetManagement modülü bulunamadı'
                ], 404);
            }
            
            $widget = \Modules\WidgetManagement\App\Models\Widget::where('id', $moduleId)
                ->where('type', 'module')
                ->where('is_active', true)
                ->first();
            
            if (!$widget) {
                return response()->json([
                    'success' => false,
                    'message' => 'Module widget bulunamadı'
                ], 404);
            }
            
            $html = '';
            $css = $widget->content_css ?? '';
            $js = $widget->content_js ?? '';
            
            $defaultSettings = [
                'limit' => 5,
                'itemLimit' => 5,
                'portfolio_limit' => 5,
                'categorySlug' => null,
                'portfolio_category_slug' => null,
                'columns' => '3',
                'widget_title' => $widget->name,
                'order_direction' => 'desc',
                'category_id' => null,
                'project_id' => null,
                'project_slug' => null,
                'portfolio_slug' => null,
                'item_slug' => null,
                'portfolio_id' => null,
                'show_related' => false,
                'module_type' => 'project_list'
            ];
            
            if (!empty($widget->file_path)) {
                try {
                    $viewPath = 'widgetmanagement::blocks.' . $widget->file_path;
                    if (view()->exists($viewPath)) {
                        $html = view($viewPath, ['settings' => $defaultSettings])->render();
                    } else {
                        $html = '<div class="alert alert-warning">Görünüm bulunamadı: ' . $viewPath . '</div>';
                    }
                } catch (\Exception $e) {
                    $html = '<div class="alert alert-danger">Görünüm render hatası: ' . $e->getMessage() . '</div>';
                }
            } else {
                $html = $widget->content_html ?? '<div class="alert alert-info">Module widget: ' . $widget->name . '</div>';
            }
            
            return response()->json([
                'success' => true,
                'content_html' => $html,
                'content_css' => $css,
                'content_js' => $js,
                'widget_name' => $widget->name,
                'widget_type' => $widget->type,
                'file_path' => $widget->file_path
            ]);
        } catch (\Exception $e) {
            Log::error('Module widget içeriği alınırken hata: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Module widget içeriği alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }
}