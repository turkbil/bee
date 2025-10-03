<?php

declare(strict_types=1);

namespace Modules\SeoManagement\App\Services;

use Illuminate\Support\Facades\Log;

/**
 * SEO RECOMMENDATIONS PARSER - 2025 STANDARDS
 * Processes AI responses into structured SEO recommendations
 * Pure content-driven without hardcoded fallbacks
 */
class SeoRecommendationsParser
{
    /**
     * PARSE AI RESPONSE TO STRUCTURED RECOMMENDATIONS
     */
    public function parseAiResponse(string $aiResponse, string $language): array
    {
        try {
            Log::info('Parsing AI Response for SEO Recommendations', [
                'response_length' => strlen($aiResponse),
                'language' => $language,
                'response_preview' => substr($aiResponse, 0, 300),
                'has_json_marker' => strpos($aiResponse, '```json') !== false,
                'has_closing_marker' => strpos($aiResponse, '```') !== false
            ]);

            // Extract JSON from response
            $cleanResponse = $this->extractJsonFromResponse($aiResponse);

            if (empty($cleanResponse)) {
                Log::error('No JSON found in AI response', [
                    'raw_response' => substr($aiResponse, 0, 500),
                    'response_length' => strlen($aiResponse),
                    'has_json_start' => strpos($aiResponse, '{') !== false,
                    'has_json_end' => strpos($aiResponse, '}') !== false
                ]);
                return [];
            }

            Log::info('Extracted JSON from AI response', [
                'clean_response_length' => strlen($cleanResponse),
                'clean_response_preview' => substr($cleanResponse, 0, 300),
                'clean_response_end' => substr($cleanResponse, -100)
            ]);

            $data = json_decode($cleanResponse, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON Parse Error', [
                    'error' => json_last_error_msg(),
                    'clean_response' => substr($cleanResponse, 0, 500)
                ]);
                return [];
            }

            if (!$data || !isset($data['recommendations']) || !is_array($data['recommendations'])) {
                Log::error('Invalid AI response structure', [
                    'has_data' => !empty($data),
                    'has_recommendations' => isset($data['recommendations']),
                    'data_keys' => is_array($data) ? array_keys($data) : 'not_array'
                ]);
                return [];
            }

            // Process recommendations
            $processedRecommendations = [];

            foreach ($data['recommendations'] as $index => $aiRec) {
                if (!is_array($aiRec) || empty($aiRec['type'])) {
                    Log::warning('Invalid recommendation format at index ' . $index, [
                        'recommendation' => $aiRec
                    ]);
                    continue;
                }

                $processedRec = $this->processRecommendation($aiRec, $index + 1, $language);

                if (!empty($processedRec)) {
                    $processedRecommendations[] = $processedRec;
                }
            }

            // Validate we have the expected 4 categories
            $expectedTypes = ['title', 'description', 'og_title', 'og_description'];
            $actualTypes = array_column($processedRecommendations, 'type');

            Log::info('AI Recommendations Parsed Successfully', [
                'total_recommendations' => count($processedRecommendations),
                'expected_types' => $expectedTypes,
                'actual_types' => $actualTypes,
                'missing_types' => array_diff($expectedTypes, $actualTypes)
            ]);

            return $processedRecommendations;

        } catch (\Exception $e) {
            Log::error('Failed to parse AI recommendations', [
                'error' => $e->getMessage(),
                'response_preview' => substr($aiResponse, 0, 500),
                'line' => $e->getLine()
            ]);

            return [];
        }
    }

    /**
     * PROCESS INDIVIDUAL RECOMMENDATION
     */
    private function processRecommendation(array $aiRec, int $id, string $language): array
    {
        $type = $aiRec['type'] ?? '';
        $alternatives = $aiRec['alternatives'] ?? [];

        if (empty($alternatives) || !is_array($alternatives)) {
            Log::warning('No alternatives found for recommendation', [
                'type' => $type,
                'recommendation' => $aiRec
            ]);
            return [];
        }

        // Process alternatives
        $processedAlternatives = [];
        foreach ($alternatives as $index => $alt) {
            if (empty($alt['value'])) {
                continue;
            }

            $processedAlternatives[] = [
                'id' => $alt['id'] ?? ($index + 1),
                'value' => $this->sanitizeValue($alt['value'], $type),
                'strategy' => $alt['strategy'] ?? 'AI-generated optimization',
                'score' => intval($alt['score'] ?? 80),
                'character_count' => strlen($alt['value'] ?? ''),
                'is_optimal_length' => $this->isOptimalLength($alt['value'] ?? '', $type)
            ];
        }

        if (empty($processedAlternatives)) {
            return [];
        }

        // Build recommendation structure
        return [
            'id' => $id,
            'type' => $type,
            'title' => $this->getTypeTitle($type),
            'description' => $this->getTypeDescription($type),
            'field_target' => $this->getFieldTarget($type, $language),
            'priority' => $this->getTypePriority($type),
            'impact_score' => $this->calculateImpactScore($type),
            'alternatives' => $processedAlternatives,
            'language' => $language,
            'auto_apply_first' => true // First alternative will be auto-applied
        ];
    }

    /**
     * EXTRACT JSON FROM AI RESPONSE
     */
    private function extractJsonFromResponse(string $response): string
    {
        // Check if response contains ```json block
        if (strpos($response, '```json') !== false) {
            // More robust regex to capture everything between ```json and ```
            $pattern = '/```json\s*([\s\S]*?)\s*```/';
            if (preg_match($pattern, $response, $matches)) {
                $jsonString = trim($matches[1]);
                Log::info('JSON extracted from markdown block', [
                    'extracted_length' => strlen($jsonString),
                    'extracted_preview' => substr($jsonString, 0, 200)
                ]);
            } else {
                // If regex fails, try manual extraction
                $startPos = strpos($response, '```json') + 7; // length of '```json'
                $endPos = strpos($response, '```', $startPos);

                if ($endPos !== false) {
                    $jsonString = trim(substr($response, $startPos, $endPos - $startPos));
                    Log::info('JSON extracted manually from markdown', [
                        'start_pos' => $startPos,
                        'end_pos' => $endPos,
                        'extracted_length' => strlen($jsonString)
                    ]);
                } else {
                    // Last resort: extract between first { and last }
                    $jsonStart = strpos($response, '{');
                    $jsonEnd = strrpos($response, '}');

                    if ($jsonStart === false || $jsonEnd === false || $jsonEnd <= $jsonStart) {
                        return '';
                    }

                    $jsonString = substr($response, $jsonStart, $jsonEnd - $jsonStart + 1);
                    Log::warning('JSON extracted as fallback method');
                }
            }
        } else {
            // Find JSON block in response
            $jsonStart = strpos($response, '{');
            $jsonEnd = strrpos($response, '}');

            if ($jsonStart === false || $jsonEnd === false || $jsonEnd <= $jsonStart) {
                return '';
            }

            $jsonString = substr($response, $jsonStart, $jsonEnd - $jsonStart + 1);
        }

        // Clean up JSON but preserve UTF-8 characters
        $jsonString = trim($jsonString);

        // Only remove control characters (0x00-0x1F) but keep valid UTF-8 (0x80-0xFF)
        $jsonString = preg_replace('/[\x00-\x1F]/', '', $jsonString);

        // Normalize whitespace without affecting content
        $jsonString = preg_replace('/\s*\n\s*/', ' ', $jsonString); // Replace newlines with single space
        $jsonString = preg_replace('/\s*\r\s*/', ' ', $jsonString); // Replace carriage returns
        $jsonString = preg_replace('/\s*\t\s*/', ' ', $jsonString); // Replace tabs
        $jsonString = preg_replace('/\s{2,}/', ' ', $jsonString); // Collapse multiple spaces

        // Ensure proper UTF-8 encoding
        if (!mb_check_encoding($jsonString, 'UTF-8')) {
            $jsonString = mb_convert_encoding($jsonString, 'UTF-8', 'UTF-8');
        }

        // Fix incomplete JSON - if ends with incomplete structure, try to repair
        $jsonString = $this->repairIncompleteJson($jsonString);

        return $jsonString;
    }

    /**
     * REPAIR INCOMPLETE JSON - ENHANCED VERSION
     */
    private function repairIncompleteJson(string $jsonString): string
    {
        $openBraces = substr_count($jsonString, '{');
        $closeBraces = substr_count($jsonString, '}');
        $openBrackets = substr_count($jsonString, '[');
        $closeBrackets = substr_count($jsonString, ']');

        // If JSON looks complete, still clean trailing commas
        if ($openBraces === $closeBraces && $openBrackets === $closeBrackets) {
            return $this->cleanTrailingCommas($jsonString);
        }

        Log::warning('Attempting to repair incomplete JSON', [
            'original_length' => strlen($jsonString),
            'open_braces' => $openBraces,
            'close_braces' => $closeBraces,
            'open_brackets' => $openBrackets,
            'close_brackets' => $closeBrackets,
            'preview' => substr($jsonString, -100)
        ]);

        // Remove trailing incomplete data (incomplete string, number, etc)
        $jsonString = $this->removeTrailingIncompleteData($jsonString);

        // Clean trailing commas before closing
        $jsonString = $this->cleanTrailingCommas($jsonString);

        // Recalculate after cleanup
        $openBraces = substr_count($jsonString, '{');
        $closeBraces = substr_count($jsonString, '}');
        $openBrackets = substr_count($jsonString, '[');
        $closeBrackets = substr_count($jsonString, ']');

        // Close incomplete arrays first
        while ($closeBrackets < $openBrackets) {
            $jsonString = rtrim($jsonString, ', ') . ']';
            $closeBrackets++;
        }

        // Close incomplete objects
        while ($closeBraces < $openBraces) {
            $jsonString = rtrim($jsonString, ', ') . '}';
            $closeBraces++;
        }

        // Final cleanup
        $jsonString = $this->cleanTrailingCommas($jsonString);

        // Validate repair
        $testDecode = json_decode($jsonString, true);
        if ($testDecode !== null && json_last_error() === JSON_ERROR_NONE) {
            Log::info('JSON repaired successfully', [
                'repaired_length' => strlen($jsonString),
                'recommendations_count' => isset($testDecode['recommendations']) ? count($testDecode['recommendations']) : 0
            ]);
            return $jsonString;
        }

        // If repair failed, try alternative approach: find last complete recommendation
        if (strpos($jsonString, '"recommendations"') !== false) {
            $recovered = $this->recoverFromLastCompleteRecommendation($jsonString);
            if ($recovered !== null) {
                Log::info('JSON recovered from last complete recommendation');
                return $recovered;
            }
        }

        Log::warning('JSON repair completed but may still have issues', [
            'repaired_length' => strlen($jsonString),
            'json_error' => json_last_error_msg()
        ]);

        return $jsonString;
    }

    /**
     * CLEAN TRAILING COMMAS FROM JSON
     */
    private function cleanTrailingCommas(string $jsonString): string
    {
        // Remove trailing commas before ] or }
        $jsonString = preg_replace('/,\s*(\]|\})/', '$1', $jsonString);
        return $jsonString;
    }

    /**
     * REMOVE TRAILING INCOMPLETE DATA
     */
    private function removeTrailingIncompleteData(string $jsonString): string
    {
        // Find last complete value (ending with ", ], }, or number)
        $patterns = [
            '/[,\s]*"[^"]*$/s',        // Incomplete string
            '/[,\s]*\d+\.?\d*$/s',     // Incomplete number
            '/[,\s]*[{\[]$/s',         // Opening bracket without closing
        ];

        foreach ($patterns as $pattern) {
            $jsonString = preg_replace($pattern, '', $jsonString);
        }

        return rtrim($jsonString, ', ');
    }

    /**
     * RECOVER FROM LAST COMPLETE RECOMMENDATION
     */
    private function recoverFromLastCompleteRecommendation(string $jsonString): ?string
    {
        // Find all complete recommendation objects
        preg_match_all('/\{\s*"type"\s*:\s*"[^"]+"\s*,\s*"alternatives"\s*:\s*\[[^\]]*\]\s*\}/s', $jsonString, $matches);

        if (empty($matches[0])) {
            return null;
        }

        // Take all complete recommendations
        $completeRecommendations = $matches[0];
        $recommendationsJson = implode(',', $completeRecommendations);

        // Build complete JSON structure
        $recovered = '{"recommendations":[' . $recommendationsJson . ']}';

        // Validate
        if (json_decode($recovered) !== null) {
            return $recovered;
        }

        return null;
    }

    /**
     * SANITIZE VALUE BASED ON TYPE
     */
    private function sanitizeValue(string $value, string $type): string
    {
        // Remove any potential HTML tags
        $value = strip_tags($value);

        // Remove extra whitespace
        $value = trim(preg_replace('/\s+/', ' ', $value));

        // Type-specific sanitization
        switch ($type) {
            case 'title':
            case 'og_title':
                // Ensure title doesn't end with punctuation except question marks
                $value = rtrim($value, '.,!;:');
                if (substr($value, -1) === '?') {
                    // Keep question marks
                }
                break;

            case 'description':
            case 'og_description':
                // Ensure description ends with proper punctuation
                if (!in_array(substr($value, -1), ['.', '!', '?'])) {
                    $value .= '.';
                }
                break;
        }

        return $value;
    }

    /**
     * CHECK IF VALUE HAS OPTIMAL LENGTH FOR TYPE
     */
    private function isOptimalLength(string $value, string $type): bool
    {
        $length = strlen($value);

        return match($type) {
            'title' => $length >= 50 && $length <= 60,
            'description' => $length >= 150 && $length <= 160,
            'og_title' => $length >= 40 && $length <= 70,
            'og_description' => $length >= 120 && $length <= 200,
            default => true
        };
    }

    /**
     * GET TYPE TITLE FOR UI
     */
    private function getTypeTitle(string $type): string
    {
        return match($type) {
            'title' => 'SEO Başlık',
            'description' => 'Meta Açıklama',
            'og_title' => 'Sosyal Medya Başlığı',
            'og_description' => 'Sosyal Medya Açıklaması',
            default => 'SEO Önerisi'
        };
    }

    /**
     * GET TYPE DESCRIPTION FOR UI
     */
    private function getTypeDescription(string $type): string
    {
        return match($type) {
            'title' => 'Arama motorlarında görünecek sayfa başlığınız',
            'description' => 'Arama sonuçlarında gösterilecek açıklama metni',
            'og_title' => 'Sosyal medyada paylaşıldığında görünecek başlık',
            'og_description' => 'Sosyal medya paylaşımlarında görünecek açıklama',
            default => 'SEO optimizasyonu önerisi'
        };
    }

    /**
     * GET FIELD TARGET FOR FORM BINDING
     */
    private function getFieldTarget(string $type, string $language): string
    {
        $langCode = $language;

        return match($type) {
            'title' => "seoDataCache.{$langCode}.seo_title",
            'description' => "seoDataCache.{$langCode}.seo_description",
            'og_title' => "seoDataCache.{$langCode}.og_title",
            'og_description' => "seoDataCache.{$langCode}.og_description",
            default => "seoDataCache.{$langCode}.seo_title"
        };
    }

    /**
     * GET TYPE PRIORITY
     */
    private function getTypePriority(string $type): string
    {
        return match($type) {
            'title' => 'high',
            'description' => 'high',
            'og_title' => 'medium',
            'og_description' => 'medium',
            default => 'medium'
        };
    }

    /**
     * CALCULATE IMPACT SCORE FOR TYPE
     */
    private function calculateImpactScore(string $type): int
    {
        return match($type) {
            'title' => 95,
            'description' => 90,
            'og_title' => 85,
            'og_description' => 80,
            default => 75
        };
    }
}