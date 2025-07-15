<?php

namespace Modules\AI\App\Services;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

class MarkdownService
{
    protected $converter;

    public function __construct()
    {
        // CommonMark ortamını yapılandır
        $environment = new Environment([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 100,
        ]);
        
        // Temel Markdown uzantısını ekle
        $environment->addExtension(new CommonMarkCoreExtension());
        
        // GitHub Flavored Markdown uzantısını ekle (tablolar, görev listeleri, vb.)
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        
        // Markdown dönüştürücüsünü oluştur
        $this->converter = new MarkdownConverter($environment);
    }

    /**
     * Markdown metnini HTML'e dönüştür
     *
     * @param string $markdown
     * @return string
     */
    public function convertToHtml(string $markdown): string
    {
        return $this->converter->convert($markdown)->getContent();
    }

    /**
     * Markdown sözdizimini algıla
     *
     * @param string $text
     * @return bool
     */
    public function hasMarkdown(string $text): bool
    {
        // Basit markdown işaretlerini ara
        $markdownPatterns = [
            '/\*\*.+?\*\*/', // Kalın
            '/\*.+?\*/',     // İtalik
            '/`[^`]+`/',     // Kod parçacığı
            '/#{1,6}\s.+/',  // Başlıklar
            '/\[.+?\]\(.+?\)/', // Bağlantılar
            '/\n\s*[\*\-\+]\s/', // Madde işaretli listeler
            '/\n\s*\d+\.\s/', // Sıralı listeler
            '/```[\s\S]*?```/m', // Kod blokları
        ];

        foreach ($markdownPatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }
}