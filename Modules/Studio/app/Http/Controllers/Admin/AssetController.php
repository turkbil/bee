<?php

namespace Modules\Studio\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Studio\App\Services\AssetService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class AssetController extends Controller
{
    protected $assetService;
    
    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }
    
    /**
     * Varlık yükleme
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function upload(Request $request): JsonResponse
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
     * Varlıkları listele
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            // İleride buraya varlık listesi eklenebilir
            return response()->json([
                'success' => true,
                'assets' => []
            ]);
        } catch (\Exception $e) {
            Log::error('Varlık listeleme hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Varlık listeleme hatası: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Varlığı optimize et
     *
     * @param Request $request
     * @param string $path
     * @return JsonResponse
     */
    public function optimize(Request $request, string $path): JsonResponse
    {
        try {
            $options = $request->all();
            
            $optimizedUrl = $this->assetService->optimizeImage($path, $options);
            
            return response()->json([
                'success' => true,
                'url' => $optimizedUrl
            ]);
        } catch (\Exception $e) {
            Log::error('Görsel optimizasyon hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Görsel optimizasyon hatası: ' . $e->getMessage()
            ]);
        }
    }
}