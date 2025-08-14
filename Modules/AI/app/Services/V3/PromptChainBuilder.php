<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\V3;

/**
 * PromptChainBuilder - V3 ROADMAP Enterprise Service
 * 
 * Advanced prompt chain optimization
 * Template-based prompt composition 
 * Smart variable substitution system
 */
readonly class PromptChainBuilder
{
    public function __construct(
        private \Illuminate\Database\DatabaseManager $database,
        private \Illuminate\Cache\Repository $cache
    ) {}

    /**
     * Feature için optimized prompt chain oluştur
     */
    public function buildChain(int $featureId, array $context = []): array
    {
        $cacheKey = "prompt_chain_{$featureId}_" . md5(serialize($context));
        
        return $this->cache->remember($cacheKey, 600, function() use ($featureId, $context) {
            // Base chain'i başlat
            $chain = [];

            // System prompts ekle (öncelik: sistem)
            $chain = $this->addSystemPrompts($chain);
            
            // Context-specific prompts ekle
            $chain = $this->addContextPrompts($chain, $context['module_type'] ?? null);
            
            // Feature-specific prompts ekle
            $chain = $this->addFeaturePrompts($chain, $featureId);
            
            // Template prompts ekle (eğer template kullanılıyorsa)
            if (isset($context['template_id'])) {
                $chain = $this->addTemplatePrompts($chain, (int)$context['template_id']);
            }
            
            // User-specific prompts ekle
            $chain = $this->addUserPrompts($chain, $context['user_inputs'] ?? []);
            
            // Chain'i optimize et
            $chain = $this->optimizeChain($chain);
            
            // Priority'ye göre sırala
            return $this->sortByPriority($chain);
        });
    }

    /**
     * Sistem-seviyesi prompt'ları ekle
     */
    public function addSystemPrompts(array $chain): array
    {
        $systemPrompts = $this->database->table('ai_prompts')
            ->where('prompt_type', 'system')
            ->where('is_active', true)
            ->whereNull('module_specific') // Global sistem prompt'ları
            ->orderBy('priority', 'desc')
            ->get();

        foreach ($systemPrompts as $prompt) {
            $chain[] = [
                'id' => $prompt->id,
                'type' => 'system',
                'text' => $prompt->prompt_text,
                'priority' => $prompt->priority,
                'variables' => json_decode($prompt->variables ?? '[]', true),
                'is_chainable' => $prompt->is_chainable,
                'source' => 'system'
            ];
        }

        return $chain;
    }

    /**
     * Context'e göre prompt'ları ekle (modül, zaman, kullanıcı tipi)
     */
    public function addContextPrompts(array $chain, ?string $moduleType): array
    {
        // Modül-specific context prompts
        if ($moduleType) {
            $modulePrompts = $this->database->table('ai_prompts')
                ->where('prompt_type', 'context')
                ->where('module_specific', $moduleType)
                ->where('is_active', true)
                ->orderBy('priority', 'desc')
                ->get();

            foreach ($modulePrompts as $prompt) {
                $chain[] = [
                    'id' => $prompt->id,
                    'type' => 'context',
                    'text' => $prompt->prompt_text,
                    'priority' => $prompt->priority,
                    'variables' => json_decode($prompt->variables ?? '[]', true),
                    'module_specific' => $moduleType,
                    'source' => 'module_context'
                ];
            }
        }

        // Zaman bazlı context (sabah/akşam, hafta içi/sonu)
        $timeContext = $this->getTimeContextPrompts();
        $chain = array_merge($chain, $timeContext);

        return $chain;
    }

    /**
     * Feature'a özgü prompt'ları ekle
     */
    public function addFeaturePrompts(array $chain, int $featureId): array
    {
        $featurePrompts = $this->database->table('ai_feature_prompt_relations')
            ->join('ai_prompts', 'ai_prompts.id', '=', 'ai_feature_prompt_relations.prompt_id')
            ->where('ai_feature_prompt_relations.feature_id', $featureId)
            ->where('ai_prompts.is_active', true)
            ->orderBy('ai_feature_prompt_relations.priority', 'desc')
            ->get();

        foreach ($featurePrompts as $prompt) {
            $chain[] = [
                'id' => $prompt->prompt_id,
                'type' => $prompt->prompt_type,
                'text' => $prompt->prompt_text,
                'priority' => $prompt->priority,
                'variables' => json_decode($prompt->variables ?? '[]', true),
                'feature_id' => $featureId,
                'source' => 'feature_specific'
            ];
        }

        return $chain;
    }

    /**
     * Template-based prompt'ları ekle
     */
    public function addTemplatePrompts(array $chain, int $templateId): array
    {
        $template = $this->database->table('ai_prompt_templates')
            ->where('id', $templateId)
            ->where('is_active', true)
            ->first();

        if ($template && $template->prompt_chain) {
            $promptChain = json_decode($template->prompt_chain, true);
            
            foreach ($promptChain as $promptId) {
                $prompt = $this->database->table('ai_prompts')
                    ->where('id', $promptId)
                    ->where('is_active', true)
                    ->first();

                if ($prompt) {
                    $chain[] = [
                        'id' => $prompt->id,
                        'type' => 'template',
                        'text' => $prompt->prompt_text,
                        'priority' => $prompt->priority + 1000, // Template'ler yüksek öncelik
                        'variables' => json_decode($prompt->variables ?? '[]', true),
                        'template_id' => $templateId,
                        'source' => 'template'
                    ];
                }
            }
        }

        return $chain;
    }

    /**
     * Kullanıcı input'larına göre prompt'ları ekle
     */
    public function addUserPrompts(array $chain, array $userInputs): array
    {
        // Tone-based prompts
        if (isset($userInputs['tone'])) {
            $tonePrompts = $this->database->table('ai_prompts')
                ->where('prompt_type', 'tone')
                ->where('is_active', true)
                ->get();

            foreach ($tonePrompts as $prompt) {
                $contextConditions = json_decode($prompt->context_conditions ?? '{}', true);
                
                if (isset($contextConditions['tone']) && $contextConditions['tone'] === $userInputs['tone']) {
                    $chain[] = [
                        'id' => $prompt->id,
                        'type' => 'tone',
                        'text' => $prompt->prompt_text,
                        'priority' => $prompt->priority,
                        'variables' => json_decode($prompt->variables ?? '[]', true),
                        'tone' => $userInputs['tone'],
                        'source' => 'user_tone'
                    ];
                }
            }
        }

        // Length-based prompts
        if (isset($userInputs['length'])) {
            $lengthPrompts = $this->database->table('ai_prompts')
                ->where('prompt_type', 'length')
                ->where('is_active', true)
                ->get();

            foreach ($lengthPrompts as $prompt) {
                $contextConditions = json_decode($prompt->context_conditions ?? '{}', true);
                
                if (isset($contextConditions['length']) && $contextConditions['length'] === $userInputs['length']) {
                    $chain[] = [
                        'id' => $prompt->id,
                        'type' => 'length',
                        'text' => $prompt->prompt_text,
                        'priority' => $prompt->priority,
                        'variables' => json_decode($prompt->variables ?? '[]', true),
                        'length' => $userInputs['length'],
                        'source' => 'user_length'
                    ];
                }
            }
        }

        return $chain;
    }

    /**
     * Chain'i optimize et - duplicate'ları kaldır, çakışanları çöz
     */
    public function optimizeChain(array $chain): array
    {
        $optimized = [];
        $seen = [];

        foreach ($chain as $prompt) {
            // Duplicate ID kontrolü
            if (in_array($prompt['id'], $seen)) {
                continue;
            }

            // Chainable olmayan prompt'lar için kontrol
            if (isset($prompt['is_chainable']) && !$prompt['is_chainable']) {
                // Sadece tek başına kullanılabilir prompt'ları özel değerlendir
                $optimized[] = $prompt;
                $seen[] = $prompt['id'];
                continue;
            }

            // Benzer tip prompt'lar arasında en yüksek priority'li olanı al
            $betterExists = false;
            foreach ($optimized as $existingPrompt) {
                if ($existingPrompt['type'] === $prompt['type'] && 
                    $existingPrompt['priority'] > $prompt['priority']) {
                    $betterExists = true;
                    break;
                }
            }

            if (!$betterExists) {
                $optimized[] = $prompt;
                $seen[] = $prompt['id'];
            }
        }

        return $optimized;
    }

    /**
     * Priority'ye göre sırala
     */
    public function sortByPriority(array $chain): array
    {
        usort($chain, function($a, $b) {
            return $b['priority'] <=> $a['priority']; // Descending order
        });

        return $chain;
    }

    /**
     * Variables'ı user inputs ile substitute et
     */
    public function substituteVariables(array $chain, array $userInputs, array $contextData = []): array
    {
        $allVariables = array_merge($userInputs, $contextData);

        foreach ($chain as &$prompt) {
            $text = $prompt['text'];
            
            // Variables'ı değiştir
            foreach ($prompt['variables'] as $variable) {
                if (isset($allVariables[$variable])) {
                    $text = str_replace(
                        '{' . $variable . '}',
                        $allVariables[$variable],
                        $text
                    );
                }
            }
            
            $prompt['processed_text'] = $text;
        }

        return $chain;
    }

    /**
     * Chain'i tek prompt'a birleştir
     */
    public function combineChain(array $chain): string
    {
        $combinedPrompts = [];
        
        foreach ($chain as $prompt) {
            $text = $prompt['processed_text'] ?? $prompt['text'];
            
            // Boş değilse ekle
            if (trim($text)) {
                $combinedPrompts[] = trim($text);
            }
        }
        
        return implode("\n\n", $combinedPrompts);
    }

    /**
     * Chain debug bilgisi
     */
    public function getChainDebugInfo(array $chain): array
    {
        return [
            'total_prompts' => count($chain),
            'prompt_types' => array_count_values(array_column($chain, 'type')),
            'sources' => array_count_values(array_column($chain, 'source')),
            'highest_priority' => max(array_column($chain, 'priority')),
            'lowest_priority' => min(array_column($chain, 'priority')),
            'average_priority' => round(array_sum(array_column($chain, 'priority')) / count($chain), 2)
        ];
    }

    /**
     * Private helper methods
     */
    private function getTimeContextPrompts(): array
    {
        $currentHour = (int)date('H');
        $isWeekend = in_array(date('w'), [0, 6]); // 0=Sunday, 6=Saturday
        
        $timePrompts = [];
        
        // Sabah/akşam prompts
        if ($currentHour >= 6 && $currentHour < 12) {
            $timeType = 'morning';
        } elseif ($currentHour >= 12 && $currentHour < 18) {
            $timeType = 'afternoon';
        } else {
            $timeType = 'evening';
        }
        
        // Zaman bazlı prompt'ları getir
        $prompts = $this->database->table('ai_prompts')
            ->where('prompt_type', 'context')
            ->where('is_active', true)
            ->get();
            
        foreach ($prompts as $prompt) {
            $conditions = json_decode($prompt->context_conditions ?? '{}', true);
            
            if (isset($conditions['time_of_day']) && $conditions['time_of_day'] === $timeType) {
                $timePrompts[] = [
                    'id' => $prompt->id,
                    'type' => 'context',
                    'text' => $prompt->prompt_text,
                    'priority' => $prompt->priority,
                    'variables' => json_decode($prompt->variables ?? '[]', true),
                    'time_context' => $timeType,
                    'source' => 'time_context'
                ];
            }
        }
        
        return $timePrompts;
    }
}