<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
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
        // Dosya yolu güvenlik kontrolü
        $path = str_replace(['../', '..\\'], '', $path);
        
        // Path işleme - storage/ öneki varsa temizle
        $path = ltrim($path, '/');
        if (strpos($path, 'storage/') === 0) {
            $path = substr($path, 8); // "storage/" kısmını çıkar
        }
        
        // Güvenlik için başka bir tenant prefix'i var mı kontrol et
        if (preg_match('/^tenant(\d+)\/(.*)$/', $path, $matches)) {
            // Path zaten bir tenant prefix'i içeriyor, ancak bu doğru tenant ID mi?
            $pathTenantId = $matches[1];
            $actualPath = $matches[2];
            
            if ($pathTenantId != $tenantId) {
                // Path'deki tenant ID, istenen tenant ID ile eşleşmiyor
                Log::warning("Path'de yanlış tenant ID tespit edildi: path={$path}, requestedTenant={$tenantId}");
            }
            
            // Her durumda path'i temizle
            $path = $actualPath;
        }

        Log::debug("tenantMedia request - tenantId: {$tenantId}, path: {$path}");
        
        // Fiziksel dosya yolunu belirle
        // Central domain için (tenant1)
        if ($tenantId == 1) {
            $filePath = storage_path("app/public/{$path}");
            Log::debug("Central için dosya yolu: {$filePath}");
        } else {
            // Gerçek tenant'lar için tenant{id} klasörüne bak
            $filePath = storage_path("tenant{$tenantId}/app/public/{$path}");
            Log::debug("Tenant için dosya yolu: {$filePath}");
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
        
        // Dosya bulunamadı, alternatif yerleri kontrol et:
        
        // 1. Eski yanlış konumu kontrol et (tenant2/tenant2 sorunu)
        if ($tenantId > 1) {
            $oldPath = storage_path("tenant{$tenantId}/tenant{$tenantId}/app/public/{$path}");
            Log::debug("Alternatif 1 (yanlış tenant yolu): {$oldPath}");
            
            if (File::exists($oldPath)) {
                $response = new BinaryFileResponse($oldPath);
                $response->headers->set('Content-Type', File::mimeType($oldPath));
                $response->setCache(['public' => true, 'max_age' => 86400]);
                return $response;
            }
        }
        
        // 2. Central storage içinde tenant1 olmadan kontrol et
        if ($tenantId == 1) {
            // tenant1 olmadan central'da kontrol et
            $centralPath = storage_path("app/public/{$path}");
            Log::debug("Alternatif 2 (central klasik yol): {$centralPath}");
            
            if (File::exists($centralPath)) {
                $response = new BinaryFileResponse($centralPath);
                $response->headers->set('Content-Type', File::mimeType($centralPath));
                $response->setCache(['public' => true, 'max_age' => 86400]);
                return $response;
            }
        }
        
        // Dosya bulunamadıysa 404 hatası döndür
        Log::warning("Medya dosyası bulunamadı - tenantId: {$tenantId}, path: {$path}");
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