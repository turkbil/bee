<?php

namespace App\Services;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

/**
 * Markdown Service
 *
 * Backend'de güvenli markdown parsing için league/commonmark kullanır.
 * Custom link formatlarını ([LINK:shop:slug]) işler.
 *
 * @package App\Services
 */
class MarkdownService
{
    protected MarkdownConverter $converter;

    public function __construct()
    {
        // Environment oluştur
        $config = [
            'html_input' => 'strip', // HTML input'u temizle (XSS koruması)
            'allow_unsafe_links' => false, // Güvenli olmayan linklere izin verme
            'max_nesting_level' => 10, // Maksimum iç içe geçme seviyesi
        ];

        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        $this->converter = new MarkdownConverter($environment);
    }

    /**
     * Markdown'ı HTML'e çevir
     *
     * @param string $markdown
     * @return string HTML
     */
    public function parse(string $markdown): string
    {
        // Önce custom link formatlarını işle
        $markdown = $this->processCustomLinks($markdown);

        // Sonra standard markdown'ı parse et
        $html = $this->converter->convert($markdown)->getContent();

        // HTML'i temizle ve formatla
        $html = $this->cleanHtml($html);

        return $html;
    }

    /**
     * Custom link formatlarını işle
     *
     * Formatlar:
     * - **Ürün Adı** [LINK:shop:slug] → Product link
     * - **Kategori Adı** [LINK:shop:category:slug] → Category link
     *
     * @param string $markdown
     * @return string
     */
    protected function processCustomLinks(string $markdown): string
    {
        // 1. Product SLUG format: **Text** [LINK:shop:slug]
        $markdown = preg_replace_callback(
            '/\*\*([^*]+)\*\*\s*\[LINK:shop:([\w\-İıĞğÜüŞşÖöÇç]+)\]/ui',
            function ($matches) {
                $linkText = trim($matches[1]);
                $slug = $matches[2];
                $url = "/shop/{$slug}";

                // Standard markdown link formatına çevir
                return "[**{$linkText}**]({$url})";
            },
            $markdown
        );

        // 2. Category SLUG format: **Text** [LINK:shop:category:slug]
        $markdown = preg_replace_callback(
            '/\*\*([^*]+)\*\*\s*\[LINK:shop:category:([\w\-İıĞğÜüŞşÖöÇç]+)\]/ui',
            function ($matches) {
                $linkText = trim($matches[1]);
                $slug = $matches[2];
                $url = "/shop/category/{$slug}";

                return "[**{$linkText}**]({$url})";
            },
            $markdown
        );

        // 3. BACKWARD COMPATIBILITY: [LINK:module:type:id]
        $markdown = preg_replace_callback(
            '/\*\*([^*]+)\*\*\s*\[LINK:(\w+):(\w+):(\d+)\]/i',
            function ($matches) {
                $linkText = trim($matches[1]);
                $module = $matches[2];
                $type = $matches[3];
                $id = $matches[4];

                // URL oluştur
                if ($module === 'shop') {
                    if ($type === 'product') {
                        $url = "/shop/product/{$id}";
                    } elseif ($type === 'category') {
                        $url = "/shop/category-by-id/{$id}";
                    } elseif ($type === 'brand') {
                        $url = "/shop/brand-by-id/{$id}";
                    } else {
                        $url = "#";
                    }
                } elseif ($module === 'blog') {
                    $url = "/blog/post-by-id/{$id}";
                } elseif ($module === 'page') {
                    $url = "/page-by-id/{$id}";
                } elseif ($module === 'portfolio') {
                    $url = "/portfolio/project-by-id/{$id}";
                } else {
                    $url = "#";
                }

                return "[**{$linkText}**]({$url})";
            },
            $markdown
        );

        // 4. Old [LINK_ID:...] format
        $markdown = preg_replace_callback(
            '/\*\*([^*]+)\*\*\s*\[LINK_ID:(\d+)(?::([a-z0-9-]+))?\]/i',
            function ($matches) {
                $linkText = trim($matches[1]);
                $productId = $matches[2];
                $url = "/shop/product/{$productId}";

                return "[**{$linkText}**]({$url})";
            },
            $markdown
        );

        return $markdown;
    }

    /**
     * HTML'i temizle ve formatla
     *
     * @param string $html
     * @return string
     */
    protected function cleanHtml(string $html): string
    {
        // Link'lere target="_blank" ve class ekle
        $html = preg_replace_callback(
            '/<a href="([^"]+)">/',
            function ($matches) {
                $href = $matches[1];

                // Internal link mi external mi kontrol et
                $isExternal = !str_starts_with($href, '/') && !str_starts_with($href, 'tel:') && !str_starts_with($href, 'mailto:');

                // Shop link class
                if (str_starts_with($href, '/shop/')) {
                    $class = 'text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium underline transition-colors';
                }
                // Category link class
                elseif (str_contains($href, '/category/')) {
                    $class = 'text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 font-medium underline transition-colors';
                }
                // External link class
                elseif ($isExternal) {
                    $class = 'text-blue-500 hover:text-blue-700 underline';
                }
                // Default class
                else {
                    $class = 'text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 underline transition-colors';
                }

                // Target attribute
                $target = ($isExternal || str_starts_with($href, '/shop/') || str_starts_with($href, '/blog/'))
                    ? 'target="_blank" rel="noopener noreferrer"'
                    : '';

                return "<a href=\"{$href}\" {$target} class=\"{$class}\">";
            },
            $html
        );

        // Gereksiz boşlukları temizle
        $html = preg_replace('/\s+/', ' ', $html);
        $html = trim($html);

        return $html;
    }

    /**
     * Markdown'ı inline HTML'e çevir (paragraf tag'leri olmadan)
     *
     * @param string $markdown
     * @return string
     */
    public function parseInline(string $markdown): string
    {
        $html = $this->parse($markdown);

        // <p> tag'lerini kaldır
        $html = preg_replace('/<\/?p>/', '', $html);

        return trim($html);
    }
}
