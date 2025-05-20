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
}