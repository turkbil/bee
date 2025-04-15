<?php

namespace Modules\Studio\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Studio\App\Services\EditorService;
use Modules\Studio\App\Services\WidgetService;
use Modules\Studio\App\Services\AssetService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class StudioController extends Controller
{
    protected $editorService;
    protected $widgetService;
    protected $assetService;
    
    public function __construct(
        EditorService $editorService, 
        WidgetService $widgetService,
        AssetService $assetService
    )
    {
        $this->editorService = $editorService;
        $this->widgetService = $widgetService;
        $this->assetService = $assetService;
    }
    
    /**
     * İçerik kaydetme işlemi
     *
     * @param Request $request
     * @param string $module
     * @param int $id
     * @return JsonResponse
     */
    public function save(Request $request, string $module, int $id): JsonResponse
    {
        try {
            // Debug: Gelen request bilgilerini logla
            Log::debug("Studio Save - Request Details", [
                'module' => $module,
                'id' => $id,
                'content_size' => strlen($request->input('content', '')),
                'css_size' => strlen($request->input('css', '')),
                'js_size' => strlen($request->input('js', ''))
            ]);

            $content = $request->input('content');
            $css = $request->input('css');
            $js = $request->input('js');
            
            $result = $this->editorService->saveContent($module, $id, $content, $css, $js);
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'İçerik başarıyla kaydedildi.'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'İçerik kaydedilemedi.'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Studio içerik kaydederken hata: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Kaydetme sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Widgetları getir
     *
     * @return JsonResponse
     */
    public function getWidgets(): JsonResponse
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
     * Dosya yükleme işlemini gerçekleştirir
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadAssets(Request $request): JsonResponse
    {
        $result = ['success' => false];
        
        try {
            if ($request->hasFile('files')) {
                $files = $request->file('files');
                $uploadedFiles = [];
                
                foreach ($files as $file) {
                    if ($file->isValid()) {
                        $assetData = $this->assetService->uploadAsset($file);
                        $uploadedFiles[] = $assetData;
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
            
            // Partials klasörü oluştur
            $destPartialsPath = $destinationPath . '/partials';
            if (!file_exists($destPartialsPath)) {
                mkdir($destPartialsPath, 0755, true);
            }
            
            return redirect()->back()->with('success', 'Kaynaklar başarıyla kopyalandı');
        } catch (\Exception $e) {
            Log::error('Kaynakları kopyalama hatası: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Kaynaklar kopyalanırken hata oluştu: ' . $e->getMessage());
        }
    }
}