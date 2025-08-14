<?php

namespace Modules\AI\App\Services\Response;

use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Services\SmartResponseFormatter;
use Modules\AI\App\Services\Response\AIResponseParsers;
use Illuminate\Support\Facades\Log;

/**
 * AI Response Formatters - Yanıt formatlama servisi
 * 
 * AIResponseRepository'den ayrılmış format metodları:
 * - formatAdminResponse
 * - formatProwessResponse  
 * - formatConversationResponse
 * - formatFeatureResponse
 * - formatHelperResponse
 * - formatSEOAnalysisResponse
 * - formatGenericResponse
 * - formatTranslationResponse
 * - formatContentGenerationResponse
 * - formatSchemaMarkupResponse
 * - formatAIChatResponse
 * - formatWidgetResponse
 */
class AIResponseFormatters
{
    private SmartResponseFormatter $smartFormatter;
    private AIResponseParsers $parsers;

    public function __construct()
    {
        $this->smartFormatter = new SmartResponseFormatter();
        $this->parsers = new AIResponseParsers();
    }

    /**
     * Admin panel response formatı
     */
    public function formatAdminResponse(string $response): array
    {
        return [
            'formatted_text' => "🤖 **AI Asistan Yanıtı**\n\n" . $response,
            'word_buffer_config' => [
                'enabled' => true,
                'delay_between_words' => 180,
                'animation_duration' => 4500,
                'container_selector' => '.ai-response-container'
            ]
        ];
    }

    /**
     * Prowess showcase response formatı
     */
    public function formatProwessResponse(?string $response, AIFeature $feature): array
    {
        // Null response durumunu handle et
        if ($response === null) {
            $response = "⚠️ AI yanıt alamadı. Lütfen tekrar deneyin veya farklı bir provider kullanın.";
        }

        // 🎨 SMART RESPONSE FORMATTER - Monoton 1-2-3 formatını kır
        try {
            // Orijinal input'u da göndermek için context'ten al
            $originalInput = $feature->quick_prompt ?? 'AI İsteği';
            
            // Smart formatter uygula
            $smartFormattedResponse = $this->smartFormatter->format($originalInput, $response, $feature);
            
            Log::info('🎨 Smart Response Formatter applied', [
                'feature' => $feature->slug,
                'original_length' => strlen($response),
                'formatted_length' => strlen($smartFormattedResponse)
            ]);
            
            $formattedResponse = $smartFormattedResponse;
            
        } catch (\Exception $e) {
            Log::warning('Smart Response Formatter failed, using fallback', [
                'error' => $e->getMessage(),
                'feature' => $feature->slug
            ]);
            
            // Fallback: Template sistemi
            $responseTemplate = $this->getFeatureTemplate($feature);
            if ($responseTemplate) {
                $formattedResponse = $this->applyResponseTemplate($response, $responseTemplate, $feature);
            } else {
                $formattedResponse = $this->wrapResponseInCard($response, $feature->name);
            }
        }

        return [
            'formatted_text' => $formattedResponse,
            'word_buffer_config' => [
                'enabled' => true,
                'delay_between_words' => 200,
                'animation_duration' => 5000,
                'container_selector' => '.prowess-response-container',
                'feature_name' => $feature->name,
                'showcase_mode' => true
            ]
        ];
    }

    /**
     * Conversation response formatı
     */
    public function formatConversationResponse(string $response): array
    {
        return [
            'success' => true,
            'response' => [
                'content' => $response,
                'timestamp' => now()->format('H:i'),
                'type' => 'ai_message'
            ],
            'word_buffer_config' => [
                'enabled' => true,
                'delay_between_words' => 150,
                'animation_duration' => 3500,
                'container_selector' => '.conversation-message'
            ]
        ];
    }

    /**
     * Feature test response formatı
     */
    public function formatFeatureResponse(string $response, AIFeature $feature, string $helperName): array
    {
        // Feature slug'a göre özel formatlamalar
        return match(true) {
            str_contains($feature->slug, 'seo') => 
                $this->formatSEOAnalysisResponse($response, $feature, $helperName),
            str_contains($feature->slug, 'chat') => 
                $this->formatAIChatResponse($response, $helperName),
            str_contains($feature->slug, 'cevirmen') || str_contains($feature->slug, 'translate') => 
                $this->formatTranslationResponse($response, $feature, $helperName),
            str_contains($feature->slug, 'icerik') || str_contains($feature->slug, 'content') => 
                $this->formatContentGenerationResponse($response, $feature, $helperName),
            str_contains($feature->slug, 'schema') => 
                $this->formatSchemaMarkupResponse($response, $feature, $helperName),
            default => 
                $this->formatGenericResponse($response, $feature, $helperName)
        };
    }

    /**
     * Helper function response formatı
     */
    public function formatHelperResponse(string $response, string $helperName): array
    {
        return [
            'success' => true,
            'response' => [
                'formatted_text' => "🔧 **{$helperName} Helper Sonucu**\n\n" . $response,
                'word_buffer_config' => [
                    'enabled' => true,
                    'delay_between_words' => 150,
                    'animation_duration' => 3000,
                    'container_selector' => '.helper-response-container'
                ]
            ],
            'helper_name' => $helperName,
            'type' => 'helper_response'
        ];
    }

    /**
     * SEO analiz response formatı
     */
    public function formatSEOAnalysisResponse(string $response, AIFeature $feature, string $helperName): array
    {
        try {
            // Smart formatter ile SEO özel formatı uygula
            $formattedResponse = $this->smartFormatter->format($helperName, $response, $feature);
            
            // SEO özgü dashboard yapısı
            $seoData = $this->parsers->parseAIResponse($response);
            $dashboardHtml = $this->buildSEODashboard($seoData, $feature);
            
            return [
                'success' => true,
                'response' => $dashboardHtml,
                'seo_data' => $seoData,
                'feature' => $feature->title,
                'helper_name' => $helperName,
                'type' => 'seo_analysis'
            ];

        } catch (\Exception $e) {
            Log::error('SEO Analysis Format Error', [
                'error' => $e->getMessage(),
                'feature_slug' => $feature->slug
            ]);

            return $this->formatGenericResponse($response, $feature, $helperName);
        }
    }

    /**
     * Generic response formatı
     */
    public function formatGenericResponse(string $response, AIFeature $feature, string $helperName): array
    {
        // Smart formatter uygula
        try {
            $formattedResponse = $this->smartFormatter->format($helperName, $response, $feature);
        } catch (\Exception $e) {
            Log::warning('Generic format Smart Formatter failed', ['error' => $e->getMessage()]);
            $formattedResponse = $this->wrapResponseInCard($response, $feature->name);
        }

        return [
            'success' => true,
            'response' => $formattedResponse,
            'feature' => $feature->title,
            'helper_name' => $helperName,
            'type' => 'generic_feature'
        ];
    }

    /**
     * Çeviri response formatı
     */
    public function formatTranslationResponse(string $response, AIFeature $feature, string $helperName): array
    {
        // Çeviriler için özel format (original + translated)
        $translationData = $this->parsers->parseTranslationResponse($response);

        $html = "<div class='translation-result'>";
        
        if ($translationData['original'] ?? false) {
            $html .= "<div class='row mb-3'>";
            $html .= "<div class='col-md-6'>";
            $html .= "<h5>📝 Orijinal Metin</h5>";
            $html .= "<div class='border p-3 rounded'>" . htmlspecialchars($translationData['original']) . "</div>";
            $html .= "</div>";
            $html .= "<div class='col-md-6'>";
            $html .= "<h5>🌐 Çevrilmiş Metin</h5>";
            $html .= "<div class='border p-3 rounded bg-light'>" . htmlspecialchars($translationData['translated']) . "</div>";
            $html .= "</div>";
            $html .= "</div>";
        } else {
            $html .= "<div class='translation-output'>";
            $html .= $response;
            $html .= "</div>";
        }
        
        $html .= "</div>";

        return [
            'success' => true,
            'response' => $html,
            'translation_data' => $translationData,
            'feature' => $feature->title,
            'type' => 'translation'
        ];
    }

    /**
     * İçerik üretimi response formatı
     */
    public function formatContentGenerationResponse(string $response, AIFeature $feature, string $helperName): array
    {
        // Blog, makale vb. için özel format
        $contentData = $this->parsers->parseContentResponse($response);

        $html = "<div class='content-generation-result'>";
        
        // Başlık varsa
        if ($contentData['title'] ?? false) {
            $html .= "<h3 class='content-title mb-3'>" . htmlspecialchars($contentData['title']) . "</h3>";
        }

        // İçerik
        $html .= "<div class='content-body'>";
        $html .= $contentData['content'] ?? $response;
        $html .= "</div>";

        // Meta bilgiler
        if ($contentData['meta'] ?? false) {
            $html .= "<div class='content-meta mt-4'>";
            $html .= "<small class='text-muted'>";
            $html .= "📊 Kelime sayısı: " . ($contentData['meta']['word_count'] ?? 'N/A');
            $html .= " | 📝 Karakter sayısı: " . ($contentData['meta']['char_count'] ?? 'N/A');
            $html .= "</small>";
            $html .= "</div>";
        }

        $html .= "</div>";

        return [
            'success' => true,
            'response' => $html,
            'content_data' => $contentData,
            'feature' => $feature->title,
            'type' => 'content_generation'
        ];
    }

    /**
     * Schema markup response formatı
     */
    public function formatSchemaMarkupResponse(string $response, AIFeature $feature, string $helperName): array
    {
        // JSON-LD schema için özel format
        $schemaData = $this->parsers->parseSchemaResponse($response);

        $html = "<div class='schema-markup-result'>";
        $html .= "<h5>🔧 Schema Markup Kodu</h5>";
        $html .= "<div class='alert alert-info'>";
        $html .= "<small>Bu kodu sitenizin &lt;head&gt; bölümüne ekleyin:</small>";
        $html .= "</div>";
        $html .= "<pre><code class='language-json'>";
        $html .= htmlspecialchars($schemaData['schema'] ?? $response);
        $html .= "</code></pre>";
        $html .= "</div>";

        return [
            'success' => true,
            'response' => $html,
            'schema_data' => $schemaData,
            'feature' => $feature->title,
            'type' => 'schema_markup'
        ];
    }

    /**
     * AI Chat response formatı
     */
    public function formatAIChatResponse(string $response, string $helperName): array
    {
        return [
            'success' => true,
            'response' => [
                'message' => $response,
                'type' => 'ai_response',
                'timestamp' => now()->toISOString(),
                'helper' => $helperName
            ],
            'word_buffer_config' => [
                'enabled' => true,
                'delay_between_words' => 120,
                'animation_duration' => 2500,
                'container_selector' => '.ai-chat-response'
            ]
        ];
    }

    /**
     * Widget response formatı
     */
    public function formatWidgetResponse(string $response, $feature = null): string
    {
        if ($feature) {
            return "<div class='widget-ai-response widget-{$feature->slug}'>{$response}</div>";
        }

        return "<div class='widget-ai-response'>{$response}</div>";
    }

    /**
     * Word buffer formatı (animasyon için)
     */
    public function formatWithWordBuffer(string $response, string $type, array $meta = []): array
    {
        $configs = [
            'admin_chat' => [
                'delay_between_words' => 180,
                'animation_duration' => 4500,
                'container_selector' => '.ai-response-container'
            ],
            'feature_test' => [
                'delay_between_words' => 150,
                'animation_duration' => 3500,
                'container_selector' => '.feature-response-container'
            ],
            'prowess_test' => [
                'delay_between_words' => 200,
                'animation_duration' => 5000,
                'container_selector' => '.prowess-response-container'
            ],
            'conversation' => [
                'delay_between_words' => 140,
                'animation_duration' => 3000,
                'container_selector' => '.conversation-message'
            ],
            'helper_function' => [
                'delay_between_words' => 150,
                'animation_duration' => 3000,
                'container_selector' => '.helper-response-container'
            ]
        ];

        $config = $configs[$type] ?? $configs['feature_test'];
        $config = array_merge($config, $meta);

        return [
            'formatted_text' => $response,
            'word_buffer_config' => array_merge($config, ['enabled' => true])
        ];
    }

    /**
     * Helper metodları
     */
    private function getFeatureTemplate(AIFeature $feature): ?array
    {
        if (!$feature->response_template) {
            return null;
        }

        try {
            // response_template zaten array ise decode etme
            if (is_array($feature->response_template)) {
                return $feature->response_template;
            } else {
                return json_decode($feature->response_template, true);
            }
        } catch (\Exception $e) {
            Log::warning("Feature response template parse hatası: {$feature->slug}", ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function applyResponseTemplate(string $response, array $template, AIFeature $feature): string
    {
        // Modern Tabler.io card layout
        $html = "<div class='card ai-feature-response ai-feature-{$feature->slug} mb-3'>";
        
        // Card header
        $html .= "<div class='card-header'>";
        $html .= "<div class='d-flex align-items-center'>";
        $html .= "<div class='me-3'><i class='fas fa-robot text-primary fs-2'></i></div>";
        $html .= "<div>";
        $html .= "<h3 class='card-title mb-1'>{$feature->name}</h3>";
        $html .= "<p class='text-muted mb-0'>AI Analiz Sonuçları</p>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";
        
        // Card body
        $html .= "<div class='card-body'>";
        
        $format = $template['format'] ?? 'text';
        
        switch ($format) {
            case 'structured':
                $html .= $this->formatStructuredResponse($response, $template);
                break;
            case 'table':
                $html .= $this->formatTableResponse($response, $template);
                break;
            default:
                $html .= "<div class='ai-response-content'>" . nl2br(htmlspecialchars($response)) . "</div>";
        }
        
        $html .= "</div>";
        $html .= "</div>";
        
        return $html;
    }

    private function formatStructuredResponse(string $response, array $template): string
    {
        $sections = $template['sections'] ?? ['Sonuç'];
        $html = '';
        
        foreach ($sections as $section) {
            $html .= "<div class='section mb-3'>";
            $html .= "<h5 class='section-title'>" . $this->getSectionIcon($section) . " " . $this->getSectionTitle($section) . "</h5>";
            $html .= "<div class='section-content'>";
            $html .= nl2br(htmlspecialchars($response));
            $html .= "</div>";
            $html .= "</div>";
        }
        
        return $html;
    }

    private function formatTableResponse(string $response, array $template): string
    {
        return "<div class='table-responsive'><table class='table table-striped'><tbody><tr><td>" . nl2br(htmlspecialchars($response)) . "</td></tr></tbody></table></div>";
    }

    private function getSectionIcon(string $section): string
    {
        $icons = [
            'Sonuç' => '🎯',
            'Analiz' => '📊',
            'Öneri' => '💡',
            'Detay' => '📋',
            'Skor' => '⭐'
        ];
        
        return $icons[$section] ?? '📝';
    }

    private function getSectionTitle(string $section): string
    {
        return $section;
    }

    private function wrapResponseInCard(string $response, string $featureName): string
    {
        return "<div class='card'><div class='card-header'><h5>{$featureName}</h5></div><div class='card-body'>" . nl2br(htmlspecialchars($response)) . "</div></div>";
    }

    private function buildSEODashboard(array $data, AIFeature $feature): string
    {
        // Basit SEO dashboard - AIResponseBuilders'da detaylı implement edilecek
        $html = "<div class='seo-dashboard'>";
        $html .= "<h4>📊 SEO Analiz Sonuçları</h4>";
        $html .= "<div class='seo-content'>";
        $html .= nl2br(htmlspecialchars(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)));
        $html .= "</div>";
        $html .= "</div>";
        
        return $html;
    }
}