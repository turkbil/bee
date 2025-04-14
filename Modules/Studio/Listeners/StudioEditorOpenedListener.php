<?php

namespace Modules\Studio\Listeners;

use Modules\Studio\Events\StudioEditorOpened;
use Illuminate\Support\Facades\Log;

class StudioEditorOpenedListener
{
    /**
     * Olayı işle
     *
     * @param StudioEditorOpened $event
     * @return void
     */
    public function handle(StudioEditorOpened $event): void
    {
        Log::info('Studio Editor açıldı', [
            'module' => $event->module,
            'id' => $event->id,
            'user' => auth()->user()->email ?? 'Bilinmeyen',
            'timestamp' => $event->timestamp
        ]);
    }
}