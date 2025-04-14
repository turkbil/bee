<?php

namespace Modules\Studio\Events;

use Illuminate\Queue\SerializesModels;

class StudioWidgetUpdated
{
    use SerializesModels;

    public $widgetId;
    public $changes;
    public $timestamp;

    /**
     * Yeni olay örneği oluştur
     *
     * @param int $widgetId Widget ID
     * @param array $changes Değişiklikler
     */
    public function __construct(int $widgetId, array $changes = [])
    {
        $this->widgetId = $widgetId;
        $this->changes = $changes;
        $this->timestamp = now();
    }
}