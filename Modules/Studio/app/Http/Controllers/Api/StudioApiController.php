<?php

namespace Modules\Studio\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Studio\App\Services\StudioWidgetService;
use Modules\Studio\App\Services\StudioThemeService;

class StudioApiController extends Controller
{
    protected $widgetService;
    protected $themeService;
    
    public function __construct(StudioWidgetService $widgetService, StudioThemeService $themeService)
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