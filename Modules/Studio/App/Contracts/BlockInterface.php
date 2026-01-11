<?php

namespace Modules\Studio\App\Contracts;

interface BlockInterface
{
    /**
     * Blok ID'sini al
     *
     * @return string
     */
    public function getId(): string;
    
    /**
     * Blok etiketini al
     *
     * @return string
     */
    public function getLabel(): string;
    
    /**
     * Blok kategorisini al
     *
     * @return string
     */
    public function getCategory(): string;
    
    /**
     * Blok içeriğini al
     *
     * @return string
     */
    public function getContent(): string;
    
    /**
     * Blok önizleme içeriğini al
     *
     * @return string
     */
    public function getPreview(): string;
    
    /**
     * Bloğu HTML olarak render et
     *
     * @param array $params
     * @return string
     */
    public function render(array $params = []): string;
}