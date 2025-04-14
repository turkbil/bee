<?php

namespace Modules\Studio\Events;

use Illuminate\Queue\SerializesModels;

class StudioContentSaved
{
    use SerializesModels;

    public $module;
    public $id;
    public $data;
    public $timestamp;

    /**
     * Yeni olay örneği oluştur
     *
     * @param string $module Modül adı
     * @param int $id İçerik ID
     * @param array $data Ek veri
     */
    public function __construct(string $module, int $id, array $data = [])
    {
        $this->module = $module;
        $this->id = $id;
        $this->data = $data;
        $this->timestamp = now();
    }
}