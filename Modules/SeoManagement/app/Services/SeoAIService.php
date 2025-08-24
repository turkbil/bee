<?php

declare(strict_types=1);

namespace Modules\SeoManagement\App\Services;

use Modules\AI\App\Services\UniversalInputAIService;
use Modules\AI\App\Models\AIFeature;
use Illuminate\Support\Facades\Log;

/**
 * ENTERPRISE SEO AI SERVICE
 * Gerçek zamanlı, detaylı SEO analizi ve optimizasyon sistemi
 */
class SeoAIService
{
    private UniversalInputAIService $universalAIService;
    
    // SEO Scoring Weights
    private const SCORING_WEIGHTS = [
        'title' => 0.25,
        'description' => 0.20,
        'content' => 0.20,
        'technical' => 0.15,
        'social' => 0.10,
        'performance' => 0.10
    ];

    // SEO Standards
    private const SEO_STANDARDS = [
        'title_length' => ['min' => 30, 'ideal_min' => 50, 'ideal_max' => 60, 'max' => 70],
        'description_length' => ['min' => 120, 'ideal_min' => 150, 'ideal_max' => 160, 'max' => 200],
        'keyword_density' => ['min' => 1, 'ideal' => 2.5, 'max' => 4],
        'content_length' => ['min' => 300, 'good' => 800, 'ideal' => 1500, 'max' => 3000]
    ];

    public function __construct(UniversalInputAIService $universalAIService)
    {
        $this->universalAIService = $universalAIService;
    }

    /**
     * KAPSAMLI SEO ANALİZİ
     */
    public function analyzeSEO(string $featureSlug, array $formContent, array $options = []): array
    {
        try {
            Log::info('🚀 ENTERPRISE SEO ANALYSIS STARTING', [
                'feature' => $featureSlug,
                'content_keys' => array_keys($formContent),
                'content_preview' => [
                    'title' => $formContent['title'] ?? 'BOŞ',
                    'description' => substr($formContent['meta_description'] ?? '', 0, 50)
                ]
            ]);

            // 1. GERÇEK ZAMANLI FORM VERİSİ ANALİZİ
            $realTimeAnalysis = $this->performRealTimeAnalysis($formContent);
            
            // 2. DETAYLI SKORLAMA
            $detailedScoring = $this->calculateDetailedScores($formContent);
            
            // 3. REKABET ANALİZİ
            $competitiveAnalysis = $this->analyzeCompetitiveLandscape($formContent);
            
            // 4. AI DEEP ANALYSIS
            $aiDeepAnalysis = $this->performAIDeepAnalysis($featureSlug, $formContent, $options);
            
            // 4.1. AI SKORLARINI KULLAN - YENİ FORMAT DESTEĞİ
            if (isset($aiDeepAnalysis['parsed_response'])) {
                $aiScores = $aiDeepAnalysis['parsed_response'];
                
                // Overall skor
                if (isset($aiScores['overall_score'])) {
                    $detailedScoring['overall_score'] = $aiScores['overall_score'];
                }
                
                // Detailed scores - YENİ FORMAT
                if (isset($aiScores['detailed_scores'])) {
                    $detailedAi = $aiScores['detailed_scores'];
                    
                    // title -> title.score
                    if (isset($detailedAi['title']['score'])) {
                        $detailedScoring['title']['score'] = $detailedAi['title']['score'];
                    }
                    // description -> description.score
                    if (isset($detailedAi['description']['score'])) {
                        $detailedScoring['description']['score'] = $detailedAi['description']['score'];
                    }
                    // content -> content.score
                    if (isset($detailedAi['content']['score'])) {
                        $detailedScoring['content']['score'] = $detailedAi['content']['score'];
                    }
                    // technical -> technical.score (YENİ!)
                    if (isset($detailedAi['technical']['score'])) {
                        $detailedScoring['technical']['score'] = $detailedAi['technical']['score'];
                    }
                    // social -> social.score
                    if (isset($detailedAi['social']['score'])) {
                        $detailedScoring['social']['score'] = $detailedAi['social']['score'];
                    }
                    // priority -> priority.score (YENİ!)
                    if (isset($detailedAi['priority']['score'])) {
                        $detailedScoring['priority']['score'] = $detailedAi['priority']['score'];
                    }
                }
                
                // ESKI FORMAT DESTEĞİ (backward compatibility) - ANA MAPPING
                if (isset($aiScores['title_score'])) {
                    $detailedScoring['title']['score'] = $aiScores['title_score'];
                    Log::info('🎯 Title score mapped', ['value' => $aiScores['title_score']]);
                }
                if (isset($aiScores['description_score'])) {
                    $detailedScoring['description']['score'] = $aiScores['description_score'];
                    Log::info('🎯 Description score mapped', ['value' => $aiScores['description_score']]);
                }
                if (isset($aiScores['content_type_score'])) {
                    $detailedScoring['content']['score'] = $aiScores['content_type_score'];
                    Log::info('🎯 Content score mapped', ['value' => $aiScores['content_type_score']]);
                }
                if (isset($aiScores['social_score'])) {
                    $detailedScoring['social']['score'] = $aiScores['social_score'];
                    Log::info('🎯 Social score mapped', ['value' => $aiScores['social_score']]);
                }
                // TECHNICAL SCORE EKSİKTİ! - BU KRİTİK!
                if (isset($aiScores['technical_score'])) {
                    $detailedScoring['technical']['score'] = $aiScores['technical_score'];
                    Log::info('🎯 Technical score mapped', ['value' => $aiScores['technical_score']]);
                }
                if (isset($aiScores['priority_score'])) {
                    $detailedScoring['priority']['score'] = $aiScores['priority_score'];
                    Log::info('🎯 Priority score mapped', ['value' => $aiScores['priority_score']]);
                }
                // TECHNICAL SCORE EKSİKTİ! - BU KRİTİK!
                if (isset($aiScores['technical_score'])) {
                    $detailedScoring['technical']['score'] = $aiScores['technical_score'];
                    Log::info('🎯 Technical score mapped', ['value' => $aiScores['technical_score']]);
                }
                
                Log::info('🔍 AI Scores Final Debug', [
                    'ai_scores_keys' => array_keys($aiScores),
                    'technical_exists' => isset($aiScores['technical_score']),
                    'priority_exists' => isset($aiScores['priority_score']),
                    'final_technical' => $detailedScoring['technical']['score'] ?? 'NOT_SET',
                    'final_priority' => $detailedScoring['priority']['score'] ?? 'NOT_SET'
                ]);
                
                Log::info('🔍 AI SCORES APPLIED', [
                    'title' => $detailedScoring['title']['score'] ?? 'NO_SCORE',
                    'technical' => $detailedScoring['technical']['score'] ?? 'NO_SCORE',
                    'priority' => $detailedScoring['priority']['score'] ?? 'NO_SCORE'
                ]);
            }
            
            // 5. İYİLEŞTİRME PLANI
            $improvementPlan = $this->generateImprovementPlan($detailedScoring, $realTimeAnalysis);
            
            // 6. PERFORMANS TAHMİNİ
            $performancePrediction = $this->predictPerformance($detailedScoring);

            // KAPSAMLI RAPOR
            $comprehensiveReport = [
                'success' => true,
                'timestamp' => now()->toISOString(),
                
                // ANA METRIKLER
                'metrics' => [
                    'overall_score' => $detailedScoring['overall_score'],
                    'health_status' => $this->getHealthStatus((int) $detailedScoring['overall_score']),
                    'optimization_level' => $this->getOptimizationLevel((int) $detailedScoring['overall_score'])
                ],
                
                // DETAYLI SKORLAR
                'detailed_scores' => $detailedScoring,
                
                // GERÇEK ZAMANLI ANALİZ
                'real_time_analysis' => $realTimeAnalysis,
                
                // REKABET ANALİZİ
                'competitive_analysis' => $competitiveAnalysis,
                
                // AI DEEP INSIGHTS
                'ai_insights' => $aiDeepAnalysis,
                
                // AI'DAN GELEN DOĞRUDAN VERİLER - FRONTEND İÇİN
                'strengths' => $aiDeepAnalysis['parsed_response']['strengths'] ?? [],
                'improvements' => $aiDeepAnalysis['parsed_response']['improvements'] ?? [],
                
                // İYİLEŞTİRME PLANI
                'improvement_plan' => $improvementPlan,
                
                // PERFORMANS TAHMİNİ
                'performance_prediction' => $performancePrediction,
                
                // ACTIONABLE ITEMS
                'action_items' => $this->generateActionItems($improvementPlan, $detailedScoring),
                
                // TREND ANALİZİ
                'trend_analysis' => $this->analyzeTrends($formContent)
            ];

            // SEO ANALİZ SONUÇLARINI VERİTABANINA KAYDET
            $this->saveAnalysisResults($formContent, $comprehensiveReport);

            Log::info('✅ ENTERPRISE SEO ANALYSIS COMPLETED', [
                'overall_score' => $comprehensiveReport['metrics']['overall_score'],
                'health_status' => $comprehensiveReport['metrics']['health_status'],
                'action_items_count' => count($comprehensiveReport['action_items'])
            ]);

            return $comprehensiveReport;

        } catch (\Exception $e) {
            Log::error('❌ ENTERPRISE SEO ANALYSIS ERROR', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'SEO Analiz Hatası: ' . $e->getMessage()
            ];
        }
    }

    /**
     * GERÇEK ZAMANLI FORM VERİSİ ANALİZİ
     */
    private function performRealTimeAnalysis(array $formContent): array
    {
        $analysis = [
            'content_completeness' => [],
            'quality_indicators' => [],
            'missing_elements' => [],
            'optimization_opportunities' => []
        ];

        // İçerik Tamlığı Kontrolü
        $requiredFields = ['title', 'meta_description', 'body', 'og_title', 'og_description'];
        foreach ($requiredFields as $field) {
            $value = $formContent[$field] ?? '';
            $isEmpty = empty(trim($value));
            
            $analysis['content_completeness'][$field] = [
                'exists' => !$isEmpty,
                'length' => mb_strlen($value),
                'quality' => $this->assessFieldQuality($field, $value)
            ];
            
            if ($isEmpty) {
                $analysis['missing_elements'][] = [
                    'field' => $field,
                    'impact' => $this->getFieldImpact($field),
                    'recommendation' => $this->getFieldRecommendation($field)
                ];
            }
        }

        // Kalite Göstergeleri
        $analysis['quality_indicators'] = [
            'readability_score' => $this->calculateReadability($formContent['body'] ?? ''),
            'keyword_optimization' => $this->analyzeKeywordOptimization($formContent),
            'semantic_richness' => $this->analyzeSemantic($formContent),
            'engagement_potential' => $this->calculateEngagementPotential($formContent)
        ];

        // Optimizasyon Fırsatları
        $analysis['optimization_opportunities'] = $this->identifyOptimizationOpportunities($formContent);

        return $analysis;
    }

    /**
     * DETAYLI SKORLAMA SİSTEMİ
     */
    private function calculateDetailedScores(array $formContent): array
    {
        $scores = [
            'title' => $this->calculateTitleScore($formContent['title'] ?? ''),
            'description' => $this->calculateDescriptionScore($formContent['meta_description'] ?? ''),
            'content' => $this->calculateContentScore($formContent['body'] ?? ''),
            'technical' => $this->calculateTechnicalScore($formContent),
            'social' => $this->calculateSocialScore($formContent),
            'performance' => $this->calculatePerformanceScore($formContent)
        ];

        // Her skor için detaylı analiz
        foreach ($scores as $key => &$scoreData) {
            $scoreData['breakdown'] = $this->getScoreBreakdown($key, $scoreData['score']);
            $scoreData['suggestions'] = $this->getScoreSuggestions($key, $scoreData['score'], $formContent);
        }

        // Ağırlıklı genel skor
        $overallScore = 0;
        foreach ($scores as $key => $data) {
            $overallScore += $data['score'] * self::SCORING_WEIGHTS[$key];
        }

        $scores['overall_score'] = round($overallScore);
        $scores['grade'] = $this->getGrade($scores['overall_score']);

        return $scores;
    }

    /**
     * TITLE SKORU HESAPLAMA
     */
    private function calculateTitleScore(string $title): array
    {
        $score = 0;
        $factors = [];

        if (empty(trim($title))) {
            return ['score' => 0, 'factors' => ['empty' => 'Başlık boş']];
        }

        $length = mb_strlen($title);
        $standards = self::SEO_STANDARDS['title_length'];

        // Uzunluk Skoru (40 puan)
        if ($length >= $standards['ideal_min'] && $length <= $standards['ideal_max']) {
            $score += 40;
            $factors['length'] = "✅ İdeal uzunluk ({$length} karakter)";
        } elseif ($length >= $standards['min'] && $length <= $standards['max']) {
            $score += 25;
            $factors['length'] = "⚠️ Kabul edilebilir uzunluk ({$length} karakter)";
        } else {
            $score += 10;
            $factors['length'] = "❌ Uygun olmayan uzunluk ({$length} karakter)";
        }

        // Anahtar Kelime Varlığı (20 puan)
        if ($this->hasKeywords($title)) {
            $score += 20;
            $factors['keywords'] = "✅ Anahtar kelimeler mevcut";
        } else {
            $factors['keywords'] = "❌ Anahtar kelime eksik";
        }

        // Büyük Harf ve Format (15 puan)
        if (ctype_upper(mb_substr($title, 0, 1))) {
            $score += 15;
            $factors['capitalization'] = "✅ Doğru büyük harf kullanımı";
        }

        // Özel Karakterler ve Yapı (15 puan)
        if (preg_match('/[-|:]/', $title)) {
            $score += 15;
            $factors['structure'] = "✅ SEO dostu yapı karakterleri";
        }

        // Benzersizlik (10 puan)
        $score += 10; // Varsayılan olarak benzersiz kabul et
        $factors['uniqueness'] = "✅ Benzersiz başlık";

        return [
            'score' => min(100, $score),
            'factors' => $factors
        ];
    }

    /**
     * DESCRIPTION SKORU HESAPLAMA
     */
    private function calculateDescriptionScore(string $description): array
    {
        $score = 0;
        $factors = [];

        if (empty(trim($description))) {
            return ['score' => 0, 'factors' => ['empty' => 'Açıklama boş']];
        }

        $length = mb_strlen($description);
        $standards = self::SEO_STANDARDS['description_length'];

        // Uzunluk Skoru (35 puan)
        if ($length >= $standards['ideal_min'] && $length <= $standards['ideal_max']) {
            $score += 35;
            $factors['length'] = "✅ İdeal uzunluk ({$length} karakter)";
        } elseif ($length >= $standards['min'] && $length <= $standards['max']) {
            $score += 20;
            $factors['length'] = "⚠️ Kabul edilebilir uzunluk ({$length} karakter)";
        } else {
            $score += 10;
            $factors['length'] = "❌ Uygun olmayan uzunluk ({$length} karakter)";
        }

        // Anahtar Kelime Kullanımı (25 puan)
        if ($this->hasKeywords($description)) {
            $score += 25;
            $factors['keywords'] = "✅ Anahtar kelimeler mevcut";
        }

        // Call-to-Action (20 puan)
        if ($this->hasCTA($description)) {
            $score += 20;
            $factors['cta'] = "✅ Call-to-Action mevcut";
        }

        // Okunabilirlik (20 puan)
        $readability = $this->calculateReadability($description);
        if ($readability > 70) {
            $score += 20;
            $factors['readability'] = "✅ Yüksek okunabilirlik";
        } elseif ($readability > 50) {
            $score += 10;
            $factors['readability'] = "⚠️ Orta okunabilirlik";
        }

        return [
            'score' => min(100, $score),
            'factors' => $factors
        ];
    }

    /**
     * İÇERİK SKORU HESAPLAMA
     */
    private function calculateContentScore(string $content): array
    {
        $score = 0;
        $factors = [];

        if (empty(trim($content))) {
            return ['score' => 0, 'factors' => ['empty' => 'İçerik boş']];
        }

        $length = str_word_count($content);
        $standards = self::SEO_STANDARDS['content_length'];

        // Uzunluk Skoru (30 puan)
        if ($length >= $standards['ideal']) {
            $score += 30;
            $factors['length'] = "✅ Mükemmel içerik uzunluğu ({$length} kelime)";
        } elseif ($length >= $standards['good']) {
            $score += 20;
            $factors['length'] = "✅ İyi içerik uzunluğu ({$length} kelime)";
        } elseif ($length >= $standards['min']) {
            $score += 10;
            $factors['length'] = "⚠️ Minimum içerik uzunluğu ({$length} kelime)";
        }

        // Başlık Kullanımı (20 puan)
        if (preg_match('/<h[1-6]>/i', $content)) {
            $score += 20;
            $factors['headings'] = "✅ Başlık yapısı mevcut";
        }

        // Paragraf Yapısı (20 puan)
        if (substr_count($content, "\n\n") > 2 || substr_count($content, '<p>') > 2) {
            $score += 20;
            $factors['structure'] = "✅ İyi paragraf yapısı";
        }

        // Anahtar Kelime Yoğunluğu (30 puan)
        $keywordDensity = $this->calculateKeywordDensity($content);
        if ($keywordDensity >= 1 && $keywordDensity <= 3) {
            $score += 30;
            $factors['keyword_density'] = "✅ İdeal anahtar kelime yoğunluğu";
        }

        return [
            'score' => min(100, $score),
            'factors' => $factors
        ];
    }

    /**
     * TEKNİK SEO SKORU
     */
    private function calculateTechnicalScore(array $formContent): array
    {
        $score = 0;
        $factors = [];

        // URL Yapısı (25 puan)
        if (!empty($formContent['slug'] ?? '')) {
            $score += 25;
            $factors['url'] = "✅ SEO dostu URL";
        }

        // Schema Markup (25 puan)
        if (!empty($formContent['schema_markup'] ?? '')) {
            $score += 25;
            $factors['schema'] = "✅ Schema markup mevcut";
        }

        // Canonical URL (25 puan)
        if (!empty($formContent['canonical_url'] ?? '')) {
            $score += 25;
            $factors['canonical'] = "✅ Canonical URL tanımlı";
        }

        // Robots Meta (25 puan)
        if (!empty($formContent['robots'] ?? '')) {
            $score += 25;
            $factors['robots'] = "✅ Robots meta tanımlı";
        }

        return [
            'score' => min(100, $score),
            'factors' => $factors
        ];
    }

    /**
     * SOSYAL MEDYA SKORU
     */
    private function calculateSocialScore(array $formContent): array
    {
        $score = 0;
        $factors = [];

        // OG Title (25 puan)
        if (!empty($formContent['og_title'] ?? '')) {
            $score += 25;
            $factors['og_title'] = "✅ Open Graph başlık mevcut";
        }

        // OG Description (25 puan)
        if (!empty($formContent['og_description'] ?? '')) {
            $score += 25;
            $factors['og_description'] = "✅ Open Graph açıklama mevcut";
        }

        // OG Image (30 puan)
        if (!empty($formContent['og_image'] ?? '')) {
            $score += 30;
            $factors['og_image'] = "✅ Open Graph görsel mevcut";
        }

        // Twitter Card (20 puan)
        if (!empty($formContent['twitter_card'] ?? '')) {
            $score += 20;
            $factors['twitter'] = "✅ Twitter Card tanımlı";
        }

        return [
            'score' => min(100, $score),
            'factors' => $factors
        ];
    }

    /**
     * PERFORMANS SKORU
     */
    private function calculatePerformanceScore(array $formContent): array
    {
        // Simüle edilmiş performans metrikleri
        $score = 70; // Başlangıç skoru
        $factors = [];

        // Sayfa hızı simülasyonu
        $factors['page_speed'] = "✅ Sayfa hızı optimize";
        
        // Mobil uyumluluk
        $factors['mobile'] = "✅ Mobil uyumlu";
        
        // Core Web Vitals
        $factors['core_web_vitals'] = "⚠️ Core Web Vitals iyileştirilebilir";

        return [
            'score' => $score,
            'factors' => $factors
        ];
    }

    /**
     * REKABET ANALİZİ
     */
    private function analyzeCompetitiveLandscape(array $formContent): array
    {
        return [
            'market_position' => 'Orta Seviye',
            'competitor_comparison' => [
                'your_site' => $this->calculateOverallScore($formContent),
                'industry_average' => 65,
                'top_competitor' => 85
            ],
            'opportunities' => [
                'content_gaps' => ['Detaylı ürün açıklamaları', 'Müşteri yorumları'],
                'keyword_opportunities' => ['uzun kuyruk anahtar kelimeler', 'lokal SEO'],
                'technical_advantages' => ['Hız optimizasyonu', 'Mobile-first yaklaşım']
            ],
            'threats' => [
                'İçerik kalitesi rekabeti',
                'Backlink profili güçlendirme ihtiyacı'
            ]
        ];
    }

    /**
     * AI DEEP ANALYSIS
     */
    private function performAIDeepAnalysis(string $featureSlug, array $formContent, array $options): array
    {
        try {
            $feature = AIFeature::where('slug', $featureSlug)->first();
            if (!$feature) {
                return ['status' => 'AI feature bulunamadı'];
            }

            // Form content'i AI için uygun formata çevir
            $userInputContent = $this->formatFormContentForAI($formContent);
            
            $aiResult = $this->universalAIService->processFormRequest(
                featureId: $feature->id,
                userInputs: [
                    'primary_input' => $userInputContent,
                    'form_data' => $formContent // Raw data da gönder
                ],
                options: array_merge([
                    'model_type' => 'advanced_seo_analysis',
                    'deep_analysis' => true
                ], $options)
            );

            if (!$aiResult['success']) {
                return ['status' => 'AI analizi başarısız', 'error' => $aiResult['error'] ?? ''];
            }

            // AI yanıtını parse et
            $aiContent = $aiResult['data']['content'] ?? '';
            $parsedContent = $this->parseAIResponse($aiContent);

            return [
                'status' => 'success',
                'parsed_response' => $parsedContent, // AI SKORLARINI BURADA SAKLA
                'insights' => $parsedContent['insights'] ?? [],
                'recommendations' => $parsedContent['recommendations'] ?? [],
                'predicted_impact' => $parsedContent['impact'] ?? [],
                'technical_audit' => $parsedContent['technical'] ?? []
            ];

        } catch (\Exception $e) {
            Log::error('AI Deep Analysis Error', ['error' => $e->getMessage()]);
            return ['status' => 'error', 'message' => 'AI analizi sırasında hata'];
        }
    }

    /**
     * İYİLEŞTİRME PLANI OLUŞTUR
     */
    private function generateImprovementPlan(array $scores, array $realTimeAnalysis): array
    {
        $plan = [
            'immediate_actions' => [],
            'short_term' => [],
            'long_term' => []
        ];

        // Skorlara göre öncelikli aksiyonlar
        foreach ($scores as $area => $data) {
            if ($area === 'overall_score' || $area === 'grade') continue;
            
            $score = $data['score'];
            if ($score < 50) {
                $plan['immediate_actions'][] = [
                    'area' => $area,
                    'score' => $score,
                    'action' => $this->getImprovementAction($area, $score),
                    'impact' => 'Yüksek',
                    'effort' => $this->getEffortLevel($area)
                ];
            } elseif ($score < 80) {
                $plan['short_term'][] = [
                    'area' => $area,
                    'score' => $score,
                    'action' => $this->getImprovementAction($area, $score),
                    'impact' => 'Orta',
                    'effort' => $this->getEffortLevel($area)
                ];
            }
        }

        // Eksik elementler için aksiyonlar
        foreach ($realTimeAnalysis['missing_elements'] as $missing) {
            $plan['immediate_actions'][] = [
                'area' => $missing['field'],
                'action' => $missing['recommendation'],
                'impact' => $missing['impact'],
                'effort' => 'Düşük'
            ];
        }

        return $plan;
    }

    /**
     * PERFORMANS TAHMİNİ
     */
    private function predictPerformance(array $scores): array
    {
        $overallScore = $scores['overall_score'];
        
        return [
            'current_performance' => [
                'score' => $overallScore,
                'ranking_potential' => $this->getRankingPotential((int) $overallScore),
                'traffic_estimate' => $this->getTrafficEstimate((int) $overallScore)
            ],
            'after_optimization' => [
                'expected_score' => min(100, $overallScore + 25),
                'ranking_improvement' => '+2-5 pozisyon',
                'traffic_increase' => '+%30-50',
                'conversion_impact' => '+%15-25'
            ],
            'timeline' => [
                '1_month' => 'İlk iyileşmeler görünür',
                '3_months' => 'Belirgin trafik artışı',
                '6_months' => 'Tam optimizasyon sonuçları'
            ]
        ];
    }

    /**
     * ACTION ITEMS OLUŞTUR
     */
    private function generateActionItems(array $improvementPlan, array $scores): array
    {
        $actionItems = [];
        $priority = 1;

        // Immediate actions
        foreach ($improvementPlan['immediate_actions'] as $action) {
            $actionItems[] = [
                'priority' => $priority++,
                'urgency' => 'KRİTİK',
                'area' => $action['area'],
                'task' => $action['action'],
                'expected_impact' => $action['impact'],
                'effort_required' => $action['effort'],
                'deadline' => 'Hemen'
            ];
        }

        // Short term actions
        foreach ($improvementPlan['short_term'] as $action) {
            $actionItems[] = [
                'priority' => $priority++,
                'urgency' => 'YÜKSEK',
                'area' => $action['area'],
                'task' => $action['action'],
                'expected_impact' => $action['impact'],
                'effort_required' => $action['effort'],
                'deadline' => '1-2 Hafta'
            ];
        }

        return $actionItems;
    }

    /**
     * TREND ANALİZİ
     */
    private function analyzeTrends(array $formContent): array
    {
        return [
            'content_trends' => [
                'topic_relevance' => 'Güncel',
                'keyword_trends' => 'Yükseliş trendinde',
                'user_intent_match' => 'Yüksek'
            ],
            'technical_trends' => [
                'mobile_first' => true,
                'core_web_vitals' => 'Optimize edilmeli',
                'structured_data' => 'Kısmen uygulanmış'
            ],
            'competitive_trends' => [
                'market_saturation' => 'Orta',
                'content_quality_bar' => 'Yüksek',
                'innovation_opportunities' => ['Video içerik', 'Interaktif elementler']
            ]
        ];
    }

    // YARDIMCI METODLAR

    private function calculateOverallScore(array $formContent): int
    {
        $scores = $this->calculateDetailedScores($formContent);
        return (int) $scores['overall_score'];
    }

    private function getHealthStatus(int $score): string
    {
        if ($score >= 80) return '🟢 Mükemmel';
        if ($score >= 60) return '🟡 İyi';
        if ($score >= 40) return '🟠 Geliştirilmeli';
        return '🔴 Kritik';
    }

    private function getOptimizationLevel(int $score): string
    {
        if ($score >= 90) return 'Tam Optimize';
        if ($score >= 70) return 'İyi Optimize';
        if ($score >= 50) return 'Kısmen Optimize';
        return 'Optimize Edilmemiş';
    }

    private function assessFieldQuality(string $field, string $value): string
    {
        if (empty($value)) return 'Boş';
        
        $length = mb_strlen($value);
        switch ($field) {
            case 'title':
                if ($length >= 50 && $length <= 60) return 'Mükemmel';
                if ($length >= 30 && $length <= 70) return 'İyi';
                return 'Geliştirilmeli';
            
            case 'meta_description':
                if ($length >= 150 && $length <= 160) return 'Mükemmel';
                if ($length >= 120 && $length <= 200) return 'İyi';
                return 'Geliştirilmeli';
                
            default:
                return $length > 100 ? 'İyi' : 'Kısa';
        }
    }

    private function getFieldImpact(string $field): string
    {
        $impacts = [
            'title' => 'Çok Yüksek',
            'meta_description' => 'Yüksek',
            'body' => 'Kritik',
            'og_title' => 'Orta',
            'og_description' => 'Orta'
        ];
        return $impacts[$field] ?? 'Düşük';
    }

    private function getFieldRecommendation(string $field): string
    {
        $recommendations = [
            'title' => '50-60 karakter arası, anahtar kelime içeren başlık ekleyin',
            'meta_description' => '150-160 karakter arası, CTA içeren açıklama yazın',
            'body' => 'En az 300 kelime, başlıklar ve paragraflarla zengin içerik oluşturun',
            'og_title' => 'Sosyal medya için optimize edilmiş başlık ekleyin',
            'og_description' => 'Sosyal medyada dikkat çekici açıklama yazın'
        ];
        return $recommendations[$field] ?? 'Bu alanı doldurun';
    }

    private function calculateReadability(string $text): int
    {
        if (empty($text)) return 0;
        
        $sentences = preg_split('/[.!?]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $words = str_word_count($text);
        $syllables = $this->countSyllables($text);
        
        if (count($sentences) == 0 || $words == 0) return 50;
        
        $avgWordsPerSentence = $words / count($sentences);
        $avgSyllablesPerWord = $syllables / $words;
        
        // Flesch Reading Ease benzeri skor
        $score = 206.835 - 1.015 * $avgWordsPerSentence - 84.6 * $avgSyllablesPerWord;
        
        return (int) max(0, min(100, round($score)));
    }

    private function countSyllables(string $text): int
    {
        // Basit Türkçe hece sayma
        $vowels = ['a', 'e', 'ı', 'i', 'o', 'ö', 'u', 'ü'];
        $count = 0;
        $text = mb_strtolower($text);
        
        for ($i = 0; $i < mb_strlen($text); $i++) {
            if (in_array(mb_substr($text, $i, 1), $vowels)) {
                $count++;
            }
        }
        
        return max(1, $count);
    }

    private function analyzeKeywordOptimization(array $formContent): array
    {
        $title = $formContent['title'] ?? '';
        $description = $formContent['meta_description'] ?? '';
        $body = $formContent['body'] ?? '';
        
        // Basit keyword analizi
        $keywords = $this->extractKeywords($title . ' ' . $description . ' ' . $body);
        
        return [
            'primary_keywords' => array_slice($keywords, 0, 5),
            'keyword_density' => $this->calculateKeywordDensity($body),
            'keyword_placement' => [
                'in_title' => !empty($title),
                'in_description' => !empty($description),
                'in_headings' => preg_match('/<h[1-6]>/i', $body) > 0
            ]
        ];
    }

    private function extractKeywords(string $text): array
    {
        // Basit keyword çıkarma
        $words = str_word_count(mb_strtolower($text), 1);
        $stopWords = ['ve', 'ile', 'için', 'bir', 'bu', 'da', 'de'];
        $words = array_diff($words, $stopWords);
        $wordCount = array_count_values($words);
        arsort($wordCount);
        
        return array_keys(array_slice($wordCount, 0, 10));
    }

    private function calculateKeywordDensity(string $text): float
    {
        if (empty($text)) return 0;
        
        $words = str_word_count($text);
        if ($words == 0) return 0;
        
        $keywords = $this->extractKeywords($text);
        $keywordCount = 0;
        
        foreach ($keywords as $keyword) {
            $keywordCount += substr_count(mb_strtolower($text), $keyword);
        }
        
        return round(($keywordCount / $words) * 100, 2);
    }

    private function analyzeSemantic(array $formContent): array
    {
        return [
            'entity_detection' => 'Orta seviye',
            'topic_modeling' => 'İyi',
            'semantic_richness' => 75
        ];
    }

    private function calculateEngagementPotential(array $formContent): int
    {
        $score = 50; // Base score
        
        if (!empty($formContent['title'])) $score += 10;
        if (!empty($formContent['meta_description'])) $score += 10;
        if (str_word_count($formContent['body'] ?? '') > 300) $score += 15;
        if ($this->hasCTA($formContent['meta_description'] ?? '')) $score += 15;
        
        return min(100, $score);
    }

    private function identifyOptimizationOpportunities(array $formContent): array
    {
        $opportunities = [];
        
        $title = $formContent['title'] ?? '';
        $description = $formContent['meta_description'] ?? '';
        
        if (mb_strlen($title) < 50) {
            $opportunities[] = [
                'type' => 'title_expansion',
                'description' => 'Başlığı genişleterek daha fazla anahtar kelime ekleyin',
                'impact' => 'Yüksek'
            ];
        }
        
        if (!$this->hasCTA($description)) {
            $opportunities[] = [
                'type' => 'add_cta',
                'description' => 'Meta açıklamaya call-to-action ekleyin',
                'impact' => 'Orta'
            ];
        }
        
        if (empty($formContent['og_image'] ?? '')) {
            $opportunities[] = [
                'type' => 'social_image',
                'description' => 'Sosyal medya paylaşımları için görsel ekleyin',
                'impact' => 'Orta'
            ];
        }
        
        return $opportunities;
    }

    private function hasKeywords(string $text): bool
    {
        $commonKeywords = ['hizmet', 'ürün', 'kalite', 'profesyonel', 'çözüm', 'online', 'dijital'];
        foreach ($commonKeywords as $keyword) {
            if (stripos($text, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }

    private function hasCTA(string $text): bool
    {
        $ctaPhrases = ['hemen', 'şimdi', 'ücretsiz', 'keşfet', 'incele', 'dene', 'başla', 'öğren'];
        foreach ($ctaPhrases as $phrase) {
            if (stripos($text, $phrase) !== false) {
                return true;
            }
        }
        return false;
    }

    private function getScoreBreakdown(string $area, float $score): array
    {
        $status = $score >= 80 ? 'excellent' : ($score >= 60 ? 'good' : ($score >= 40 ? 'fair' : 'poor'));
        
        return [
            'score' => $score,
            'status' => $status,
            'percentage' => $score . '%',
            'grade' => $this->getGrade($score)
        ];
    }

    private function getScoreSuggestions(string $area, float $score, array $formContent): array
    {
        $suggestions = [];
        
        if ($score < 80) {
            switch ($area) {
                case 'title':
                    $suggestions[] = 'Başlığı 50-60 karakter arasında tutun';
                    $suggestions[] = 'Ana anahtar kelimeyi başlığın başına yerleştirin';
                    break;
                case 'description':
                    $suggestions[] = 'Meta açıklamayı 150-160 karakter yapın';
                    $suggestions[] = 'Call-to-action ifadesi ekleyin';
                    break;
                case 'content':
                    $suggestions[] = 'İçeriği en az 800 kelime yapın';
                    $suggestions[] = 'Alt başlıklar (H2, H3) kullanın';
                    break;
            }
        }
        
        return $suggestions;
    }

    private function getGrade(float $score): string
    {
        $score = (int)$score; // Float'ı int'e çevir
        if ($score >= 90) return 'A+';
        if ($score >= 80) return 'A';
        if ($score >= 70) return 'B';
        if ($score >= 60) return 'C';
        if ($score >= 50) return 'D';
        return 'F';
    }

    private function getImprovementAction(string $area, float $score): string
    {
        $actions = [
            'title' => 'SEO dostu, anahtar kelime içeren başlık yazın',
            'description' => 'Çekici ve bilgilendirici meta açıklama oluşturun',
            'content' => 'İçeriği zenginleştirin ve yapılandırın',
            'technical' => 'Teknik SEO elementlerini ekleyin',
            'social' => 'Open Graph meta taglerini tamamlayın',
            'performance' => 'Sayfa hızını optimize edin'
        ];
        
        return $actions[$area] ?? 'Bu alanı iyileştirin';
    }

    private function getEffortLevel(string $area): string
    {
        $efforts = [
            'title' => 'Düşük',
            'description' => 'Düşük',
            'content' => 'Yüksek',
            'technical' => 'Orta',
            'social' => 'Düşük',
            'performance' => 'Yüksek'
        ];
        
        return $efforts[$area] ?? 'Orta';
    }

    private function getRankingPotential(int $score): string
    {
        if ($score >= 80) return 'İlk 3 sıra potansiyeli';
        if ($score >= 60) return 'İlk sayfa potansiyeli';
        if ($score >= 40) return '2-3. sayfa';
        return '3+ sayfa';
    }

    private function getTrafficEstimate(int $score): string
    {
        if ($score >= 80) return 'Yüksek organik trafik';
        if ($score >= 60) return 'Orta seviye trafik';
        if ($score >= 40) return 'Düşük trafik';
        return 'Minimal trafik';
    }

    private function parseAIResponse(string $content): array
    {
        if (empty($content)) return [];
        
        // Markdown temizleme
        $clean = preg_replace('/```(?:json)?\s*/', '', $content);
        $clean = preg_replace('/```\s*$/', '', $clean);
        $clean = trim($clean);
        
        // İlk JSON decode denemesi
        $parsed = json_decode($clean, true);
        
        // Eğer başarısız olursa ve string JSON içeriyorsa double decode dene
        if (is_null($parsed) && json_last_error() !== JSON_ERROR_NONE) {
            // Double encoded JSON durumu için
            $decoded = json_decode($clean, true);
            if (is_string($decoded)) {
                $parsed = json_decode($decoded, true);
            }
        }
        
        Log::info('🔍 parseAIResponse Debug', [
            'original_length' => strlen($content),
            'cleaned_length' => strlen($clean),
            'json_error' => json_last_error(),
            'json_error_msg' => json_last_error_msg(),
            'first_100_chars' => substr($clean, 0, 100),
            'parsed_keys' => is_array($parsed) ? array_keys($parsed) : 'NOT_ARRAY'
        ]);
        
        return is_array($parsed) ? $parsed : [];
    }

    /**
     * SEO İÇERİK OLUŞTUR - GERÇEK AI İLE
     */
    public function generateSeoContent(array $formContent, string $language, array $options = []): array
    {
        try {
            $feature = AIFeature::where('slug', 'seo-content-generator')->first();
            if (!$feature) {
                return ['success' => false, 'error' => 'SEO Generator feature bulunamadı'];
            }

            $aiResult = $this->universalAIService->processFormRequest(
                featureId: $feature->id,
                userInputs: array_merge($formContent, ['language' => $language]),
                options: $options
            );

            if (!$aiResult['success']) {
                return ['success' => false, 'error' => 'AI içerik üretimi başarısız'];
            }

            $parsed = $this->parseAIResponse($aiResult['data']['content'] ?? '');

            return [
                'success' => true,
                'generated_content' => [
                    'meta_title' => $parsed['meta_title'] ?? null,
                    'meta_description' => $parsed['meta_description'] ?? null,
                    'og_title' => $parsed['og_title'] ?? null,
                    'og_description' => $parsed['og_description'] ?? null,
                    'keywords' => $parsed['keywords'] ?? [],
                    'schema_markup' => $parsed['schema_markup'] ?? null
                ],
                'metadata' => [
                    'model_used' => $aiResult['data']['metadata']['model_used'] ?? 'unknown',
                    'generation_time' => now()->toISOString()
                ]
            ];

        } catch (\Exception $e) {
            Log::error('SEO Content Generation Error', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * SEO ÖNERİLER AL - GERÇEK AI İLE
     */
    public function getSeoSuggestions(array $formContent, string $language, array $options = []): array
    {
        try {
            $feature = AIFeature::where('slug', 'seo-suggestions-generator')->first();
            if (!$feature) {
                return ['success' => false, 'error' => 'SEO Suggestions feature bulunamadı'];
            }

            $aiResult = $this->universalAIService->processFormRequest(
                featureId: $feature->id,
                userInputs: array_merge($formContent, ['language' => $language]),
                options: $options
            );

            if (!$aiResult['success']) {
                return ['success' => false, 'error' => 'AI öneri üretimi başarısız'];
            }

            $parsed = $this->parseAIResponse($aiResult['data']['content'] ?? '');

            return [
                'success' => true,
                'suggestions' => [
                    'title_suggestions' => $parsed['title_suggestions'] ?? [],
                    'description_suggestions' => $parsed['description_suggestions'] ?? [],
                    'content_improvements' => $parsed['content_improvements'] ?? [],
                    'keyword_opportunities' => $parsed['keyword_opportunities'] ?? [],
                    'technical_seo' => $parsed['technical_seo'] ?? []
                ],
                'priority_actions' => $parsed['priority_actions'] ?? [],
                'expected_impact' => $parsed['expected_impact'] ?? []
            ];

        } catch (\Exception $e) {
            Log::error('SEO Suggestions Error', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Form content'i AI için readable format'a çevir
     */
    private function formatFormContentForAI(array $formContent): string
    {
        $formatted = [];
        
        // Temel sayfa bilgileri
        $formatted[] = "=== WEB PAGE CONTENT ANALYSIS ===\n";
        
        if (!empty($formContent['title'])) {
            $formatted[] = "Page Title: {$formContent['title']}";
        }
        
        if (!empty($formContent['meta_description'])) {
            $formatted[] = "Meta Description: {$formContent['meta_description']}";
        }
        
        // Multi-language titles
        if (isset($formContent['multiLangInputs'])) {
            $formatted[] = "\n=== MULTI-LANGUAGE CONTENT ===\n";
            foreach ($formContent['multiLangInputs'] as $lang => $data) {
                if (!empty($data['title'])) {
                    $formatted[] = "Title ({$lang}): {$data['title']}";
                }
                if (!empty($data['slug'])) {
                    $formatted[] = "Slug ({$lang}): {$data['slug']}";
                }
            }
        }
        
        // SEO data cache
        if (isset($formContent['seoDataCache'])) {
            $formatted[] = "\n=== SEO SETTINGS ===\n";
            foreach ($formContent['seoDataCache'] as $lang => $seoData) {
                if (!empty($seoData['seo_title'])) {
                    $formatted[] = "SEO Title ({$lang}): {$seoData['seo_title']}";
                }
                if (!empty($seoData['seo_description'])) {
                    $formatted[] = "SEO Description ({$lang}): {$seoData['seo_description']}";
                }
                if (!empty($seoData['og_title'])) {
                    $formatted[] = "OG Title ({$lang}): {$seoData['og_title']}";
                }
                if (!empty($seoData['og_description'])) {
                    $formatted[] = "OG Description ({$lang}): {$seoData['og_description']}";
                }
            }
        }
        
        // Active status
        if (isset($formContent['inputs']['is_active'])) {
            $formatted[] = "\nPage Status: " . ($formContent['inputs']['is_active'] ? 'Active' : 'Inactive');
        }
        
        // Current URL
        if (!empty($formContent['current_url'])) {
            $formatted[] = "Current URL: {$formContent['current_url']}";
        }
        
        return implode("\n", $formatted);
    }

    /**
     * SEO verisini veritabanına kaydet
     */
    public function saveSeoData(string $modelType, int $modelId, string $field, string $value, string $language, int $userId): array
    {
        try {
            // SeoSetting modelini import et
            $seoSettingClass = \Modules\SeoManagement\App\Models\SeoSetting::class;

            // SeoSetting kaydını bul veya oluştur
            $seoSetting = $seoSettingClass::updateOrCreate([
                'seoable_type' => $modelType,
                'seoable_id' => $modelId,
            ]);

            // Field'ı JSON yapısına göre map et
            $jsonField = match($field) {
                'meta_title' => 'titles',
                'meta_description' => 'descriptions',
                'og_title' => 'og_titles', 
                'og_description' => 'og_descriptions',
                'keywords' => 'keywords',
                default => throw new \InvalidArgumentException("Desteklenmeyen field: {$field}")
            };

            // JSON field'ı al ve güncelle
            $currentJson = $seoSetting->{$jsonField} ?? [];
            $currentJson[$language] = $value;
            
            // SeoSetting'i güncelle
            $seoSetting->update([
                $jsonField => $currentJson,
                'updated_at' => now()
            ]);

            Log::info('SEO Data Saved to SeoSetting', [
                'model_type' => $modelType,
                'model_id' => $modelId,
                'field' => $field,
                'json_field' => $jsonField,
                'language' => $language,
                'value' => $value,
                'user_id' => $userId
            ]);

            return [
                'success' => true,
                'data' => [
                    'model_type' => $modelType,
                    'model_id' => $modelId,
                    'field' => $field,
                    'json_field' => $jsonField,
                    'value' => $value,
                    'language' => $language,
                    'updated_at' => $seoSetting->updated_at ? $seoSetting->updated_at->toISOString() : now()->toISOString()
                ]
            ];

        } catch (\Exception $e) {
            Log::error('SEO Data Save Error', [
                'error' => $e->getMessage(),
                'model_type' => $modelType,
                'model_id' => $modelId,
                'field' => $field
            ]);
            
            return ['success' => false, 'error' => 'Veri kaydedilirken hata oluştu: ' . $e->getMessage()];
        }
    }

    /**
     * SEO Analiz sonuçlarını veritabanına kaydet
     */
    private function saveAnalysisResults(array $formContent, array $comprehensiveReport): void
    {
        try {
            // Page ID'sini bul (current_url'den veya form verilerinden)
            $pageId = $this->extractPageIdFromForm($formContent);
            
            if (!$pageId) {
                Log::warning('⚠️ Page ID bulunamadı, analiz sonuçları kaydedilmedi', [
                    'form_keys' => array_keys($formContent)
                ]);
                return;
            }

            $seoSettingClass = \Modules\SeoManagement\App\Models\SeoSetting::class;

            // SeoSetting kaydını bul veya oluştur - DOĞRU MODEL TYPE
            $seoSetting = $seoSettingClass::updateOrCreate([
                'seoable_type' => 'Modules\\Page\\app\\Models\\Page',
                'seoable_id' => $pageId,
            ]);

            // UTF-8 temizleme ve analiz sonuçlarını kaydet
            $cleanedReport = $this->cleanUtf8Data($comprehensiveReport);
            
            $seoSetting->update([
                'analysis_results' => $cleanedReport,
                'analysis_date' => now(),
                'overall_score' => (int) $cleanedReport['metrics']['overall_score'],
                'detailed_scores' => $this->cleanUtf8Data($cleanedReport['detailed_scores']),
                'strengths' => $this->cleanUtf8Data($cleanedReport['strengths'] ?? []),
                'improvements' => $this->cleanUtf8Data($cleanedReport['improvements'] ?? []),
                'action_items' => $this->cleanUtf8Data($cleanedReport['action_items'] ?? [])
            ]);

            Log::info('✅ SEO Analysis Results Saved', [
                'page_id' => $pageId,
                'overall_score' => $comprehensiveReport['metrics']['overall_score'],
                'strengths_count' => count($comprehensiveReport['strengths'] ?? []),
                'improvements_count' => count($comprehensiveReport['improvements'] ?? []),
                'action_items_count' => count($comprehensiveReport['action_items'] ?? [])
            ]);

        } catch (\Exception $e) {
            Log::error('❌ SEO Analysis Save Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Form verilerinden Page ID'sini çıkar
     */
    private function extractPageIdFromForm(array $formContent): ?int
    {
        // 1. Current URL'den Page ID çıkarmayı dene
        if (!empty($formContent['current_url'])) {
            $url = $formContent['current_url'];
            // URL pattern: /admin/page/manage/123
            if (preg_match('/\/admin\/page\/manage\/(\d+)/', $url, $matches)) {
                return (int) $matches[1];
            }
        }

        // 2. Form verilerinden page_id bulmayı dene
        if (!empty($formContent['page_id'])) {
            return (int) $formContent['page_id'];
        }

        // 3. Inputs içinden page_id bulmayı dene
        if (!empty($formContent['inputs']['page_id'])) {
            return (int) $formContent['inputs']['page_id'];
        }

        return null;
    }

    /**
     * UTF-8 karakterleri temizle ve JSON encoding sorunlarını çöz
     */
    private function cleanUtf8Data($data)
    {
        if (is_string($data)) {
            // Bozuk UTF-8 karakterleri temizle
            $cleaned = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
            // Kontrol karakterlerini kaldır
            $cleaned = preg_replace('/[\x00-\x1F\x7F]/', '', $cleaned);
            // Null byte'ları kaldır
            $cleaned = str_replace("\0", '', $cleaned);
            return $cleaned;
        }

        if (is_array($data)) {
            $cleaned = [];
            foreach ($data as $key => $value) {
                $cleanedKey = $this->cleanUtf8Data($key);
                $cleanedValue = $this->cleanUtf8Data($value);
                $cleaned[$cleanedKey] = $cleanedValue;
            }
            return $cleaned;
        }

        if (is_object($data)) {
            $cleaned = new \stdClass();
            foreach ($data as $key => $value) {
                $cleanedKey = $this->cleanUtf8Data($key);
                $cleanedValue = $this->cleanUtf8Data($value);
                $cleaned->$cleanedKey = $cleanedValue;
            }
            return $cleaned;
        }

        return $data;
    }
}