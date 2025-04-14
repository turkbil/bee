<?php

namespace Modules\Studio\Events;

use Illuminate\Queue\SerializesModels;

class StudioThemeChanged
{
    use SerializesModels;

    public $module;
    public $id;
    public $theme;
    public $timestamp;

    /**
     * Yeni olay örneği oluştur
     *
     * @param string $module Modül adı
     * @param int $id İçerik ID
     * @param string $theme Tema adı
     */
    public function __construct(string $module, int $id, string $theme)
    {
        $this->module = $module;
        $this->id = $id;
        $this->theme = $theme;
        $this->timestamp = now();
    }
}