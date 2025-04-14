<?php

namespace Modules\Studio\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Studio\App\Services\StudioManagerService;
use Modules\Studio\App\Services\StudioContentParserService;
use Modules\Studio\App\Services\StudioWidgetService;
use Modules\Studio\App\Services\StudioThemeService;

class StudioApiController extends Controller
{
    protected $managerService;
    protected $contentParserService;
    protected $widgetService;
    protected $themeService;
    
    public function __construct(
        StudioManagerService $managerService,
        StudioContentParserService $contentParserService,
        StudioWidgetService $widgetService,
        StudioThemeService $themeService
    ) {
        $this->managerService = $managerService;
        $this->contentParserService = $contentParserService;
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
        try {
            return response()->json([
                'success' => true,
                'widgets' => $this->widgetService->getWidgetsAsBlocks(),
                'categories' => $this->widgetService->getCategories()
            ]);
        } catch (\Exception $e) {
            Log::error('API: Widget verileri alınırken hata: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Widget verileri alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Temaları getir
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getThemes()
    {
        try {
            $themes = $this->themeService->getAllThemes();
            $defaultTheme = $this->themeService->getDefaultTheme();
            $themeName = $defaultTheme ? $defaultTheme['folder_name'] : 'default';
            $templates = $this->themeService->getTemplatesForTheme($themeName);
            
            return response()->json([
                'success' => true,
                'themes' => $themes,
                'defaultTheme' => $defaultTheme,
                'templates' => $templates
            ]);
        } catch (\Exception $e) {
            Log::error('API: Tema verileri alınırken hata: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Tema verileri alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * İçerik kaydetme API
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveContent(Request $request)
    {
        try {
            // Gelen verileri doğrula
            $validated = $request->validate([
                'module' => 'required|string',
                'id' => 'required|integer',
                'content' => 'required|string',
                'css' => 'nullable|string',
                'js' => 'nullable|string',
                'theme' => 'nullable|string',
                'header_template' => 'nullable|string',
                'footer_template' => 'nullable|string',
                'settings' => 'nullable|array',
            ]);
            
            $module = $validated['module'];
            $id = (int)$validated['id'];
            
            // Modül türüne göre kaydetme işlemi
            switch ($module) {
                case 'page':
                    if (!class_exists('Modules\Page\App\Models\Page')) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Page modülü bulunamadı veya yüklenmedi.'
                        ], 404);
                    }
                    
                    $page = \Modules\Page\App\Models\Page::findOrFail($id);
                    
                    // İçeriği hazırla
                    $preparedContent = $this->contentParserService->prepareContentForSave(
                        $validated['content'],
                        $validated['css'] ?? '',
                        $validated['js'] ?? ''
                    );
                    
                    // İçerikleri güncelle
                    $page->body = $preparedContent['html'];
                    $page->css = $preparedContent['css'];
                    $page->js = $preparedContent['js'];
                    $page->save();
                    
                    // Tema ayarlarını kaydet (varsa)
                    if (isset($validated['theme']) || isset($validated['header_template']) || 
                        isset($validated['footer_template']) || isset($validated['settings'])) {
                        
                        $settingsData = [
                            'theme' => $validated['theme'] ?? null,
                            'header_template' => $validated['header_template'] ?? null,
                            'footer_template' => $validated['footer_template'] ?? null,
                            'settings' => $validated['settings'] ?? [],
                        ];
                        
                        $this->managerService->saveModuleSettings($module, $id, $settingsData);
                    }
                    
                    // Aktivite kaydı
                    if (function_exists('activity')) {
                        activity()
                            ->performedOn($page)
                            ->withProperties(['studio' => true, 'api' => true])
                            ->log('studio ile düzenlendi (API)');
                    }
                    
                    // İçerik kaydedilme olayını tetikle
                    event(new \Modules\Studio\Events\StudioContentSaved($module, $id, [
                        'title' => $page->title,
                        'content_length' => strlen($page->body),
                        'css_length' => strlen($page->css),
                        'js_length' => strlen($page->js),
                        'api' => true
                    ]));
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Sayfa başarıyla kaydedildi.',
                        'data' => [
                            'id' => $page->id,
                            'title' => $page->title
                        ]
                    ]);
                    
                // Diğer modüller için eklemeler burada yapılabilir
                
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Desteklenmeyen modül: ' . $module
                    ], 400);
            }
        } catch (\Exception $e) {
            Log::error('API: İçerik kaydederken hata: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'İçerik kaydedilirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Widget içeriğini getir
     *
     * @param int $widgetId Widget ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWidgetContent(int $widgetId)
    {
        try {
            $content = $this->widgetService->getWidgetContent($widgetId);
            
            if (!$content) {
                return response()->json([
                    'success' => false,
                    'message' => 'Widget bulunamadı.'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $content
            ]);
        } catch (\Exception $e) {
            Log::error('API: Widget içeriği alınırken hata: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Widget içeriği alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Module ayarlarını getir
     *
     * @param string $module Modül adı
     * @param int $id İçerik ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSettings(string $module, int $id)
    {
        try {
            $settings = $this->managerService->getModuleSettings($module, $id);
            
            return response()->json([
                'success' => true,
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            Log::error('API: Ayarlar alınırken hata: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Ayarlar alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Module ayarlarını kaydet
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveSettings(Request $request)
    {
        try {
            $validated = $request->validate([
                'module' => 'required|string',
                'id' => 'required|integer',
                'theme' => 'nullable|string',
                'header_template' => 'nullable|string',
                'footer_template' => 'nullable|string',
                'settings' => 'nullable|array',
            ]);
            
            $module = $validated['module'];
            $id = (int)$validated['id'];
            
            $settingsData = [
                'theme' => $validated['theme'] ?? null,
                'header_template' => $validated['header_template'] ?? null,
                'footer_template' => $validated['footer_template'] ?? null,
                'settings' => $validated['settings'] ?? [],
            ];
            
            $result = $this->managerService->saveModuleSettings($module, $id, $settingsData);
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ayarlar başarıyla kaydedildi.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Ayarlar kaydedilemedi.'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('API: Ayarlar kaydedilirken hata: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Ayarlar kaydedilirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}