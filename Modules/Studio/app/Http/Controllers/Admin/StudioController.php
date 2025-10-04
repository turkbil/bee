<?php

namespace Modules\Studio\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Studio\App\Services\EditorService;
use Modules\Studio\App\Services\WidgetService;
use Modules\Studio\App\Services\AssetService;
use Modules\Studio\App\Services\BlockService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class StudioController extends Controller
{
    protected $editorService;
    protected $widgetService;
    protected $assetService;
    protected $blockService;
    
    public function __construct(
        EditorService $editorService,
        WidgetService $widgetService,
        AssetService $assetService,
        BlockService $blockService
    )
    {
        $this->editorService = $editorService;
        $this->widgetService = $widgetService;
        $this->assetService = $assetService;
        $this->blockService = $blockService;
    }

    /**
     * Studio ana sayfası
     */
    public function index()
    {
        return view('studio::admin.index');
    }

    /**
     * Studio editor sayfası
     */
    public function editor(string $module, int $id, ?string $locale = null)
    {
        return view('studio::admin.editor', compact('module', 'id', 'locale'));
    }

    public function save(Request $request, string $module, int $id): JsonResponse
    {
        try {
            Log::debug("Studio Save - Request Details", [
                'module' => $module,
                'id' => $id,
                'content_size' => strlen($request->input('content', '')),
                'css_size' => strlen($request->input('css', '')),
                'js_size' => strlen($request->input('js', '')),
                'content_type' => gettype($request->input('content')),
                'css_type' => gettype($request->input('css')),
                'js_type' => gettype($request->input('js'))
            ]);

            $content = $request->input('content') ?? '';
            $css = $request->input('css') ?? '';
            $js = $request->input('js') ?? '';
            
            if (!is_string($content)) $content = (string)$content;
            if (!is_string($css)) $css = (string)$css;
            if (!is_string($js)) $js = (string)$js;
            
            Log::debug("Studio Save - Prepared Values", [
                'content_type' => gettype($content),
                'css_type' => gettype($css),
                'js_type' => gettype($js)
            ]);
            
            $result = $this->editorService->saveContent(
                $module, 
                $id, 
                $content, 
                $css, 
                $js
            );
            
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
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Kaydetme sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
    
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

    public function getBlocks(): JsonResponse
    {
        try {
            $blocks = $this->blockService->getAllBlocks();
            
            Log::debug('getBlocks HTTP yanıtı', [
                'blocks_count' => count($blocks)
            ]);
            
            $categories = [];
                
            $categories['active-widgets'] = [
                'name' => 'Aktif Bileşenler',
                'icon' => 'fa fa-star',
                'order' => 0
            ];
            
            if (class_exists('Modules\WidgetManagement\App\Models\WidgetCategory')) {
                $rootCategories = \Modules\WidgetManagement\App\Models\WidgetCategory::where('is_active', true)
                    ->whereNull('parent_id')
                    ->orderBy('order')
                    ->get();
                
                $order = 1;
                
                foreach ($rootCategories as $rootCategory) {
                    $categories[$rootCategory->slug] = [
                        'name' => $rootCategory->title,
                        'icon' => $rootCategory->icon ?? 'fa fa-folder',
                        'order' => $order++
                    ];
                    
                    $childCategories = \Modules\WidgetManagement\App\Models\WidgetCategory::where('is_active', true)
                        ->where('parent_id', $rootCategory->widget_category_id)
                        ->orderBy('order')
                        ->get();
                    
                    foreach ($childCategories as $childCategory) {
                        $parentChildKey = $rootCategory->slug . '-' . $childCategory->slug;
                        $categories[$parentChildKey] = [
                            'name' => $childCategory->title,
                            'icon' => $childCategory->icon ?? 'fa fa-folder-open',
                            'order' => $order++,
                            'parent' => $rootCategory->slug
                        ];
                        
                        $grandchildCategories = \Modules\WidgetManagement\App\Models\WidgetCategory::where('is_active', true)
                            ->where('parent_id', $childCategory->widget_category_id)
                            ->orderBy('order')
                            ->get();
                        
                        foreach ($grandchildCategories as $grandchildCategory) {
                            $nestedKey = $parentChildKey . '-' . $grandchildCategory->slug;
                            $categories[$nestedKey] = [
                                'name' => $grandchildCategory->title,
                                'icon' => $grandchildCategory->icon ?? 'fa fa-folder-open',
                                'order' => $order++,
                                'parent' => $parentChildKey
                            ];
                        }
                    }
                }
            }
            
            $formattedCategories = [];
            foreach ($categories as $key => $value) {
                $formattedCategories[$key] = $value['name'];
            }
            
            foreach ($blocks as $index => $block) {
                Log::debug("Blok #{$index}: {$block['id']} - {$block['label']} - Kategori: {$block['category']}");
            }
            
            Log::debug('Yanıt kategorileri', $formattedCategories);
            
            return response()->json([
                'success' => true,
                'blocks' => $blocks,
                'categories' => $formattedCategories,
                'categories_full' => $categories
            ]);
        } catch (\Exception $e) {
            Log::error('Blok verileri alınırken hata: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Blok verileri alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    public function publishResources()
    {
        try {
            $sourcePath = module_path('Studio', 'resources/assets');
            $destinationPath = public_path('admin/libs/studio');
            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $sourceCssPath = $sourcePath . '/css';
            $destCssPath = $destinationPath . '/css';
            
            if (!file_exists($destCssPath)) {
                mkdir($destCssPath, 0755, true);
            }
            
            if (is_dir($sourceCssPath)) {
                foreach (glob($sourceCssPath . '/*.css') as $file) {
                    copy($file, $destCssPath . '/' . basename($file));
                }
            }
            
            $sourceJsPath = $sourcePath . '/js';
            $destJsPath = $destinationPath;
            
            if (!file_exists($destJsPath)) {
                mkdir($destJsPath, 0755, true);
            }
            
            if (is_dir($sourceJsPath)) {
                foreach (glob($sourceJsPath . '/*.js') as $file) {
                    copy($file, $destJsPath . '/' . basename($file));
                }
            }
            
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