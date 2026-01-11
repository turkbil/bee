<?php

namespace Modules\AI\App\Services\Response;

use Illuminate\Support\Facades\Log;

/**
 * AI Response Parsers - Yanıt parsing servisi
 * 
 * AIResponseRepository'den ayrılmış parser metodları:
 * - parseAIResponse (genel JSON parsing)
 * - parseTextResponse (metin parsing)
 * - parseTranslationResponse (çeviri parsing)
 * - parseContentResponse (içerik parsing)
 * - parseSchemaResponse (schema parsing)
 * - parseSEOResponse (SEO parsing)
 * - extractXXX metodları (özel veri çıkarma)
 */
class AIResponseParsers
{
    /**
     * Genel AI response parsing
     */
    public function parseAIResponse(string $response): array
    {
        try {
            // JSON formatında mı kontrol et
            $decoded = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }

            // Text formatında parse et
            return $this->parseTextResponse($response);

        } catch (\Exception $e) {
            Log::warning('AI Response parsing failed', [
                'error' => $e->getMessage(),
                'response_length' => strlen($response)
            ]);

            return [
                'content' => $response,
                'type' => 'raw_text',
                'parsed' => false
            ];
        }
    }

    /**
     * Text response parsing
     */
    public function parseTextResponse(string $response): array
    {
        $lines = explode("\n", $response);
        $sections = [];
        $currentSection = null;
        $currentContent = '';

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Başlık tespiti (1., 2., ##, **, büyük harf)
            if ($this->isHeading($line)) {
                // Önceki bölümü kaydet
                if ($currentSection && $currentContent) {
                    $sections[$currentSection] = trim($currentContent);
                }

                $currentSection = $this->cleanHeading($line);
                $currentContent = '';
            } else {
                $currentContent .= ($currentContent ? "\n" : '') . $line;
            }
        }

        // Son bölümü kaydet
        if ($currentSection && $currentContent) {
            $sections[$currentSection] = trim($currentContent);
        }

        return [
            'sections' => $sections,
            'raw_content' => $response,
            'type' => 'structured_text',
            'section_count' => count($sections)
        ];
    }

    /**
     * Çeviri response parsing
     */
    public function parseTranslationResponse(string $response): array
    {
        // Çeviri formatlarını yakala
        $patterns = [
            // "Original: ... | Translated: ..." formatı
            '/Original:\s*(.+?)\s*\|\s*Translated:\s*(.+)/is',
            '/Orijinal:\s*(.+?)\s*\|\s*Çeviri:\s*(.+)/is',
            // "From: ... To: ..." formatı
            '/From:\s*(.+?)\s*To:\s*(.+)/is',
            // JSON formatı
            '/"original":\s*"([^"]+)"\s*,\s*"translated":\s*"([^"]+)"/is'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $response, $matches)) {
                return [
                    'original' => trim($matches[1]),
                    'translated' => trim($matches[2]),
                    'type' => 'translation_pair',
                    'success' => true
                ];
            }
        }

        // Tek bir çeviri metni (orijinal yok)
        return [
            'translated' => $response,
            'type' => 'translation_only',
            'success' => true
        ];
    }

    /**
     * İçerik üretimi response parsing
     */
    public function parseContentResponse(string $response): array
    {
        $lines = explode("\n", $response);
        $title = null;
        $content = '';
        $meta = [];

        // İlk satır başlık olabilir
        $firstLine = trim($lines[0] ?? '');
        if (strlen($firstLine) > 10 && strlen($firstLine) < 100 && !str_contains($firstLine, '.')) {
            $title = $firstLine;
            $content = implode("\n", array_slice($lines, 1));
        } else {
            $content = $response;
        }

        // Meta bilgileri hesapla
        $words = str_word_count($content);
        $chars = strlen($content);

        $meta = [
            'word_count' => $words,
            'char_count' => $chars,
            'estimated_read_time' => ceil($words / 200), // dakika
            'has_title' => !is_null($title)
        ];

        return [
            'title' => $title,
            'content' => trim($content),
            'meta' => $meta,
            'type' => 'content_generation'
        ];
    }

    /**
     * Schema markup response parsing
     */
    public function parseSchemaResponse(string $response): array
    {
        // JSON-LD formatını yakala
        if (preg_match('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is', $response, $matches)) {
            $jsonLd = trim($matches[1]);
            
            try {
                $decoded = json_decode($jsonLd, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return [
                        'schema' => $jsonLd,
                        'parsed' => $decoded,
                        'type' => 'json_ld',
                        'success' => true
                    ];
                }
            } catch (\Exception $e) {
                Log::warning('Schema JSON parsing failed', ['error' => $e->getMessage()]);
            }
        }

        // Sadece JSON formatı
        try {
            $decoded = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return [
                    'schema' => $response,
                    'parsed' => $decoded,
                    'type' => 'json_only',
                    'success' => true
                ];
            }
        } catch (\Exception $e) {
            // Ignore JSON parsing errors
        }

        // Raw schema kodu
        return [
            'schema' => $response,
            'type' => 'raw_schema',
            'success' => true
        ];
    }

    /**
     * SEO response parsing
     */
    public function parseSEOResponse(string $response): array
    {
        $seoData = [
            'score' => $this->extractSEOScore($response),
            'issues' => $this->extractSEOIssues($response),
            'recommendations' => $this->extractRecommendations($response),
            'technical_details' => $this->extractTechnicalDetails($response),
            'keywords' => $this->extractKeywords($response),
            'meta_info' => $this->extractMetaInfo($response)
        ];

        return [
            'seo_data' => $seoData,
            'raw_response' => $response,
            'type' => 'seo_analysis',
            'parsed_successfully' => !empty(array_filter($seoData))
        ];
    }

    /**
     * SEO skoru çıkarma
     */
    private function extractSEOScore(string $response): int
    {
        // Skor formatlarını yakala
        $patterns = [
            '/SEO\s*(?:Score|Skor|Puan)[:\s]*(\d+)(?:\/100|%)?/i',
            '/(?:Score|Skor|Puan)[:\s]*(\d+)(?:\/100|%)?/i',
            '/(\d+)\/100/i',
            '/(\d+)%/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $response, $matches)) {
                $score = intval($matches[1]);
                // 0-100 arası kontrolü
                return max(0, min(100, $score));
            }
        }

        return 50; // Varsayılan skor
    }

    /**
     * SEO sorunları çıkarma
     */
    private function extractSEOIssues(string $response): array
    {
        $issues = [];
        $patterns = [
            '/(?:Sorun|Issue|Problem)[s]?[:\s]*([^\n]+)/i',
            '/(?:Hata|Error)[s]?[:\s]*([^\n]+)/i',
            '/(?:Eksik|Missing)[s]?[:\s]*([^\n]+)/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $response, $matches)) {
                foreach ($matches[1] as $issue) {
                    $issues[] = trim($issue);
                }
            }
        }

        return array_unique($issues);
    }

    /**
     * Önerileri çıkarma
     */
    private function extractRecommendations(string $response): array
    {
        $recommendations = [];
        $patterns = [
            '/(?:Öneri|Recommendation|Suggest)[s]?[:\s]*([^\n]+)/i',
            '/(?:Tavsiye|Advice)[s]?[:\s]*([^\n]+)/i',
            '/(?:İyileştirme|Improvement)[s]?[:\s]*([^\n]+)/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $response, $matches)) {
                foreach ($matches[1] as $recommendation) {
                    $recommendations[] = trim($recommendation);
                }
            }
        }

        return array_unique($recommendations);
    }

    /**
     * Teknik detayları çıkarma
     */
    private function extractTechnicalDetails(string $response): array
    {
        $details = [];

        // Meta tag bilgileri
        if (preg_match('/title[:\s]*([^\n]+)/i', $response, $matches)) {
            $details['title'] = trim($matches[1]);
        }

        if (preg_match('/description[:\s]*([^\n]+)/i', $response, $matches)) {
            $details['description'] = trim($matches[1]);
        }

        // H1, H2 bilgileri
        if (preg_match_all('/h[1-6][:\s]*([^\n]+)/i', $response, $matches)) {
            $details['headings'] = array_map('trim', $matches[1]);
        }

        // Kelime sayısı
        if (preg_match('/(?:word|kelime)\s*(?:count|sayısı)[:\s]*(\d+)/i', $response, $matches)) {
            $details['word_count'] = intval($matches[1]);
        }

        return $details;
    }

    /**
     * Anahtar kelimeleri çıkarma
     */
    private function extractKeywords(string $response): array
    {
        $keywords = [];
        $patterns = [
            '/(?:keyword|anahtar kelime)[s]?[:\s]*([^\n]+)/i',
            '/(?:key phrase|anahtar ifade)[s]?[:\s]*([^\n]+)/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $response, $matches)) {
                $keywordString = $matches[1];
                // Virgül, nokta virgül veya | ile ayrılmış kelimeleri parse et
                $keywords = array_merge($keywords, preg_split('/[,;|]/', $keywordString));
            }
        }

        return array_map('trim', array_filter(array_unique($keywords)));
    }

    /**
     * Meta bilgileri çıkarma
     */
    private function extractMetaInfo(string $response): array
    {
        $meta = [];

        // Sayfa hızı
        if (preg_match('/(?:speed|hız)[:\s]*([^\n]+)/i', $response, $matches)) {
            $meta['speed'] = trim($matches[1]);
        }

        // Mobile friendly
        if (preg_match('/(?:mobile|mobil)(?:\s+friendly)?[:\s]*([^\n]+)/i', $response, $matches)) {
            $meta['mobile_friendly'] = trim($matches[1]);
        }

        // SSL durumu
        if (preg_match('/ssl[:\s]*([^\n]+)/i', $response, $matches)) {
            $meta['ssl'] = trim($matches[1]);
        }

        return $meta;
    }

    /**
     * Ana noktaları çıkarma
     */
    public function extractMainPoints(string $response): array
    {
        $points = [];
        $lines = explode("\n", $response);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Numaralı liste (1., 2., 3.)
            if (preg_match('/^\d+\.\s*(.+)/', $line, $matches)) {
                $points[] = trim($matches[1]);
            }
            // Bullet liste (-, *, •)
            elseif (preg_match('/^[-\*•]\s*(.+)/', $line, $matches)) {
                $points[] = trim($matches[1]);
            }
            // Kısa cümleler (50-200 karakter arası)
            elseif (strlen($line) > 50 && strlen($line) < 200 && !str_contains($line, ':')) {
                $points[] = $line;
            }
        }

        return array_slice($points, 0, 10); // İlk 10 nokta
    }

    /**
     * JSON formatı kontrol et
     */
    public function tryParseResponse(string $response): mixed
    {
        try {
            $decoded = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        } catch (\Exception $e) {
            // JSON değil, normal metin olarak devam et
        }

        return null;
    }

    /**
     * Helper metodları
     */
    private function isHeading(string $line): bool
    {
        // Çeşitli başlık formatlarını yakala
        $patterns = [
            '/^##?\s+(.+)$/', // Markdown başlıklar
            '/^([A-Za-züğşçıÜĞŞÇI][^\.]{3,50}):?\s*$/', // Normal başlık formatı
            '/^\d+[\.\)]\s*([A-Za-züğşçıÜĞŞÇI][^\.]+)$/', // Numaralı başlık
            '/^\*\*(.+)\*\*$/', // Bold başlık
            '/^[A-ZÜĞŞÇI]{2,}[a-züğşçıA-Z\s]+$/', // Büyük harf başlık
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, trim($line))) {
                return strlen($line) < 120; // Çok uzun satırlar başlık değil
            }
        }
        
        return false;
    }

    private function cleanHeading(string $heading): string
    {
        $heading = trim($heading);
        
        // Markdown başlık temizle
        $heading = preg_replace('/^##?\s+/', '', $heading);
        
        // Numaralı başlık temizle
        $heading = preg_replace('/^\d+[\.\)]\s*/', '', $heading);
        
        // Bold işareti temizle
        $heading = preg_replace('/^\*\*(.*)\*\*$/', '$1', $heading);
        
        // : işareti temizle
        $heading = rtrim($heading, ':');
        
        return trim($heading);
    }

    /**
     * İçerik tipini tespit et
     */
    public function detectContentType(string $response): string
    {
        // JSON formatı
        if ($this->tryParseResponse($response)) {
            return 'json';
        }

        // HTML formatı
        if (str_contains($response, '<') && str_contains($response, '>')) {
            return 'html';
        }

        // Schema markup
        if (str_contains($response, 'application/ld+json') || str_contains($response, '@context')) {
            return 'schema';
        }

        // Liste formatı
        if (preg_match('/^\d+\.\s/m', $response) || preg_match('/^[-\*]\s/m', $response)) {
            return 'list';
        }

        // Başlık formatı
        if (preg_match('/^##?\s/m', $response) || preg_match('/\*\*[^*]+\*\*/m', $response)) {
            return 'structured';
        }

        return 'text';
    }

    /**
     * Confidence score hesapla
     */
    public function calculateConfidenceScore(array $parsedData): int
    {
        $score = 50; // Base score
        
        // Structured data varsa +20
        if (isset($parsedData['sections']) && !empty($parsedData['sections'])) {
            $score += 20;
        }
        
        // JSON format başarılıysa +15
        if (isset($parsedData['parsed']) && $parsedData['parsed'] !== false) {
            $score += 15;
        }
        
        // Meta bilgiler varsa +10
        if (isset($parsedData['meta']) && !empty($parsedData['meta'])) {
            $score += 10;
        }
        
        // Özel alanlar varsa +5
        if (isset($parsedData['seo_data']) || isset($parsedData['translation_data'])) {
            $score += 5;
        }
        
        return max(0, min(100, $score));
    }
}