<?php

declare(strict_types=1);

namespace Modules\SeoManagement\App\Services;

use Modules\AI\App\Services\AIService;
use Modules\AI\App\Models\AIFeature;
use Illuminate\Support\Facades\Log;

/**
 * SEO RECOMMENDATIONS SERVICE - 2025 STANDARDS
 * Modern, hardcode-free SEO recommendation system
 * Content-driven approach without fallbacks
 */
class SeoRecommendationsService
{
    private AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * GENERATE SEO RECOMMENDATIONS - 2025 APPROACH
     * Pure AI-driven without hardcoded fallbacks
     */
    public function generateSeoRecommendations(string $featureSlug, array $formContent, string $language = 'tr', array $options = []): array
    {
        try {
            Log::info('SEO Recommendations Generation Started - 2025 Standards', [
                'feature_slug' => $featureSlug,
                'language' => $language,
                'user_id' => $options['user_id'] ?? null,
                'content_keys' => array_keys($formContent)
            ]);

            // Validate required AI feature
            $feature = AIFeature::where('slug', $featureSlug)->first();
            if (!$feature) {
                return [
                    'success' => false,
                    'error' => 'SEO recommendations feature not found: ' . $featureSlug
                ];
            }

            // Extract and analyze page content
            $pageAnalysis = $this->analyzePageContent($formContent);

            // Check if we have at least title or meta description for analysis
            if (empty($pageAnalysis['title']) && empty($pageAnalysis['meta_description'])) {
                return [
                    'success' => false,
                    'error' => 'No content available for SEO analysis. Please provide at least a title or meta description.'
                ];
            }

            // Build AI prompt for 2025 SEO standards
            $aiPrompt = $this->buildModernSeoPrompt($pageAnalysis, $language);

            // Call AI service
            $aiResponse = $this->aiService->askFeature($featureSlug, $aiPrompt, [
                'language' => $language,
                'user_id' => $options['user_id'] ?? null,
                'stream' => false,
                'temperature' => 0.7,
                'max_tokens' => 3000
            ]);

            // Process AI response
            $aiResponseText = $this->extractResponseText($aiResponse);

            if (empty($aiResponseText)) {
                return [
                    'success' => false,
                    'error' => 'AI service returned empty response'
                ];
            }

            // Check if AI response contains error message instead of JSON
            if (strpos($aiResponseText, 'Üzgünüm') !== false ||
                strpos($aiResponseText, 'hata oluştu') !== false ||
                strpos($aiResponseText, 'API hatası') !== false) {

                Log::warning('AI service returned error message instead of recommendations', [
                    'ai_error_message' => substr($aiResponseText, 0, 200)
                ]);

                return [
                    'success' => false,
                    'error' => 'AI service is temporarily unavailable. Please try again in a few moments.',
                    'ai_error' => substr($aiResponseText, 0, 100)
                ];
            }

            // Parse and structure recommendations
            $parser = new SeoRecommendationsParser();
            $recommendations = $parser->parseAiResponse($aiResponseText, $language);

            if (empty($recommendations)) {
                return [
                    'success' => false,
                    'error' => 'Failed to parse AI recommendations'
                ];
            }

            Log::info('SEO Recommendations Generated Successfully', [
                'total_recommendations' => count($recommendations),
                'language' => $language,
                'categories' => array_unique(array_column($recommendations, 'type'))
            ]);

            return [
                'success' => true,
                'recommendations' => $recommendations,
                'language' => $language,
                'generated_at' => now()->toISOString(),
                'analysis_summary' => [
                    'content_length' => $pageAnalysis['content_length'],
                    'title_length' => $pageAnalysis['title_length'],
                    'has_focus_keyword' => $pageAnalysis['has_focus_keyword']
                ]
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
                'error' => 'SEO recommendation generation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * ANALYZE PAGE CONTENT - EXTRACT KEY SEO ELEMENTS
     */
    private function analyzePageContent(array $formContent): array
    {
        // Extract content from form data
        $title = $this->extractValue($formContent, ['title', 'seo_title', 'page_title']);
        $content = $this->extractValue($formContent, ['content', 'body', 'description']);
        $metaDescription = $this->extractValue($formContent, ['meta_description', 'seo_description']);
        $focusKeyword = $this->extractValue($formContent, ['focus_keyword', 'target_keyword', 'keyword']);

        // Clean and analyze content
        $cleanContent = strip_tags($content);
        $cleanTitle = strip_tags($title);

        // Content metrics
        $contentLength = strlen($cleanContent);
        $titleLength = strlen($cleanTitle);
        $wordCount = str_word_count($cleanContent);

        // Extract main topics from all available content
        $allText = trim($cleanTitle . ' ' . $cleanContent . ' ' . $metaDescription);
        $mainTopics = $this->extractMainTopics($allText);

        return [
            'title' => $cleanTitle,
            'content' => $cleanContent,
            'meta_description' => $metaDescription,
            'focus_keyword' => $focusKeyword,
            'content_length' => $contentLength,
            'title_length' => $titleLength,
            'word_count' => $wordCount,
            'main_topics' => $mainTopics,
            'has_content' => $contentLength > 10, // Lowered threshold for pages without body content
            'has_focus_keyword' => !empty($focusKeyword),
            'content_quality' => $this->assessContentQuality($cleanContent, $wordCount),
            'analysis_source' => $this->determineAnalysisSource($cleanTitle, $cleanContent, $metaDescription)
        ];
    }

    /**
     * EXTRACT VALUE FROM FORM DATA - FLEXIBLE KEY MATCHING
     */
    private function extractValue(array $data, array $possibleKeys): string
    {
        foreach ($possibleKeys as $key) {
            if (!empty($data[$key])) {
                return $data[$key];
            }

            // Check nested keys (like seoDataCache.tr.seo_title)
            if (strpos($key, '.') !== false) {
                $value = $this->getNestedValue($data, $key);
                if (!empty($value)) {
                    return $value;
                }
            }
        }

        return '';
    }

    /**
     * GET NESTED VALUE FROM ARRAY
     */
    private function getNestedValue(array $data, string $key): string
    {
        $keys = explode('.', $key);
        $value = $data;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return '';
            }
            $value = $value[$k];
        }

        return is_string($value) ? $value : '';
    }

    /**
     * EXTRACT MAIN TOPICS FROM CONTENT
     */
    private function extractMainTopics(string $content): array
    {
        if (empty($content)) {
            return [];
        }

        // Extract meaningful words (AI will handle the semantic analysis)
        $words = preg_split('/[^a-zA-ZÇçĞğıİÖöŞşÜü]+/', $content);
        $meaningfulWords = array_filter($words, function($word) {
            return strlen($word) > 3; // Only filter by length, let AI handle semantics
        });

        return array_unique(array_slice($meaningfulWords, 0, 20));
    }

    /**
     * ASSESS CONTENT QUALITY
     */
    private function assessContentQuality(string $content, int $wordCount): string
    {
        if ($wordCount < 100) return 'poor';
        if ($wordCount < 300) return 'basic';
        if ($wordCount < 800) return 'good';
        return 'excellent';
    }

    /**
     * DETERMINE ANALYSIS SOURCE FOR AI PROMPT
     */
    private function determineAnalysisSource(string $title, string $content, string $metaDescription): string
    {
        if (!empty($content) && strlen($content) > 100) {
            return 'content_primary'; // Full content available
        }

        if (!empty($title) && !empty($metaDescription)) {
            return 'title_meta_combined'; // Title + meta description
        }

        if (!empty($title)) {
            return 'title_only'; // Only title available
        }

        if (!empty($metaDescription)) {
            return 'meta_only'; // Only meta description available
        }

        return 'minimal'; // Very limited content
    }

    /**
     * BUILD MODERN SEO PROMPT - 2025 STANDARDS
     */
    private function buildModernSeoPrompt(array $pageAnalysis, string $language): string
    {
        $prompt = "You are a professional SEO expert specializing in modern SEO standards. Analyze the provided content and generate 4 strategic alternatives for each SEO category.\n\n";

        $prompt .= "CONTENT ANALYSIS:\n";
        $prompt .= "Title: " . ($pageAnalysis['title'] ?: '[No title provided]') . "\n";
        $prompt .= "Meta Description: " . ($pageAnalysis['meta_description'] ?: '[No meta description]') . "\n";
        $prompt .= "Content: " . (!empty($pageAnalysis['content']) ? substr($pageAnalysis['content'], 0, 1000) : '[No detailed content - analyze based on title and meta description]') . "\n";
        $prompt .= "Focus Keyword: " . ($pageAnalysis['focus_keyword'] ?: '[Derive from available content]') . "\n";
        $prompt .= "Analysis Source: " . $pageAnalysis['analysis_source'] . "\n";
        $prompt .= "Content Quality: " . $pageAnalysis['content_quality'] . "\n";
        $prompt .= "Main Topics: " . (!empty($pageAnalysis['main_topics']) ? implode(', ', $pageAnalysis['main_topics']) : '[Extract from title and meta description]') . "\n\n";

        $prompt .= "MODERN SEO REQUIREMENTS:\n";
        $prompt .= "- Content-driven approach (NO generic templates)\n";
        $prompt .= "- User intent optimization\n";
        $prompt .= "- E-E-A-T signals integration\n";
        $prompt .= "- Mobile-first consideration\n";
        $prompt .= "- Semantic keyword usage\n";
        $prompt .= "- Click-through rate optimization\n";
        $prompt .= "- Character limits: Title 50-60, Description 150-160\n";
        $prompt .= "- NO emojis in SEO elements\n";
        $prompt .= "- NO year references unless already in original content\n";
        $prompt .= "- Content relevance priority over keyword stuffing\n\n";

        $prompt .= "GENERATE EXACTLY 4 ALTERNATIVES FOR EACH CATEGORY:\n";
        $prompt .= "1. SEO Title (4 variations)\n";
        $prompt .= "2. Meta Description (4 variations)\n";
        $prompt .= "3. Social Title (4 variations)\n";
        $prompt .= "4. Social Description (4 variations)\n\n";

        // Provide language-specific example
        $exampleValues = match($language) {
            'tr' => [
                'title' => 'İçerik Tabanlı Başlık',
                'description' => 'İçeriğe dayalı meta açıklama',
                'og_title' => 'Sosyal Medya Başlığı',
                'og_description' => 'Sosyal medya açıklaması'
            ],
            'en' => [
                'title' => 'Content-based title',
                'description' => 'Content-based meta description',
                'og_title' => 'Social media title',
                'og_description' => 'Social media description'
            ],
            default => [
                'title' => 'Content-based title',
                'description' => 'Content-based description',
                'og_title' => 'Social title',
                'og_description' => 'Social description'
            ]
        };

        $prompt .= "RESPONSE FORMAT - JSON ONLY:\n";
        $prompt .= "{\n";
        $prompt .= '  "recommendations": [' . "\n";
        $prompt .= '    {' . "\n";
        $prompt .= '      "type": "title",' . "\n";
        $prompt .= '      "alternatives": [' . "\n";
        $prompt .= '        {"id": 1, "value": "' . $exampleValues['title'] . '", "strategy": "Primary keyword focus", "score": 95},' . "\n";
        $prompt .= '        {"id": 2, "value": "User intent variant", "strategy": "Search intent match", "score": 90},' . "\n";
        $prompt .= '        {"id": 3, "value": "CTR optimized variant", "strategy": "Click-through optimization", "score": 85},' . "\n";
        $prompt .= '        {"id": 4, "value": "Semantic variant", "strategy": "Semantic keyword usage", "score": 80}' . "\n";
        $prompt .= '      ]' . "\n";
        $prompt .= '    },' . "\n";
        $prompt .= '    {' . "\n";
        $prompt .= '      "type": "description",' . "\n";
        $prompt .= '      "alternatives": [' . "\n";
        $prompt .= '        {"id": 1, "value": "' . $exampleValues['description'] . ' 1", "strategy": "Primary approach", "score": 95},' . "\n";
        $prompt .= '        {"id": 2, "value": "' . $exampleValues['description'] . ' 2", "strategy": "Secondary approach", "score": 90},' . "\n";
        $prompt .= '        {"id": 3, "value": "' . $exampleValues['description'] . ' 3", "strategy": "Third approach", "score": 85},' . "\n";
        $prompt .= '        {"id": 4, "value": "' . $exampleValues['description'] . ' 4", "strategy": "Fourth approach", "score": 80}' . "\n";
        $prompt .= '      ]' . "\n";
        $prompt .= '    },' . "\n";
        $prompt .= '    {' . "\n";
        $prompt .= '      "type": "og_title",' . "\n";
        $prompt .= '      "alternatives": [' . "\n";
        $prompt .= '        {"id": 1, "value": "' . $exampleValues['og_title'] . ' 1", "strategy": "Social optimization", "score": 95},' . "\n";
        $prompt .= '        {"id": 2, "value": "' . $exampleValues['og_title'] . ' 2", "strategy": "Engagement focus", "score": 90},' . "\n";
        $prompt .= '        {"id": 3, "value": "' . $exampleValues['og_title'] . ' 3", "strategy": "Viral potential", "score": 85},' . "\n";
        $prompt .= '        {"id": 4, "value": "' . $exampleValues['og_title'] . ' 4", "strategy": "Brand focus", "score": 80}' . "\n";
        $prompt .= '      ]' . "\n";
        $prompt .= '    },' . "\n";
        $prompt .= '    {' . "\n";
        $prompt .= '      "type": "og_description",' . "\n";
        $prompt .= '      "alternatives": [' . "\n";
        $prompt .= '        {"id": 1, "value": "' . $exampleValues['og_description'] . ' 1", "strategy": "Social engagement", "score": 95},' . "\n";
        $prompt .= '        {"id": 2, "value": "' . $exampleValues['og_description'] . ' 2", "strategy": "Call to action", "score": 90},' . "\n";
        $prompt .= '        {"id": 3, "value": "' . $exampleValues['og_description'] . ' 3", "strategy": "Curiosity driven", "score": 85},' . "\n";
        $prompt .= '        {"id": 4, "value": "' . $exampleValues['og_description'] . ' 4", "strategy": "Value proposition", "score": 80}' . "\n";
        $prompt .= '      ]' . "\n";
        $prompt .= '    }' . "\n";
        $prompt .= '  ]' . "\n";
        $prompt .= "}\n\n";

        $prompt .= "CRITICAL RULES:\n";
        $prompt .= "- Base ALL suggestions on actual content analysis\n";
        $prompt .= "- NO hardcoded templates or generic phrases\n";
        $prompt .= "- NO year references (2024, 2025, etc.) unless in original content\n";
        $prompt .= "- Each alternative must have unique strategic value\n";
        $prompt .= "- Ensure character limits compliance\n";
        $prompt .= "- Focus on user value proposition\n";

        // Add specific instructions based on analysis source
        switch ($pageAnalysis['analysis_source']) {
            case 'title_meta_combined':
                $prompt .= "- SPECIAL: Work with title and meta description to create comprehensive recommendations\n";
                $prompt .= "- Extract themes and keywords from existing title/meta to inform all suggestions\n";
                break;
            case 'title_only':
                $prompt .= "- SPECIAL: Expand on the title to create meta descriptions and social content\n";
                $prompt .= "- Use title themes to generate complementary descriptions\n";
                break;
            case 'meta_only':
                $prompt .= "- SPECIAL: Use meta description content to create compelling titles\n";
                $prompt .= "- Extract key themes from description for title optimization\n";
                break;
        }

        $prompt .= "- RESPOND IN " . strtoupper($language) . " LANGUAGE\n";
        $prompt .= "- RETURN ONLY JSON, NO EXPLANATIONS\n";
        $prompt .= "- MANDATORY: Each recommendation type must have EXACTLY 4 alternatives\n";
        $prompt .= "- MANDATORY: All 4 recommendation types must be present (title, description, og_title, og_description)\n";
        $prompt .= "- KEEP JSON COMPACT: Use short 'strategy' values, no extra fields\n";
        $prompt .= "- MAX RESPONSE LENGTH: Keep total JSON under 2000 characters\n";
        $prompt .= "- FORMAT: Single line JSON where possible to prevent truncation\n\n";

        return $prompt;
    }

    /**
     * EXTRACT RESPONSE TEXT FROM AI SERVICE
     */
    private function extractResponseText($aiResponse): string
    {
        if (is_string($aiResponse)) {
            return $aiResponse;
        }

        if (is_array($aiResponse) && isset($aiResponse['response'])) {
            return $aiResponse['response'];
        }

        if (is_array($aiResponse) && isset($aiResponse['content'])) {
            return $aiResponse['content'];
        }

        Log::error('Unexpected AI response format', [
            'type' => gettype($aiResponse),
            'keys' => is_array($aiResponse) ? array_keys($aiResponse) : 'not_array'
        ]);

        return '';
    }

}