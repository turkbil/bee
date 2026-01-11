<?php

namespace Modules\Studio\App\Contracts;

interface RendererInterface
{
    /**
     * İçeriği render et
     *
     * @param string $content
     * @param array $params
     * @return string
     */
    public function render(string $content, array $params = []): string;
    
    /**
     * Şablonu render et
     *
     * @param string $template
     * @param array $params
     * @return string
     */
    public function renderTemplate(string $template, array $params = []): string;
    
    /**
     * Şablonu derleme
     *
     * @param string $template
     * @param array $params
     * @return string
     */
    public function compile(string $template, array $params = []): string;
}