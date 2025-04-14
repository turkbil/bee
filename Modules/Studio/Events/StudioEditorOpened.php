<?php

namespace Modules\Studio\Events;

use Illuminate\Queue\SerializesModels;

class StudioEditorOpened
{
    use SerializesModels;

    public $module;
    public $id;
    public $timestamp;

    /**
     * Yeni olay örneği oluştur
     *
     * @param string $module Modül adı
     * @param int $id İçerik ID
     */
    public function __construct(string $module, int $id)
    {
        $this->module = $module;
        $this->id = $id;
        $this->timestamp = now();
    }
}