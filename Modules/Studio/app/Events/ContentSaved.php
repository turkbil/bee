<?php

namespace Modules\Studio\App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContentSaved
{
    use Dispatchable, SerializesModels;

    public $module;
    public $moduleId;
    public $content;
    public $userId;

    /**
     * İçerik kaydedildiğinde çalışan olay
     *
     * @param string $module
     * @param int $moduleId
     * @param string $content
     * @param int|null $userId
     */
    public function __construct(string $module, int $moduleId, string $content, ?int $userId = null)
    {
        $this->module = $module;
        $this->moduleId = $moduleId;
        $this->content = $content;
        $this->userId = $userId ?? auth()->id();
    }
}