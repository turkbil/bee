<?php
namespace Modules\SettingManagement\App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TenantStorageHelper 
{
    /**
     * Dosyayı tenant için doğru bir şekilde yükler
     */
    public static function storeTenantFile($file, $relativePath, $fileName, $tenantId) 
    {
        // Log başlat
        DebugHelper::logFileUpload('TenantStorageHelper - Dosya yüklemeye başlanıyor', [
            'tenant_id' => $tenantId,
            'relative_path' => $relativePath, 
            'file_name' => $fileName,
            'uploaded_file_path' => $file->getPathname(),
            'uploaded_file_size' => filesize($file->getPathname())
        ]);
        
        if ($tenantId == 1) {
            // CENTRAL - app/public/ altına yükle
            $targetDir = storage_path('app/public/' . $relativePath);
            $targetFile = $targetDir . '/' . $fileName;
            
            // Klasör yoksa oluştur
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            
            DebugHelper::logFileUpload('Central dosya yükleme hedefi', [
                'target_dir' => $targetDir,
                'target_file' => $targetFile
            ]);
            
            try {
                // Dosyayı kopyala
                if (is_uploaded_file($file->getPathname())) {
                    $result = move_uploaded_file($file->getPathname(), $targetFile);
                } else {
                    $result = copy($file->getPathname(), $targetFile);
                }
                
                if (!$result) {
                    throw new \Exception("Central dosya kopyalanamadı");
                }
                
                DebugHelper::logFileUpload('Central dosya yükleme tamamlandı', [
                    'target_file' => $targetFile,
                    'exists' => file_exists($targetFile) ? 'true' : 'false',
                    'size' => file_exists($targetFile) ? filesize($targetFile) : 'not found'
                ]);
                
                return 'storage/tenant1/' . $relativePath . '/' . $fileName;
            } catch (\Exception $e) {
                DebugHelper::logFileUpload('Central dosya yükleme hatası', [
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        } else {
            // TENANT - direkt app/public/ altına yükle
            // Base path'i alırken storage_path() önce tenant bilgisini ekliyor, bu yüzden tekrar tenant bilgisi eklenmemeli
            
            // Şu anki çalışma dizini hakkında bilgi alıp yol analizini yapalım
            $rootPath = base_path(); // laravel root dizini
            $storagePath = storage_path();
            $appPublicPath = storage_path('app/public');
            
            DebugHelper::logFileUpload('Yol analizi', [
                'root_path' => $rootPath,
                'storage_path' => $storagePath,
                'app_public_path' => $appPublicPath,
                'tenant_id' => $tenantId
            ]);
            
            // Tenant'a göre paths.php yapılandırması yapılmış ise storage_path() fonksiyonu zaten tenant bilgisini ekliyor olabilir
            // O nedenle direkt app/public/ altına hedefliyoruz
            $correctDir = storage_path('app/public/' . $relativePath);
            $correctFile = $correctDir . '/' . $fileName;
            
            DebugHelper::logFileUpload('Tenant dosya yolları (Düzeltilmiş)', [
                'correct_dir' => $correctDir,
                'correct_file' => $correctFile,
                'tenant_id' => $tenantId
            ]);
            
            try {
                // Doğru klasörü oluştur
                if (!file_exists($correctDir)) {
                    mkdir($correctDir, 0755, true);
                    DebugHelper::logFileUpload('Tenant için klasör oluşturuldu', [
                        'dir' => $correctDir
                    ]);
                }
                
                // Dosyayı kopyala
                if (is_uploaded_file($file->getPathname())) {
                    $result = move_uploaded_file($file->getPathname(), $correctFile);
                    $method = 'move_uploaded_file';
                } else {
                    $result = copy($file->getPathname(), $correctFile);
                    $method = 'copy';
                }
                
                if (!$result) {
                    throw new \Exception("Tenant dosya kopyalanamadı");
                }
                
                DebugHelper::logFileUpload('Tenant dosya yükleme tamamlandı', [
                    'method' => $method,
                    'correct_file' => $correctFile,
                    'exists' => file_exists($correctFile) ? 'true' : 'false',
                    'file_size' => file_exists($correctFile) ? filesize($correctFile) : 'not found'
                ]);
                
                return "storage/tenant{$tenantId}/{$relativePath}/{$fileName}";
            } catch (\Exception $e) {
                DebugHelper::logFileUpload('Tenant dosya yükleme hatası', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                Log::error('Tenant dosya yükleme hatası: ' . $e->getMessage(), [
                    'tenant_id' => $tenantId,
                    'target_file' => $correctFile,
                    'exception' => $e->getMessage()
                ]);
                
                throw $e;
            }
        }
    }
    
    /**
     * Dosyayı siler
     */
    public static function deleteFile($path) 
    {
        if (empty($path)) {
            return false;
        }
        
        DebugHelper::logFileUpload('Dosya silme işlemi başladı', [
            'path' => $path
        ]);
        
        // "storage/tenant{id}" formatını kontrol et
        if (preg_match('/^storage\/tenant(\d+)\/(.*)$/', $path, $matches)) {
            $tenantId = $matches[1];
            $relativePath = $matches[2];
            
            if ($tenantId == 1) {
                // Central dosyası
                $fullPath = storage_path('app/public/' . $relativePath);
                $result = file_exists($fullPath) && unlink($fullPath);
                
                DebugHelper::logFileUpload('Central dosya silme sonucu', [
                    'full_path' => $fullPath,
                    'result' => $result ? 'success' : 'failed'
                ]);
                
                return $result;
            } else {
                // Tenant dosyası - direkt app/public altında olmalı
                $correctPath = storage_path('app/public/' . $relativePath);
                $result = file_exists($correctPath) && unlink($correctPath);
                
                // YANLIŞ yol - iç içe tenant klasöründeki dosyayı da sil
                $wrongPath = storage_path('tenant' . $tenantId . '/app/public/' . $relativePath);
                $wrongResult = file_exists($wrongPath) && unlink($wrongPath);
                
                DebugHelper::logFileUpload('Tenant dosya silme sonuçları', [
                    'correct_path' => $correctPath,
                    'correct_result' => $result ? 'success' : 'failed',
                    'wrong_path' => $wrongPath, 
                    'wrong_exists' => file_exists($wrongPath) ? 'true' : 'false',
                    'wrong_result' => $wrongResult ? 'success' : 'failed'
                ]);
                
                return $result || $wrongResult;
            }
        }
        
        // Eski format (tenant{id}/) için destek
        if (preg_match('/^tenant(\d+)\/(.*)$/', $path, $matches)) {
            $tenantId = $matches[1];
            $relativePath = $matches[2];
            
            if ($tenantId == 1) {
                // Central dosyası
                $fullPath = storage_path('app/public/' . $relativePath);
                $result = file_exists($fullPath) && unlink($fullPath);
                
                DebugHelper::logFileUpload('Central dosya silme (eski format)', [
                    'full_path' => $fullPath,
                    'result' => $result ? 'success' : 'failed'
                ]);
                
                return $result;
            } else {
                // Tenant dosyası - direkt app/public altında olmalı
                $correctPath = storage_path('app/public/' . $relativePath);
                $result = file_exists($correctPath) && unlink($correctPath);
                
                // Yanlış yol
                $wrongPath = storage_path('tenant' . $tenantId . '/app/public/' . $relativePath);
                $wrongResult = file_exists($wrongPath) && unlink($wrongPath);
                
                DebugHelper::logFileUpload('Tenant dosya silme (eski format)', [
                    'correct_path' => $correctPath,
                    'correct_result' => $result ? 'success' : 'failed',
                    'wrong_path' => $wrongPath,
                    'wrong_result' => $wrongResult ? 'success' : 'failed'
                ]);
                
                return $result || $wrongResult;
            }
        }
        
        // Prefix olmayan yol
        $fullPath = storage_path('app/public/' . $path);
        $result = file_exists($fullPath) && unlink($fullPath);
        
        DebugHelper::logFileUpload('Prefix olmayan dosya silme', [
            'full_path' => $fullPath,
            'result' => $result ? 'success' : 'failed'
        ]);
        
        return $result;
    }
}