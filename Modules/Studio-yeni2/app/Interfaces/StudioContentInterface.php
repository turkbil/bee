<?php

namespace Modules\Studio\App\Interfaces;

interface StudioContentInterface
{
    /**
     * Studio içeriğine sahip mi?
     * 
     * @return bool
     */
    public function hasStudioContent(): bool;
    
    /**
     * Studio içerik verisini döndürür
     * 
     * @return array
     */
    public function getStudioContent(): array;
    
    /**
     * Studio içerik verisini ayarlar
     * 
     * @param array $content
     * @return void
     */
    public function setStudioContent(array $content): void;
    
    /**
     * Studio içeriğini render eder
     * 
     * @return string
     */
    public function renderStudioContent(): string;
}