<?php

declare(strict_types=1);

namespace Modules\SeoManagement\App\Services;

use Modules\AI\App\Services\AIService;
use Modules\AI\App\Models\AIFeature;
use Illuminate\Support\Facades\Log;

/**
 * SEO RECOMMENDATIONS SERVICE
 * AI-powered SEO önerileri için ayrı servis
 */
class SeoRecommendationsService
{
    private AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * AI SEO ÖNERİLERİ ÜRETİCİSİ - PREMIUM FEATURE
     */
    public function generateSeoRecommendations(string $featureSlug, array $formContent, string $language = 'tr', array $options = []): array
    {
        try {
            Log::info('SEO Recommendations Generation Started', [
                'feature_slug' => $featureSlug,
                'language' => $language,
                'user_id' => $options['user_id'] ?? null
            ]);

            // Form içeriğini analiz et
            $currentTitle = $formContent['title'] ?? '';
            $currentDescription = $formContent['description'] ?? '';
            $currentContent = $formContent['content'] ?? '';
            
            // Sayfa türü ve context analizi
            $pageContext = $this->analyzePageContext($formContent);
            
            // AI Feature'ı bul
            $feature = AIFeature::where('slug', $featureSlug)->first();
            if (!$feature) {
                return [
                    'success' => false,
                    'error' => 'SEO önerileri özelliği bulunamadı'
                ];
            }

            // AI prompt hazırla
            $aiPrompt = $this->buildRecommendationsPrompt($formContent, $language, $pageContext);
            
            // GERÇEK AI İLE ÇALIŞ - Premium özellik
            try {
                Log::info('Calling Real AI for SEO Recommendations', ['feature_slug' => $featureSlug]);
                
                $aiResponse = $this->aiService->askFeature($featureSlug, $aiPrompt, [
                    'language' => $language,
                    'user_id' => $options['user_id'] ?? null,
                    'stream' => false,
                    'temperature' => 0.7,
                    'max_tokens' => 2000
                ]);
                
                if ($aiResponse && isset($aiResponse['response'])) {
                    // AI yanıtını parse et ve alternatifleri üret
                    $aiRecommendations = $this->parseAiRecommendationsWithAlternatives($aiResponse['response'], $language);
                    if (!empty($aiRecommendations)) {
                        $recommendations = $aiRecommendations;
                        Log::info('AI SEO Recommendations Generated Successfully', [
                            'count' => count($recommendations),
                            'language' => $language
                        ]);
                    } else {
                        throw new \Exception('AI response parsing failed, using fallback');
                    }
                } else {
                    throw new \Exception('AI service returned empty response');
                }
                
            } catch (\Exception $aiError) {
                Log::warning('AI SEO Recommendations failed, using intelligent fallback', [
                    'error' => $aiError->getMessage(),
                    'feature_slug' => $featureSlug
                ]);
                
                // AI başarısız olursa akıllı fallback'e düş
                $recommendations = $this->generateIntelligentRecommendations($formContent, $language, $pageContext);
            }
            
            Log::info('SEO Recommendations Generated Successfully', [
                'total_recommendations' => count($recommendations),
                'language' => $language
            ]);

            return [
                'success' => true,
                'recommendations' => $recommendations,
                'language' => $language,
                'generated_at' => now()->toISOString()
            ];

        } catch (\Exception $e) {
            Log::error('SEO Recommendations Generation Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'feature_slug' => $featureSlug,
                'language' => $language
            ]);

            return [
                'success' => false,
                'error' => 'Öneri üretimi hatası: ' . $e->getMessage()
            ];
        }
    }

    /**
     * SAYFA CONTEXT ANALİZİ
     */
    private function analyzePageContext(array $formContent): array
    {
        $title = $formContent['title'] ?? '';
        $content = $formContent['content'] ?? '';
        
        // Basit sayfa türü tespiti
        $pageType = 'Genel Sayfa';
        
        if (stripos($title, 'blog') !== false || stripos($content, 'blog') !== false) {
            $pageType = 'Blog Sayfası';
        } elseif (stripos($title, 'ürün') !== false || stripos($title, 'product') !== false) {
            $pageType = 'Ürün Sayfası';
        } elseif (stripos($title, 'hakkımızda') !== false || stripos($title, 'about') !== false) {
            $pageType = 'Kurumsal Sayfa';
        } elseif (stripos($title, 'iletişim') !== false || stripos($title, 'contact') !== false) {
            $pageType = 'İletişim Sayfası';
        } elseif (stripos($title, 'hizmet') !== false || stripos($title, 'service') !== false) {
            $pageType = 'Hizmet Sayfası';
        }
        
        return [
            'type' => $pageType,
            'content_length' => strlen($content),
            'title_length' => strlen($title)
        ];
    }

    /**
     * SEO ÖNERİLERİ İÇİN AI PROMPT HAZIRLA
     */
    private function buildRecommendationsPrompt(array $formContent, string $language, array $pageContext): string
    {
        $currentTitle = $formContent['title'] ?? 'Başlık yok';
        $currentDescription = $formContent['description'] ?? 'Açıklama yok';
        $currentContent = $formContent['content'] ?? 'İçerik yok';
        
        $pageType = $pageContext['type'] ?? 'Genel Sayfa';
        
        $prompt = "SEN BİR SEO UZMANISIN! Aşağıdaki sayfa için akıllı SEO önerilerini üret.\n\n";
        
        $prompt .= "SAYFA BİLGİLERİ:\n";
        $prompt .= "- Sayfa Türü: {$pageType}\n";
        $prompt .= "- Mevcut Başlık: {$currentTitle}\n";
        $prompt .= "- Mevcut Açıklama: {$currentDescription}\n";
        $prompt .= "- İçerik Uzunluğu: " . strlen($currentContent) . " karakter\n\n";
        
        $prompt .= "İSTENEN ÖNERİ FORMATI (JSON):\n";
        $prompt .= "{\n";
        $prompt .= '  "recommendations": [' . "\n";
        $prompt .= '    {' . "\n";
        $prompt .= '      "id": 1,' . "\n";
        $prompt .= '      "type": "title",' . "\n";
        $prompt .= '      "priority": "high",' . "\n";
        $prompt .= '      "title": "Başlık optimizasyonu",' . "\n";
        $prompt .= '      "description": "Detaylı açıklama",' . "\n";
        $prompt .= '      "suggested_value": "Önerilen başlık metni",' . "\n";
        $prompt .= '      "field_target": "title",' . "\n";
        $prompt .= '      "impact_score": 85' . "\n";
        $prompt .= '    },' . "\n";
        $prompt .= '    {' . "\n";
        $prompt .= '      "id": 2,' . "\n";
        $prompt .= '      "type": "description",' . "\n";
        $prompt .= '      "priority": "medium",' . "\n";
        $prompt .= '      "title": "Meta açıklama geliştirmesi",' . "\n";
        $prompt .= '      "description": "Detaylı açıklama",' . "\n";
        $prompt .= '      "suggested_value": "Önerilen açıklama metni",' . "\n";
        $prompt .= '      "field_target": "description",' . "\n";
        $prompt .= '      "impact_score": 75' . "\n";
        $prompt .= '    }' . "\n";
        $prompt .= '  ]' . "\n";
        $prompt .= "}\n\n";
        
        $prompt .= "KURALLAR:\n";
        $prompt .= "- Türkçe dil kurallarına uygun öneriler ver\n";
        $prompt .= "- priority: high, medium, low\n";
        $prompt .= "- type: title, description, content\n";
        $prompt .= "- field_target: hangi form alanına uygulanacak\n";
        $prompt .= "- impact_score: 1-100 arası etki puanı\n";
        $prompt .= "- En az 3, en fazla 8 öneri ver\n";
        $prompt .= "- Her öneri uygulanabilir ve spesifik olmalı\n\n";
        
        $prompt .= "SADECE JSON FORMATINDA YANIT VER, BAŞKA HİÇBİR METIN EKLEME!";
        
        return $prompt;
    }

    /**
     * AI YANITINI PARSE ET VE ALTERNATİFLERLE ÖNERİLERE DÖNÜŞTÜR
     * Gerçek AI yanıtından her öneri için çoklu alternatifler üretir
     */
    private function parseAiRecommendationsWithAlternatives(string $aiResponse, string $language): array
    {
        try {
            Log::info('Parsing AI Response for SEO Recommendations', [
                'response_length' => strlen($aiResponse),
                'language' => $language
            ]);
            
            // JSON ayıkla
            $cleanResponse = $this->extractJsonFromResponse($aiResponse);
            $data = json_decode($cleanResponse, true);
            
            if (!$data || !isset($data['recommendations'])) {
                throw new \Exception('Invalid AI response format - no recommendations found');
            }
            
            $recommendations = [];
            $id = 1;
            
            foreach ($data['recommendations'] as $aiRec) {
                $recType = $aiRec['type'] ?? 'general';
                $suggestedValue = $aiRec['suggested_value'] ?? '';
                $priority = $aiRec['priority'] ?? 'medium';
                $fieldTarget = $aiRec['field_target'] ?? null;
                
                // AI önerisine dayalı alternatifleri üret
                $alternatives = $this->generateAlternativesFromAiRecommendation($aiRec, $recType, $language);
                
                $recommendation = [
                    'id' => $id++,
                    'type' => $recType,
                    'priority' => $priority,
                    'title' => $aiRec['title'] ?? 'SEO Önerisi',
                    'description' => $aiRec['description'] ?? 'AI tarafından önerilen optimizasyon',
                    'field_target' => $fieldTarget,
                    'impact_score' => intval($aiRec['impact_score'] ?? 75),
                    'language' => $language
                ];
                
                // Eğer alternatifleri ürettiyse ekle
                if (!empty($alternatives)) {
                    $recommendation['alternatives'] = $alternatives;
                } else {
                    // Alternatif üretilemezse direkt önerilen değeri kullan
                    $recommendation['suggested_value'] = $suggestedValue;
                }
                
                $recommendations[] = $recommendation;
            }
            
            Log::info('AI Recommendations Parsed Successfully', [
                'total_recommendations' => count($recommendations),
                'with_alternatives' => count(array_filter($recommendations, function($r) {
                    return isset($r['alternatives']);
                }))
            ]);
            
            return $recommendations;
            
        } catch (\Exception $e) {
            Log::warning('Failed to parse AI recommendations with alternatives', [
                'error' => $e->getMessage(),
                'response' => substr($aiResponse, 0, 500) . '...'
            ]);
            
            // Fallback: akıllı önerileri döndür
            return $this->generateIntelligentRecommendations([], $language, ['type' => 'Genel Sayfa']);
        }
    }

    /**
     * AI ÖNERİSİNDEN ALTERNATİFLER ÜRET
     * AI'ın tek önerisini alıp çoklu alternatiflere çevirir
     */
    private function generateAlternativesFromAiRecommendation(array $aiRec, string $type, string $language): array
    {
        $suggestedValue = $aiRec['suggested_value'] ?? '';
        if (empty($suggestedValue)) {
            return [];
        }
        
        $alternatives = [];
        
        switch ($type) {
            case 'title':
                $alternatives = $this->generateTitleAlternativesFromAi($suggestedValue);
                break;
                
            case 'description':
                $alternatives = $this->generateDescriptionAlternativesFromAi($suggestedValue);
                break;
                
                
            case 'og_title':
                $alternatives = $this->generateOgTitleAlternativesFromAi($suggestedValue);
                break;
                
            case 'og_description':
                $alternatives = $this->generateOgDescriptionAlternativesFromAi($suggestedValue);
                break;
                
            default:
                // Genel alternatifler
                $alternatives = [
                    [
                        'id' => 'ai_1',
                        'label' => 'AI Önerisi',
                        'value' => $suggestedValue,
                        'description' => 'Yapay zeka tarafından önerilen optimizasyon',
                        'score' => 85
                    ]
                ];
        }
        
        return $alternatives;
    }

    /**
     * AI BAŞLIK ÖNERİSİNDEN ALTERNATİFLER
     */
    private function generateTitleAlternativesFromAi(string $aiTitle): array
    {
        $alternatives = [];
        
        // ALTERNATİF 1: AI önerisi olduğu gibi
        $alternatives[] = [
            'id' => 'ai_title_1',
            'label' => 'AI Önerisi',
            'value' => $aiTitle,
            'description' => 'Yapay zeka tarafından optimize edilmiş başlık',
            'score' => 95
        ];
        
        // ALTERNATİF 2: Kısaltılmış versiyon
        if (strlen($aiTitle) > 50) {
            $alternatives[] = [
                'id' => 'ai_title_2',
                'label' => 'Kısa Format',
                'value' => $this->shortenTitle($aiTitle),
                'description' => 'Daha kısa ve öz versiyon',
                'score' => 80
            ];
        }
        
        // ALTERNATİF 3: Emoji eklenmiş versiyon
        $alternatives[] = [
            'id' => 'ai_title_3',
            'label' => 'Çekici Format',
            'value' => $this->addEmojisToTitle($aiTitle),
            'description' => 'Görsel çekicilik eklenmiş',
            'score' => 85
        ];
        
        // ALTERNATİF 4: Soru formatı
        $alternatives[] = [
            'id' => 'ai_title_4',
            'label' => 'Soru Formatı',
            'value' => $this->convertToQuestion($aiTitle),
            'description' => 'Merak uyandıran soru şeklinde',
            'score' => 75
        ];
        
        return $alternatives;
    }

    /**
     * AI AÇIKLAMA ÖNERİSİNDEN ALTERNATİFLER
     */
    private function generateDescriptionAlternativesFromAi(string $aiDescription): array
    {
        $alternatives = [];
        
        // ALTERNATİF 1: AI önerisi olduğu gibi
        $alternatives[] = [
            'id' => 'ai_desc_1',
            'label' => 'AI Önerisi',
            'value' => $aiDescription,
            'description' => 'Yapay zeka tarafından optimize edilmiş açıklama',
            'score' => 95
        ];
        
        // ALTERNATİF 2: Aksiyon odaklı
        $alternatives[] = [
            'id' => 'ai_desc_2',
            'label' => 'Aksiyon Odaklı',
            'value' => $this->makeActionOriented($aiDescription),
            'description' => 'Harekete geçme vurgusu eklendi',
            'score' => 88
        ];
        
        // ALTERNATİF 3: Sosyal kanıt eklenmiş
        $alternatives[] = [
            'id' => 'ai_desc_3',
            'label' => 'Sosyal Kanıt',
            'value' => $this->addSocialProof($aiDescription),
            'description' => 'Güvenilirlik vurgusu eklendi',
            'score' => 90
        ];
        
        // ALTERNATİF 4: Kısaltılmış versiyon (160 karakter limiti için)
        if (strlen($aiDescription) > 160) {
            $alternatives[] = [
                'id' => 'ai_desc_4',
                'label' => 'Kısa Format',
                'value' => $this->shortenDescription($aiDescription),
                'description' => 'SEO limitleri için kısaltılmış',
                'score' => 85
            ];
        }
        
        return $alternatives;
    }

    /**
     * AI ANAHTAR KELİME ÖNERİSİNDEN ALTERNATİFLER
     */
    private function generateKeywordAlternativesFromAi(string $aiKeywords): array
    {
        $keywords = array_map('trim', explode(',', $aiKeywords));
        $alternatives = [];
        
        // ALTERNATİF 1: AI önerisi olduğu gibi
        $alternatives[] = [
            'id' => 'ai_keywords_1',
            'label' => 'AI Stratejisi',
            'value' => $aiKeywords,
            'description' => 'Yapay zeka tarafından önerilen anahtar kelimeler',
            'score' => 95
        ];
        
        // ALTERNATİF 2: Sadece kısa kuyruk kelimeler
        $shortTail = array_filter($keywords, function($k) { return str_word_count($k) <= 2; });
        if (!empty($shortTail)) {
            $alternatives[] = [
                'id' => 'ai_keywords_2',
                'label' => 'Kısa Kuyruk',
                'value' => implode(', ', $shortTail),
                'description' => 'Yüksek hacimli kısa kelimeler',
                'score' => 80
            ];
        }
        
        // ALTERNATİF 3: Sadece uzun kuyruk kelimeler  
        $longTail = array_filter($keywords, function($k) { return str_word_count($k) >= 3; });
        if (!empty($longTail)) {
            $alternatives[] = [
                'id' => 'ai_keywords_3',
                'label' => 'Uzun Kuyruk',
                'value' => implode(', ', $longTail),
                'description' => 'Hedeflenmiş spesifik ifadeler',
                'score' => 90
            ];
        }
        
        // ALTERNATİF 4: Yerel SEO eklenmiş
        $alternatives[] = [
            'id' => 'ai_keywords_4',
            'label' => 'Yerel SEO',
            'value' => $aiKeywords . ', İstanbul, yakınımda, bölgemde',
            'description' => 'Coğrafi hedefleme eklendi',
            'score' => 85
        ];
        
        return $alternatives;
    }

    /**
     * AI OG BAŞLIK ÖNERİSİNDEN ALTERNATİFLER
     */
    private function generateOgTitleAlternativesFromAi(string $aiOgTitle): array
    {
        return [
            [
                'id' => 'ai_og_title_1',
                'label' => 'AI Önerisi',
                'value' => $aiOgTitle,
                'description' => 'Yapay zeka sosyal medya optimizasyonu',
                'score' => 95
            ],
            [
                'id' => 'ai_og_title_2',
                'label' => 'Emoji Destekli',
                'value' => '✨ ' . $aiOgTitle . ' 🚀',
                'description' => 'Sosyal medya için emoji eklendi',
                'score' => 85
            ],
            [
                'id' => 'ai_og_title_3',
                'label' => 'Hashtag Entegreli',
                'value' => $aiOgTitle . ' #keşfet',
                'description' => 'Sosyal medya etiketleri eklendi',
                'score' => 80
            ]
        ];
    }

    /**
     * AI OG AÇIKLAMA ÖNERİSİNDEN ALTERNATİFLER
     */
    private function generateOgDescriptionAlternativesFromAi(string $aiOgDesc): array
    {
        return [
            [
                'id' => 'ai_og_desc_1',
                'label' => 'AI Önerisi',
                'value' => $aiOgDesc,
                'description' => 'Yapay zeka sosyal medya açıklaması',
                'score' => 95
            ],
            [
                'id' => 'ai_og_desc_2',
                'label' => 'Kişisel Ton',
                'value' => 'Sen de ' . strtolower($aiOgDesc) . ' Hemen bak! 👀',
                'description' => 'Daha samimi dil kullanımı',
                'score' => 85
            ],
            [
                'id' => 'ai_og_desc_3',
                'label' => 'Aciliyet Vurgusu',
                'value' => 'Kaçırma! ' . $aiOgDesc,
                'description' => 'Hemen harekete geçme teşviki',
                'score' => 88
            ]
        ];
    }

    // ========== YARDIMCI AI DÖNÜŞÜM METODLARı ==========

    private function shortenTitle(string $title): string
    {
        if (strlen($title) <= 50) return $title;
        
        $words = explode(' ', $title);
        $shortened = '';
        foreach ($words as $word) {
            if (strlen($shortened . $word) > 47) break;
            $shortened .= $word . ' ';
        }
        return trim($shortened) . '...';
    }

    private function addEmojisToTitle(string $title): string
    {
        $emojis = ['🚀', '✨', '💡', '🎯', '⚡', '🔥'];
        $emoji = $emojis[array_rand($emojis)];
        return $emoji . ' ' . $title;
    }

    private function convertToQuestion(string $title): string
    {
        if (str_ends_with($title, '?')) return $title;
        
        // Soru eklemek için basit kurallar
        if (stripos($title, 'nasıl') !== false) return $title . '?';
        if (stripos($title, 'neden') !== false) return $title . '?';
        if (stripos($title, 'nedir') !== false) return $title . '?';
        
        return $title . ' Nedir?';
    }

    private function makeActionOriented(string $description): string
    {
        $actionWords = ['Keşfedin', 'Öğrenin', 'Başlayın', 'Deneyin', 'Katılın'];
        $action = $actionWords[array_rand($actionWords)];
        
        if (!str_ends_with($description, '.')) {
            $description .= '.';
        }
        
        return $description . ' ' . $action . ' şimdi!';
    }

    private function addSocialProof(string $description): string
    {
        $proofs = [
            'Binlerce kullanıcı memnun.',
            'Uzmanlar tarafından önerilen.',
            'En çok tercih edilen.',
            'Güvenilir kaynak.'
        ];
        $proof = $proofs[array_rand($proofs)];
        
        return $proof . ' ' . $description;
    }

    private function shortenDescription(string $description): string
    {
        if (strlen($description) <= 160) return $description;
        return substr($description, 0, 157) . '...';
    }

    /**
     * AI YANITINI PARSE ET VE ÖNERİLERE DÖNÜŞTÜR
     * @deprecated - parseAiRecommendationsWithAlternatives kullanın
     */
    private function parseRecommendationsResponse(string $aiResponse, string $language): array
    {
        try {
            // JSON ayıkla
            $cleanResponse = $this->extractJsonFromResponse($aiResponse);
            $data = json_decode($cleanResponse, true);
            
            if (!$data || !isset($data['recommendations'])) {
                throw new \Exception('Invalid AI response format');
            }
            
            $recommendations = [];
            $id = 1;
            
            foreach ($data['recommendations'] as $rec) {
                $recommendations[] = [
                    'id' => $id++,
                    'type' => $rec['type'] ?? 'general',
                    'priority' => $rec['priority'] ?? 'medium',
                    'title' => $rec['title'] ?? 'SEO Önerisi',
                    'description' => $rec['description'] ?? '',
                    'suggested_value' => $rec['suggested_value'] ?? '',
                    'field_target' => $rec['field_target'] ?? null,
                    'impact_score' => intval($rec['impact_score'] ?? 50),
                    'language' => $language
                ];
            }
            
            return $recommendations;
            
        } catch (\Exception $e) {
            Log::warning('Failed to parse AI recommendations response', [
                'error' => $e->getMessage(),
                'response' => $aiResponse
            ]);
            
            // Fallback önerileri
            return $this->getFallbackRecommendations($language);
        }
    }

    /**
     * AKILLI ÖNERİ ÜRETİCİSİ - TAM 4 KATEGORİ: SEO Title, SEO Description, OG Title, OG Description
     * HER KATEGORİ İÇİN 4 ALTERNATİF SUNAR
     */
    private function generateIntelligentRecommendations(array $formContent, string $language, array $pageContext): array
    {
        $recommendations = [];
        
        // Mevcut değerleri al
        $currentTitle = $formContent['seoDataCache.tr.seo_title'] ?? $formContent['title'] ?? '';
        $currentDescription = $formContent['seoDataCache.tr.seo_description'] ?? $formContent['meta_description'] ?? '';
        $currentOgTitle = $formContent['seoDataCache.tr.og_title'] ?? '';
        $currentOgDescription = $formContent['seoDataCache.tr.og_description'] ?? '';
        $pageType = $pageContext['type'] ?? 'Genel Sayfa';
        
        // 1. SEO TITLE ÖNERİLERİ - 4 alternatif
        $titleAlternatives = $this->generateMetaTitleAlternatives($currentTitle, $pageType);
        $recommendations[] = [
            'id' => 1,
            'type' => 'seo_title',
            'priority' => 'high',
            'title' => 'SEO Başlık Önerileri',
            'description' => 'Arama motorlarında görünecek başlığınız için optimum seçenekler:',
            'alternatives' => $titleAlternatives,
            'field_target' => 'seoDataCache.tr.seo_title',
            'impact_score' => 95,
            'language' => $language
        ];
        
        // 2. SEO DESCRIPTION ÖNERİLERİ - 4 alternatif
        $descriptionAlternatives = $this->generateMetaDescriptionAlternatives($currentDescription, $pageType);
        $recommendations[] = [
            'id' => 2,
            'type' => 'seo_description',
            'priority' => 'high',
            'title' => 'SEO Açıklama Önerileri',
            'description' => 'Arama sonuçlarında görünecek açıklamanız için en etkili seçenekler:',
            'alternatives' => $descriptionAlternatives,
            'field_target' => 'seoDataCache.tr.seo_description',
            'impact_score' => 90,
            'language' => $language
        ];
        
        // 3. OG TITLE ÖNERİLERİ - 4 alternatif
        $ogTitleAlternatives = $this->generateSocialMediaTitleAlternatives($currentTitle, $pageType);
        $recommendations[] = [
            'id' => 3,
            'type' => 'og_title',
            'priority' => 'medium',
            'title' => 'Sosyal Medya Başlık Önerileri',
            'description' => 'Facebook, Twitter ve LinkedIn paylaşımları için tıklanma artıran başlıklar:',
            'alternatives' => $ogTitleAlternatives,
            'field_target' => 'seoDataCache.tr.og_title',
            'impact_score' => 85,
            'language' => $language
        ];

        // 4. OG DESCRIPTION ÖNERİLERİ - 4 alternatif
        $ogDescAlternatives = $this->generateSocialMediaDescriptionAlternatives($currentDescription, $pageType);
        $recommendations[] = [
            'id' => 4,
            'type' => 'og_description',
            'priority' => 'medium',
            'title' => 'Sosyal Medya Açıklama Önerileri',
            'description' => 'Sosyal medya paylaşımlarında etkileşimi artıran açıklamalar:',
            'alternatives' => $ogDescAlternatives,
            'field_target' => 'seoDataCache.tr.og_description',
            'impact_score' => 80,
            'language' => $language
        ];
        
        return $recommendations;
    }

    /**
     * META TITLE ALTERNATİFLERİ - Arama sonuçları için optimize
     */
    private function generateMetaTitleAlternatives(string $currentTitle, string $pageType): array
    {
        $baseTitle = trim($currentTitle) ?: 'Sayfa Başlığı';
        $alternatives = [];

        // ALTERNATİF 1: SEO Optimize Format (50-60 karakter)
        $alternatives[] = [
            'id' => 'meta_title_1',
            'label' => 'SEO Optimized',
            'value' => $this->createSeoOptimizedTitle($baseTitle, $pageType),
            'description' => 'Google arama sonuçları için 50-60 karakter arası optimum başlık',
            'score' => 95
        ];

        // ALTERNATİF 2: Tıklanma Odaklı
        $alternatives[] = [
            'id' => 'meta_title_2',
            'label' => 'Click-Through Optimized',
            'value' => $this->createClickThroughTitle($baseTitle, $pageType),
            'description' => 'Arama sonuçlarında tıklanma oranını artıracak format',
            'score' => 88
        ];

        // ALTERNATİF 3: Marka Vurgulu
        $alternatives[] = [
            'id' => 'meta_title_3',
            'label' => 'Brand Focused',
            'value' => $this->createBrandFocusedTitle($baseTitle, $pageType),
            'description' => 'Marka bilinirliği ve güvenilirlik vurgusu',
            'score' => 85
        ];

        // ALTERNATİF 4: Yıl/Güncellik Vurgulu
        $alternatives[] = [
            'id' => 'meta_title_4',
            'label' => 'Current Year Focused',
            'value' => $this->createCurrentYearTitle($baseTitle, $pageType),
            'description' => '2025 güncelliği vurgulanarak tazelik algısı',
            'score' => 82
        ];

        return $alternatives;
    }

    /**
     * META DESCRIPTION ALTERNATİFLERİ - Arama sonuçları için optimize
     */
    private function generateMetaDescriptionAlternatives(string $currentDescription, string $pageType): array
    {
        $baseDescription = trim($currentDescription) ?: 'Bu sayfada değerli bilgiler bulunmaktadır';
        $alternatives = [];

        // ALTERNATİF 1: CTA Odaklı (Call-to-Action)
        $alternatives[] = [
            'id' => 'meta_desc_1',
            'label' => 'Action-Oriented',
            'value' => $this->createActionOrientedDescription($baseDescription, $pageType),
            'description' => 'Harekete geçmeyi teşvik eden güçlü CTA ile 150-160 karakter',
            'score' => 92
        ];

        // ALTERNATİF 2: Fayda Listeli
        $alternatives[] = [
            'id' => 'meta_desc_2',
            'label' => 'Benefits Listed',
            'value' => $this->createBenefitsListedDescription($baseDescription, $pageType),
            'description' => 'Kullanıcının elde edeceği faydalar net bir şekilde listelenir',
            'score' => 90
        ];

        // ALTERNATİF 3: Problem-Çözüm Odaklı
        $alternatives[] = [
            'id' => 'meta_desc_3',
            'label' => 'Problem-Solution',
            'value' => $this->createProblemSolutionDescription($baseDescription, $pageType),
            'description' => 'Kullanıcı problemini tanımlayıp çözüm sunan yaklaşım',
            'score' => 88
        ];

        // ALTERNATİF 4: Sosyal Kanıt Vurgulu
        $alternatives[] = [
            'id' => 'meta_desc_4',
            'label' => 'Social Proof Enhanced',
            'value' => $this->createSocialProofMetaDescription($baseDescription, $pageType),
            'description' => 'İstatistik, başarı hikayesi ve güvenilirlik göstergeleri',
            'score' => 87
        ];

        return $alternatives;
    }

    /**
     * İÇERİK TÜRÜ ALTERNATİFLERİ - Yapılandırılmış veri için optimize
     */
    private function generateContentTypeAlternatives(string $currentContentType, string $pageType): array
    {
        $alternatives = [];

        // Sayfa türüne göre önerilen content type'ları
        switch ($pageType) {
            case 'Blog Sayfası':
                $alternatives = [
                    [
                        'id' => 'content_type_1',
                        'label' => 'Article',
                        'value' => 'article',
                        'description' => 'Blog yazıları ve makale içerikleri için ideal yapılandırılmış veri türü',
                        'score' => 95
                    ],
                    [
                        'id' => 'content_type_2',
                        'label' => 'BlogPosting',
                        'value' => 'blog_posting',
                        'description' => 'Blog gönderileri için özel Schema.org veri türü',
                        'score' => 90
                    ],
                    [
                        'id' => 'content_type_3',
                        'label' => 'NewsArticle',
                        'value' => 'news_article',
                        'description' => 'Haber niteliğindeki blog yazıları için uygun',
                        'score' => 80
                    ]
                ];
                break;
                
            case 'Ürün Sayfası':
                $alternatives = [
                    [
                        'id' => 'content_type_1',
                        'label' => 'Product',
                        'value' => 'product',
                        'description' => 'E-ticaret ürün sayfaları için optimize edilmiş yapılandırılmış veri',
                        'score' => 98
                    ],
                    [
                        'id' => 'content_type_2',
                        'label' => 'Offer',
                        'value' => 'offer',
                        'description' => 'Ürün teklifleri ve fiyat bilgileri için uygun',
                        'score' => 85
                    ],
                    [
                        'id' => 'content_type_3',
                        'label' => 'ItemPage',
                        'value' => 'item_page',
                        'description' => 'Genel ürün detay sayfaları için alternatif',
                        'score' => 75
                    ]
                ];
                break;
                
            case 'Hizmet Sayfası':
                $alternatives = [
                    [
                        'id' => 'content_type_1',
                        'label' => 'Service',
                        'value' => 'service',
                        'description' => 'Profesyonel hizmet sayfaları için en uygun yapılandırılmış veri',
                        'score' => 95
                    ],
                    [
                        'id' => 'content_type_2',
                        'label' => 'LocalBusiness',
                        'value' => 'local_business',
                        'description' => 'Yerel hizmet sağlayıcıları için coğrafi SEO desteği',
                        'score' => 88
                    ],
                    [
                        'id' => 'content_type_3',
                        'label' => 'ProfessionalService',
                        'value' => 'professional_service',
                        'description' => 'Uzman danışmanlık ve profesyonel hizmetler için',
                        'score' => 85
                    ]
                ];
                break;
                
            case 'İletişim Sayfası':
                $alternatives = [
                    [
                        'id' => 'content_type_1',
                        'label' => 'ContactPage',
                        'value' => 'contact_page',
                        'description' => 'İletişim sayfaları için özel yapılandırılmış veri türü',
                        'score' => 95
                    ],
                    [
                        'id' => 'content_type_2',
                        'label' => 'Organization',
                        'value' => 'organization',
                        'description' => 'Kurum bilgileri ve iletişim detayları için uygun',
                        'score' => 85
                    ],
                    [
                        'id' => 'content_type_3',
                        'label' => 'Place',
                        'value' => 'place',
                        'description' => 'Fiziksel konum ve adres bilgileri vurgusu',
                        'score' => 80
                    ]
                ];
                break;
                
            default:
                $alternatives = [
                    [
                        'id' => 'content_type_1',
                        'label' => 'WebPage',
                        'value' => 'webpage',
                        'description' => 'Genel web sayfaları için standart yapılandırılmış veri',
                        'score' => 85
                    ],
                    [
                        'id' => 'content_type_2',
                        'label' => 'WebSite',
                        'value' => 'website',
                        'description' => 'Ana sayfa ve genel site tanımlamaları için',
                        'score' => 80
                    ],
                    [
                        'id' => 'content_type_3',
                        'label' => 'AboutPage',
                        'value' => 'about_page',
                        'description' => 'Hakkımızda ve tanıtım sayfaları için optimize',
                        'score' => 75
                    ]
                ];
        }

        return $alternatives;
    }

    /**
     * BAŞLIK ALTERNATİFLERİ ÜRETİCİSİ - 4 FARKLI SEÇİM
     * @deprecated - generateMetaTitleAlternatives kullanın
     */
    private function generateTitleAlternatives(string $currentTitle, string $pageType): array
    {
        $baseTitle = trim($currentTitle) ?: 'Sayfa Başlığı';
        $alternatives = [];

        // ALTERNATİF 1: Klasik SEO Formatı
        $alternatives[] = [
            'id' => 'title_1',
            'label' => 'Klasik SEO Format',
            'value' => $this->createClassicSeoTitle($baseTitle, $pageType),
            'description' => 'Geleneksel SEO kuralları ile optimize edilmiş',
            'score' => 85
        ];

        // ALTERNATİF 2: Duygusal Çekicilik
        $alternatives[] = [
            'id' => 'title_2',
            'label' => 'Duygusal Çekicilik',
            'value' => $this->createEmotionalTitle($baseTitle, $pageType),
            'description' => 'Kullanıcı ilgisini çeken duygusal öğeler',
            'score' => 80
        ];

        // ALTERNATİF 3: Sayı ve İstatistik
        $alternatives[] = [
            'id' => 'title_3',
            'label' => 'Sayı ve İstatistik',
            'value' => $this->createNumberBasedTitle($baseTitle, $pageType),
            'description' => 'Somut sayılar ve verilerle güçlendirilmiş',
            'score' => 88
        ];

        // ALTERNATİF 4: Soru Formatı
        $alternatives[] = [
            'id' => 'title_4',
            'label' => 'Soru Formatı',
            'value' => $this->createQuestionTitle($baseTitle, $pageType),
            'description' => 'Merak uyandıran soru şeklinde başlık',
            'score' => 75
        ];

        return $alternatives;
    }

    /**
     * AÇIKLAMA ALTERNATİFLERİ ÜRETİCİSİ - 4 FARKLI STİL
     */
    private function generateDescriptionAlternatives(string $currentDescription, string $pageType): array
    {
        $baseDescription = trim($currentDescription) ?: 'Bu sayfada önemli bilgiler bulunmaktadır';
        $alternatives = [];

        // ALTERNATİF 1: Fayda Odaklı
        $alternatives[] = [
            'id' => 'desc_1',
            'label' => 'Fayda Odaklı',
            'value' => $this->createBenefitDescription($baseDescription, $pageType),
            'description' => 'Kullanıcıya sağlayacağı faydalar vurgulanır',
            'score' => 90
        ];

        // ALTERNATİF 2: Problem Çözücü
        $alternatives[] = [
            'id' => 'desc_2',
            'label' => 'Problem Çözücü',
            'value' => $this->createProblemSolverDescription($baseDescription, $pageType),
            'description' => 'Çözdüğü problemler ön plana çıkarılır',
            'score' => 85
        ];

        // ALTERNATİF 3: Sosyal Kanıt
        $alternatives[] = [
            'id' => 'desc_3',
            'label' => 'Sosyal Kanıt',
            'value' => $this->createSocialProofDescription($baseDescription, $pageType),
            'description' => 'Başarı hikayeleri ve güvenilirlik vurgusu',
            'score' => 87
        ];

        // ALTERNATİF 4: Aciliyet Yaratıcı
        $alternatives[] = [
            'id' => 'desc_4',
            'label' => 'Aciliyet Yaratıcı',
            'value' => $this->createUrgencyDescription($baseDescription, $pageType),
            'description' => 'Hemen harekete geçme isteği uyandırır',
            'score' => 82
        ];

        return $alternatives;
    }

    /**
     * ANAHTAR KELİME STRATEJİLERİ - 5 FARKLI YAKLAŞIM
     */
    private function generateKeywordAlternatives(string $title, string $description, string $pageType): array
    {
        $alternatives = [];

        // STRATEJİ 1: Kısa Kuyruk (Short-tail)
        $alternatives[] = [
            'id' => 'keyword_1',
            'label' => 'Kısa Kuyruk Stratejisi',
            'value' => $this->createShortTailKeywords($title, $description, $pageType),
            'description' => '1-2 kelimelik yüksek hacimli anahtar kelimeler',
            'score' => 70
        ];

        // STRATEJİ 2: Uzun Kuyruk (Long-tail)
        $alternatives[] = [
            'id' => 'keyword_2',
            'label' => 'Uzun Kuyruk Stratejisi',
            'value' => $this->createLongTailKeywords($title, $description, $pageType),
            'description' => '3+ kelimelik spesifik ve hedeflenen ifadeler',
            'score' => 88
        ];

        // STRATEJİ 3: Yerel SEO
        $alternatives[] = [
            'id' => 'keyword_3',
            'label' => 'Yerel SEO Odaklı',
            'value' => $this->createLocalSeoKeywords($title, $description, $pageType),
            'description' => 'Coğrafi konumlar ve yerel aramalar',
            'score' => 85
        ];

        // STRATEJİ 4: Intent Tabanlı
        $alternatives[] = [
            'id' => 'keyword_4',
            'label' => 'Arama Amacı Tabanlı',
            'value' => $this->createIntentBasedKeywords($title, $description, $pageType),
            'description' => 'Kullanıcı amacına göre kategorize edilmiş',
            'score' => 92
        ];

        // STRATEJİ 5: Semantik İlişkili
        $alternatives[] = [
            'id' => 'keyword_5',
            'label' => 'Semantik İlişkili',
            'value' => $this->createSemanticKeywords($title, $description, $pageType),
            'description' => 'Ana konuyla anlam olarak bağlantılı kelimeler',
            'score' => 90
        ];

        return $alternatives;
    }

    /**
     * OG BAŞLIK ALTERNATİFLERİ - 3 SOSYAL MEDYA STİLİ
     */
    private function generateOgTitleAlternatives(string $currentTitle): array
    {
        $baseTitle = trim($currentTitle) ?: 'İlginç İçerik';
        $alternatives = [];

        // ALTERNATİF 1: Emoji Destekli
        $alternatives[] = [
            'id' => 'og_title_1',
            'label' => 'Emoji Destekli',
            'value' => '🚀 ' . $baseTitle . ' ✨',
            'description' => 'Görsel çekicilik için emoji kullanımı',
            'score' => 80
        ];

        // ALTERNATİF 2: Hashtag Entegreli
        $alternatives[] = [
            'id' => 'og_title_2',
            'label' => 'Hashtag Entegreli',
            'value' => $baseTitle . ' #keşfet #paylaş',
            'description' => 'Sosyal medya etiketleri ile desteklenmiş',
            'score' => 75
        ];

        // ALTERNATİF 3: Kısa ve Çarpıcı
        $alternatives[] = [
            'id' => 'og_title_3',
            'label' => 'Kısa ve Çarpıcı',
            'value' => $this->shortenForSocial($baseTitle),
            'description' => 'Sosyal medya için optimize edilmiş kısa format',
            'score' => 85
        ];

        return $alternatives;
    }

    /**
     * OG AÇIKLAMA ALTERNATİFLERİ - 3 SOSYAL MEDYA YAKLAŞIMI
     */
    private function generateOgDescriptionAlternatives(string $currentDescription): array
    {
        $baseDesc = trim($currentDescription) ?: 'Paylaşmaya değer içerik';
        $alternatives = [];

        // ALTERNATİF 1: Kişisel Ton
        $alternatives[] = [
            'id' => 'og_desc_1',
            'label' => 'Kişisel Ton',
            'value' => 'Sen de ' . strtolower($baseDesc) . ' Hemen göz at! 👀',
            'description' => 'Samimi ve kişisel dil kullanımı',
            'score' => 85
        ];

        // ALTERNATİF 2: Sosyal Kanıt
        $alternatives[] = [
            'id' => 'og_desc_2',
            'label' => 'Sosyal Kanıt',
            'value' => 'Binlerce kişi beğendi: ' . $baseDesc . ' Sen de katıl!',
            'description' => 'Toplumsal onay vurgusu',
            'score' => 88
        ];

        // ALTERNATİF 3: Merak Uyandırıcı
        $alternatives[] = [
            'id' => 'og_desc_3',
            'label' => 'Merak Uyandırıcı',
            'value' => 'Bu sırrı öğrenmek istiyorsan: ' . $baseDesc,
            'description' => 'Merak duygusu yaratarak tıklama oranını artırır',
            'score' => 90
        ];

        return $alternatives;
    }

    // ========== ALTERNATİF ÜRETİCİ YARDIMCI METODLARI ==========

    private function createClassicSeoTitle(string $title, string $pageType): string
    {
        return match($pageType) {
            'Blog Sayfası' => $title . ' | Blog - Güncel Bilgiler',
            'Ürün Sayfası' => $title . ' - En İyi Fiyat ve Kalite',
            'Hizmet Sayfası' => $title . ' - Profesyonel Hizmetler',
            'İletişim Sayfası' => $title . ' | İletişim Bilgileri',
            default => $title . ' - Kapsamlı Rehber'
        };
    }

    private function createEmotionalTitle(string $title, string $pageType): string
    {
        return match($pageType) {
            'Blog Sayfası' => 'Hayatınızı Değiştirecek: ' . $title,
            'Ürün Sayfası' => 'Hayal Ettiğiniz ' . $title . ' Burada',
            'Hizmet Sayfası' => 'Güvenilir ' . $title . ' Deneyimi',
            'İletişim Sayfası' => 'Size Yakın ' . $title . ' İmkanı',
            default => 'Şaşırtıcı ' . $title . ' Keşfi'
        };
    }

    private function createNumberBasedTitle(string $title, string $pageType): string
    {
        return match($pageType) {
            'Blog Sayfası' => '10 Adımda ' . $title . ' Rehberi',
            'Ürün Sayfası' => $title . ' - 5 Yıl Garanti',
            'Hizmet Sayfası' => '24/7 ' . $title . ' Desteği',
            'İletişim Sayfası' => '3 Dakikada ' . $title,
            default => '2024\'ün En İyi ' . $title
        };
    }

    private function createQuestionTitle(string $title, string $pageType): string
    {
        return match($pageType) {
            'Blog Sayfası' => $title . ' Hakkında Bilmeniz Gerekenler?',
            'Ürün Sayfası' => 'En İyi ' . $title . ' Hangisi?',
            'Hizmet Sayfası' => $title . ' İhtiyacınız Var Mı?',
            'İletişim Sayfası' => $title . ' Nasıl Yapılır?',
            default => $title . ' Nedir ve Neden Önemli?'
        };
    }

    private function createBenefitDescription(string $description, string $pageType): string
    {
        $benefits = match($pageType) {
            'Blog Sayfası' => 'yeni bilgiler öğrenerek uzmanlaşın',
            'Ürün Sayfası' => 'hayat kalitenizi artıracak çözümler edinin',
            'Hizmet Sayfası' => 'profesyonel destek alarak hedeflerinize ulaşın',
            'İletişim Sayfası' => 'hızlı ve güvenilir iletişim kurun',
            default => 'değerli içeriklerle kendinizi geliştirin'
        };
        
        return $description . ' Bu sayfa ile ' . $benefits . '. Hemen keşfedin!';
    }

    private function createProblemSolverDescription(string $description, string $pageType): string
    {
        $problems = match($pageType) {
            'Blog Sayfası' => 'bilgi eksikliği sorununuzu çözüyoruz',
            'Ürün Sayfası' => 'kalite arayışınıza son veriyoruz',
            'Hizmet Sayfası' => 'ihtiyaçlarınıza mükemmel çözüm sunuyoruz',
            'İletişim Sayfası' => 'ulaşım zorluklarınızı ortadan kaldırıyoruz',
            default => 'yaşadığınız zorluklara pratik çözümler getiriyoruz'
        };
        
        return $description . ' ' . ucfirst($problems) . '. Artık endişelenmeyin!';
    }

    private function createSocialProofDescription(string $description, string $pageType): string
    {
        $proof = match($pageType) {
            'Blog Sayfası' => 'Binlerce okuyucunun tercihi olan',
            'Ürün Sayfası' => 'Müşterilerimizin %98\'i memnun kaldığı',
            'Hizmet Sayfası' => 'Sektör lideri olarak tanınan',
            'İletişim Sayfası' => '7/24 destek sağlayan güvenilir',
            default => 'Uzmanlar tarafından önerilen'
        };
        
        return $proof . ' platform. ' . $description . ' Siz de aramıza katılın!';
    }

    private function createUrgencyDescription(string $description, string $pageType): string
    {
        $urgency = match($pageType) {
            'Blog Sayfası' => 'Bu bilgileri kaçırmayın',
            'Ürün Sayfası' => 'Stoklar tükeneden önce',
            'Hizmet Sayfası' => 'Şimdi harekete geçin',
            'İletişim Sayfası' => 'Hemen iletişime geçin',
            default => 'Bugün başlayın'
        };
        
        return $urgency . '! ' . $description . ' Fırsatı kaçırmayın!';
    }

    // Anahtar kelime üreticileri...
    private function createShortTailKeywords(string $title, string $description, string $pageType): string
    {
        return match($pageType) {
            'Blog Sayfası' => 'blog, makale, bilgi, rehber',
            'Ürün Sayfası' => 'ürün, satış, fiyat, kalite',
            'Hizmet Sayfası' => 'hizmet, destek, çözüm, uzman',
            'İletişim Sayfası' => 'iletişim, adres, telefon, mail',
            default => 'bilgi, hizmet, kalite, güven'
        };
    }

    private function createLongTailKeywords(string $title, string $description, string $pageType): string
    {
        return match($pageType) {
            'Blog Sayfası' => 'en iyi blog yazıları, güncel bilgiler burada, uzman rehberleri',
            'Ürün Sayfası' => 'kaliteli ürün satışı, en uygun fiyat garantisi, güvenli alışveriş',
            'Hizmet Sayfası' => 'profesyonel hizmet desteği, uzman ekip hizmetleri, güvenilir çözümler',
            'İletişim Sayfası' => 'hızlı iletişim kanalları, güvenilir iletişim bilgileri, kolay ulaşım',
            default => 'kapsamlı bilgi kaynağı, güvenilir hizmet sağlayıcısı'
        };
    }

    private function createLocalSeoKeywords(string $title, string $description, string $pageType): string
    {
        return 'İstanbul, Ankara, İzmir, Bursa, yakınımda, bölgemde, şehrimde, mahallede';
    }

    private function createIntentBasedKeywords(string $title, string $description, string $pageType): string
    {
        return match($pageType) {
            'Blog Sayfası' => 'nasıl yapılır, öğrenmek istiyorum, rehber arıyorum, bilgi almak',
            'Ürün Sayfası' => 'satın almak istiyorum, fiyat karşılaştırması, ürün incelemeleri',
            'Hizmet Sayfası' => 'hizmet almak, uzman aramak, profesyonel destek',
            'İletişim Sayfası' => 'iletişim kurmak, ulaşmak istiyorum, randevu almak',
            default => 'aramak, bulmak, öğrenmek, çözmek'
        };
    }

    private function createSemanticKeywords(string $title, string $description, string $pageType): string
    {
        return match($pageType) {
            'Blog Sayfası' => 'makale, yazı, içerik, bilgi paylaşımı, eğitici',
            'Ürün Sayfası' => 'mal, eşya, alışveriş, pazarlama, e-ticaret',
            'Hizmet Sayfası' => 'yardım, destek, danışmanlık, konsültasyon',
            'İletişim Sayfası' => 'haberleşme, bağlantı, koordinasyon, network',
            default => 'platform, kaynak, sistem, araç, çözüm'
        };
    }

    private function shortenForSocial(string $title): string
    {
        if (strlen($title) <= 40) return $title;
        return substr($title, 0, 37) . '...';
    }

    // ========== META TITLE YARDIMCI METODLARI ==========
    
    private function createSeoOptimizedTitle(string $title, string $pageType): string
    {
        // 50-60 karakter arası optimal uzunluk
        $optimized = match($pageType) {
            'Blog Sayfası' => $this->truncateTitle($title . ' - 2025 Güncel Rehber', 60),
            'Ürün Sayfası' => $this->truncateTitle($title . ' | En İyi Fiyat Garantisi', 60),
            'Hizmet Sayfası' => $this->truncateTitle($title . ' - Profesyonel Hizmet', 60),
            'İletişim Sayfası' => $this->truncateTitle($title . ' | Hızlı İletişim', 60),
            default => $this->truncateTitle($title . ' - Detaylı Bilgi', 60)
        };
        
        return $optimized;
    }
    
    private function createClickThroughTitle(string $title, string $pageType): string
    {
        return match($pageType) {
            'Blog Sayfası' => '✅ ' . $this->truncateTitle($title . ' Keşfedin!', 58),
            'Ürün Sayfası' => '🔥 ' . $this->truncateTitle($title . ' İndirimde!', 58),
            'Hizmet Sayfası' => '⭐ ' . $this->truncateTitle($title . ' Hemen Başlayın', 58),
            'İletişim Sayfası' => '📞 ' . $this->truncateTitle($title . ' Hemen Arayın', 58),
            default => '💡 ' . $this->truncateTitle($title . ' Öğrenin!', 58)
        };
    }
    
    private function createBrandFocusedTitle(string $title, string $pageType): string
    {
        return match($pageType) {
            'Blog Sayfası' => $this->truncateTitle($title . ' | Uzman Blog', 60),
            'Ürün Sayfası' => $this->truncateTitle($title . ' | Güvenilir Marka', 60),
            'Hizmet Sayfası' => $this->truncateTitle($title . ' | Sektör Lideri', 60),
            'İletişim Sayfası' => $this->truncateTitle($title . ' | 7/24 Destek', 60),
            default => $this->truncateTitle($title . ' | Güvenilir Platform', 60)
        };
    }
    
    private function createCurrentYearTitle(string $title, string $pageType): string
    {
        return match($pageType) {
            'Blog Sayfası' => $this->truncateTitle('2025: ' . $title . ' Güncel', 60),
            'Ürün Sayfası' => $this->truncateTitle('2025 ' . $title . ' Modelleri', 60),
            'Hizmet Sayfası' => $this->truncateTitle('2025 ' . $title . ' Hizmetleri', 60),
            'İletişim Sayfası' => $this->truncateTitle('2025 ' . $title . ' Bilgileri', 60),
            default => $this->truncateTitle('2025 ' . $title . ' Rehberi', 60)
        };
    }
    
    // ========== META DESCRIPTION YARDIMCI METODLARI ==========
    
    private function createActionOrientedDescription(string $description, string $pageType): string
    {
        $cta = match($pageType) {
            'Blog Sayfası' => 'Hemen okuyun ve öğrenin!',
            'Ürün Sayfası' => 'Şimdi satın alın, avantajlı fiyat!',
            'Hizmet Sayfası' => 'Hemen başvurun, uzman desteği!',
            'İletişim Sayfası' => 'Hemen iletişime geçin!',
            default => 'Hemen keşfedin!'
        };
        
        return $this->truncateDescription($description . ' ' . $cta, 160);
    }
    
    private function createBenefitsListedDescription(string $description, string $pageType): string
    {
        $benefits = match($pageType) {
            'Blog Sayfası' => '✓ Güncel bilgiler ✓ Uzman görüşleri ✓ Pratik öneriler',
            'Ürün Sayfası' => '✓ En iyi kalite ✓ Uygun fiyat ✓ Hızlı teslimat',
            'Hizmet Sayfası' => '✓ Uzman ekip ✓ Hızlı çözüm ✓ Güvenilir hizmet',
            'İletişim Sayfası' => '✓ 7/24 destek ✓ Hızlı yanıt ✓ Kolay ulaşım',
            default => '✓ Kaliteli içerik ✓ Güncel bilgi ✓ Profesyonel yaklaşım'
        };
        
        return $this->truncateDescription($description . ' ' . $benefits, 160);
    }
    
    private function createProblemSolutionDescription(string $description, string $pageType): string
    {
        $solution = match($pageType) {
            'Blog Sayfası' => 'Bilgi eksikliğinizi giderin, öğrenmeye başlayın.',
            'Ürün Sayfası' => 'İhtiyacınıza uygun ürünü bulun, hemen sahip olun.',
            'Hizmet Sayfası' => 'Sorununuza çözüm bulun, uzmanlardan destek alın.',
            'İletişim Sayfası' => 'Ulaşım sorununuz yok, hemen bağlantı kurun.',
            default => 'Aradığınızı bulun, hedeflerinize ulaşın.'
        };
        
        return $this->truncateDescription($description . ' ' . $solution, 160);
    }
    
    private function createSocialProofMetaDescription(string $description, string $pageType): string
    {
        $proof = match($pageType) {
            'Blog Sayfası' => '10,000+ okuyucu güveniyor.',
            'Ürün Sayfası' => '%98 müşteri memnuniyeti.',
            'Hizmet Sayfası' => '5,000+ başarılı proje.',
            'İletişim Sayfası' => '24/7 kesintisiz hizmet.',
            default => 'Binlerce kullanıcı tercihi.'
        };
        
        return $this->truncateDescription($proof . ' ' . $description, 160);
    }
    
    // ========== YARDIMCI METODLAR ==========
    
    private function truncateTitle(string $title, int $maxLength): string
    {
        if (strlen($title) <= $maxLength) return $title;
        return substr($title, 0, $maxLength - 3) . '...';
    }
    
    private function truncateDescription(string $description, int $maxLength): string
    {
        if (strlen($description) <= $maxLength) return $description;
        return substr($description, 0, $maxLength - 3) . '...';
    }
    
    private function improveTitleForSEO(string $currentTitle, string $pageType): string
    {
        if (empty($currentTitle)) {
            return match($pageType) {
                'Blog Sayfası' => 'Blog Yazısı - [Konu] | Site Adı',
                'Ürün Sayfası' => 'Ürün Adı - Özellikler ve Fiyat | Site Adı',
                'Hizmet Sayfası' => 'Hizmet Adı - Profesyonel Çözümler | Site Adı',
                'İletişim Sayfası' => 'İletişim - Bize Ulaşın | Site Adı',
                default => 'Sayfa Başlığı - Açıklayıcı Bilgi | Site Adı'
            };
        }
        
        // Mevcut başlığı iyileştir
        $title = trim($currentTitle);
        if (strlen($title) < 30) {
            $title .= ' - Detaylı Bilgi ve Çözümler';
        } elseif (strlen($title) > 70) {
            $title = substr($title, 0, 57) . '...';
        }
        
        return $title;
    }
    
    private function improveDescriptionForSEO(string $currentDescription, string $pageType): string  
    {
        if (empty($currentDescription)) {
            return match($pageType) {
                'Blog Sayfası' => 'Bu blog yazısında [konu] hakkında detaylı bilgiler, uzman görüşleri ve pratik öneriler bulabilirsiniz. Hemen okuyun!',
                'Ürün Sayfası' => 'Yüksek kaliteli [ürün adı] için en iyi fiyat ve özellikler. Hızlı teslimat ve güvenli ödeme seçenekleri mevcuttur.',
                'Hizmet Sayfası' => 'Profesyonel [hizmet adı] hizmetlerimiz ile hedeflerinize ulaşın. Uzman ekibimiz size özel çözümler sunar.',
                'İletişim Sayfası' => 'Bizimle iletişime geçin! Telefon, e-posta ve adres bilgilerimiz. Sorularınızı yanıtlamak için buradayız.',
                default => 'Bu sayfada [konu] hakkında kapsamlı bilgiler ve güncel içerikler bulabilirsiniz. Hemen keşfedin!'
            };
        }
        
        $description = trim($currentDescription);
        if (strlen($description) < 120) {
            $description .= ' Daha fazla bilgi ve detaylar için sayfamızı inceleyin.';
        } elseif (strlen($description) > 160) {
            $description = substr($description, 0, 157) . '...';
        }
        
        return $description;
    }
    
    private function suggestKeywords(string $title, string $description, string $pageType): string
    {
        $keywords = [];
        
        // Sayfa tipine göre genel anahtar kelimeler
        $keywords[] = match($pageType) {
            'Blog Sayfası' => 'blog, makale, bilgi',
            'Ürün Sayfası' => 'ürün, satış, fiyat',
            'Hizmet Sayfası' => 'hizmet, profesyonel, çözüm',
            'İletişim Sayfası' => 'iletişim, telefon, adres',
            default => 'bilgi, hizmet, kalite'
        };
        
        // Başlık ve açıklamadan anahtar kelimeler çıkar
        $text = strtolower($title . ' ' . $description);
        $commonWords = ['ve', 'ile', 'için', 'olan', 'bir', 'bu', 'şu', 'o'];
        $words = array_filter(explode(' ', $text), function($word) use ($commonWords) {
            return strlen($word) > 3 && !in_array($word, $commonWords);
        });
        
        $keywords = array_merge($keywords, array_slice($words, 0, 3));
        
        return implode(', ', array_unique($keywords));
    }
    
    private function createSocialTitle(string $title): string
    {
        if (empty($title)) return 'Sosyal Medya İçin Özel Başlık';
        
        // Sosyal medya için daha çekici başlık
        $socialTitle = $title;
        if (!str_contains($title, '🎯') && !str_contains($title, '✨')) {
            $socialTitle = '✨ ' . $title;
        }
        
        return $socialTitle;
    }
    
    private function createSocialDescription(string $description): string
    {
        if (empty($description)) return 'Bu içeriği sosyal medyada paylaşmaya değer! Hemen göz atın.';
        
        // Sosyal medya için daha çekici açıklama
        return $description . ' #paylaş #keşfet';
    }

    /**
     * FALLBACK ÖNERİLERİ
     */
    private function getFallbackRecommendations(string $language): array
    {
        return [
            [
                'id' => 1,
                'type' => 'title',
                'priority' => 'high',
                'title' => 'Başlık Optimizasyonu',
                'description' => 'Başlığınızı daha etkili ve SEO dostu hale getirin.',
                'suggested_value' => '',
                'field_target' => 'title',
                'impact_score' => 85,
                'language' => $language
            ],
            [
                'id' => 2,
                'type' => 'description',
                'priority' => 'high',
                'title' => 'Meta Açıklama Geliştirmesi',
                'description' => 'Meta açıklamanızı daha çekici ve bilgilendirici yapın.',
                'suggested_value' => '',
                'field_target' => 'description',
                'impact_score' => 75,
                'language' => $language
            ],
        ];
    }

    /**
     * SOSYAL MEDYA BAŞLIK ALTERNATİFLERİ - Tıklanma odaklı, emoji yok
     */
    private function generateSocialMediaTitleAlternatives(string $currentTitle, string $pageType): array
    {
        $baseTitle = trim($currentTitle) ?: 'İlgi Çekici İçerik';
        $alternatives = [];

        // ALTERNATİF 1: Merak Uyandırıcı
        $alternatives[] = [
            'id' => 'social_title_1',
            'label' => 'Merak Uyandırıcı',
            'value' => $this->createCuriosityDrivenSocialTitle($baseTitle, $pageType),
            'description' => 'Kullanıcının merakını uyandırarak tıklama oranını artırır',
            'score' => 95
        ];

        // ALTERNATİF 2: Sosyal Kanıt Vurgulu
        $alternatives[] = [
            'id' => 'social_title_2',
            'label' => 'Sosyal Kanıt',
            'value' => $this->createSocialProofTitle($baseTitle, $pageType),
            'description' => 'Popülerlik ve güvenilirlik vurgusu ile çekicilik artırır',
            'score' => 90
        ];

        // ALTERNATİF 3: Acil Eylem Çağrısı
        $alternatives[] = [
            'id' => 'social_title_3',
            'label' => 'Acil Eylem',
            'value' => $this->createUrgentActionSocialTitle($baseTitle, $pageType),
            'description' => 'Hemen harekete geçme isteği uyandırır',
            'score' => 87
        ];

        return $alternatives;
    }

    /**
     * SOSYAL MEDYA AÇIKLAMA ALTERNATİFLERİ - Etkileşim odaklı
     */
    private function generateSocialMediaDescriptionAlternatives(string $currentDescription, string $pageType): array
    {
        $baseDesc = trim($currentDescription) ?: 'Bu içerik ilginizi çekecek';
        $alternatives = [];

        // ALTERNATİF 1: Hikaye Anlatıcı
        $alternatives[] = [
            'id' => 'social_desc_1',
            'label' => 'Hikaye Formatı',
            'value' => $this->createStorytellingDescription($baseDesc, $pageType),
            'description' => 'Hikaye anlatımı ile duygusal bağ kurar',
            'score' => 92
        ];

        // ALTERNATİF 2: Fayda Listesi
        $alternatives[] = [
            'id' => 'social_desc_2',
            'label' => 'Net Faydalar',
            'value' => $this->createBenefitListSocialDescription($baseDesc, $pageType),
            'description' => 'Somut faydalar listesi ile değer gösterir',
            'score' => 90
        ];

        // ALTERNATİF 3: Topluluk Odaklı
        $alternatives[] = [
            'id' => 'social_desc_3',
            'label' => 'Topluluk Vurgu',
            'value' => $this->createCommunityFocusedDescription($baseDesc, $pageType),
            'description' => 'Topluluk aidiyeti ve paylaşım teşviki',
            'score' => 85
        ];

        return $alternatives;
    }

    /**
     * ÖNCELİK PUANI ALTERNATİFLERİ - Sayfa türüne göre optimize
     */
    private function generatePriorityAlternatives(string $pageType, int $currentPriority): array
    {
        $alternatives = [];

        switch ($pageType) {
            case 'Blog Sayfası':
                $alternatives = [
                    ['id' => 'priority_1', 'label' => 'Düşük (3)', 'value' => '3', 'description' => 'Genel blog içeriği için standart öncelik', 'score' => 70],
                    ['id' => 'priority_2', 'label' => 'Orta (5)', 'value' => '5', 'description' => 'Popüler konular için dengeli öncelik', 'score' => 85],
                    ['id' => 'priority_3', 'label' => 'Yüksek (7)', 'value' => '7', 'description' => 'Trend konular ve özel yazılar için', 'score' => 90]
                ];
                break;

            case 'Ürün Sayfası':
                $alternatives = [
                    ['id' => 'priority_1', 'label' => 'Orta (5)', 'value' => '5', 'description' => 'Standart ürün sayfaları için', 'score' => 75],
                    ['id' => 'priority_2', 'label' => 'Yüksek (7)', 'value' => '7', 'description' => 'Popüler ürünler için önerilen', 'score' => 90],
                    ['id' => 'priority_3', 'label' => 'Kritik (9)', 'value' => '9', 'description' => 'Bestseller ve kampanya ürünleri', 'score' => 95]
                ];
                break;

            default:
                $alternatives = [
                    ['id' => 'priority_1', 'label' => 'Orta (6)', 'value' => '6', 'description' => 'Standart sayfa önceliği', 'score' => 80],
                    ['id' => 'priority_2', 'label' => 'Yüksek (8)', 'value' => '8', 'description' => 'Önemli sayfa içerikleri', 'score' => 90]
                ];
        }

        return $alternatives;
    }

    // ========== SOSYAL MEDYA ÜRETİCİ METODLARI ==========

    private function createCuriosityDrivenSocialTitle(string $title, string $pageType): string
    {
        return match($pageType) {
            'Blog Sayfası' => 'Bu ' . $title . ' Gerçeğini Biliyor Muydunuz?',
            'Ürün Sayfası' => $title . ' Hakkında Kimsenin Bilmediği 5 Şey',
            'Hizmet Sayfası' => $title . ' İçin Gizli Kalmış İpuçları',
            default => $title . ' Hakkında Şaşırtıcı Gerçekler'
        };
    }

    private function createSocialProofTitle(string $title, string $pageType): string
    {
        return match($pageType) {
            'Blog Sayfası' => 'Binlerce Kişi Paylaştı: ' . $title,
            'Ürün Sayfası' => 'Müşteriler Diyor: En İyi ' . $title,
            'Hizmet Sayfası' => '5000+ İnsan Tercih Etti: ' . $title,
            default => 'Popüler Seçim: ' . $title
        };
    }

    private function createUrgentActionSocialTitle(string $title, string $pageType): string
    {
        return match($pageType) {
            'Blog Sayfası' => $title . ' - Hemen Okumaya Başlayın!',
            'Ürün Sayfası' => 'Son Şans: ' . $title . ' Fırsatı',
            'Hizmet Sayfası' => 'Bugün Başvurun: ' . $title,
            default => 'Kaçırmayın: ' . $title . ' İmkanı'
        };
    }

    private function createStorytellingDescription(string $description, string $pageType): string
    {
        $story = match($pageType) {
            'Blog Sayfası' => 'Geçen hafta bir okuyucu sordu...',
            'Ürün Sayfası' => 'Müşterimiz Ali Bey yaşadığı deneyimi anlattı...',
            'Hizmet Sayfası' => 'İşte başarı hikayemizden bir kesit...',
            default => 'Gerçek bir hikaye paylaşmak istiyoruz...'
        };
        
        return $story . ' ' . $description . ' Siz de bu hikayenin parçası olun!';
    }

    private function createBenefitListSocialDescription(string $description, string $pageType): string
    {
        $benefits = match($pageType) {
            'Blog Sayfası' => 'Yeni bilgiler öğrenecek, uzmanlaşacak, fark yaratacaksınız.',
            'Ürün Sayfası' => 'Kalite garantisi, hızlı teslimat, memnuniyet güvencesi.',
            'Hizmet Sayfası' => 'Uzman destek, hızlı çözüm, güvenilir hizmet.',
            default => 'Kaliteli içerik, güncel bilgi, faydalı öneriler.'
        };
        
        return $description . ' Bu sayfada: ' . $benefits;
    }

    private function createCommunityFocusedDescription(string $description, string $pageType): string
    {
        $community = match($pageType) {
            'Blog Sayfası' => 'Binlerce okuyucu topluluğumuza katılın, paylaşın!',
            'Ürün Sayfası' => 'Memnun müşteri ailemizin bir parçası olun!',
            'Hizmet Sayfası' => 'Başarılı projeler ailesine dahil olun!',
            default => 'Büyük topluluğumuza siz de katılın!'
        };
        
        return $description . ' ' . $community;
    }

    /**
     * JSON AYIKLA
     */
    private function extractJsonFromResponse(string $response): string
    {
        // JSON bloğunu bul
        $patterns = [
            '/\{.*\}/s',
            '/```json\s*(\{.*\})\s*```/s',
            '/```\s*(\{.*\})\s*```/s'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $response, $matches)) {
                return $matches[1] ?? $matches[0];
            }
        }
        
        return $response;
    }

}
