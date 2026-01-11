<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Log;

class HtmlTranslationService
{
    private $aiService;

    public function __construct($aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * 🔥 UZUN HTML İÇERİK ÇEVİRİ SİSTEMİ - TOKEN LİMİT AŞIMI ENGELLEYİCİ
     * Uzun HTML içeriği parçalara böler ve sadece text kısımlarını çevirir
     */
    public function translateLongHtmlContent(string $html, string $fromLang, string $toLang, string $context): string
    {
        Log::info('🔧 Uzun HTML chunk çeviri başlıyor', [
            'html_length' => strlen($html),
            'from_lang' => $fromLang,
            'to_lang' => $toLang
        ]);

        try {
            // HTML'deki tüm text nodeları bul ve çevir
            $dom = new \DOMDocument('1.0', 'UTF-8');
            
            // HTML parse hatalarını bastır
            $originalErrorSetting = libxml_use_internal_errors(true);
            
            // UTF-8 desteği için meta tag ekle
            $htmlWithMeta = '<meta charset="UTF-8">' . $html;
            $dom->loadHTML($htmlWithMeta, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            
            $xpath = new \DOMXPath($dom);
            
            // Sadece text nodeları bul (element içinde olmayan)
            $textNodes = $xpath->query('//text()[normalize-space()]');
            
            $translatedTexts = [];
            $originalTexts = [];
            
            foreach ($textNodes as $textNode) {
                $originalText = trim($textNode->nodeValue);
                
                // Boş veya çok kısa metinleri atla
                if (strlen($originalText) < 3) {
                    continue;
                }
                
                // Sadece sayı veya sembol olan metinleri atla
                if (preg_match('/^[\d\s\-\.\,\+\*\/\=\(\)]+$/', $originalText)) {
                    continue;
                }
                
                $originalTexts[] = $originalText;
                
                // Her text node'u ayrı ayrı çevir
                $translatedText = $this->aiService->translateText(
                    $originalText,
                    $fromLang,
                    $toLang,
                    [
                        'context' => 'html_text_node',
                        'preserve_html' => false
                    ]
                );
                
                $translatedTexts[] = $translatedText;
                $textNode->nodeValue = $translatedText;
            }
            
            // HTML'i geri çıkar (meta tag'ı çıkar)
            $translatedHtml = $dom->saveHTML();
            $translatedHtml = preg_replace('/<meta charset="UTF-8">/', '', $translatedHtml);
            
            // libxml hata ayarını geri yükle
            libxml_use_internal_errors($originalErrorSetting);
            
            Log::info('✅ HTML chunk çeviri tamamlandı', [
                'original_length' => strlen($html),
                'translated_length' => strlen($translatedHtml),
                'text_nodes_translated' => count($translatedTexts)
            ]);
            
            return trim($translatedHtml);
            
        } catch (\Exception $e) {
            Log::error('❌ HTML chunk çeviri hatası', [
                'error' => $e->getMessage(),
                'html_length' => strlen($html)
            ]);
            
            // Fallback: Normal çeviri yap (kesilse bile)
            return $this->aiService->translateText($html, $fromLang, $toLang, ['context' => $context, 'preserve_html' => true]);
        }
    }
}