<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Log;
use Modules\AI\App\Models\AIFeature;

/**
 * Smart Response Formatter - Monoton 1-2-3 formatını kıran akıllı formatlayıcı
 * 
 * PROBLEM: AI sürekli 1-2-3 şeklinde madde listesi veriyor
 * ÇÖZÜM: Feature'a ve içeriğe göre dinamik format belirleme
 */
class SmartResponseFormatter
{
    /**
     * Strictness levels - Her feature'ın katılık seviyesi
     * 
     * STRICT: Format değişmez (çeviri, kod üretim)
     * FLEXIBLE: Karma format (blog, SEO analiz)  
     * ADAPTIVE: Tamamen serbest (yaratıcı yazım)
     */
    public const STRICTNESS_LEVELS = [
        // STRICT - Format korunur
        'cevirmen' => 'strict',
        'dil-cevirisi' => 'strict',
        'ingilizce-turkce' => 'strict',
        'kod-uret' => 'strict',
        'sql-sorgu' => 'strict',
        'regex-olustur' => 'strict',
        
        // FLEXIBLE - Karma format
        'blog-yazisi-jeneratoru' => 'flexible',
        'hizli-seo-analizi' => 'flexible',
        'makale-olustur' => 'flexible',
        'icerik-optimizasyon' => 'flexible',
        'meta-etiket-olustur' => 'flexible',
        'anahtar-kelime-analiz' => 'flexible',
        
        // ADAPTIVE - Serbest format
        'yaratici-yazi' => 'adaptive',
        'hikaye-yaz' => 'adaptive',
        'icerik-uret' => 'adaptive',
        'yaratici-fikir' => 'adaptive',
    ];

    /**
     * Format detection patterns - İçerikten format çıkarımı
     */
    public const FORMAT_PATTERNS = [
        'comparison' => [
            'karşılaştır', 'vs', 'fark', 'avantaj.*dezavantaj', 
            'farkı.*nedir', 'hangisi.*daha', 'tercih.*etmeli'
        ],
        'list' => [
            'özellik', 'adım', 'yöntem', 'ipucu', 'tavsiye', 
            'neden', 'sebep', 'faktör', 'kriter'
        ],
        'table' => [
            'analiz', 'rapor', 'sonuç', 'değerlendirme',
            'puan', 'skor', 'metrik', 'ölçüm'
        ],
        'narrative' => [
            'hikaye', 'anlatım', 'açıklama', 'tanıtım',
            'hakkında', 'nasıl', 'nedir', 'yaratıcı'
        ],
        'code' => [
            'kod', 'fonksiyon', 'script', 'query',
            'algoritma', 'regex', 'sql', 'javascript'
        ]
    ];

    /**
     * Ana format metoduj - Giriş noktası
     */
    public function format(string $input, string $output, AIFeature $feature): string
    {
        try {
            $strictness = $this->getStrictnessLevel($feature->slug);
            
            Log::info('🎨 SmartResponseFormatter started', [
                'feature' => $feature->slug,
                'strictness' => $strictness,
                'input_length' => strlen($input),
                'output_length' => strlen($output)
            ]);
            
            switch ($strictness) {
                case 'strict':
                    return $this->applyStrictFormat($input, $output, $feature);
                case 'flexible':
                    return $this->applyFlexibleFormat($input, $output, $feature);
                case 'adaptive':
                    return $this->applyAdaptiveFormat($output, $feature);
                default:
                    return $this->applyFlexibleFormat($input, $output, $feature);
            }
            
        } catch (\Exception $e) {
            Log::error('SmartResponseFormatter error', [
                'error' => $e->getMessage(),
                'feature' => $feature->slug
            ]);
            return $output; // Hata durumunda orijinal yanıtı döndür
        }
    }

    /**
     * Strict Format - INPUT formatını korur
     */
    private function applyStrictFormat(string $input, string $output, AIFeature $feature): string
    {
        $inputFormat = $this->detectInputFormat($input);
        
        Log::info('🔒 Strict format applied', [
            'feature' => $feature->slug,
            'detected_format' => $inputFormat
        ]);
        
        switch ($inputFormat) {
            case 'numbered_list':
                return $this->convertToNumberedList($output);
            case 'bullet_list':
                return $this->convertToBulletList($output);
            case 'table_request':
                return $this->convertToTable($output);
            case 'simple_question':
                return $this->keepSimpleAnswer($output);
            default:
                return $this->removeListing($output); // 1-2-3'ü kaldır
        }
    }

    /**
     * Flexible Format - İçeriğe göre UYGUN format seçer
     */
    private function applyFlexibleFormat(string $input, string $output, AIFeature $feature): string
    {
        Log::info('🎨 Flexible format applied', [
            'feature' => $feature->slug
        ]);
        
        // Blog yazısı özel işlemi
        if (str_contains($feature->slug, 'blog')) {
            return $this->formatBlogPost($input, $output, $feature);
        }
        
        // SEO analizi özel işlemi  
        if (str_contains($feature->slug, 'seo')) {
            return $this->formatSeoAnalysis($input, $output, $feature);
        }
        
        // Genel flexible formatting
        return $this->formatGenericFlexible($input, $output, $feature);
    }

    /**
     * Adaptive Format - Tamamen serbest, yaratıcı format
     */
    private function applyAdaptiveFormat(string $output, AIFeature $feature): string
    {
        Log::info('🌟 Adaptive format applied', [
            'feature' => $feature->slug
        ]);
        
        // Adaptive format'ta sadece liste formatını kırarız
        return $this->enhanceCreativeFormat($output);
    }

    /**
     * Blog yazısı özel formatı
     */
    private function formatBlogPost(string $input, string $output, AIFeature $feature): string
    {
        $segments = [];
        
        // Giriş paragrafı çıkar
        $intro = $this->extractIntro($output);
        $remainingContent = $output;
        
        if ($intro) {
            $segments[] = "<div class='blog-intro mb-4'>{$intro}</div>";
            // Intro'yu içerikten çıkar ki tekrar etmesin
            $remainingContent = str_replace($intro, '', $remainingContent);
            $remainingContent = trim($remainingContent);
        }
        
        // Ana içerik başlıklara böl (intro çıkarılmış hali)
        $content = $this->createHeadingSections($remainingContent ?: $output);
        if ($content) {
            $segments[] = "<div class='blog-content'>{$content}</div>";
        }
        
        // Örnekler varsa kutulara al
        $examples = $this->extractAndFormatExamples($output);
        if ($examples) {
            $segments[] = "<div class='blog-examples mt-4'>{$examples}</div>";
        }
        
        // Sonuç paragrafı
        $conclusion = $this->extractConclusion($output);
        if ($conclusion) {
            $segments[] = "<div class='blog-conclusion mt-4 p-3 bg-light rounded'>{$conclusion}</div>";
        }
        
        return implode("\n\n", array_filter($segments));
    }

    /**
     * SEO analizi özel formatı
     */
    private function formatSeoAnalysis(string $input, string $output, AIFeature $feature): string
    {
        $formatted = [];
        
        // SEO skoru tablosu oluştur
        $scoreTable = $this->createSeoScoreTable($output);
        if ($scoreTable) {
            $formatted[] = "<h4>📊 SEO Analiz Sonuçları</h4>";
            $formatted[] = $scoreTable;
        }
        
        // Anahtar kelime analizi
        $keywordAnalysis = $this->extractKeywordAnalysis($output);
        if ($keywordAnalysis) {
            $formatted[] = "<h5 class='mt-4'>🔍 Anahtar Kelime Analizi</h5>";
            $formatted[] = $keywordAnalysis;
        }
        
        // İyileştirme önerileri
        $suggestions = $this->extractSuggestions($output);
        if ($suggestions) {
            $formatted[] = "<h5 class='mt-4'>📝 İyileştirme Önerileri</h5>";
            $formatted[] = $this->createPrettyList($suggestions);
        }
        
        // Teknik detaylar
        $technicalDetails = $this->extractTechnicalDetails($output);
        if ($technicalDetails) {
            $formatted[] = "<h5 class='mt-4'>⚙️ Teknik Detaylar</h5>";
            $formatted[] = $this->createTechnicalTable($technicalDetails);
        }
        
        return implode("\n", array_filter($formatted));
    }

    /**
     * Input format tespiti
     */
    private function detectInputFormat(string $input): string
    {
        if (preg_match('/^\s*\d+[\.\)]\s/m', $input)) {
            return 'numbered_list';
        }
        
        if (preg_match('/^\s*[-\*\•]\s/m', $input)) {
            return 'bullet_list';
        }
        
        if (preg_match('/(tablo|table|listele|karşılaştır)/i', $input)) {
            return 'table_request';
        }
        
        if (preg_match('/\?$/', trim($input))) {
            return 'simple_question';
        }
        
        return 'paragraph';
    }

    /**
     * Monoton listeyi kaldır - CORE işlem
     */
    private function removeListing(string $text): string
    {
        Log::info('🔧 removeListing started', ['original_text' => $text]);
        
        // Escape slash'ları düzelt (\n gerçek newline'a çevir)
        $text = str_replace('\\n', "\n", $text);
        
        // 1. 2. 3. formatını kaldır ve başlığa çevir
        $text = preg_replace('/^\s*\d+[\.\)]\s+([^\n]+)/m', "**$1**", $text);
        
        // - • * formatını kaldır
        $text = preg_replace('/^\s*[-\*\•]\s+([^\n]+)/m', "$1", $text);
        
        // Paragrafları düzgün böl
        $lines = explode("\n", $text);
        $paragraphs = [];
        $currentParagraph = '';
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                // Boş satır - paragraf bitir
                if (!empty($currentParagraph)) {
                    $paragraphs[] = "<p>{$currentParagraph}</p>";
                    $currentParagraph = '';
                }
            } else {
                // Bold başlık kontrolü
                if (preg_match('/^\*\*(.+)\*\*$/', $line, $matches)) {
                    // Önceki paragrafı bitir
                    if (!empty($currentParagraph)) {
                        $paragraphs[] = "<p>{$currentParagraph}</p>";
                        $currentParagraph = '';
                    }
                    // Başlık olarak ekle
                    $paragraphs[] = "<h5>{$matches[1]}</h5>";
                } else {
                    // Normal içerik
                    $currentParagraph .= ($currentParagraph ? ' ' : '') . $line;
                }
            }
        }
        
        // Son paragraf varsa ekle
        if (!empty($currentParagraph)) {
            $paragraphs[] = "<p>{$currentParagraph}</p>";
        }
        
        $result = implode("\n", $paragraphs);
        Log::info('🔧 removeListing completed', ['result' => $result]);
        
        return $result;
    }

    /**
     * Pretty list oluştur - Güzel görünümlü liste
     */
    private function createPrettyList(array $items): string
    {
        $html = "<ul class='list-unstyled'>";
        foreach ($items as $item) {
            $html .= "<li class='d-flex align-items-start mb-2'>";
            $html .= "<i class='fas fa-check-circle text-success me-2 mt-1'></i>";
            $html .= "<span>{$item}</span>";
            $html .= "</li>";
        }
        $html .= "</ul>";
        
        return $html;
    }

    /**
     * SEO skor tablosu oluştur
     */
    private function createSeoScoreTable(string $content): string
    {
        // SEO skorlarını çıkarmaya çalış
        $scores = $this->extractScores($content);
        
        if (empty($scores)) {
            return '';
        }
        
        $html = "<table class='table table-bordered'>";
        $html .= "<thead><tr><th>Metrik</th><th>Değer</th><th>Durum</th></tr></thead>";
        $html .= "<tbody>";
        
        foreach ($scores as $metric => $data) {
            $status = $this->getStatusIcon($data['score']);
            $html .= "<tr>";
            $html .= "<td><strong>{$metric}</strong></td>";
            $html .= "<td>{$data['value']}</td>";
            $html .= "<td>{$status}</td>";
            $html .= "</tr>";
        }
        
        $html .= "</tbody></table>";
        
        return $html;
    }

    /**
     * Strictness level belirleme
     */
    private function getStrictnessLevel(string $featureSlug): string
    {
        return self::STRICTNESS_LEVELS[$featureSlug] ?? 'flexible';
    }

    /**
     * Giriş paragrafı çıkar
     */
    private function extractIntro(string $content): ?string
    {
        $lines = explode("\n", $content);
        $intro = '';
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // İlk anlamlı paragraf
            if (strlen($line) > 50 && !preg_match('/^\d+[\.\)]/', $line)) {
                $intro = $line;
                break;
            }
        }
        
        return $intro ?: null;
    }

    /**
     * Başlıklı bölümler oluştur
     */
    private function createHeadingSections(string $content): string
    {
        $sections = [];
        $lines = explode("\n", $content);
        $currentSection = '';
        $currentTitle = '';
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Başlık tespiti
            if ($this->isHeading($line)) {
                if ($currentSection) {
                    $sections[] = "<h5>{$currentTitle}</h5><p>{$currentSection}</p>";
                }
                $currentTitle = $this->cleanHeading($line);
                $currentSection = '';
            } else {
                $currentSection .= ($currentSection ? ' ' : '') . $line;
            }
        }
        
        if ($currentSection) {
            $sections[] = "<h5>{$currentTitle}</h5><p>{$currentSection}</p>";
        }
        
        return implode("\n", $sections);
    }

    /**
     * Başlık mı kontrolü
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

    /**
     * Başlığı temizle
     */
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
     * Yaratıcı format geliştir
     */
    private function enhanceCreativeFormat(string $content): string
    {
        // Liste formatını kaldır ama içeriği koru
        $enhanced = $this->removeListing($content);
        
        // Emoji ekle (yaratıcı yazılarda uygun)
        $enhanced = $this->addCreativeEmojis($enhanced);
        
        return $enhanced;
    }

    /**
     * Yaratıcı emoji ekleme
     */
    private function addCreativeEmojis(string $content): string
    {
        $emojiMap = [
            'başlık' => '📝',
            'örnek' => '💡',
            'önemli' => '⚠️',
            'sonuç' => '🎯',
            'tavsiye' => '💫'
        ];
        
        foreach ($emojiMap as $keyword => $emoji) {
            $content = preg_replace("/\b{$keyword}\b/ui", "{$emoji} $keyword", $content);
        }
        
        return $content;
    }

    /**
     * Basit helper metodları
     */
    private function extractScores(string $content): array
    {
        // Basit score extraction - daha geliştirilecek
        return [];
    }

    private function getStatusIcon(int $score): string
    {
        if ($score >= 80) return '✅ Mükemmel';
        if ($score >= 60) return '⚠️ İyi';
        return '❌ Geliştirilmeli';
    }

    private function extractSuggestions(string $content): array
    {
        // Önerileri çıkar
        return [];
    }

    private function extractKeywordAnalysis(string $content): ?string
    {
        // Anahtar kelime analizini çıkar
        return null;
    }

    private function extractTechnicalDetails(string $content): array
    {
        // Teknik detayları çıkar
        return [];
    }

    private function createTechnicalTable(array $details): string
    {
        return "<div class='alert alert-info'>Teknik detaylar geliştirilecek</div>";
    }

    private function extractAndFormatExamples(string $content): ?string
    {
        return null; // Örnekleri çıkar ve formatla
    }

    private function extractConclusion(string $content): ?string
    {
        return null; // Sonuç paragrafını çıkar
    }

    private function formatGenericFlexible(string $input, string $output, AIFeature $feature): string
    {
        // Genel flexible formatting
        return $this->removeListing($output);
    }

    private function convertToNumberedList(string $content): string
    {
        // Numaralı listeye çevir
        return $content;
    }

    private function convertToBulletList(string $content): string
    {
        // Madde listesine çevir
        return $content;
    }

    private function convertToTable(string $content): string
    {
        // Tablo formatına çevir
        return $content;
    }

    private function keepSimpleAnswer(string $content): string
    {
        // Basit yanıt olarak bırak
        return $this->removeListing($content);
    }
}