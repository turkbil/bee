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
     * Parse markdown to HTML (smart detection)
     *
     * ⚠️ KRİTİK: HTML input'u tespit edip double-encode önleme
     *
     * @param string $input
     * @return string
     */
    public function parse(string $input): string
    {
        if (empty($input)) {
            return '';
        }

        // HTML tag kontrolü - eğer zaten HTML ise direkt döndür (double encoding önleme!)
        if ($this->isAlreadyHTML($input)) {
            \Log::info('✅ MarkdownService: Input is already HTML, skipping conversion');
            return $input;
        }

        // Markdown varsa HTML'e çevir
        if ($this->hasMarkdown($input)) {
            \Log::info('✅ MarkdownService: Converting markdown to HTML');
            return $this->convertToHtml($input);
        }

        // Ne markdown ne HTML - düz text, paragraf wrap et
        \Log::info('✅ MarkdownService: Plain text detected, wrapping in <p>');
        return '<p>' . htmlspecialchars($input, ENT_QUOTES, 'UTF-8') . '</p>';
    }

    /**
     * Check if input is already HTML
     *
     * @param string $text
     * @return bool
     */
    protected function isAlreadyHTML(string $text): bool
    {
        // HTML tag pattern'leri - yaygın HTML elementleri
        $htmlPatterns = [
            '/<p[\s>]/',           // <p> or <p class="...">
            '/<div[\s>]/',         // <div>
            '/<span[\s>]/',        // <span>
            '/<h[1-6][\s>]/',      // <h1>, <h2>, etc.
            '/<ul[\s>]/',          // <ul>
            '/<ol[\s>]/',          // <ol>
            '/<li[\s>]/',          // <li>
            '/<strong[\s>]/',      // <strong>
            '/<em[\s>]/',          // <em>
            '/<a[\s>]/',           // <a>
            '/<br[\s>\/]/',        // <br> or <br/>
        ];

        foreach ($htmlPatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
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