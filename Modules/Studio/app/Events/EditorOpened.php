<?php

namespace Modules\Studio\App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EditorOpened
{
    use Dispatchable, SerializesModels;

    public $module;
    public $moduleId;
    public $userId;

    /**
     * Editör açıldığında çalışan olay
     *
     * @param string $module
     * @param int $moduleId
     * @param int|null $userId
     */
    public function __construct(string $module, int $moduleId, ?int $userId = null)
    {
        $this->module = $module;
        $this->moduleId = $moduleId;
        $this->userId = $userId ?? auth()->id();
    }
}