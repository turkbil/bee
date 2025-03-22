<?php
namespace Modules\SettingManagement\App\Helpers;

use Illuminate\Support\Facades\Storage;

class TenantStorageHelper 
{
    /**
     * Dosyayı tenant için doğru bir şekilde yükler
     */
    public static function storeTenantFile($file, $relativePath, $fileName, $tenantId) 
    {
        if ($tenantId == 1) {
            // CENTRAL - app/public/ altına yükle
            $targetDir = storage_path('app/public/' . $relativePath);
            $targetFile = $targetDir . '/' . $fileName;
            
            // Klasör yoksa oluştur
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            
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
                
                return 'storage/tenant1/' . $relativePath . '/' . $fileName;
            } catch (\Exception $e) {
                throw $e;
            }
        } else {
            // TENANT - direkt app/public/ altına yükle
            $correctDir = storage_path('app/public/' . $relativePath);
            $correctFile = $correctDir . '/' . $fileName;
            
            try {
                // Doğru klasörü oluştur
                if (!file_exists($correctDir)) {
                    mkdir($correctDir, 0755, true);
                }
                
                // Dosyayı kopyala
                if (is_uploaded_file($file->getPathname())) {
                    $result = move_uploaded_file($file->getPathname(), $correctFile);
                } else {
                    $result = copy($file->getPathname(), $correctFile);
                }
                
                if (!$result) {
                    throw new \Exception("Tenant dosya kopyalanamadı");
                }
                
                return "storage/tenant{$tenantId}/{$relativePath}/{$fileName}";
            } catch (\Exception $e) {
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
        
        // "storage/tenant{id}" formatını kontrol et
        if (preg_match('/^storage\/tenant(\d+)\/(.*)$/', $path, $matches)) {
            $tenantId = $matches[1];
            $relativePath = $matches[2];
            
            if ($tenantId == 1) {
                // Central dosyası
                $fullPath = storage_path('app/public/' . $relativePath);
                $result = file_exists($fullPath) && unlink($fullPath);
                
                return $result;
            } else {
                // Tenant dosyası - direkt app/public altında olmalı
                $correctPath = storage_path('app/public/' . $relativePath);
                $result = file_exists($correctPath) && unlink($correctPath);
                
                // YANLIŞ yol - iç içe tenant klasöründeki dosyayı da sil
                $wrongPath = storage_path('tenant' . $tenantId . '/app/public/' . $relativePath);
                $wrongResult = file_exists($wrongPath) && unlink($wrongPath);
                
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
                
                return $result;
            } else {
                // Tenant dosyası - direkt app/public altında olmalı
                $correctPath = storage_path('app/public/' . $relativePath);
                $result = file_exists($correctPath) && unlink($correctPath);
                
                // Yanlış yol
                $wrongPath = storage_path('tenant' . $tenantId . '/app/public/' . $relativePath);
                $wrongResult = file_exists($wrongPath) && unlink($wrongPath);
                
                return $result || $wrongResult;
            }
        }
        
        // Prefix olmayan yol
        $fullPath = storage_path('app/public/' . $path);
        $result = file_exists($fullPath) && unlink($fullPath);
        
        return $result;
    }
}