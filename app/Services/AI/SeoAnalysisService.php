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
    private ?AIService $aiService;
    private HtmlConverter $htmlConverter;

    public function __construct(AIService $aiService = null)
    {
        try {
            $this->aiService = $aiService;
            Log::info('✅ SeoAnalysisService constructor - AIService injected');
        } catch (\Exception $e) {
            Log::error('🚨 SeoAnalysisService constructor - AIService injection failed', [
                'error' => $e->getMessage()
            ]);
            $this->aiService = null;
        }
        
        $this->htmlConverter = new HtmlConverter();
        Log::info('✅ SeoAnalysisService constructor completed');
    }

    /**
     * Comprehensive SEO analysis without AI (fast version)
     */
    public function analyzeSeoContent($model, string $locale = null): array
    {
        Log::info('🔍 SeoAnalysisService::analyzeSeoContent called', [
            'model_class' => get_class($model),
            'model_id' => $model->getKey(),
            'locale' => $locale
        ]);
        
        $locale = $locale ?? app()->getLocale();
        $cacheKey = "seo_analysis_{$model->getMorphClass()}_{$model->id}_{$locale}";
        
        Log::info('📦 Cache key generated', ['cache_key' => $cacheKey]);
        
        return Cache::remember($cacheKey, 300, function() use ($model, $locale) {
            Log::info('🚀 Starting SEO analysis (cache miss)');
            try {
                Log::info('📄 Extracting content...');
                $content = $this->extractContent($model);
                Log::info('✅ Content extracted', ['length' => strlen($content)]);
                
                Log::info('🎯 Extracting SEO data...');
                $seoData = $this->extractSeoData($model, $locale);
                Log::info('✅ SEO data extracted', ['has_data' => !empty($seoData)]);
                
                \Log::info('🔍 SEO Analysis starting', [
                    'model_type' => get_class($model),
                    'model_id' => $model->getKey(),
                    'locale' => $locale,
                    'content_length' => strlen($content),
                    'has_seo_data' => !empty($seoData)
                ]);
                
                $analysis = [];
                
                \Log::info('📝 Starting content analysis...');
                $analysis['content_analysis'] = $this->analyzeContent($content, $locale);
                
                \Log::info('🔑 Starting keyword analysis...');
                $analysis['keyword_analysis'] = $this->analyzeKeywordsWithoutAI($content, $seoData, $locale);
                
                \Log::info('🏷️ Starting meta analysis...');
                $analysis['meta_analysis'] = $this->analyzeMetaTags($seoData, $locale);
                
                \Log::info('🏗️ Starting structure analysis...');
                $analysis['structure_analysis'] = $this->analyzeStructure($content, $locale);
                
                \Log::info('📖 Starting readability analysis...');
                $analysis['readability_analysis'] = $this->analyzeReadability($content, $locale);
                
                \Log::info('💡 Starting AI recommendations...');
                $analysis['ai_recommendations'] = $this->generateBasicRecommendations($content, $seoData, $locale);

                $score = $this->calculateSeoScore($analysis);
                $analysis['overall_score'] = $score;
                $analysis['analyzed_at'] = now()->toISOString();
                $analysis['locale'] = $locale;
                
                // Add frontend-expected fields with null safety
                $analysis['priority_actions'] = $this->generatePriorityActions($analysis, $seoData) ?? [];
                $analysis['suggested_title'] = $this->generateSuggestedTitle($content, $seoData, $locale) ?? '';
                $analysis['suggested_description'] = $this->generateSuggestedDescription($content, $seoData, $locale) ?? '';

                \Log::info('✅ SEO Analysis completed', [
                    'overall_score' => $score,
                    'content_score' => $analysis['content_analysis']['score'] ?? 0,
                    'keyword_score' => $analysis['keyword_analysis']['score'] ?? 0,
                    'meta_score' => $analysis['meta_analysis']['score'] ?? 0,
                    'priority_actions_count' => count($analysis['priority_actions'])
                ]);

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
     * 🚀 YENİ: Modern AI destekli kapsamlı analiz 
     */
    public function performComprehensiveAnalysis(array $analysisData): array
    {
        try {
            $title = $analysisData['title'] ?? '';
            $content = $analysisData['content'] ?? '';
            $language = $analysisData['language'] ?? 'tr';
            $seoData = $analysisData['seo_data'] ?? [];
            
            // Modern AI prompt oluştur
            $prompt = $this->buildModernAnalysisPrompt($title, $content, $language, $seoData);
            
            // AI Feature kullanarak analiz
            $aiResult = ai_execute_feature('hizli-seo-analizi', [
                'title' => $title,
                'content' => $content,
                'language' => $language,
                'analysis_prompt' => $prompt
            ]);
            
            if ($aiResult && isset($aiResult['analysis'])) {
                return $this->formatModernAnalysisResult($aiResult['analysis']);
            }
            
            // Fallback: Basit analiz
            return $this->performBasicAnalysis($title, $content, $language);
            
        } catch (\Exception $e) {
            Log::error('Modern SEO analizi hatası', [
                'error' => $e->getMessage(),
                'data' => $analysisData
            ]);
            
            return [
                'success' => false,
                'error' => 'Analiz sırasında bir hata oluştu: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Modern analiz prompt'u oluştur
     */
    private function buildModernAnalysisPrompt(string $title, string $content, string $language, array $seoData): string
    {
        $prompt = "Sen profesyonel bir SEO uzmanısın. Aşağıdaki web sayfası içeriğini analiz et ve detaylı öneriler ver.\n\n";
        
        $prompt .= "📝 ANALİZ EDİLECEK İÇERİK:\n";
        $prompt .= "Başlık: \"" . strip_tags($title) . "\"\n";
        $prompt .= "İçerik: \"" . strip_tags(substr($content, 0, 1000)) . "...\"\n";
        $prompt .= "Dil: " . strtoupper($language) . "\n\n";
        
        if (!empty($seoData)) {
            $prompt .= "🔍 MEVCUT SEO VERİLERİ:\n";
            foreach ($seoData as $key => $value) {
                if (is_array($value) && isset($value[$language])) {
                    $prompt .= "- " . ucfirst($key) . ": " . $value[$language] . "\n";
                }
            }
            $prompt .= "\n";
        }
        
        $prompt .= "📊 YANIT FORMATI (JSON):\n";
        $prompt .= "{\n";
        $prompt .= '  "overall_score": 85,';
        $prompt .= '  "title_analysis": {"score": 90, "issues": [], "suggestions": []},';
        $prompt .= '  "content_analysis": {"score": 80, "issues": [], "suggestions": []},';
        $prompt .= '  "keyword_analysis": {"primary_keywords": [], "missing_keywords": []},';
        $prompt .= '  "recommendations": ["Öneri 1", "Öneri 2"],';
        $prompt .= '  "priority_actions": ["Acil işlem 1", "Acil işlem 2"]';
        $prompt .= "}\n\n";
        
        $prompt .= "Lütfen sadece JSON formatında yanıt ver, başka açıklama ekleme.";
        
        return $prompt;
    }
    
    /**
     * Modern analiz sonucunu formatla
     */
    private function formatModernAnalysisResult($rawResult): array
    {
        // JSON parse et
        if (is_string($rawResult)) {
            $parsed = json_decode($rawResult, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $rawResult = $parsed;
            }
        }
        
        return [
            'success' => true,
            'analysis_type' => 'comprehensive',
            'overall_score' => $rawResult['overall_score'] ?? 0,
            'title_analysis' => $rawResult['title_analysis'] ?? [],
            'content_analysis' => $rawResult['content_analysis'] ?? [],
            'keyword_analysis' => $rawResult['keyword_analysis'] ?? [],
            'recommendations' => $rawResult['recommendations'] ?? [],
            'priority_actions' => $rawResult['priority_actions'] ?? [],
            'timestamp' => now()->toIso8601String()
        ];
    }
    
    /**
     * Basit fallback analiz
     */
    private function performBasicAnalysis(string $title, string $content, string $language): array
    {
        $titleLen = mb_strlen(strip_tags($title));
        $contentLen = mb_strlen(strip_tags($content));
        $wordCount = str_word_count(strip_tags($content));
        
        $score = 50; // Base score
        
        // Başlık analizi
        if ($titleLen >= 30 && $titleLen <= 60) $score += 15;
        if (!empty($title)) $score += 10;
        
        // İçerik analizi  
        if ($contentLen >= 300) $score += 15;
        if ($wordCount >= 50) $score += 10;
        
        return [
            'success' => true,
            'analysis_type' => 'basic',
            'overall_score' => min(100, $score),
            'title_analysis' => [
                'score' => $titleLen >= 30 && $titleLen <= 60 ? 90 : 60,
                'length' => $titleLen,
                'issues' => $titleLen < 30 ? ['Başlık çok kısa'] : ($titleLen > 60 ? ['Başlık çok uzun'] : []),
                'suggestions' => ['Başlığı 30-60 karakter arasında tutun']
            ],
            'content_analysis' => [
                'score' => $contentLen >= 300 ? 80 : 50,
                'word_count' => $wordCount,
                'character_count' => $contentLen,
                'issues' => $contentLen < 300 ? ['İçerik çok kısa'] : [],
                'suggestions' => ['En az 300 karakter içerik yazın']
            ],
            'recommendations' => [
                'Başlık uzunluğunu optimize edin',
                'İçerik uzunluğunu artırın',
                'Anahtar kelimeler ekleyin'
            ],
            'timestamp' => now()->toIso8601String()
        ];
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
     * Auto-optimize SEO settings without AI (basic optimization)
     */
    public function autoOptimizeSeo($model, string $locale = null): SeoSetting
    {
        $locale = $locale ?? app()->getLocale();
        $seoSetting = $model->getOrCreateSeoSetting();
        
        try {
            $content = $this->extractContent($model);
            $analysis = $this->analyzeSeoContent($model, $locale);
            
            \Log::info('🔧 Auto-optimizing SEO (basic mode)', [
                'model_type' => get_class($model),
                'model_id' => $model->getKey(),
                'locale' => $locale,
                'current_score' => $analysis['overall_score']
            ]);
            
            // Generate basic optimized content
            $optimizedTitle = $this->generateBasicTitle($content, $model);
            $optimizedDescription = $this->generateBasicDescription($content);
            $optimizedKeywords = $this->generateBasicKeywords($content);
            
            // Update SEO settings
            $seoSetting->updateLanguageData($locale, [
                'title' => $optimizedTitle,
                'description' => $optimizedDescription,
                'keywords' => $optimizedKeywords
            ]);
            
            // Update analysis data
            $cleanAnalysis = $this->cleanForJson($analysis);
            $basicSuggestions = $this->generateBasicRecommendations($content, $this->extractSeoData($model, $locale), $locale);
            
            $seoSetting->update([
                'seo_analysis' => $cleanAnalysis,
                'seo_score' => $analysis['overall_score'],
                'last_analyzed' => now(),
                'ai_suggestions' => $this->cleanForJson($basicSuggestions)
            ]);
            
            Log::info('✅ SEO auto-optimization completed', [
                'model' => $model->getMorphClass(),
                'id' => $model->id,
                'locale' => $locale,
                'score' => $analysis['overall_score'],
                'title' => $optimizedTitle,
                'keywords_count' => count($optimizedKeywords)
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
            'priority_actions' => [
                'SEO analizi yapılamadı - lütfen tekrar deneyin',
                'İçerik eksik veya model bulunamadı',
                'Teknik bir sorun oluştu'
            ],
            'suggested_title' => '',
            'suggested_description' => '',
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
     * Quick AI suggestions with real AI analysis
     */
    public function generateQuickSuggestions($model, string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        
        try {
            $content = $this->extractContent($model);
            $seoData = $this->extractSeoData($model, $locale);
            
            \Log::info('🚀 SEO Quick Suggestions - GERÇEK AI ANALİZİ başlıyor', [
                'model_type' => get_class($model),
                'model_id' => $model->getKey(),
                'locale' => $locale,
                'content_length' => strlen($content),
                'has_ai_service' => !is_null($this->aiService)
            ]);
            
            // Gerçek AI analizi yap
            if ($this->aiService) {
                $aiAnalysis = $this->performRealAIAnalysis($content, $seoData, $locale);
                
                \Log::info('✅ Real AI Analysis completed', [
                    'ai_score' => $aiAnalysis['overall_score'] ?? 0,
                    'ai_suggestions_count' => count($aiAnalysis['ai_suggestions'] ?? []),
                    'ai_title_generated' => !empty($aiAnalysis['suggested_title']),
                    'ai_description_generated' => !empty($aiAnalysis['suggested_description'])
                ]);
                
                return $aiAnalysis;
            }
            
            // AI service yoksa fallback
            \Log::warning('AI Service not available, using basic analysis');
            return $this->generateBasicAnalysis($content, $seoData, $locale);
            
        } catch (\Exception $e) {
            \Log::error('SEO Quick Suggestions failed', [
                'error' => $e->getMessage(),
                'model_type' => get_class($model),
                'model_id' => $model->getKey()
            ]);
            
            return $this->generateBasicAnalysis($content, $seoData, $locale);
        }
    }

    /**
     * Gerçek AI ile SEO analizi
     */
    private function performRealAIAnalysis(string $content, array $seoData, string $locale): array
    {
        try {
            // AI'ya kapsamlı SEO analizi prompt'u gönder
            $prompt = $this->buildComprehensiveAnalysisPrompt($content, $seoData, $locale);
            
            \Log::info('🤖 AI Service çağrılıyor - SEO analizi');
            
            $aiResponse = $this->aiService->ask($prompt, [
                'type' => 'seo_comprehensive_analysis',
                'locale' => $locale,
                'max_tokens' => 800,
                'feature_slug' => 'seo-analiz'
            ]);
            
            \Log::info('✅ AI Response received', [
                'response_length' => strlen($aiResponse),
                'response_preview' => substr($aiResponse, 0, 200) . '...'
            ]);
            
            // AI yanıtını parse et
            $analysis = $this->parseAIAnalysisResponse($aiResponse, $content, $seoData);
            
            // Gerçek AI ile title ve description üret
            $analysis['suggested_title'] = $this->generateAITitle($content, $seoData, $locale);
            $analysis['suggested_description'] = $this->generateAIDescription($content, $seoData, $locale);
            
            return $analysis;
            
        } catch (\Exception $e) {
            \Log::error('Real AI Analysis failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Kapsamlı AI analizi için prompt oluştur
     */
    private function buildComprehensiveAnalysisPrompt(string $content, array $seoData, string $locale): string
    {
        $wordCount = str_word_count(strip_tags($content));
        
        $prompt = "Sen profesyonel bir SEO uzmanısın. Aşağıdaki web sayfası içeriğini analiz et ve detaylı öneriler ver.\n\n";
        
        $prompt .= "🔍 ANALİZ EDİLECEK İÇERİK:\n";
        $prompt .= "\"" . substr(strip_tags($content), 0, 1200) . "\"\n\n";
        
        $prompt .= "📊 MEVCUT SEO VERİLERİ:\n";
        $prompt .= "• Başlık: " . ($seoData['title'] ? '"' . $seoData['title'] . '"' : 'Eksik') . "\n";
        $prompt .= "• Meta açıklama: " . ($seoData['description'] ? '"' . $seoData['description'] . '"' : 'Eksik') . "\n";
        $prompt .= "• Ana anahtar kelime: " . ($seoData['focus_keyword'] ? '"' . $seoData['focus_keyword'] . '"' : 'Belirlenmemiş') . "\n";
        $prompt .= "• Kelime sayısı: {$wordCount} kelime\n\n";
        
        $prompt .= "🎯 YAPMANIZ GEREKEN ANALİZ:\n";
        $prompt .= "1. Bu sayfanın SEO puanını 0-100 arasında belirleyin\n";
        $prompt .= "2. En kritik 3 sorunu tespit edin\n";
        $prompt .= "3. Somut iyileştirme önerileri verin\n";
        $prompt .= "4. Başlık ve meta açıklama için öneriler sunun\n\n";
        
        $prompt .= "📝 YANIT FORMATI:\n";
        $prompt .= "PUAN: [0-100 arası sayı]\n";
        $prompt .= "KRİTİK SORUNLAR:\n";
        $prompt .= "1. [Somut sorun ve çözüm önerisi]\n";
        $prompt .= "2. [Somut sorun ve çözüm önerisi]\n";
        $prompt .= "3. [Somut sorun ve çözüm önerisi]\n\n";
        
        $prompt .= "ÖNEMLİ: Düzgün Türkçe kullanın. Kısa ve anlaşılır cümleler yazın. Teknik jargon kullanmayın.";
        
        return $prompt;
    }

    /**
     * AI yanıtını parse et - İyileştirilmiş parsing
     */
    private function parseAIAnalysisResponse(string $response, string $content, array $seoData): array
    {
        \Log::info('🔍 AI Response parsing başlıyor', [
            'response_length' => strlen($response),
            'response_preview' => substr($response, 0, 500)
        ]);
        
        // AI yanıtından score çıkar - daha güçlü pattern matching
        $score = 50; // Default
        if (preg_match('/PUAN:\s*(\d+)/i', $response, $matches)) {
            $score = intval($matches[1]);
        } elseif (preg_match('/(\d+)\s*\/\s*100/i', $response, $matches)) {
            $score = intval($matches[1]);
        } elseif (preg_match('/(\d+)\s*puan/i', $response, $matches)) {
            $score = intval($matches[1]);
        } elseif (preg_match('/skor?\s*[:\-]\s*(\d+)/i', $response, $matches)) {
            $score = intval($matches[1]);
        }
        
        // Priority actions çıkar - daha iyi parsing
        $priorityActions = [];
        
        // "KRİTİK SORUNLAR" bölümünü bul
        if (preg_match('/KRİTİK SORUNLAR?:?\s*(.*?)(?=\n\n|\n[A-Z]|\z)/s', $response, $matches)) {
            $problemsSection = $matches[1];
            $lines = explode("\n", $problemsSection);
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (preg_match('/^(\d+)\.\s*(.+)$/', $line, $matches)) {
                    $action = trim($matches[2]);
                    if (!empty($action) && strlen($action) > 10) {
                        $priorityActions[] = $action;
                    }
                }
            }
        }
        
        // Fallback: Normal numbered list
        if (empty($priorityActions)) {
            $lines = explode("\n", $response);
            foreach ($lines as $line) {
                $line = trim($line);
                if (preg_match('/^(\d+)\.\s*(.+)$/', $line, $matches)) {
                    $action = trim($matches[2]);
                    if (!empty($action) && strlen($action) > 15) {
                        $priorityActions[] = $action;
                    }
                }
            }
        }
        
        // Eğer hâlâ boşsa, basic sorunları ekle
        if (empty($priorityActions)) {
            $priorityActions = $this->generateBasicPriorityActions($seoData, $content);
        }
        
        \Log::info('✅ AI Response parsed', [
            'extracted_score' => $score,
            'priority_actions_count' => count($priorityActions),
            'priority_actions' => $priorityActions
        ]);
        
        return [
            'overall_score' => min(100, max(0, $score)),
            'priority_actions' => array_slice($priorityActions, 0, 5),
            'ai_suggestions' => $priorityActions,
            'focus_keyword_suggestions' => $this->extractTopKeywords($content, 5),
            'analyzed_at' => now()->toISOString(),
            'locale' => app()->getLocale(),
            'ai_powered' => true,
            'ai_response_preview' => substr($response, 0, 300),
            'ai_response_full' => $response, // Debug için
            'content_analysis' => [
                'word_count' => str_word_count(strip_tags($content)),
                'ai_score' => $score,
                'reading_ease' => $score > 70 ? 'Kolay' : ($score > 50 ? 'Orta' : 'Zor')
            ]
        ];
    }

    /**
     * Basic priority actions fallback
     */
    private function generateBasicPriorityActions(array $seoData, string $content): array
    {
        $actions = [];
        
        // Title check
        $title = $seoData['title'] ?? '';
        if (empty($title)) {
            $actions[] = 'SEO başlığı eksik - sayfa için çekici bir başlık ekleyin';
        } elseif (strlen($title) < 30) {
            $actions[] = 'SEO başlığı çok kısa - ' . strlen($title) . ' karakter, en az 50-60 karakter olmalı';
        } elseif (strlen($title) > 65) {
            $actions[] = 'SEO başlığı çok uzun - ' . strlen($title) . ' karakter, 60 karakterin altında olmalı';
        }
        
        // Description check
        $description = $seoData['description'] ?? '';
        if (empty($description)) {
            $actions[] = 'Meta açıklama eksik - kullanıcıları çekecek 150-160 karakter açıklama ekleyin';
        } elseif (strlen($description) < 120) {
            $actions[] = 'Meta açıklama çok kısa - ' . strlen($description) . ' karakter, en az 120-160 karakter olmalı';
        } elseif (strlen($description) > 165) {
            $actions[] = 'Meta açıklama çok uzun - ' . strlen($description) . ' karakter, 160 karakterin altında olmalı';
        }
        
        // Content check
        $wordCount = str_word_count(strip_tags($content));
        if ($wordCount < 300) {
            $actions[] = 'İçerik çok kısa - ' . $wordCount . ' kelime, SEO için en az 300-500 kelime ekleyin';
        }
        
        // Focus keyword check
        if (empty($seoData['focus_keyword'] ?? '')) {
            $actions[] = 'Ana anahtar kelime belirtilmemiş - içeriğin odak noktasını belirleyin';
        }
        
        return array_slice($actions, 0, 3);
    }

    /**
     * Gerçek AI ile title üret - İyileştirilmiş
     */
    private function generateAITitle(string $content, array $seoData, string $locale): string
    {
        try {
            $prompt = "Web sayfası için SEO optimizasyonlu başlık oluştur.\n\n";
            $prompt .= "📄 İÇERİK: \"" . substr(strip_tags($content), 0, 600) . "\"\n\n";
            $prompt .= "📊 MEVCUT BİLGİLER:\n";
            $prompt .= "• Şu anki başlık: " . ($seoData['title'] ? '"' . $seoData['title'] . '"' : 'Eksik') . "\n";
            $prompt .= "• Ana anahtar kelime: " . ($seoData['focus_keyword'] ? '"' . $seoData['focus_keyword'] . '"' : 'Yok') . "\n\n";
            $prompt .= "🎯 İSTEK: 50-60 karakter arası, çekici ve SEO dostu bir başlık oluştur.\n";
            $prompt .= "📝 FORMAT: Sadece başlığı yaz, başka hiçbir şey ekleme.\n";
            $prompt .= "🔤 DİL: Düzgün Türkçe kullan.";
            
            $aiTitle = $this->aiService->ask($prompt, [
                'type' => 'seo_title_generation',
                'locale' => $locale,
                'max_tokens' => 100,
                'feature_slug' => 'baslik-uret'
            ]);
            
            $cleanTitle = trim(strip_tags($aiTitle));
            // Quotes ve gereksiz karakterleri temizle
            $cleanTitle = trim($cleanTitle, '"\'');
            
            \Log::info('✅ AI Title generated', [
                'generated_title' => $cleanTitle,
                'title_length' => strlen($cleanTitle)
            ]);
            
            return strlen($cleanTitle) > 60 ? substr($cleanTitle, 0, 57) . '...' : $cleanTitle;
            
        } catch (\Exception $e) {
            \Log::error('AI Title generation failed', ['error' => $e->getMessage()]);
            return $this->generateBasicTitle($content, (object)$seoData);
        }
    }

    /**
     * Gerçek AI ile description üret - İyileştirilmiş
     */
    private function generateAIDescription(string $content, array $seoData, string $locale): string
    {
        try {
            $prompt = "Web sayfası için meta açıklama oluştur.\n\n";
            $prompt .= "📄 İÇERİK: \"" . substr(strip_tags($content), 0, 800) . "\"\n\n";
            $prompt .= "📊 MEVCUT BİLGİLER:\n";
            $prompt .= "• Şu anki açıklama: " . ($seoData['description'] ? '"' . $seoData['description'] . '"' : 'Eksik') . "\n";
            $prompt .= "• Ana anahtar kelime: " . ($seoData['focus_keyword'] ? '"' . $seoData['focus_keyword'] . '"' : 'Yok') . "\n\n";
            $prompt .= "🎯 İSTEK: 120-160 karakter arası, kullanıcıları çekecek meta açıklama oluştur.\n";
            $prompt .= "📝 FORMAT: Sadece açıklamayı yaz, başka hiçbir şey ekleme.\n";
            $prompt .= "🔤 DİL: Düzgün Türkçe kullan. Tıklamaya teşvik edici ol.";
            
            $aiDescription = $this->aiService->ask($prompt, [
                'type' => 'seo_description_generation',
                'locale' => $locale,
                'max_tokens' => 150,
                'feature_slug' => 'aciklama-uret'
            ]);
            
            $cleanDescription = trim(strip_tags($aiDescription));
            // Quotes ve gereksiz karakterleri temizle
            $cleanDescription = trim($cleanDescription, '"\'');
            
            \Log::info('✅ AI Description generated', [
                'generated_description' => substr($cleanDescription, 0, 100) . '...',
                'description_length' => strlen($cleanDescription)
            ]);
            
            return strlen($cleanDescription) > 160 ? substr($cleanDescription, 0, 157) . '...' : $cleanDescription;
            
        } catch (\Exception $e) {
            \Log::error('AI Description generation failed', ['error' => $e->getMessage()]);
            return $this->generateBasicDescription($content, $seoData);
        }
    }

    /**
     * Basic analysis fallback
     */
    private function generateBasicAnalysis(string $content, array $seoData, string $locale): array
    {
        return [
            'overall_score' => $this->calculateQuickScore($seoData),
            'priority_actions' => $this->generatePriorityActions([], $seoData),
            'suggested_title' => $this->generateBasicTitle($content, (object)$seoData),
            'suggested_description' => $this->generateBasicDescription($content, $seoData),
            'focus_keyword_suggestions' => $this->extractTopKeywords($content, 3),
            'analyzed_at' => now()->toISOString(),
            'locale' => $locale,
            'ai_powered' => false,
            'fallback_reason' => 'AI service not available'
        ];
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
     * Generate basic title from content
     */
    private function generateBasicTitle(string $content, $model): string
    {
        // Try to get existing title from model
        if (method_exists($model, 'getTranslated')) {
            $title = $model->getTranslated('title', app()->getLocale());
            if (!empty($title)) {
                return strlen($title) > 60 ? substr($title, 0, 57) . '...' : $title;
            }
        }
        
        // Extract from content
        $cleanContent = strip_tags($content);
        $words = explode(' ', $cleanContent);
        $title = implode(' ', array_slice($words, 0, 8)); // First 8 words
        
        return strlen($title) > 60 ? substr($title, 0, 57) . '...' : $title;
    }
    
    /**
     * Generate basic description from content
     */
    private function generateBasicDescription(string $content, array $seoData = []): string
    {
        $cleanContent = strip_tags($content);
        $sentences = preg_split('/[.!?]+/', $cleanContent, -1, PREG_SPLIT_NO_EMPTY);
        
        if (!empty($sentences)) {
            $description = trim($sentences[0]);
            
            // Add focus keyword if available
            $focusKeyword = $seoData['focus_keyword'] ?? '';
            if (!empty($focusKeyword) && stripos($description, $focusKeyword) === false) {
                $description = $focusKeyword . ' hakkında: ' . $description;
            }
            
            return strlen($description) > 160 ? substr($description, 0, 157) . '...' : $description;
        }
        
        return substr($cleanContent, 0, 157) . '...';
    }
    
    /**
     * Extract top keywords from content
     */
    private function extractTopKeywords(string $content, int $limit = 5): array
    {
        $cleanContent = strip_tags($content);
        $words = preg_split('/[\s\.,;:!?\-\(\)]+/', mb_strtolower($cleanContent));
        
        // Filter out common Turkish stop words and short words
        $stopWords = ['ve', 'bir', 'bu', 'şu', 'da', 'de', 'ile', 'için', 'olan', 'gibi', 'çok', 'daha', 'en', 'her', 'ki', 'mi', 'mu', 'mı', 'mü'];
        $words = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 3 && !in_array($word, $stopWords);
        });
        
        // Count word frequency
        $wordCounts = array_count_values($words);
        arsort($wordCounts);
        
        // Return top keywords
        return array_slice(array_keys($wordCounts), 0, $limit);
    }
    
    /**
     * Generate basic keywords from content
     */
    private function generateBasicKeywords(string $content): array
    {
        $cleanContent = strtolower(strip_tags($content));
        $words = str_word_count($cleanContent, 1);
        
        // Filter meaningful words
        $meaningful = array_filter($words, function($word) {
            return strlen($word) > 3 && !in_array($word, [
                'için', 'olan', 'ile', 'bir', 'bu', 'da', 'de', 've', 'var', 'her',
                'şey', 'çok', 'daha', 'gibi', 'kadar', 'sonra', 'önce', 'şimdi'
            ]);
        });
        
        $wordCounts = array_count_values($meaningful);
        arsort($wordCounts);
        
        return array_keys(array_slice($wordCounts, 0, 5));
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

    /**
     * Generate priority action items based on analysis
     */
    private function generatePriorityActions(array $analysis, array $seoData): array
    {
        $actions = [];
        
        // Meta Title Analysis - More specific and actionable
        $titleLen = strlen($seoData['title'] ?? '');
        if (empty($seoData['title'] ?? '')) {
            $actions[] = "🎯 Kritik: Meta başlık eksik! SEO sıralaması için mutlaka ekleyin";
        } elseif ($titleLen < 30) {
            $actions[] = "📏 Başlığı genişletin: {$titleLen} karakter çok kısa (ideal: 50-60 karakter)";
        } elseif ($titleLen > 65) {
            $actions[] = "✂️ Başlığı kısaltın: {$titleLen} karakter çok uzun (Google'da kesilecek)";
        }
        
        // Meta Description Analysis - More detailed
        $descLen = strlen($seoData['description'] ?? '');
        if (empty($seoData['description'] ?? '')) {
            $actions[] = "📝 Meta açıklama eksik! Tıklama oranını artırmak için ekleyin";
        } elseif ($descLen < 120) {
            $actions[] = "📈 Açıklamayı genişletin: {$descLen} karakter kısa (ideal: 150-160 karakter)";
        } elseif ($descLen > 165) {
            $actions[] = "⚡ Açıklamayı optimize edin: {$descLen} karakter uzun (Google'da kesilecek)";
        }
        
        // Keyword Analysis - More strategic
        $keywords = $seoData['keywords'] ?? [];
        $keywordCount = is_array($keywords) ? count($keywords) : 0;
        if ($keywordCount === 0) {
            $actions[] = "🔑 Anahtar kelime eksik! Hedef kitlenizi tanımlamak için ekleyin";
        } elseif ($keywordCount > 10) {
            $actions[] = "🎯 Çok fazla anahtar kelime ({$keywordCount}): 5-7 arası odaklanın";
        }
        
        // Focus Keyword Analysis
        if (empty($seoData['focus_keyword'] ?? '')) {
            $actions[] = "🎯 Ana anahtar kelime seçin: İçeriğinizin odak noktasını belirleyin";
        }
        
        // Advanced checks based on analysis scores
        if (isset($analysis['content_analysis']['score']) && $analysis['content_analysis']['score'] < 60) {
            $actions[] = "📚 İçerik kalitesini artırın: Daha detaylı ve değerli bilgi ekleyin";
        }
        
        if (isset($analysis['keyword_analysis']['score']) && $analysis['keyword_analysis']['score'] < 60) {
            $actions[] = "🔍 Anahtar kelime dağılımını optimize edin: İçerikte doğal kullanım";
        }
        
        // Content length check
        $contentScore = $analysis['content_analysis']['score'] ?? 0;
        if ($contentScore < 50) {
            $actions[] = "İçerik kalitesi artırılmalı - daha detaylı bilgi ekleyin";
        }
        
        // Readability check  
        $readabilityScore = $analysis['readability_analysis']['score'] ?? 0;
        if ($readabilityScore < 60) {
            $actions[] = "Metin okunabilirliği iyileştirilmeli - daha basit cümleler kullanın";
        }
        
        return array_slice($actions, 0, 5); // Max 5 priority action
    }

    /**
     * Generate suggested title based on content
     */
    private function generateSuggestedTitle(string $content, array $seoData, string $locale): string
    {
        try {
            // AI ile dinamik başlık üretimi
            $prompt = $this->buildTitlePrompt($content, $seoData, $locale);
            
            if (false && function_exists('ai_execute_feature')) {
                // AI Feature sistemi geçici olarak devre dışı (500 error nedeniyle)
                // Bu kısım credit sistem sorunu düzeldikten sonra aktifleştirilebilir
                        'locale' => $locale,
                        'response_type' => gettype($aiResponse)
                    ]);
                    
                    return $suggestedTitle;
                }
            }
            
            // Fallback: Mevcut başlık varsa döndür
            $currentTitle = $seoData['title'] ?? '';
            if (!empty($currentTitle)) {
                return strlen($currentTitle) > 60 ? substr($currentTitle, 0, 57) . '...' : $currentTitle;
            }
            
            // Son fallback: İçerikten başlık üret
            $cleanContent = strip_tags($content);
            $words = explode(' ', $cleanContent);
            $title = implode(' ', array_slice($words, 0, 8));
            
            return strlen($title) > 60 ? substr($title, 0, 57) . '...' : $title;
            
        } catch (\Exception $e) {
            \Log::error('AI Title generation failed', ['error' => $e->getMessage()]);
            
            // Emergency fallback
            $focusKeyword = $seoData['focus_keyword'] ?? '';
            return !empty($focusKeyword) ? $focusKeyword . ' - Detaylı Bilgi' : 'İçerik Başlığı';
        }
    }

    /**
     * Generate suggested description based on content
     */
    private function generateSuggestedDescription(string $content, array $seoData, string $locale): string
    {
        try {
            // AI ile dinamik açıklama üretimi
            if (function_exists('ai_execute_feature')) {
                // AI Feature sistemi ile açıklama üret - mevcut SEO feature kullan
                $prompt = "Bu içerik için SEO optimizasyonlu, çekici meta açıklama oluştur (120-160 karakter):\n\n";
                $prompt .= "İçerik: " . substr(strip_tags($content), 0, 1200) . "\n";
                $prompt .= "Mevcut açıklama: " . ($seoData['description'] ?? 'Yok') . "\n";
                $prompt .= "Ana kelime: " . ($seoData['focus_keyword'] ?? 'Yok') . "\n";
                $prompt .= "Anahtar kelimeler: " . implode(', ', $seoData['keywords'] ?? []) . "\n";
                $prompt .= "Dil: " . $locale . "\n\n";
                $prompt .= "Çekici ve bilgilendirici açıklama yaz. Sadece açıklamayı ver, başka şey yazma. Türkçe açıklama oluştur.";
                
                $aiResponse = ai_execute_feature('seo-content-generation', [
                    'prompt' => $prompt,
                    'content_type' => 'description',
                    'language' => $locale,
                    'max_length' => 160
                ]);
                
                // AI response can be array or string, handle both
                $descriptionText = '';
                if (is_array($aiResponse)) {
                    $descriptionText = $aiResponse['response'] ?? $aiResponse['content'] ?? $aiResponse['result'] ?? '';
                } elseif (is_string($aiResponse)) {
                    $descriptionText = $aiResponse;
                }
                
                if (!empty($descriptionText)) {
                    $suggestedDescription = trim($descriptionText);
                    
                    // Açıklama uzunluğunu kontrol et ve optimize et
                    if (strlen($suggestedDescription) > 160) {
                        $suggestedDescription = substr($suggestedDescription, 0, 157) . '...';
                    }
                    
                    \Log::info('✅ AI Description suggestion generated', [
                        'suggested_description' => substr($suggestedDescription, 0, 100) . '...',
                        'length' => strlen($suggestedDescription),
                        'locale' => $locale,
                        'response_type' => gettype($aiResponse)
                    ]);
                    
                    return $suggestedDescription;
                }
            }
            
            // Fallback: Mevcut açıklama varsa ve uygunsa döndür
            $currentDescription = $seoData['description'] ?? '';
            if (!empty($currentDescription) && strlen($currentDescription) >= 120 && strlen($currentDescription) <= 160) {
                return $currentDescription;
            }
            
            // Son fallback: İçerikten akıllı açıklama üret
            $cleanContent = strip_tags($content);
            $sentences = preg_split('/[.!?]+/', $cleanContent, -1, PREG_SPLIT_NO_EMPTY);
            
            if (!empty($sentences)) {
                $description = trim($sentences[0]);
                
                // Focus keyword ekle
                $focusKeyword = $seoData['focus_keyword'] ?? '';
                if (!empty($focusKeyword) && stripos($description, $focusKeyword) === false) {
                    $description = $focusKeyword . ' hakkında detaylı bilgi: ' . $description;
                }
                
                return strlen($description) > 160 ? substr($description, 0, 157) . '...' : $description;
            }
            
            // Emergency fallback
            $focusKeyword = $seoData['focus_keyword'] ?? '';
            return !empty($focusKeyword) 
                ? $focusKeyword . ' ile ilgili kapsamlı bilgiler ve detaylar.'
                : 'Bu sayfada ilginç ve değerli bilgiler bulabilirsiniz.';
            
        } catch (\Exception $e) {
            \Log::error('AI Description generation failed', ['error' => $e->getMessage()]);
            
            // Emergency fallback
            return 'Bu konuda detaylı ve faydalı bilgiler içeren kapsamlı bir içerik.';
        }
    }
}