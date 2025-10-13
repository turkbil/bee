<?php
namespace Modules\SettingManagement\App\Helpers;

use Illuminate\Support\Facades\Storage;

class TenantStorageHelper 
{
    /**
     * Dosyayı tenant için doğru bir şekilde yükler
     *
     * @param mixed $file Yüklenecek dosya
     * @param string $relativePath Relative path (örn: "settings/1" veya "widgets")
     * @param string $fileName Dosya adı
     * @param int $tenantId Tenant ID
     * @return string Public URL path
     */
    public static function storeTenantFile($file, $relativePath, $fileName, $tenantId)
    {
        $disk = Storage::disk('public');
        $fullPath = $relativePath . '/' . $fileName;

        try {
            // Klasör yoksa oluştur (Laravel Storage kullanarak - tenant-aware)
            if (!$disk->exists($relativePath)) {
                // makeDirectory nested path'leri de oluşturabilsin diye recursive true gönder
                $disk->makeDirectory($relativePath, 0775, true);

                // Directory oluşturulduktan sonra permission düzelt (tenant-aware)
                $actualPath = $disk->path($relativePath);
                if (file_exists($actualPath)) {
                    @chmod($actualPath, 0775);
                }
            }

            // Dosyayı Laravel Storage ile yükle (tenant-aware)
            $fileContent = file_get_contents($file->getRealPath());
            $result = $disk->put($fullPath, $fileContent);

            if (!$result) {
                throw new \Exception("Dosya yüklenemedi: " . $fullPath);
            }

            // Dosya permission'ını düzelt (tenant-aware)
            $actualFile = $disk->path($fullPath);
            if (file_exists($actualFile)) {
                @chmod($actualFile, 0664);
            }

            // Tenant prefix ile path döndür
            if ($tenantId == 1) {
                return 'storage/tenant1/' . $fullPath;
            } else {
                return "storage/tenant{$tenantId}/{$fullPath}";
            }

        } catch (\Exception $e) {
            // Detaylı hata mesajı (tenant-aware debug info)
            $errorMsg = "Dosya yükleme hatası (Tenant {$tenantId}): " . $e->getMessage();
            $errorMsg .= " | Path: {$relativePath}/{$fileName}";

            // Debug: Actual storage path
            try {
                $errorMsg .= " | Actual Storage: " . $disk->path($relativePath);
            } catch (\Exception $ex) {
                $errorMsg .= " | Storage path alınamadı";
            }

            throw new \Exception($errorMsg);
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

        $disk = Storage::disk('public');

        try {
            // "storage/tenant{id}" formatını kontrol et
            if (preg_match('/^storage\/tenant(\d+)\/(.*)$/', $path, $matches)) {
                $relativePath = $matches[2];

                // Laravel Storage ile sil
                if ($disk->exists($relativePath)) {
                    return $disk->delete($relativePath);
                }

                // Yanlış yoldaki dosyayı da kontrol et (eski hatalar için)
                $tenantId = $matches[1];
                $wrongPath = storage_path('tenant' . $tenantId . '/app/public/' . $relativePath);
                if (file_exists($wrongPath)) {
                    return @unlink($wrongPath);
                }

                return false;
            }

            // Eski format (tenant{id}/) için destek
            if (preg_match('/^tenant(\d+)\/(.*)$/', $path, $matches)) {
                $relativePath = $matches[2];

                if ($disk->exists($relativePath)) {
                    return $disk->delete($relativePath);
                }

                return false;
            }

            // Prefix olmayan yol
            if ($disk->exists($path)) {
                return $disk->delete($path);
            }

            return false;

        } catch (\Exception $e) {
            // Sessizce başarısız ol (log kaydı yapılabilir)
            return false;
        }
    }
}