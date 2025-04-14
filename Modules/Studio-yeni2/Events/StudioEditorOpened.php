<?php

namespace Modules\Studio\Events;

use Illuminate\Queue\SerializesModels;

class StudioEditorOpened
{
    use SerializesModels;

    public $module;
    public $moduleId;
    public $timestamp;

    /**
     * Yeni olay örneği oluştur
     *
     * @param string $module Modül adı
     * @param int $moduleId Modül ID
     */
    public function __construct(string $module, int $moduleId)
    {
        $this->module = $module;
        $this->moduleId = $moduleId;
        $this->timestamp = now();
    }
}