<?php
namespace Modules\WidgetManagement\App\Helpers;

use Illuminate\Support\Facades\Storage;

class WidgetStorageHelper 
{
    /**
     * Widget dosyasını tenant için doğru bir şekilde yükler
     */
    public static function storeWidgetFile($file, $relativePath, $fileName, $tenantId) 
    {
        if ($tenantId == 1) {
            $targetDir = storage_path('app/public/' . $relativePath);
            $targetFile = $targetDir . '/' . $fileName;
            
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            
            try {
                if (is_uploaded_file($file->getPathname())) {
                    $result = move_uploaded_file($file->getPathname(), $targetFile);
                } else {
                    $result = copy($file->getPathname(), $targetFile);
                }
                
                if (!$result) {
                    throw new \Exception("Central widget dosya kopyalanamadı");
                }
                
                return 'storage/tenant1/' . $relativePath . '/' . $fileName;
            } catch (\Exception $e) {
                throw $e;
            }
        } else {
            $correctDir = storage_path('app/public/' . $relativePath);
            $correctFile = $correctDir . '/' . $fileName;
            
            try {
                if (!file_exists($correctDir)) {
                    mkdir($correctDir, 0755, true);
                }
                
                if (is_uploaded_file($file->getPathname())) {
                    $result = move_uploaded_file($file->getPathname(), $correctFile);
                } else {
                    $result = copy($file->getPathname(), $correctFile);
                }
                
                if (!$result) {
                    throw new \Exception("Tenant widget dosya kopyalanamadı");
                }
                
                return "storage/tenant{$tenantId}/{$relativePath}/{$fileName}";
            } catch (\Exception $e) {
                throw $e;
            }
        }
    }
    
    /**
     * Widget dosyasını siler
     */
    public static function deleteWidgetFile($path) 
    {
        if (empty($path)) {
            return false;
        }
        
        if (preg_match('/^storage\/tenant(\d+)\/(.*)$/', $path, $matches)) {
            $tenantId = $matches[1];
            $relativePath = $matches[2];
            
            if ($tenantId == 1) {
                $fullPath = storage_path('app/public/' . $relativePath);
                $result = file_exists($fullPath) && unlink($fullPath);
                
                return $result;
            } else {
                $correctPath = storage_path('app/public/' . $relativePath);
                $result = file_exists($correctPath) && unlink($correctPath);
                
                $wrongPath = storage_path('tenant' . $tenantId . '/app/public/' . $relativePath);
                $wrongResult = file_exists($wrongPath) && unlink($wrongPath);
                
                return $result || $wrongResult;
            }
        }
        
        if (preg_match('/^tenant(\d+)\/(.*)$/', $path, $matches)) {
            $tenantId = $matches[1];
            $relativePath = $matches[2];
            
            if ($tenantId == 1) {
                $fullPath = storage_path('app/public/' . $relativePath);
                $result = file_exists($fullPath) && unlink($fullPath);
                
                return $result;
            } else {
                $correctPath = storage_path('app/public/' . $relativePath);
                $result = file_exists($correctPath) && unlink($correctPath);
                
                $wrongPath = storage_path('tenant' . $tenantId . '/app/public/' . $relativePath);
                $wrongResult = file_exists($wrongPath) && unlink($wrongPath);
                
                return $result || $wrongResult;
            }
        }
        
        $fullPath = storage_path('app/public/' . $path);
        $result = file_exists($fullPath) && unlink($fullPath);
        
        return $result;
    }
}