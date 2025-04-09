<?php

namespace Modules\Studio\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Studio\App\Services\StudioWidgetService;
use Modules\Studio\App\Services\StudioThemeService;
use Modules\Page\App\Models\Page;

class StudioController extends Controller
{
    protected $widgetService;
    protected $themeService;
    
    public function __construct(StudioWidgetService $widgetService, StudioThemeService $themeService)
    {
        $this->widgetService = $widgetService;
        $this->themeService = $themeService;
    }
    
    /**
     * İçerik kaydetme işlemi
     *
     * @param Request $request
     * @param string $module
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request, $module, $id)
    {
        $content = $request->get('content');
        $css = $request->get('css');
        $js = $request->get('js');
        
        if ($module === 'page') {
            $page = Page::findOrFail($id);
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
     * Dosya yükleme işlemini gerçekleştirir
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadAssets(Request $request)
    {
        $result = ['success' => false];
        
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $uploadedFiles = [];
            
            foreach ($files as $file) {
                if ($file->isValid()) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $mimeType = $file->getMimeType();
                    $size = $file->getSize();
                    
                    // Şu anki tenant için dosyayı yükle
                    $path = $file->storeAs(
                        'studio/assets', 
                        $fileName, 
                        'public'
                    );
                    
                    // Dosya bilgilerini ekle
                    $uploadedFiles[] = [
                        'name' => $fileName,
                        'type' => $mimeType,
                        'size' => $size,
                        'src' => asset('storage/' . $path)
                    ];
                }
            }
            
            if (count($uploadedFiles) > 0) {
                $result = [
                    'success' => true,
                    'data' => $uploadedFiles
                ];
            }
        }
        
        return response()->json($result);
    }

    /**
     * Widget sayfası
     *
     * @return \Illuminate\View\View
     */
    public function widgets()
    {
        return view('studio::admin.widgets', [
            'widgets' => $this->widgetService->getAllWidgets(),
            'categories' => $this->widgetService->getCategories()
        ]);
    }
}