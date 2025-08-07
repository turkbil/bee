<?php

namespace Modules\AI\App\Services;

use Modules\AI\App\Services\SmartResponseFormatter;
use Modules\AI\App\Models\AIFeature;
use Illuminate\Support\Facades\Log;

/**
 * Response Formatter Service - AIResponseRepository için wrapper
 * 
 * Bu servis AIResponseRepository'nin formatProwessResponse metodunu 
 * SmartResponseFormatter ile geliştirir
 */
class ResponseFormatterService
{
    private SmartResponseFormatter $smartFormatter;

    public function __construct()
    {
        $this->smartFormatter = new SmartResponseFormatter();
    }

    /**
     * Ana format metodu - AIResponseRepository'den çağrılır
     */
    public function formatResponse(string $response, AIFeature $feature, array $context = []): string
    {
        try {
            // Input context'inden orijinal kullanıcı girdisini al
            $originalInput = $context['original_input'] ?? $feature->quick_prompt ?? 'AI İsteği';
            
            // Smart formatter uygula
            $smartFormatted = $this->smartFormatter->format($originalInput, $response, $feature);
            
            Log::info('🎨 ResponseFormatterService: Smart formatting applied', [
                'feature' => $feature->slug,
                'original_length' => strlen($response),
                'formatted_length' => strlen($smartFormatted),
                'improvement' => $this->calculateImprovement($response, $smartFormatted)
            ]);
            
            return $smartFormatted;
            
        } catch (\Exception $e) {
            Log::error('ResponseFormatterService: Smart formatting failed', [
                'error' => $e->getMessage(),
                'feature' => $feature->slug,
                'fallback' => 'original_response'
            ]);
            
            // Hata durumunda orijinal response'u döndür
            return $response;
        }
    }

    /**
     * Legacy wrapper - eski applyResponseTemplate metodunu override
     */
    public function applyResponseTemplate(string $response, array $template, AIFeature $feature): string
    {
        // Template varsa Smart Formatter'ı kullan
        return $this->formatResponse($response, $feature, [
            'template' => $template,
            'legacy_mode' => true
        ]);
    }

    /**
     * Prowess response formatter - özel format
     */
    public function formatProwessResponse(string $response, AIFeature $feature): string
    {
        $formattedResponse = $this->formatResponse($response, $feature, [
            'mode' => 'prowess',
            'enhanced' => true
        ]);

        // Prowess için özel wrapper
        return $this->wrapInProwessCard($formattedResponse, $feature);
    }

    /**
     * Helper response formatter
     */
    public function formatHelperResponse(string $response, AIFeature $feature, string $helperName): string
    {
        $formattedResponse = $this->formatResponse($response, $feature, [
            'mode' => 'helper',
            'helper_name' => $helperName
        ]);

        return [
            'formatted_text' => "🔧 **{$helperName} Helper Sonucu**\n\n" . $formattedResponse,
            'word_buffer_config' => [
                'enabled' => true,
                'delay_between_words' => 150,
                'animation_duration' => 3000,
                'container_selector' => '.helper-response-container'
            ]
        ];
    }

    /**
     * Prowess card wrapper
     */
    private function wrapInProwessCard(string $content, AIFeature $feature): array
    {
        return [
            'formatted_text' => $content,
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
     * İyileştirme metriği hesapla
     */
    private function calculateImprovement(string $original, string $formatted): array
    {
        return [
            'removed_numbering' => $this->countNumberedLists($original) - $this->countNumberedLists($formatted),
            'added_paragraphs' => $this->countParagraphs($formatted) - $this->countParagraphs($original),
            'enhanced_headings' => $this->countHeadings($formatted) - $this->countHeadings($original),
            'format_score' => $this->calculateFormatScore($formatted)
        ];
    }

    /**
     * Numaralı liste sayısı
     */
    private function countNumberedLists(string $text): int
    {
        return preg_match_all('/^\s*\d+[\.\)]\s/m', $text);
    }

    /**
     * Paragraf sayısı
     */
    private function countParagraphs(string $text): int
    {
        return preg_match_all('/<p[^>]*>/i', $text);
    }

    /**
     * Başlık sayısı
     */
    private function countHeadings(string $text): int
    {
        return preg_match_all('/<h[1-6][^>]*>/i', $text);
    }

    /**
     * Format skoru (0-100)
     */
    private function calculateFormatScore(string $text): int
    {
        $score = 50; // Base score
        
        // HTML tag'ları varsa +20
        if (preg_match('/<[^>]+>/', $text)) {
            $score += 20;
        }
        
        // Paragraf varsa +10
        if (preg_match('/<p[^>]*>/', $text)) {
            $score += 10;
        }
        
        // Başlık varsa +10  
        if (preg_match('/<h[1-6][^>]*>/', $text)) {
            $score += 10;
        }
        
        // Liste varsa +5
        if (preg_match('/<[uo]l[^>]*>/', $text)) {
            $score += 5;
        }
        
        // Monoton 1-2-3 formatı varsa -15
        if (preg_match('/^\s*\d+[\.\)]\s.*\n\s*\d+[\.\)]\s.*\n\s*\d+[\.\)]\s/m', $text)) {
            $score -= 15;
        }
        
        return max(0, min(100, $score));
    }
}