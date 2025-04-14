<?php

namespace Modules\Studio\Listeners;

use Modules\Studio\Events\StudioEditorOpened;
use Illuminate\Support\Facades\Log;
use Modules\Studio\App\Services\StudioCacheService;

class StudioEditorOpenedListener
{
    protected $cacheService;

    /**
     * Create the event listener.
     */
    public function __construct(StudioCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the event.
     */
    public function handle(StudioEditorOpened $event): void
    {
        // Editör açılma olayını logla
        Log::info('Studio Editor açıldı', [
            'module' => $event->module,
            'module_id' => $event->moduleId,
            'user' => auth()->user()->email ?? 'Bilinmeyen',
            'timestamp' => $event->timestamp
        ]);
        
        // İlgili önbellekleri temizle
        if (method_exists($this->cacheService, 'clearByModule')) {
            $this->cacheService->clearByModule($event->module, $event->moduleId);
        }
    }
}