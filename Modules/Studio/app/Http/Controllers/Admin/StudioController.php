<?php

namespace Modules\Studio\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Studio\App\Services\StudioWidgetService;
use Modules\Studio\App\Services\StudioThemeService;
use Modules\Page\App\Models\Page;
use Illuminate\Support\Facades\Log;

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
        try {
            $content = $request->get('content');
            $css = $request->get('css');
            $js = $request->get('js');
            
            if ($module === 'page') {
                $page = Page::findOrFail($id);
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
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Desteklenmeyen modül: ' . $module
            ], 400);
        } catch (\Exception $e) {
            Log::error('Studio içerik kaydederken hata: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Kaydetme sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
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
                'widgets' => $this->widgetService->getWidgetsAsBlocks(),
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
     * Temaları getir
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getThemes()
    {
        try {
            $defaultTheme = $this->themeService->getDefaultTheme();
            $themeName = $defaultTheme ? $defaultTheme['folder_name'] : 'blank';
            
            return response()->json([
                'themes' => $this->themeService->getAllThemes(),
                'defaultTheme' => $defaultTheme,
                'templates' => $this->themeService->getHeaderFooterTemplates($themeName)
            ]);
        } catch (\Exception $e) {
            Log::error('Tema verileri alınırken hata: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Tema verileri alınamadı: ' . $e->getMessage()
            ], 500);
        }
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
        
        try {
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
                } else {
                    $result = [
                        'success' => false,
                        'message' => 'Dosyalar yüklenemedi. Geçerli dosya bulunamadı.'
                    ];
                }
            } else {
                $result = [
                    'success' => false,
                    'message' => 'Lütfen bir dosya seçin.'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Dosya yükleme hatası: ' . $e->getMessage());
            $result = [
                'success' => false,
                'message' => 'Dosya yükleme hatası: ' . $e->getMessage()
            ];
        }
        
        return response()->json($result);
    }

    /**
     * Statik kaynakları kopyala
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function publishResources()
    {
        try {
            // grapes.js ve diğer dosyaları public klasörüne kopyala
            $sourcePath = module_path('Studio', 'resources/assets');
            $destinationPath = public_path('admin/libs/studio');
            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            // Css klasörü
            $sourceCssPath = $sourcePath . '/css';
            $destCssPath = $destinationPath . '/css';
            
            if (!file_exists($destCssPath)) {
                mkdir($destCssPath, 0755, true);
            }
            
            // Css dosyalarını kopyala
            if (is_dir($sourceCssPath)) {
                foreach (glob($sourceCssPath . '/*.css') as $file) {
                    copy($file, $destCssPath . '/' . basename($file));
                }
            }
            
            // JS klasörü
            $sourceJsPath = $sourcePath . '/js';
            $destJsPath = $destinationPath;
            
            if (!file_exists($destJsPath)) {
                mkdir($destJsPath, 0755, true);
            }
            
            // JS dosyalarını kopyala
            if (is_dir($sourceJsPath)) {
                foreach (glob($sourceJsPath . '/*.js') as $file) {
                    copy($file, $destJsPath . '/' . basename($file));
                }
            }
            
            // Plugins klasörü
            $sourcePluginsPath = $sourcePath . '/js/plugins';
            $destPluginsPath = $destinationPath . '/plugins';
            
            if (!file_exists($destPluginsPath)) {
                mkdir($destPluginsPath, 0755, true);
            }
            
            // Plugin dosyalarını kopyala
            if (is_dir($sourcePluginsPath)) {
                foreach (glob($sourcePluginsPath . '/*.js') as $file) {
                    copy($file, $destPluginsPath . '/' . basename($file));
                }
            }
            
            return redirect()->back()->with('success', 'Kaynaklar başarıyla kopyalandı');
        } catch (\Exception $e) {
            Log::error('Kaynakları kopyalama hatası: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Kaynaklar kopyalanırken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Widget sayfası
     *
     * @return \Illuminate\View\View
     */
    public function widgets()
    {
        try {
            return view('studio::admin.widgets', [
                'widgets' => $this->widgetService->getAllWidgets(),
                'categories' => $this->widgetService->getCategories()
            ]);
        } catch (\Exception $e) {
            Log::error('Widget sayfası yüklenirken hata: ' . $e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Widget sayfası yüklenirken hata oluştu.');
        }
    }
}