<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Prompts;

use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\Prompt;
use Modules\AI\App\Models\AIPromptTemplate;
use Modules\AI\App\Models\AIContextRules;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

readonly class PromptChainBuilder
{
    /**
     * Prompt chain oluştur
     */
    public function buildChain(int $featureId, array $context): array
    {
        $cacheKey = "prompt_chain_{$featureId}_" . md5(serialize($context));
        
        return Cache::remember($cacheKey, 600, function () use ($featureId, $context) {
            $feature = AIFeature::with(['prompts'])->find($featureId);
            
            if (!$feature) {
                throw new \Exception("AI Feature not found: {$featureId}");
            }

            $chain = [];
            
            // 1. System prompts (her zaman ilk)
            $chain = $this->addSystemPrompts($chain, $feature);
            
            // 2. Feature quick prompt
            if ($feature->quick_prompt) {
                $chain[] = [
                    'type' => 'quick',
                    'priority' => 100,
                    'content' => $feature->quick_prompt,
                    'variables' => []
                ];
            }
            
            // 3. Context-aware prompts
            $chain = $this->addContextPrompts($chain, $context['module_type'] ?? '', $featureId);
            
            // 4. Expert prompts (priority sırasına göre)
            $chain = $this->addExpertPrompts($chain, $feature);
            
            // 5. User input prompts
            if (isset($context['user_inputs'])) {
                $chain = $this->addUserPrompts($chain, $context['user_inputs']);
            }
            
            // 6. Template prompts
            if (isset($context['template_id'])) {
                $chain = $this->addTemplatePrompts($chain, (int) $context['template_id']);
            }
            
            // Optimize and sort
            $chain = $this->optimizeChain($chain);
            $chain = $this->sortByPriority($chain);
            
            return [
                'prompts' => $chain,
                'total_prompts' => count($chain),
                'estimated_tokens' => $this->estimateTokens($chain),
                'variables' => $context['variables'] ?? [],
                'context' => $context
            ];
        });
    }

    /**
     * System prompt'ları ekle
     */
    public function addSystemPrompts(array $chain): array
    {
        $systemPrompts = Prompt::where('prompt_type', 'system')
            ->where('is_active', true)
            ->orderBy('priority')
            ->get();

        foreach ($systemPrompts as $prompt) {
            $chain[] = [
                'id' => $prompt->id,
                'type' => 'system',
                'priority' => $prompt->priority,
                'content' => $prompt->prompt_text,
                'variables' => json_decode($prompt->variables, true) ?? [],
                'is_chainable' => $prompt->is_chainable
            ];
        }
        
        return $chain;
    }

    /**
     * Context-aware prompt'ları ekle
     */
    public function addContextPrompts(array $chain, string $moduleType, int $featureId): array
    {
        if (empty($moduleType)) {
            return $chain;
        }

        $contextPrompts = Prompt::where('module_specific', $moduleType)
            ->where('is_active', true)
            ->whereJsonContains('applies_to', $featureId)
            ->orWhereNull('applies_to')
            ->orderBy('priority')
            ->get();

        foreach ($contextPrompts as $prompt) {
            $chain[] = [
                'id' => $prompt->id,
                'type' => 'context',
                'priority' => $prompt->priority,
                'content' => $prompt->prompt_text,
                'variables' => json_decode($prompt->variables, true) ?? [],
                'module_specific' => $prompt->module_specific,
                'is_chainable' => $prompt->is_chainable
            ];
        }

        return $chain;
    }

    /**
     * Expert prompt'ları ekle
     */
    public function addExpertPrompts(array $chain, AIFeature $feature): array
    {
        if (!$feature->expert_prompt_id) {
            return $chain;
        }

        $expertPrompt = Prompt::find($feature->expert_prompt_id);
        
        if ($expertPrompt && $expertPrompt->is_active) {
            $chain[] = [
                'id' => $expertPrompt->id,
                'type' => 'expert',
                'priority' => $expertPrompt->priority,
                'content' => $expertPrompt->prompt_text,
                'variables' => json_decode($expertPrompt->variables, true) ?? [],
                'is_chainable' => $expertPrompt->is_chainable
            ];
        }

        return $chain;
    }

    /**
     * User input prompt'ları ekle
     */
    public function addUserPrompts(array $chain, array $userInputs): array
    {
        if (empty($userInputs)) {
            return $chain;
        }

        $userPromptText = $this->buildUserPromptFromInputs($userInputs);
        
        $chain[] = [
            'type' => 'user_input',
            'priority' => 500,
            'content' => $userPromptText,
            'variables' => array_keys($userInputs),
            'user_data' => $userInputs,
            'is_chainable' => true
        ];

        return $chain;
    }

    /**
     * Template prompt'ları ekle
     */
    public function addTemplatePrompts(array $chain, int $templateId): array
    {
        $template = AIPromptTemplate::find($templateId);
        
        if (!$template || !$template->is_active) {
            return $chain;
        }

        $templatePrompts = json_decode($template->prompt_chain, true) ?? [];
        
        foreach ($templatePrompts as $promptId) {
            $prompt = Prompt::find($promptId);
            
            if ($prompt && $prompt->is_active) {
                $chain[] = [
                    'id' => $prompt->id,
                    'type' => 'template',
                    'priority' => $prompt->priority,
                    'content' => $prompt->prompt_text,
                    'variables' => json_decode($prompt->variables, true) ?? [],
                    'template_id' => $templateId,
                    'is_chainable' => $prompt->is_chainable
                ];
            }
        }

        return $chain;
    }

    /**
     * Chain'i optimize et (duplicate'ları kaldır)
     */
    public function optimizeChain(array $chain): array
    {
        $seen = [];
        $optimized = [];
        
        foreach ($chain as $prompt) {
            $hash = md5($prompt['content']);
            
            if (!isset($seen[$hash])) {
                $seen[$hash] = true;
                $optimized[] = $prompt;
            } else {
                // Duplicate bulundu, priority'si yüksek olan kalır
                foreach ($optimized as &$existing) {
                    if (md5($existing['content']) === $hash) {
                        if ($prompt['priority'] < $existing['priority']) {
                            $existing = $prompt;
                        }
                        break;
                    }
                }
            }
        }
        
        return $optimized;
    }

    /**
     * Priority'ye göre sırala
     */
    public function sortByPriority(array $chain): array
    {
        usort($chain, function ($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });
        
        return $chain;
    }

    /**
     * Variables'ları prompt'a inject et
     */
    public function injectVariables(array $chain, array $variables): array
    {
        foreach ($chain as &$prompt) {
            $content = $prompt['content'];
            
            foreach ($variables as $key => $value) {
                $placeholder = '{{' . $key . '}}';
                $content = str_replace($placeholder, (string) $value, $content);
            }
            
            $prompt['processed_content'] = $content;
        }
        
        return $chain;
    }

    /**
     * Final prompt text oluştur
     */
    public function buildFinalPrompt(array $chain, array $variables = []): string
    {
        $chain = $this->injectVariables($chain, $variables);
        
        $promptParts = [];
        
        foreach ($chain as $prompt) {
            if ($prompt['is_chainable'] ?? true) {
                $promptParts[] = $prompt['processed_content'] ?? $prompt['content'];
            }
        }
        
        return implode("\n\n", $promptParts);
    }

    /**
     * Chain'in token sayısını tahmin et
     */
    private function estimateTokens(array $chain): int
    {
        $totalLength = 0;
        
        foreach ($chain as $prompt) {
            $totalLength += strlen($prompt['content']);
        }
        
        // Ortalama 4 karakter = 1 token
        return (int) ceil($totalLength / 4);
    }

    /**
     * User input'lardan prompt text oluştur
     */
    private function buildUserPromptFromInputs(array $userInputs): string
    {
        $parts = [];
        
        foreach ($userInputs as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            
            $parts[] = ucfirst(str_replace('_', ' ', $key)) . ': ' . $value;
        }
        
        return "Kullanıcı girdileri:\n" . implode("\n", $parts);
    }

    /**
     * Context conditions'ı kontrol et
     */
    public function matchesContextConditions(array $conditions, array $context): bool
    {
        foreach ($conditions as $key => $expectedValue) {
            if (!isset($context[$key])) {
                return false;
            }
            
            $actualValue = $context[$key];
            
            if (is_array($expectedValue)) {
                if (!in_array($actualValue, $expectedValue)) {
                    return false;
                }
            } else {
                if ($actualValue !== $expectedValue) {
                    return false;
                }
            }
        }
        
        return true;
    }

    /**
     * Response template'i uygula
     */
    public function applyResponseTemplate(AIFeature $feature, string $aiResponse): array
    {
        if (empty($feature->response_template)) {
            return ['raw_response' => $aiResponse];
        }
        
        $template = json_decode($feature->response_template, true);
        
        if (!$template) {
            return ['raw_response' => $aiResponse];
        }
        
        // Template'e göre response'u formatla
        return $this->formatResponseWithTemplate($aiResponse, $template);
    }

    /**
     * Response'u template ile formatla
     */
    private function formatResponseWithTemplate(string $response, array $template): array
    {
        $formatted = [
            'raw_response' => $response,
            'template_applied' => true,
            'template_config' => $template
        ];
        
        // Template tipine göre işle
        if (isset($template['sections'])) {
            $formatted['sections'] = $this->extractSections($response, $template['sections']);
        }
        
        if (isset($template['format']) && $template['format'] === 'json') {
            $formatted['json_data'] = $this->tryParseJson($response);
        }
        
        if (isset($template['scoring']) && $template['scoring']) {
            $formatted['score'] = $this->calculateScore($response);
        }
        
        return $formatted;
    }

    /**
     * Response'dan section'ları çıkar
     */
    private function extractSections(string $response, array $sectionNames): array
    {
        $sections = [];
        
        foreach ($sectionNames as $sectionName) {
            $pattern = '/(' . preg_quote($sectionName) . ':?\s*)(.*?)(?=\n\w+:|$)/is';
            
            if (preg_match($pattern, $response, $matches)) {
                $sections[$sectionName] = trim($matches[2]);
            } else {
                $sections[$sectionName] = '';
            }
        }
        
        return $sections;
    }

    /**
     * JSON parse etmeye çalış
     */
    private function tryParseJson(string $response): ?array
    {
        $json = json_decode($response, true);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            return $json;
        }
        
        return null;
    }

    /**
     * Response'un score'unu hesapla
     */
    private function calculateScore(string $response): int
    {
        $score = 0;
        
        // Uzunluk skorlaması
        $length = strlen($response);
        if ($length > 100) $score += 20;
        if ($length > 500) $score += 20;
        if ($length > 1000) $score += 20;
        
        // Yapılandırılmışlık skorlaması
        if (strpos($response, "\n") !== false) $score += 10;
        if (preg_match('/\d+\.|\-|\*/', $response)) $score += 10;
        if (preg_match('/[A-Z][a-z]+:/', $response)) $score += 20;
        
        return min($score, 100);
    }
}