<?php

namespace Modules\Studio\App\Listeners;

use Modules\Studio\App\Events\ContentSaved;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ContentSavedListener
{
    /**
     * Event'i işle
     *
     * @param ContentSaved $event
     * @return void
     */
    public function handle(ContentSaved $event)
    {
        try {
            // İlgili içerik önbelleğini temizle
            $cacheKey = 'studio_' . $event->module . '_' . $event->moduleId;
            Cache::forget($cacheKey);
            
            // Log kaydı
            Log::info('Studio içerik kaydedildi', [
                'module' => $event->module,
                'module_id' => $event->moduleId,
                'user_id' => $event->userId,
                'content_length' => strlen($event->content)
            ]);
            
            // Aktivite logunu burada oluşturmak yerine sadece log mesajı bırak
            Log::info('Log: ' . $event->module . ' - studio ile düzenlendi');
        } catch (\Exception $e) {
            Log::error('ContentSavedListener hata: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }
}