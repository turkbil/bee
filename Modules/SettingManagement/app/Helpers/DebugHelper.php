<?php
// Modules/SettingManagement/App/Helpers/DebugHelper.php
namespace Modules\SettingManagement\App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class DebugHelper
{
    /**
     * Dosya yükleme debug bilgisi
     */
    public static function logFileUpload($message, $data = [])
    {
        // Düzenli log formatı
        $formattedData = json_encode($data, JSON_PRETTY_PRINT);
        
        // Log dosyasına yaz
        Log::channel('daily')->info("SETTING_DEBUG: " . $message, $data);
        
        // Ayrıca özel dosyaya da yaz
        $logPath = storage_path('logs/setting_debug.log');
        $date = now()->format('Y-m-d H:i:s');
        $logEntry = "[{$date}] {$message}\n{$formattedData}\n\n";
        
        File::append($logPath, $logEntry);
    }
}