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
        // 🔧 FIX 0: Çoklu ürün - Her ⭐ yeni satırda başlamalı
        // Problem: "Fiyat: $X ⭐ Ürün 2" → "Fiyat: $X\n\n⭐ Ürün 2"
        $markdown = $this->separateMultipleProducts($markdown);

        // 🔧 FIX 1: AI tire ile başlayan satırları markdown list formatına çevir (ÖNCE!)
        // AI yazdığı: "[LINK] - özellik1 - özellik2 - özellik3"
        // Markdown: "[LINK]\n\n- özellik1\n- özellik2\n- özellik3"
        $markdown = $this->fixInlineListsToMarkdown($markdown);

        // SONRA custom link formatlarını işle
        $markdown = $this->processCustomLinks($markdown);

        // Standard markdown'ı parse et
        $html = $this->converter->convert($markdown)->getContent();

        // HTML'i temizle ve formatla
        $html = $this->cleanHtml($html);

        return $html;
    }

    /**
     * Çoklu ürün gösteriminde her ⭐'yı yeni satıra al
     *
     * @param string $markdown
     * @return string
     */
    protected function separateMultipleProducts(string $markdown): string
    {
        // ⭐ öncesinde 2 satır boşluk yoksa ekle
        // "... text ⭐ Ürün" → "... text\n\n⭐ Ürün"
        $markdown = preg_replace('/([^\n])\s*⭐/u', "$1\n\n⭐", $markdown);

        // ⭐'dan sonra doğrudan [LINK] veya ** geliyorsa arada boşluk olsun
        $markdown = preg_replace('/⭐\s*(\[LINK|\*\*)/u', "⭐ $1", $markdown);

        return $markdown;
    }

    /**
     * AI'nin yanında yazdığı tire'li özellikleri markdown list formatına çevir
     *
     * ÖNCE: "Ürün Adı** [LINK] - özellik1 - özellik2 - özellik3"
     * SONRA: "Ürün Adı** [LINK]\n\n- özellik1\n- özellik2\n- özellik3"
     *
     * @param string $markdown
     * @return string
     */
    protected function fixInlineListsToMarkdown(string $markdown): string
    {
        // Pattern: Link'ten sonra tire ile başlayan özellikler
        // AI yazdığı: "**Ürün** [LINK] - özellik1 - özellik2 - özellik3 Fiyat: X"
        // Hedef: "**Ürün** [LINK]\n\n- özellik1\n- özellik2\n- özellik3\n\nFiyat: X"

        // ADIM 1: Link'ten sonra tire'ye kadar olan kısmı bul ve satırları ayır
        $markdown = preg_replace_callback(
            '/(\[LINK:[^\]]+\])\s+([^⭐]+?)(?=\s*(?:Fiyat:|⭐|$))/us',
            function ($matches) {
                $link = $matches[1]; // [LINK:shop:slug]
                $content = trim($matches[2]); // "- özellik1 - özellik2 - özellik3"

                // Eğer tire varsa listeye çevir
                if (strpos($content, ' - ') !== false) {
                    // Tire ile başlıyorsa kaldır
                    $content = preg_replace('/^\s*-\s*/', '', $content);

                    // Tire ile ayrılmış özellikleri parçala
                    $items = preg_split('/\s+-\s+/', $content, -1, PREG_SPLIT_NO_EMPTY);

                    if (count($items) > 1) {
                        // Son item'da "Fiyat:" veya "⭐" varsa ayır
                        $lastItem = array_pop($items);
                        $priceText = "";

                        // "Fiyat:" ve sonrasını ayır
                        if (preg_match('/(.+?)\s+(Fiyat:.*)$/us', $lastItem, $priceMatch)) {
                            $items[] = trim($priceMatch[1]); // Özellik
                            $priceText = "\n\n" . trim($priceMatch[2]); // Fiyat ayrı satır
                        } else {
                            $items[] = $lastItem;
                        }

                        // Her item'dan "⭐" sonrasını temizle (yeni ürün başlıyorsa)
                        $items = array_map(function($item) {
                            // "Fiyat: X ⭐" gibi durumlar için
                            if (preg_match('/^(.*?)\s*⭐/us', $item, $match)) {
                                return trim($match[1]);
                            }
                            return $item;
                        }, $items);

                        // Boş item'ları temizle
                        $items = array_filter($items, fn($item) => !empty(trim($item)));

                        // Markdown list formatına çevir
                        $list = "\n\n" . implode("\n", array_map(fn($item) => "- " . trim($item), $items)) . $priceText . "\n";
                        return $link . $list;
                    }
                }

                // Değişiklik yoksa olduğu gibi döndür
                return $link . ' ' . $content;
            },
            $markdown
        );

        return $markdown;
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
        // 🔧 FIX: DOM tabanlı HTML düzeltme (daha güvenilir)
        $html = $this->fixHtmlStructureWithDom($html);

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

        return $html;
    }

    /**
     * DOM tabanlı HTML yapı düzeltme
     *
     * @param string $html
     * @return string
     */
    protected function fixHtmlStructureWithDom(string $html): string
    {
        // Boş content kontrol
        if (empty(trim($html))) {
            return $html;
        }

        // UTF-8 encoding için meta tag ekle
        $htmlWrapped = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>' . $html . '</body></html>';

        // DOM parser oluştur
        $dom = new \DOMDocument('1.0', 'UTF-8');

        // Hataları bastır (malformed HTML için)
        libxml_use_internal_errors(true);

        // HTML'i yükle
        $dom->loadHTML($htmlWrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // Hataları temizle
        libxml_clear_errors();

        // XPath oluştur
        $xpath = new \DOMXPath($dom);

        // 🔧 FIX 1: <ul> içindeki yanlış yerleştirilmiş elementleri düzelt
        $ulElements = $xpath->query('//ul');
        foreach ($ulElements as $ul) {
            $nodesToMove = [];

            // <ul> içindeki direkt child'ları kontrol et
            foreach ($ul->childNodes as $child) {
                // Text node ise ve boş değilse
                if ($child->nodeType === XML_TEXT_NODE) {
                    $text = trim($child->textContent);
                    if (!empty($text)) {
                        // Text'i <li> içine al
                        $li = $dom->createElement('li');
                        $li->textContent = $text;
                        $nodesToMove[] = ['old' => $child, 'new' => $li];
                    }
                }
                // <a>, <p>, <strong> gibi taglar <ul> içinde direkt ise
                elseif ($child->nodeType === XML_ELEMENT_NODE && !in_array($child->nodeName, ['li'])) {
                    // Bu elementi <li> içine al
                    $li = $dom->createElement('li');
                    $clonedChild = $child->cloneNode(true);
                    $li->appendChild($clonedChild);
                    $nodesToMove[] = ['old' => $child, 'new' => $li];
                }
            }

            // Değişiklikleri uygula
            foreach ($nodesToMove as $move) {
                $ul->replaceChild($move['new'], $move['old']);
            }
        }

        // 🔧 FIX 2: Boş tagları temizle
        $emptyTags = $xpath->query('//ul[not(normalize-space())] | //ol[not(normalize-space())] | //p[not(normalize-space())] | //li[not(normalize-space())]');
        foreach ($emptyTags as $tag) {
            $tag->parentNode->removeChild($tag);
        }

        // Body içeriğini al
        $body = $dom->getElementsByTagName('body')->item(0);
        $result = '';
        foreach ($body->childNodes as $node) {
            $result .= $dom->saveHTML($node);
        }

        // Fazla boşlukları temizle
        $result = preg_replace('/\s+/', ' ', $result);
        $result = trim($result);

        return $result;
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
