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
     * @param int $mediaId Media ID
     * @param string $filename Dosya adı
     * @return BinaryFileResponse
     */
    public function tenantMedia($tenantId, $mediaId, $filename)
    {
        // Tenant klasörü yolu
        $tenantPath = storage_path("tenant{$tenantId}/app/public/{$mediaId}/{$filename}");
        
        // Central klasörü yolu (tenant olmayan durum için)
        $centralPath = storage_path("app/public/{$mediaId}/{$filename}");
        
        // Önce tenant yolunu kontrol et
        if (File::exists($tenantPath)) {
            $path = $tenantPath;
        } 
        // Tenant yolunda yoksa, central yolunu kontrol et
        elseif (File::exists($centralPath)) {
            $path = $centralPath;
        }
        // Dosya bulunamadıysa 404 hatası döndür
        else {
            abort(404, 'Medya dosyası bulunamadı');
        }
        
        // Dosya yanıtını oluştur
        $response = new BinaryFileResponse($path);
        $response->headers->set('Content-Type', File::mimeType($path));
        $response->setCache([
            'public' => true,
            'max_age' => 86400,
            'must_revalidate' => false,
        ]);
        
        return $response;
    }
}