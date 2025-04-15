<?php

namespace Modules\Studio\App\Contracts;

interface ParserInterface
{
    /**
     * İçeriği ayrıştır
     *
     * @param string $content
     * @return string
     */
    public function parse(string $content): string;
    
    /**
     * İçeriği kaydetmeye hazırla
     *
     * @param string $content
     * @return string
     */
    public function prepareForSave(string $content): string;
    
    /**
     * İçeriği göstermeye hazırla
     *
     * @param string $content
     * @return string
     */
    public function prepareForDisplay(string $content): string;
}