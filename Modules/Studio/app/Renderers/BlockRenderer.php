<?php

namespace Modules\Studio\App\Renderers;

use Modules\Studio\App\Contracts\RendererInterface;
use Modules\Studio\App\Support\BlockManager;
use Modules\Studio\App\Parsers\HtmlParser;
use Illuminate\Support\Facades\View;

class BlockRenderer implements RendererInterface
{
    /**
     * @var BlockManager
     */
    protected $blockManager;
    
    /**
     * @var HtmlParser
     */
    protected $htmlParser;
    
    /**
     * BlockRenderer constructor.
     *
     * @param BlockManager $blockManager
     * @param HtmlParser $htmlParser
     */
    public function __construct(BlockManager $blockManager, HtmlParser $htmlParser)
    {
        $this->blockManager = $blockManager;
        $this->htmlParser = $htmlParser;
    }
    
    /**
     * İçeriği render et
     *
     * @param string $content
     * @param array $params
     * @return string
     */
    public function render(string $content, array $params = []): string
    {
        $content = $this->htmlParser->parseAndFixHtml($content);
        
        return $content;
    }
    
    /**
     * Belirli bir bloğu render et
     *
     * @param string $blockId
     * @param array $params
     * @return string
     */
    public function renderBlock(string $blockId, array $params = []): string
    {
        $block = $this->blockManager->getById($blockId);
        
        if (!$block) {
            return '<!-- Block not found: ' . $blockId . ' -->';
        }
        
        // Şablon dosyasından mı yoksa ham içerikten mi render edileceğini kontrol et
        if (isset($block['template']) && $block['template']) {
            return $this->renderTemplate($block['template'], array_merge($params, [
                'block' => $block
            ]));
        }
        
        return $block['content'] ?? '';
    }
    
    /**
     * Şablonu render et
     *
     * @param string $template
     * @param array $params
     * @return string
     */
    public function renderTemplate(string $template, array $params = []): string
    {
        // Tam şablon adını kontrol et
        if (!str_contains($template, '::')) {
            $template = 'studio::blocks.' . $template;
        }
        
        if (View::exists($template)) {
            return View::make($template, $params)->render();
        }
        
        return '<!-- Template not found: ' . $template . ' -->';
    }
    
    /**
     * Şablonu derle
     *
     * @param string $template
     * @param array $params
     * @return string
     */
    public function compile(string $template, array $params = []): string
    {
        // Blade şablonunu derle
        return \Illuminate\Support\Facades\Blade::render($template, $params);
    }
    
    /**
     * Blok önizlemesi oluştur
     *
     * @param string $blockId
     * @return string
     */
    public function renderPreview(string $blockId): string
    {
        $block = $this->blockManager->getById($blockId);
        
        if (!$block) {
            return '<!-- Block preview not available: ' . $blockId . ' -->';
        }
        
        // Eğer önizleme varsa onu kullan, yoksa içeriği kısalt
        if (isset($block['preview']) && $block['preview']) {
            return $block['preview'];
        }
        
        $content = $block['content'] ?? '';
        
        // Basit HTML kısaltma
        $content = preg_replace('/\s+/', ' ', $content);
        
        if (strlen($content) > 100) {
            $content = substr($content, 0, 100) . '...';
        }
        
        return $content;
    }
}