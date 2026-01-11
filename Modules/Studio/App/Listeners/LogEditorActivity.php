<?php

namespace Modules\Studio\App\Listeners;

use Modules\Studio\App\Events\EditorOpened;
use Illuminate\Support\Facades\Log;

class LogEditorActivity
{
    /**
     * Event'i işle
     *
     * @param EditorOpened $event
     * @return void
     */
    public function handle(EditorOpened $event)
    {
        try {
            // Log kaydı
            Log::info('Studio editör açıldı', [
                'module' => $event->module,
                'module_id' => $event->moduleId,
                'user_id' => $event->userId
            ]);
            
            // Aktivite kaydı (eğer fonksiyon varsa)
            if (function_exists('log_activity')) {
                log_activity([
                    'module' => $event->module,
                    'module_id' => $event->moduleId
                ], 'studio ile editör açıldı');
            }
        } catch (\Exception $e) {
            Log::error('LogEditorActivity hata: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }
}