<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StorageController extends Controller
{
    /**
     * Tenant medya dosyalarını sunmak için kullanılır
     * 
     * @param int $tenantId Tenant ID
     * @param string $path Dosya yolu
     * @return BinaryFileResponse
     */
    public function tenantMedia($tenantId, $path = null)
    {
        // Central domain için (tenant1)
        if ($tenantId == 1) {
            // Central domain için public içindeki dosyalara bak
            $filePath = storage_path("app/public/{$path}");
        } else {
            // Gerçek tenant'lar için tenant{id} klasörüne bak
            $filePath = storage_path("tenant{$tenantId}/app/public/{$path}");
        }
        
        // Dosya var mı kontrol et
        if (File::exists($filePath)) {
            // Dosya yanıtını oluştur
            $response = new BinaryFileResponse($filePath);
            $response->headers->set('Content-Type', File::mimeType($filePath));
            $response->setCache([
                'public' => true,
                'max_age' => 86400,
                'must_revalidate' => false,
            ]);
            
            return $response;
        }
        
        // Dosya bulunamadıysa 404 hatası döndür
        abort(404, 'Medya dosyası bulunamadı');
    }
    
    /**
     * Normal storage dosyalarına erişmek için (storage:link ile oluşturulan)
     * 
     * @param string $path
     * @return \Illuminate\Http\Response
     */
    public function publicStorage($path)
    {
        // Normal storage/app/public içindeki dosyalara erişim
        $storagePath = storage_path('app/public/' . $path);
        
        if (File::exists($storagePath)) {
            return response()->file($storagePath);
        }
        
        abort(404, 'Dosya bulunamadı');
    }
}