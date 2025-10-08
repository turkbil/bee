<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;

class TocService
{
    /**
     * HTML içeriğinden Table of Contents oluştur
     */
    public static function generateToc(string $html): array
    {
        if (empty($html)) {
            return [];
        }

        $dom = new DOMDocument();
        $dom->encoding = 'UTF-8';

        // HTML parse etmek için UTF-8 encoding ekle
        $htmlWithEncoding = '<?xml encoding="UTF-8">' . $html;

        // Hataları suppress et
        libxml_use_internal_errors(true);
        $dom->loadHTML($htmlWithEncoding, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $headings = $xpath->query('//h1 | //h2 | //h3 | //h4 | //h5 | //h6');

        $toc = [];
        $headingCounts = [];

        foreach ($headings as $heading) {
            $level = (int) substr($heading->tagName, 1);
            $text = trim($heading->textContent);

            if (empty($text)) {
                continue;
            }

            // Unique ID oluştur
            $baseSlug = static::createSlug($text);
            $slug = $baseSlug;
            $counter = 1;

            while (in_array($slug, array_column($toc, 'id'))) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            // Heading'e ID ekle
            $heading->setAttribute('id', $slug);

            $toc[] = [
                'id' => $slug,
                'text' => $text,
                'level' => $level,
                'tag' => $heading->tagName
            ];
        }

        return static::buildHierarchy($toc);
    }

    /**
     * HTML içeriğine heading anchor'ları ekle
     */
    public static function addHeadingAnchors(string $html): string
    {
        if (empty($html)) {
            return $html;
        }

        $dom = new DOMDocument();
        $dom->encoding = 'UTF-8';

        $htmlWithEncoding = '<?xml encoding="UTF-8">' . $html;

        libxml_use_internal_errors(true);
        $dom->loadHTML($htmlWithEncoding, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $headings = $xpath->query('//h1 | //h2 | //h3 | //h4 | //h5 | //h6');

        $headingCounts = [];

        foreach ($headings as $heading) {
            $text = trim($heading->textContent);

            if (empty($text)) {
                continue;
            }

            // Unique ID oluştur
            $baseSlug = static::createSlug($text);
            $existingIds = [];

            // Mevcut ID'leri topla
            $allElements = $xpath->query('//*[@id]');
            foreach ($allElements as $el) {
                $existingIds[] = $el->getAttribute('id');
            }

            $slug = $baseSlug;
            $counter = 1;

            while (in_array($slug, $existingIds)) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            // ID zaten yoksa ekle
            if (!$heading->hasAttribute('id')) {
                $heading->setAttribute('id', $slug);
            }

            // Anchor link butonunu ekle
            $anchorLink = $dom->createElement('a');
            $anchorLink->setAttribute('href', '#' . $slug);
            $anchorLink->setAttribute('class', 'heading-anchor opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-blue-500 hover:text-blue-600 ml-2');
            $anchorLink->setAttribute('title', 'Bu bölüme bağlantı');
            $anchorLink->textContent = '#';

            // Heading'e group class ekle
            $headingClass = $heading->getAttribute('class');
            $heading->setAttribute('class', trim($headingClass . ' group relative'));

            $heading->appendChild($anchorLink);
        }

        // Sadece body içeriğini döndür
        $body = $dom->getElementsByTagName('body')->item(0);
        $result = '';

        if ($body) {
            foreach ($body->childNodes as $child) {
                $result .= $dom->saveHTML($child);
            }
        } else {
            $result = $dom->saveHTML();
        }

        return $result;
    }

    /**
     * String'i URL slug'a çevir
     */
    private static function createSlug(string $text): string
    {
        // Türkçe karakterleri dönüştür
        $turkishChars = ['ç', 'ğ', 'ı', 'ö', 'ş', 'ü', 'Ç', 'Ğ', 'İ', 'Ö', 'Ş', 'Ü'];
        $englishChars = ['c', 'g', 'i', 'o', 's', 'u', 'c', 'g', 'i', 'o', 's', 'u'];

        $text = str_replace($turkishChars, $englishChars, $text);

        // Küçük harfe çevir ve özel karakterleri kaldır
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\-_\s]/', '', $text);
        $text = preg_replace('/[\s_]+/', '-', $text);
        $text = trim($text, '-');

        return $text ?: 'heading';
    }

    /**
     * Düz TOC array'ini hierarchical yapıya çevir
     */
    private static function buildHierarchy(array $toc): array
    {
        if (empty($toc)) {
            return [];
        }

        $hierarchy = [];

        // Level ve referansları ayrı stack'lerde tutarak nested yapı oluştur
        $levelStack = [0];
        $referenceStack = [&$hierarchy];

        foreach ($toc as $item) {
            $item['children'] = [];
            $currentLevel = max(1, (int) $item['level']);

            // Mevcut heading level'ından yüksek veya eşit seviyedeki parent'ları stack'ten çıkar
            while (count($levelStack) > 0 && end($levelStack) >= $currentLevel) {
                array_pop($levelStack);
                array_pop($referenceStack);
            }

            // Child'ı mevcut parent'ın children array'ine ekle
            $referenceStack[count($referenceStack) - 1][] = $item;
            $parentChildren = &$referenceStack[count($referenceStack) - 1];
            $childKey = array_key_last($parentChildren);

            // Yeni child'ın children array'ine referans ekle
            $referenceStack[] = &$parentChildren[$childKey]['children'];
            $levelStack[] = $currentLevel;
        }

        return $hierarchy;
    }

    /**
     * Reading time hesapla (kelime sayısına göre)
     */
    public static function calculateReadingTime(string $content): int
    {
        if (empty($content)) {
            return 0;
        }

        // HTML tag'larını kaldır
        $text = strip_tags($content);

        // Kelime sayısını hesapla
        $wordCount = str_word_count($text);

        // Ortalama okuma hızı: dakikada 200 kelime
        $readingTimeMinutes = ceil($wordCount / 200);

        return max(1, $readingTimeMinutes);
    }
}
