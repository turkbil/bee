<?php

namespace Modules\Studio\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Studio\App\Services\WidgetService;
use Illuminate\Support\Facades\Log;

class StudioApiController extends Controller
{
    protected $widgetService;
    
    public function __construct(WidgetService $widgetService)
    {
        $this->widgetService = $widgetService;
    }
    
    /**
     * Widget'ları getir
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWidgets()
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
     * Widget içeriğini getir
     *
     * @param int $tenantWidgetId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWidgetContent($tenantWidgetId)
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
}