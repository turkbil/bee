<?php

namespace Modules\Studio\App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WidgetUpdated
{
    use Dispatchable, SerializesModels;

    public $widgetId;
    public $content;
    public $userId;

    /**
     * Widget güncellendiğinde çalışan olay
     *
     * @param int $widgetId
     * @param string $content
     * @param int|null $userId
     */
    public function __construct(int $widgetId, string $content, ?int $userId = null)
    {
        $this->widgetId = $widgetId;
        $this->content = $content;
        $this->userId = $userId ?? auth()->id();
    }
}