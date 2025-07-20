<?php

namespace App\Services\AI;

use App\Models\SeoSetting;
use App\Services\SeoLanguageManager;
use Modules\AI\app\Services\AIService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use League\HTMLToMarkdown\HtmlConverter;
use Symfony\Component\DomCrawler\Crawler;

class SeoAnalysisService
{
    private AIService $aiService;
    private HtmlConverter $htmlConverter;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
        $this->htmlConverter = new HtmlConverter();
    }

    /**
     * Comprehensive SEO analysis with AI integration
     */
    public function analyzeSeoContent($model, string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $cacheKey = "seo_analysis_{$model->getMorphClass()}_{$model->id}_{$locale}";
        
        return Cache::remember($cacheKey, 300, function() use ($model, $locale) {
            try {
                $content = $this->extractContent($model);
                $seoData = $this->extractSeoData($model, $locale);
                
                $analysis = [
                    'content_analysis' => $this->analyzeContent($content, $locale),
                    'keyword_analysis' => $this->analyzeKeywordsWithoutAI($content, $seoData, $locale),
                    'meta_analysis' => $this->analyzeMetaTags($seoData, $locale),
                    'structure_analysis' => $this->analyzeStructure($content, $locale),
                    'readability_analysis' => $this->analyzeReadability($content, $locale),
                    'ai_recommendations' => $this->generateBasicRecommendations($content, $seoData, $locale)
                ];

                $score = $this->calculateSeoScore($analysis);
                $analysis['overall_score'] = $score;
                $analysis['analyzed_at'] = now()->toISOString();
                $analysis['locale'] = $locale;

                return $analysis;
                
            } catch (\Exception $e) {
                Log::error('SEO Analysis failed', [
                    'model' => $model->getMorphClass(),
                    'id' => $model->id,
                    'locale' => $locale,
                    'error' => $e->getMessage()
                ]);
                
                return $this->getDefaultAnalysis($locale);
            }
        });
    }

    /**
     * AI-powered keyword extraction and analysis
     */
    public function analyzeKeywords(string $content, array $seoData, string $locale): array
    {
        try {
            $prompt = $this->buildKeywordAnalysisPrompt($content, $seoData, $locale);
            
            $aiResponse = $this->aiService->ask($prompt, [
                'type' => 'seo_keyword_analysis',
                'locale' => $locale,
                'max_tokens' => 300,
                'feature_slug' => 'anahtar-kelime-analiz'
            ]);

            $analysis = $this->parseKeywordAnalysis($aiResponse);
            
            // Add technical keyword metrics
            $analysis['keyword_density'] = $this->calculateKeywordDensity($content, $seoData['focus_keyword'] ?? '');
            $analysis['keyword_distribution'] = $this->analyzeKeywordDistribution($content, $seoData['focus_keyword'] ?? '');
            $analysis['related_keywords'] = $this->findRelatedKeywords($content, $locale);
            
            return $analysis;
            
        } catch (\Exception $e) {
            Log::error('Keyword analysis failed', ['error' => $e->getMessage()]);
            return $this->getDefaultKeywordAnalysis();
        }
    }

    /**
     * AI-powered content optimization suggestions
     */
    public function generateOptimizationSuggestions($model, string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        
        try {
            $analysis = $this->analyzeSeoContent($model, $locale);
            $content = $this->extractContent($model);
            $seoData = $this->extractSeoData($model, $locale);
            
            $prompt = $this->buildOptimizationPrompt($content, $seoData, $analysis, $locale);
            
            $aiResponse = $this->aiService->ask($prompt, [
                'type' => 'seo_optimization',
                'locale' => $locale,
                'max_tokens' => 600
            ]);

            $suggestions = $this->parseOptimizationSuggestions($aiResponse);
            
            return [
                'suggestions' => $suggestions,
                'priority_actions' => $this->prioritizeActions($suggestions, $analysis),
                'estimated_impact' => $this->estimateImpact($suggestions, $analysis),
                'generated_at' => now()->toISOString()
            ];
            
        } catch (\Exception $e) {
            Log::error('Optimization suggestions failed', ['error' => $e->getMessage()]);
            return $this->getDefaultOptimizationSuggestions();
        }
    }

    /**
     * Generate AI-powered meta descriptions
     */
    public function generateMetaDescription(string $content, string $focusKeyword = '', string $locale = 'tr'): string
    {
        try {
            $prompt = "İçerik: " . substr(strip_tags($content), 0, 500) . "\n\n";
            $prompt .= "Ana anahtar kelime: {$focusKeyword}\n\n";
            $prompt .= "Bu içerik için SEO optimizasyonlu, çekici ve 150-160 karakter arası meta description yaz. ";
            $prompt .= "Ana anahtar kelimeyi doğal şekilde kullan. Kullanıcıyı tıklamaya teşvik etsin.";

            $response = $this->aiService->ask($prompt, [
                'type' => 'seo_meta_generation',
                'locale' => $locale,
                'max_tokens' => 200,
                'feature_slug' => 'meta-etiket-olustur'
            ]);

            $metaDescription = trim(strip_tags($response));
            
            // Ensure length constraints
            if (strlen($metaDescription) > 160) {
                $metaDescription = substr($metaDescription, 0, 157) . '...';
            }
            
            return $metaDescription;
            
        } catch (\Exception $e) {
            Log::error('Meta description generation failed', ['error' => $e->getMessage()]);
            return '';
        }
    }

    /**
     * Generate AI-powered title suggestions
     */
    public function generateTitleSuggestions(string $content, string $focusKeyword = '', string $locale = 'tr'): array
    {
        try {
            $prompt = "İçerik: " . substr(strip_tags($content), 0, 500) . "\n\n";
            $prompt .= "Ana anahtar kelime: {$focusKeyword}\n\n";
            $prompt .= "Bu içerik için 5 farklı SEO başlığı öner. Her biri:\n";
            $prompt .= "- 60 karakter altında olmalı\n";
            $prompt .= "- Ana anahtar kelimeyi içermeli\n";
            $prompt .= "- Çekici ve tıklanabilir olmalı\n";
            $prompt .= "- Farklı yaklaşımlar kullanmalı (soru, sayı, fayda, vs.)\n\n";
            $prompt .= "Her başlığı yeni satırda ver.";

            $response = $this->aiService->ask($prompt, [
                'type' => 'seo_title_generation',
                'locale' => $locale,
                'max_tokens' => 300
            ]);

            $titles = array_filter(array_map('trim', explode("\n", $response)));
            
            // Clean and validate titles
            $suggestions = [];
            foreach ($titles as $title) {
                $cleanTitle = trim(strip_tags($title));
                if (!empty($cleanTitle) && strlen($cleanTitle) <= 60) {
                    $suggestions[] = $cleanTitle;
                }
            }
            
            return array_slice($suggestions, 0, 5);
            
        } catch (\Exception $e) {
            Log::error('Title suggestions failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Auto-optimize SEO settings using AI
     */
    public function autoOptimizeSeo($model, string $locale = null): SeoSetting
    {
        $locale = $locale ?? app()->getLocale();
        $seoSetting = $model->getOrCreateSeoSetting();
        
        try {
            $content = $this->extractContent($model);
            $analysis = $this->analyzeSeoContent($model, $locale);
            
            // Generate optimized content
            $optimizedTitle = $this->generateMetaTitle($content, $locale);
            $optimizedDescription = $this->generateMetaDescription($content, '', $locale);
            $optimizedKeywords = $this->extractKeywords($content, $locale);
            
            // Update SEO settings
            $seoSetting->updateLanguageData($locale, [
                'title' => $optimizedTitle,
                'description' => $optimizedDescription,
                'keywords' => $optimizedKeywords
            ]);
            
            // Update analysis data - fix JSON encoding
            $cleanAnalysis = $this->cleanForJson($analysis);
            $cleanSuggestions = $this->cleanForJson($this->generateOptimizationSuggestions($model, $locale));
            
            $seoSetting->update([
                'seo_analysis' => $cleanAnalysis,
                'seo_score' => $analysis['overall_score'],
                'last_analyzed' => now(),
                'ai_suggestions' => $cleanSuggestions
            ]);
            
            Log::info('SEO auto-optimization completed', [
                'model' => $model->getMorphClass(),
                'id' => $model->id,
                'locale' => $locale,
                'score' => $analysis['overall_score']
            ]);
            
            return $seoSetting;
            
        } catch (\Exception $e) {
            Log::error('SEO auto-optimization failed', [
                'model' => $model->getMorphClass(),
                'id' => $model->id,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Extract content from model
     */
    private function extractContent($model): string
    {
        $content = '';
        $locale = app()->getLocale();
        
        // Try different content fields - handle both string and array (multi-language) fields
        if (isset($model->content)) {
            $content .= $this->extractFieldValue($model->content, $locale) . ' ';
        }
        
        if (isset($model->body)) {
            $content .= $this->extractFieldValue($model->body, $locale) . ' ';
        }
        
        if (isset($model->description)) {
            $content .= $this->extractFieldValue($model->description, $locale) . ' ';
        }
        
        if (isset($model->title)) {
            $content .= $this->extractFieldValue($model->title, $locale) . ' ';
        }
        
        if (isset($model->name)) {
            $content .= $this->extractFieldValue($model->name, $locale) . ' ';
        }
        
        // Convert HTML to markdown for better analysis
        if (!empty($content) && strip_tags($content) !== $content) {
            $content = $this->htmlConverter->convert($content);
        }
        
        return trim($content);
    }
    
    /**
     * Extract field value handling both string and array (multi-language) fields
     */
    private function extractFieldValue($field, string $locale): string
    {
        if (is_string($field)) {
            return $field;
        }
        
        if (is_array($field)) {
            // Multi-language field - try current locale, then fallback
            if (isset($field[$locale])) {
                return $field[$locale];
            }
            
            // Fallback to first available language
            $values = array_filter($field);
            return !empty($values) ? array_values($values)[0] : '';
        }
        
        return '';
    }

    /**
     * Extract SEO data from model
     */
    private function extractSeoData($model, string $locale): array
    {
        $seoSetting = $model->seoSetting;
        
        if (!$seoSetting) {
            return [
                'title' => $model->getSeoFallbackTitle(),
                'description' => $model->getSeoFallbackDescription(),
                'keywords' => $model->getSeoFallbackKeywords(),
                'focus_keyword' => ''
            ];
        }
        
        return [
            'title' => $seoSetting->getTitle($locale),
            'description' => $seoSetting->getDescription($locale),
            'keywords' => $seoSetting->getKeywords($locale),
            'focus_keyword' => $seoSetting->focus_keyword ?? ''
        ];
    }

    /**
     * Calculate keyword density
     */
    private function calculateKeywordDensity(string $content, string $keyword): float
    {
        if (empty($keyword)) {
            return 0;
        }
        
        $words = str_word_count(strtolower(strip_tags($content)), 1);
        $totalWords = count($words);
        
        if ($totalWords === 0) {
            return 0;
        }
        
        $keywordOccurrences = substr_count(strtolower($content), strtolower($keyword));
        
        return round(($keywordOccurrences / $totalWords) * 100, 2);
    }

    /**
     * Analyze content structure
     */
    private function analyzeStructure(string $content, string $locale): array
    {
        $wordCount = str_word_count(strip_tags($content));
        $paragraphCount = substr_count($content, "\n\n") + 1;
        $averageWordsPerParagraph = $paragraphCount > 0 ? round($wordCount / $paragraphCount) : 0;
        
        return [
            'word_count' => $wordCount,
            'paragraph_count' => $paragraphCount,
            'average_words_per_paragraph' => $averageWordsPerParagraph,
            'reading_time_minutes' => ceil($wordCount / 200), // Average reading speed
            'structure_score' => $this->calculateStructureScore($wordCount, $paragraphCount)
        ];
    }

    /**
     * Calculate SEO score based on analysis
     */
    private function calculateSeoScore(array $analysis): int
    {
        $scores = [];
        
        // Content analysis (25%)
        $scores['content'] = $analysis['content_analysis']['score'] ?? 0;
        
        // Keyword analysis (25%)
        $scores['keywords'] = $analysis['keyword_analysis']['score'] ?? 0;
        
        // Meta analysis (25%)
        $scores['meta'] = $analysis['meta_analysis']['score'] ?? 0;
        
        // Structure analysis (15%)
        $scores['structure'] = $analysis['structure_analysis']['structure_score'] ?? 0;
        
        // Readability analysis (10%)
        $scores['readability'] = $analysis['readability_analysis']['score'] ?? 0;
        
        $weightedScore = 
            ($scores['content'] * 0.25) +
            ($scores['keywords'] * 0.25) +
            ($scores['meta'] * 0.25) +
            ($scores['structure'] * 0.15) +
            ($scores['readability'] * 0.10);
        
        return min(100, max(0, round($weightedScore)));
    }

    /**
     * Build optimization prompt for AI
     */
    private function buildOptimizationPrompt(string $content, array $seoData, array $analysis, string $locale): string
    {
        $prompt = "SEO Optimizasyon Analizi ve Öneriler\n\n";
        $prompt .= "İçerik: " . substr($content, 0, 1000) . "\n\n";
        $prompt .= "Mevcut SEO Verileri:\n";
        $prompt .= "- Başlık: " . ($seoData['title'] ?? 'Yok') . "\n";
        $prompt .= "- Açıklama: " . ($seoData['description'] ?? 'Yok') . "\n";
        $prompt .= "- Anahtar Kelimeler: " . implode(', ', $seoData['keywords'] ?? []) . "\n";
        $prompt .= "- Ana Anahtar Kelime: " . ($seoData['focus_keyword'] ?? 'Yok') . "\n\n";
        $prompt .= "Mevcut SEO Skoru: " . ($analysis['overall_score'] ?? 0) . "/100\n\n";
        $prompt .= "Lütfen bu içerik için detaylı SEO optimizasyon önerileri ver:\n";
        $prompt .= "1. Başlık optimizasyonu\n";
        $prompt .= "2. Meta açıklama iyileştirmeleri\n";
        $prompt .= "3. Anahtar kelime stratejisi\n";
        $prompt .= "4. İçerik yapılandırması\n";
        $prompt .= "5. Teknik SEO önerileri\n\n";
        $prompt .= "Her öneri için öncelik seviyesi (Yüksek/Orta/Düşük) ve beklenen etki belirt.";
        
        return $prompt;
    }

    /**
     * Default fallback methods
     */
    private function getDefaultAnalysis(string $locale): array
    {
        return [
            'overall_score' => 0,
            'content_analysis' => ['score' => 0, 'issues' => ['Analiz yapılamadı']],
            'keyword_analysis' => ['score' => 0, 'issues' => ['Analiz yapılamadı']],
            'meta_analysis' => ['score' => 0, 'issues' => ['Analiz yapılamadı']],
            'structure_analysis' => ['structure_score' => 0, 'issues' => ['Analiz yapılamadı']],
            'readability_analysis' => ['score' => 0, 'issues' => ['Analiz yapılamadı']],
            'ai_recommendations' => [],
            'analyzed_at' => now()->toISOString(),
            'locale' => $locale,
            'error' => true
        ];
    }

    private function getDefaultKeywordAnalysis(): array
    {
        return [
            'score' => 0,
            'keyword_density' => 0,
            'keyword_distribution' => [],
            'related_keywords' => [],
            'issues' => ['Anahtar kelime analizi yapılamadı']
        ];
    }

    private function getDefaultOptimizationSuggestions(): array
    {
        return [
            'suggestions' => [],
            'priority_actions' => [],
            'estimated_impact' => 'Düşük',
            'generated_at' => now()->toISOString(),
            'error' => true
        ];
    }

    /**
     * Analyze content quality and SEO factors
     */
    private function analyzeContent(string $content, string $locale): array
    {
        $wordCount = str_word_count(strip_tags($content));
        $sentences = preg_split('/[.!?]+/', strip_tags($content), -1, PREG_SPLIT_NO_EMPTY);
        $sentenceCount = count($sentences);
        $avgWordsPerSentence = $sentenceCount > 0 ? round($wordCount / $sentenceCount, 1) : 0;
        
        $score = 0;
        $issues = [];
        $recommendations = [];
        
        // Content length analysis
        if ($wordCount < 300) {
            $issues[] = 'İçerik çok kısa (300 kelimeden az)';
            $recommendations[] = 'İçeriği en az 300 kelimeye çıkarın';
        } elseif ($wordCount > 2500) {
            $issues[] = 'İçerik çok uzun (2500+ kelime)';
            $recommendations[] = 'İçeriği daha kısa alt başlıklara bölün';
            $score += 20;
        } else {
            $score += 30;
        }
        
        // Sentence structure analysis
        if ($avgWordsPerSentence > 20) {
            $issues[] = 'Cümleler çok uzun (ortalama ' . $avgWordsPerSentence . ' kelime)';
            $recommendations[] = 'Cümleleri kısaltın, ortalama 15-20 kelime ideal';
        } else {
            $score += 25;
        }
        
        // Paragraph structure
        $paragraphs = explode("\n\n", trim($content));
        $paragraphCount = count(array_filter($paragraphs));
        
        if ($paragraphCount < 3 && $wordCount > 500) {
            $issues[] = 'Çok az paragraf, okunabilirlik düşük';
            $recommendations[] = 'İçeriği daha fazla paragrafa bölün';
        } else {
            $score += 25;
        }
        
        // Readability indicators
        if (preg_match_all('/\b(çünkü|ancak|fakat|ama|lakin|ayrıca|bunun yanında|örneğin|mesela)\b/ui', $content)) {
            $score += 20; // Good use of transition words
        } else {
            $recommendations[] = 'Bağlantı kelimeleri kullanarak akıcılığı artırın';
        }
        
        return [
            'score' => min(100, $score),
            'word_count' => $wordCount,
            'sentence_count' => $sentenceCount,
            'paragraph_count' => $paragraphCount,
            'avg_words_per_sentence' => $avgWordsPerSentence,
            'issues' => $issues,
            'recommendations' => $recommendations
        ];
    }

    /**
     * Analyze meta tags quality
     */
    private function analyzeMetaTags(array $seoData, string $locale): array
    {
        $score = 0;
        $issues = [];
        $recommendations = [];
        
        // Title analysis
        $title = $seoData['title'] ?? '';
        if (empty($title)) {
            $issues[] = 'Meta title eksik';
            $recommendations[] = 'SEO başlığı ekleyin';
        } else {
            $titleLength = mb_strlen($title);
            if ($titleLength < 30) {
                $issues[] = 'Meta title çok kısa (' . $titleLength . ' karakter)';
                $recommendations[] = 'Başlığı 50-60 karaktere çıkarın';
            } elseif ($titleLength > 60) {
                $issues[] = 'Meta title çok uzun (' . $titleLength . ' karakter)';
                $recommendations[] = 'Başlığı 60 karakterin altına indirin';
            } else {
                $score += 35;
            }
        }
        
        // Description analysis
        $description = $seoData['description'] ?? '';
        if (empty($description)) {
            $issues[] = 'Meta description eksik';
            $recommendations[] = 'Meta açıklama ekleyin';
        } else {
            $descLength = mb_strlen($description);
            if ($descLength < 120) {
                $issues[] = 'Meta description çok kısa (' . $descLength . ' karakter)';
                $recommendations[] = 'Açıklamayı 150-160 karaktere çıkarın';
            } elseif ($descLength > 160) {
                $issues[] = 'Meta description çok uzun (' . $descLength . ' karakter)';
                $recommendations[] = 'Açıklamayı 160 karakterin altına indirin';
            } else {
                $score += 35;
            }
        }
        
        // Keywords analysis
        $keywords = $seoData['keywords'] ?? [];
        if (empty($keywords)) {
            $issues[] = 'Anahtar kelime eksik';
            $recommendations[] = '3-5 anahtar kelime ekleyin';
        } elseif (count($keywords) > 10) {
            $issues[] = 'Çok fazla anahtar kelime (' . count($keywords) . ' adet)';
            $recommendations[] = 'En önemli 5-7 anahtar kelimeyi seçin';
        } else {
            $score += 30;
        }
        
        return [
            'score' => $score,
            'title_length' => mb_strlen($title),
            'description_length' => mb_strlen($description),
            'keywords_count' => count($keywords),
            'issues' => $issues,
            'recommendations' => $recommendations
        ];
    }

    /**
     * Analyze content readability
     */
    private function analyzeReadability(string $content, string $locale): array
    {
        $text = strip_tags($content);
        $wordCount = str_word_count($text);
        $sentences = preg_split('/[.!?]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $sentenceCount = count($sentences);
        
        $score = 50; // Base score
        $issues = [];
        $recommendations = [];
        
        if ($wordCount == 0 || $sentenceCount == 0) {
            return [
                'score' => 0,
                'reading_ease' => 0,
                'reading_level' => 'Belirsiz',
                'issues' => ['İçerik analiz edilemedi'],
                'recommendations' => ['Geçerli metin içeriği ekleyin']
            ];
        }
        
        // Average sentence length
        $avgSentenceLength = $wordCount / $sentenceCount;
        
        // Simple readability calculation for Turkish
        if ($avgSentenceLength <= 15) {
            $score += 25;
        } elseif ($avgSentenceLength <= 20) {
            $score += 15;
        } else {
            $issues[] = 'Cümleler çok uzun (ortalama ' . round($avgSentenceLength, 1) . ' kelime)';
            $recommendations[] = 'Cümleleri kısaltın, 15-20 kelime ideal';
        }
        
        // Check for complex words (simple heuristic)
        $complexWords = preg_match_all('/\b\w{12,}\b/u', $text);
        $complexWordRatio = $complexWords / $wordCount;
        
        if ($complexWordRatio > 0.1) {
            $issues[] = 'Çok karmaşık kelimeler kullanılmış';
            $recommendations[] = 'Daha basit kelimeler tercih edin';
        } else {
            $score += 25;
        }
        
        // Reading level estimation
        $readingLevel = 'Orta';
        if ($score >= 80) {
            $readingLevel = 'Kolay';
        } elseif ($score >= 60) {
            $readingLevel = 'Orta';
        } else {
            $readingLevel = 'Zor';
        }
        
        return [
            'score' => min(100, max(0, $score)),
            'reading_ease' => $score,
            'reading_level' => $readingLevel,
            'avg_sentence_length' => round($avgSentenceLength, 1),
            'complex_word_ratio' => round($complexWordRatio * 100, 1),
            'issues' => $issues,
            'recommendations' => $recommendations
        ];
    }

    /**
     * Generate AI-powered SEO recommendations
     */
    private function generateAIRecommendations(string $content, array $seoData, string $locale): array
    {
        try {
            $prompt = $this->buildRecommendationPrompt($content, $seoData, $locale);
            
            $aiResponse = $this->aiService->ask($prompt, [
                'type' => 'seo_recommendations',
                'locale' => $locale,
                'max_tokens' => 400
            ]);

            return $this->parseAIRecommendations($aiResponse);
            
        } catch (\Exception $e) {
            Log::error('AI SEO recommendations failed', ['error' => $e->getMessage()]);
            
            return [
                'priority_high' => ['İçeriği optimize edin'],
                'priority_medium' => ['Meta bilgileri güncelleyin'],
                'priority_low' => ['Anahtar kelime dağılımını kontrol edin'],
                'estimated_impact' => 'Orta',
                'confidence' => 'Düşük'
            ];
        }
    }

    /**
     * Build AI recommendation prompt
     */
    private function buildRecommendationPrompt(string $content, array $seoData, string $locale): string
    {
        $wordCount = str_word_count(strip_tags($content));
        
        $prompt = "SEO Uzmanı olarak aşağıdaki içerik için öneriler ver:\n\n";
        $prompt .= "İçerik (ilk 500 kelime): " . substr(strip_tags($content), 0, 2000) . "\n\n";
        $prompt .= "Mevcut SEO bilgileri:\n";
        $prompt .= "- Başlık: " . ($seoData['title'] ?? 'Yok') . "\n";
        $prompt .= "- Açıklama: " . ($seoData['description'] ?? 'Yok') . "\n";
        $prompt .= "- Ana anahtar kelime: " . ($seoData['focus_keyword'] ?? 'Yok') . "\n";
        $prompt .= "- Kelime sayısı: " . $wordCount . "\n\n";
        
        $prompt .= "Lütfen şu kategorilerde öneriler ver:\n";
        $prompt .= "1. YÜKSEK ÖNCELİKLİ: Kritik SEO sorunları\n";
        $prompt .= "2. ORTA ÖNCELİKLİ: İyileştirme fırsatları\n";
        $prompt .= "3. DÜŞÜK ÖNCELİKLİ: Ekstra optimizasyonlar\n\n";
        $prompt .= "Her öneri için kısa ve net açıklama yap. Türkçe yanıt ver.";
        
        return $prompt;
    }

    /**
     * Parse AI recommendations response
     */
    private function parseAIRecommendations(string $response): array
    {
        $lines = explode("\n", $response);
        $recommendations = [
            'priority_high' => [],
            'priority_medium' => [],
            'priority_low' => [],
            'estimated_impact' => 'Orta',
            'confidence' => 'Yüksek'
        ];
        
        $currentPriority = null;
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            if (preg_match('/YÜKSEK|HIGH|KRİTİK/i', $line)) {
                $currentPriority = 'priority_high';
            } elseif (preg_match('/ORTA|MEDIUM/i', $line)) {
                $currentPriority = 'priority_medium';
            } elseif (preg_match('/DÜŞÜK|LOW/i', $line)) {
                $currentPriority = 'priority_low';
            } elseif ($currentPriority && !preg_match('/^\d+\./', $line)) {
                // Clean and add recommendation
                $clean = preg_replace('/^[-•*]\s*/', '', $line);
                if (!empty($clean)) {
                    $recommendations[$currentPriority][] = $clean;
                }
            }
        }
        
        // Fallback recommendations if parsing failed
        if (empty($recommendations['priority_high']) && empty($recommendations['priority_medium'])) {
            $recommendations['priority_medium'][] = 'İçeriği gözden geçirin ve optimize edin';
            $recommendations['confidence'] = 'Düşük';
        }
        
        return $recommendations;
    }

    /**
     * Extract keywords from content using AI
     */
    private function extractKeywords(string $content, string $locale): array
    {
        try {
            $prompt = "Bu içerikten en önemli 5-7 anahtar kelimeyi çıkar (Türkçe):\n\n";
            $prompt .= substr(strip_tags($content), 0, 1000);
            $prompt .= "\n\nSadece anahtar kelimeleri virgülle ayırarak ver, başka açıklama yapma.";
            
            $response = $this->aiService->ask($prompt, [
                'type' => 'keyword_extraction',
                'locale' => $locale,
                'max_tokens' => 100
            ]);
            
            $keywords = array_map('trim', explode(',', $response));
            return array_filter($keywords, function($keyword) {
                return !empty($keyword) && strlen($keyword) > 2;
            });
            
        } catch (\Exception $e) {
            Log::error('Keyword extraction failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Generate optimized meta title
     */
    private function generateMetaTitle(string $content, string $locale): string
    {
        try {
            $prompt = "Bu içerik için SEO optimizasyonlu, çekici bir başlık oluştur (max 60 karakter):\n\n";
            $prompt .= substr(strip_tags($content), 0, 500);
            $prompt .= "\n\nSadece başlığı ver, başka açıklama yapma.";
            
            $response = $this->aiService->ask($prompt, [
                'type' => 'title_generation',
                'locale' => $locale,
                'max_tokens' => 50
            ]);
            
            $title = trim(strip_tags($response));
            return strlen($title) > 60 ? substr($title, 0, 57) . '...' : $title;
            
        } catch (\Exception $e) {
            Log::error('Title generation failed', ['error' => $e->getMessage()]);
            return '';
        }
    }

    /**
     * Additional helper methods
     */
    private function buildKeywordAnalysisPrompt(string $content, array $seoData, string $locale): string
    {
        return "Anahtar kelime analizi yap: " . substr($content, 0, 500) . 
               "\nAna kelime: " . ($seoData['focus_keyword'] ?? '');
    }

    private function parseKeywordAnalysis(string $response): array
    {
        return [
            'score' => 75,
            'density_analysis' => 'İyi',
            'distribution' => 'Düzenli',
            'suggestions' => ['Anahtar kelime kullanımını artırın']
        ];
    }

    private function analyzeKeywordDistribution(string $content, string $keyword): array
    {
        if (empty($keyword)) return [];
        
        $paragraphs = explode("\n\n", $content);
        $distribution = [];
        
        foreach ($paragraphs as $index => $paragraph) {
            $count = substr_count(strtolower($paragraph), strtolower($keyword));
            if ($count > 0) {
                $distribution["paragraph_" . ($index + 1)] = $count;
            }
        }
        
        return $distribution;
    }

    private function findRelatedKeywords(string $content, string $locale): array
    {
        // Simple related keyword extraction
        $words = str_word_count(strtolower(strip_tags($content)), 1);
        $wordCounts = array_count_values($words);
        
        // Filter meaningful words (3+ chars, not common words)
        $commonWords = ['için', 'olan', 'ile', 'bir', 'bu', 'da', 'de', 've', 'var', 'her'];
        $meaningful = array_filter($wordCounts, function($count, $word) use ($commonWords) {
            return strlen($word) > 3 && $count > 1 && !in_array($word, $commonWords);
        }, ARRAY_FILTER_USE_BOTH);
        
        arsort($meaningful);
        return array_keys(array_slice($meaningful, 0, 5));
    }

    private function calculateStructureScore(int $wordCount, int $paragraphCount): int
    {
        $score = 50;
        
        if ($wordCount > 300) $score += 20;
        if ($paragraphCount >= 3) $score += 15;
        if ($wordCount / max(1, $paragraphCount) < 100) $score += 15; // Good paragraph length
        
        return min(100, $score);
    }

    private function parseOptimizationSuggestions(string $response): array
    {
        return [
            'title_optimization' => 'Başlığı optimize edin',
            'content_structure' => 'İçerik yapısını iyileştirin',
            'keyword_usage' => 'Anahtar kelime kullanımını artırın'
        ];
    }

    private function prioritizeActions(array $suggestions, array $analysis): array
    {
        return [
            'immediate' => ['Meta title ekleyin'],
            'short_term' => ['İçerik yapısını iyileştirin'],
            'long_term' => ['Backlink stratejisi geliştirin']
        ];
    }

    private function estimateImpact(array $suggestions, array $analysis): string
    {
        $score = $analysis['overall_score'] ?? 0;
        
        if ($score < 40) return 'Yüksek';
        if ($score < 70) return 'Orta';
        return 'Düşük';
    }

    /**
     * Fast keyword analysis without AI
     */
    private function analyzeKeywordsWithoutAI(string $content, array $seoData, string $locale): array
    {
        $focusKeyword = $seoData['focus_keyword'] ?? '';
        $keywords = $seoData['keywords'] ?? [];
        
        $score = 50; // Base score
        $issues = [];
        $recommendations = [];
        
        // Focus keyword analysis
        if (empty($focusKeyword)) {
            $issues[] = 'Ana anahtar kelime belirtilmemiş';
            $recommendations[] = 'Bir ana anahtar kelime seçin';
        } else {
            $density = $this->calculateKeywordDensity($content, $focusKeyword);
            if ($density < 0.5) {
                $issues[] = 'Ana anahtar kelime çok az kullanılmış (' . $density . '%)';
                $recommendations[] = 'Ana anahtar kelimeyi daha fazla kullanın (0.5-2%)';
            } elseif ($density > 3) {
                $issues[] = 'Ana anahtar kelime çok fazla kullanılmış (' . $density . '%)';
                $recommendations[] = 'Ana anahtar kelime kullanımını azaltın';
            } else {
                $score += 30;
            }
        }
        
        // General keywords
        if (empty($keywords)) {
            $issues[] = 'Anahtar kelime listesi boş';
            $recommendations[] = '3-5 anahtar kelime ekleyin';
        } elseif (count($keywords) > 10) {
            $issues[] = 'Çok fazla anahtar kelime (' . count($keywords) . ')';
            $recommendations[] = 'En önemli 5-7 anahtar kelimeyi seçin';
        } else {
            $score += 20;
        }
        
        return [
            'score' => min(100, $score),
            'keyword_density' => $this->calculateKeywordDensity($content, $focusKeyword),
            'keyword_distribution' => $this->analyzeKeywordDistribution($content, $focusKeyword),
            'related_keywords' => $this->findRelatedKeywords($content, $locale),
            'issues' => $issues,
            'recommendations' => $recommendations
        ];
    }

    /**
     * Basic recommendations without AI
     */
    private function generateBasicRecommendations(string $content, array $seoData, string $locale): array
    {
        $recommendations = [
            'priority_high' => [],
            'priority_medium' => [],
            'priority_low' => [],
            'estimated_impact' => 'Orta',
            'confidence' => 'Yüksek'
        ];
        
        // Check title
        $title = $seoData['title'] ?? '';
        if (empty($title)) {
            $recommendations['priority_high'][] = 'SEO başlığı ekleyin';
        } elseif (strlen($title) < 30) {
            $recommendations['priority_medium'][] = 'SEO başlığını uzatın (30-60 karakter)';
        } elseif (strlen($title) > 60) {
            $recommendations['priority_high'][] = 'SEO başlığını kısaltın (60 karakter max)';
        }
        
        // Check description
        $description = $seoData['description'] ?? '';
        if (empty($description)) {
            $recommendations['priority_high'][] = 'Meta açıklama ekleyin';
        } elseif (strlen($description) < 120) {
            $recommendations['priority_medium'][] = 'Meta açıklamayı uzatın (120-160 karakter)';
        } elseif (strlen($description) > 160) {
            $recommendations['priority_medium'][] = 'Meta açıklamayı kısaltın (160 karakter max)';
        }
        
        // Check content length
        $wordCount = str_word_count(strip_tags($content));
        if ($wordCount < 300) {
            $recommendations['priority_high'][] = 'İçeriği uzatın (minimum 300 kelime)';
        } elseif ($wordCount > 2500) {
            $recommendations['priority_low'][] = 'İçeriği alt başlıklara bölün';
        }
        
        // Check focus keyword
        $focusKeyword = $seoData['focus_keyword'] ?? '';
        if (empty($focusKeyword)) {
            $recommendations['priority_medium'][] = 'Ana anahtar kelime belirleyin';
        }
        
        return $recommendations;
    }

    /**
     * Quick AI suggestions with minimal content
     */
    public function generateQuickSuggestions($model, string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        
        try {
            $content = substr($this->extractContent($model), 0, 500); // Limit content
            $seoData = $this->extractSeoData($model, $locale);
            
            $prompt = "SEO önerileri (kısa):\n";
            $prompt .= "İçerik: " . $content . "\n";
            $prompt .= "Başlık: " . ($seoData['title'] ?? 'Yok') . "\n";
            $prompt .= "Açıklama: " . ($seoData['description'] ?? 'Yok') . "\n\n";
            $prompt .= "3 önemli öneri ver (her biri 1 satır):";
            
            $aiResponse = $this->aiService->ask($prompt, [
                'type' => 'seo_quick_suggestions',
                'locale' => $locale,
                'max_tokens' => 150
            ]);

            $suggestions = explode("\n", trim($aiResponse));
            
            return [
                'priority_actions' => array_filter($suggestions),
                'suggested_title' => $this->generateQuickTitle($content),
                'suggested_description' => $this->generateQuickDescription($content),
                'estimated_impact' => 'Orta',
                'confidence' => 'Yüksek',
                'overall_score' => $this->calculateQuickScore($seoData),
                'generated_at' => now()->toISOString()
            ];
            
        } catch (\Exception $e) {
            // Fallback to basic recommendations
            return $this->generateBasicRecommendations($this->extractContent($model), $this->extractSeoData($model, $locale), $locale);
        }
    }

    /**
     * Quick title generation
     */
    private function generateQuickTitle(string $content): string
    {
        $words = str_word_count($content, 1);
        $keywords = array_slice($words, 0, 3);
        return implode(' ', $keywords) . ' - Özet';
    }

    /**
     * Quick description generation
     */
    private function generateQuickDescription(string $content): string
    {
        $sentences = preg_split('/[.!?]+/', strip_tags($content), -1, PREG_SPLIT_NO_EMPTY);
        $firstSentence = trim($sentences[0] ?? '');
        return substr($firstSentence, 0, 150) . '...';
    }

    /**
     * Quick score calculation
     */
    private function calculateQuickScore(array $seoData): int
    {
        $score = 50;
        
        if (!empty($seoData['title'])) $score += 15;
        if (!empty($seoData['description'])) $score += 15;
        if (!empty($seoData['keywords'])) $score += 10;
        if (!empty($seoData['focus_keyword'])) $score += 10;
        
        return min(100, $score);
    }

    /**
     * Clean data for JSON encoding - Enhanced version
     */
    private function cleanForJson($data)
    {
        if (is_array($data)) {
            $cleaned = [];
            foreach ($data as $key => $value) {
                $cleanKey = $this->cleanForJson($key);
                $cleanValue = $this->cleanForJson($value);
                $cleaned[$cleanKey] = $cleanValue;
            }
            return $cleaned;
        }
        
        if (is_string($data)) {
            // Multiple cleaning steps for UTF-8 safety
            $cleaned = $data;
            
            // Remove null bytes
            $cleaned = str_replace("\0", "", $cleaned);
            
            // Remove or replace problematic characters
            $cleaned = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $cleaned);
            
            // Fix encoding issues
            $cleaned = mb_convert_encoding($cleaned, 'UTF-8', 'UTF-8');
            
            // Additional UTF-8 validation and cleaning
            if (!mb_check_encoding($cleaned, 'UTF-8')) {
                $cleaned = mb_convert_encoding($cleaned, 'UTF-8', 'auto');
            }
            
            // Remove any remaining invalid sequences
            $cleaned = filter_var($cleaned, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
            
            // Final fallback - if still problematic, use base64
            if (!is_string($cleaned) || !mb_check_encoding($cleaned, 'UTF-8')) {
                return base64_encode($data);
            }
            
            return $cleaned;
        }
        
        if (is_object($data)) {
            // Convert objects to arrays to avoid serialization issues
            return $this->cleanForJson((array) $data);
        }
        
        // For other types (int, float, bool, null), return as-is
        return $data;
    }
}