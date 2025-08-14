<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\V3;

/**
 * UniversalInputManager - V3 ROADMAP Enterprise Service
 * 
 * Form yapısı yönetimi ve context rules
 * Dynamic input generation with module awareness
 * Context-aware form building engine
 */
readonly class UniversalInputManager
{
    public function __construct(
        private \Illuminate\Database\DatabaseManager $database,
        private \Illuminate\Cache\Repository $cache
    ) {}

    /**
     * Form structure'ını context'e göre dinamik oluştur
     */
    public function getFormStructure(int $featureId, array $context = []): array
    {
        $cacheKey = "form_structure_{$featureId}_" . md5(serialize($context));
        
        return $this->cache->remember($cacheKey, 300, function() use ($featureId, $context) {
            // ai_features tablosundan feature bilgilerini al
            $feature = $this->database->table('ai_features')
                ->where('id', $featureId)
                ->first();

            if (!$feature) {
                throw new \Exception("Feature not found: {$featureId}");
            }

            // Context rules'ları uygula
            $contextRules = json_decode($feature->context_rules ?? '{}', true);
            $formStructure = $this->applyContextRules([], $context, $contextRules);

            // Dynamic inputs oluştur
            $inputs = $this->buildDynamicInputs($featureId, $context['module_type'] ?? null);

            return array_merge($formStructure, ['inputs' => $inputs]);
        });
    }

    /**
     * Modül türüne göre dinamik input'lar oluştur
     */
    public function buildDynamicInputs(int $featureId, ?string $moduleType): array
    {
        $baseInputs = [
            'topic' => [
                'type' => 'text',
                'label' => 'Konu/Başlık',
                'required' => true,
                'placeholder' => 'Hangi konuda içerik oluşturmak istiyorsunuz?'
            ],
            'tone' => [
                'type' => 'select',
                'label' => 'Yazım Tonu',
                'options' => [
                    'formal' => 'Resmi',
                    'casual' => 'Samimi',
                    'fun' => 'Eğlenceli',
                    'professional' => 'Profesyonel'
                ],
                'default' => 'professional'
            ],
            'length' => [
                'type' => 'select',
                'label' => 'İçerik Uzunluğu',
                'options' => [
                    'çok_kısa' => 'Çok Kısa (100-200 kelime)',
                    'kısa' => 'Kısa (200-400 kelime)',
                    'orta' => 'Orta (400-800 kelime)',
                    'uzun' => 'Uzun (800-1200 kelime)'
                ],
                'default' => 'orta'
            ]
        ];

        // Modül-specific inputs ekle
        if ($moduleType === 'blog') {
            $baseInputs['category'] = [
                'type' => 'select',
                'label' => 'Kategori',
                'options' => $this->getBlogCategories(),
                'required' => false
            ];
        }

        if ($moduleType === 'seo') {
            $baseInputs['target_keyword'] = [
                'type' => 'text',
                'label' => 'Hedef Anahtar Kelime',
                'required' => true,
                'placeholder' => 'SEO için optimize edilecek anahtar kelime'
            ];
        }

        return $baseInputs;
    }

    /**
     * Context kurallarını uygula
     */
    public function applyContextRules(array $inputs, array $context, array $contextRules): array
    {
        foreach ($contextRules as $rule) {
            // Rule conditions'ları kontrol et
            if ($this->evaluateConditions($rule['conditions'] ?? [], $context)) {
                // Rule actions'ları uygula
                $inputs = $this->applyRuleActions($inputs, $rule['actions'] ?? []);
            }
        }

        return $inputs;
    }

    /**
     * User inputs'ları prompt'lara map et
     */
    public function mapInputsToPrompts(array $userInputs, int $featureId): array
    {
        $mappedPrompts = [];
        
        // Feature'ın prompt chain'ini al
        $prompts = $this->database->table('ai_feature_prompt_relations')
            ->join('ai_prompts', 'ai_prompts.id', '=', 'ai_feature_prompt_relations.prompt_id')
            ->where('ai_feature_prompt_relations.feature_id', $featureId)
            ->orderBy('ai_feature_prompt_relations.priority', 'desc')
            ->get();

        foreach ($prompts as $prompt) {
            // Prompt variables'ını user inputs ile değiştir
            $promptText = $prompt->prompt_text;
            $variables = json_decode($prompt->variables ?? '[]', true);

            foreach ($variables as $variable) {
                if (isset($userInputs[$variable])) {
                    $promptText = str_replace(
                        '{' . $variable . '}',
                        $userInputs[$variable],
                        $promptText
                    );
                }
            }

            $mappedPrompts[] = [
                'id' => $prompt->id,
                'type' => $prompt->prompt_type,
                'text' => $promptText,
                'priority' => $prompt->priority
            ];
        }

        return $mappedPrompts;
    }

    /**
     * Input validation
     */
    public function validateInputs(array $inputs, int $featureId): array
    {
        $errors = [];

        // Required field kontrolü
        foreach ($inputs as $key => $value) {
            if (empty($value) && $this->isRequiredField($key, $featureId)) {
                $errors[$key] = "Bu alan zorunludur.";
            }
        }

        // Length validations
        if (isset($inputs['topic']) && strlen($inputs['topic']) < 3) {
            $errors['topic'] = "Konu en az 3 karakter olmalıdır.";
        }

        return $errors;
    }

    /**
     * Kullanıcı tercihlerini kaydet
     */
    public function saveUserPreferences(int $userId, int $featureId, array $inputs): void
    {
        $this->database->table('ai_user_preferences')->updateOrInsert(
            [
                'user_id' => $userId,
                'feature_id' => $featureId,
                'preference_key' => 'last_inputs'
            ],
            [
                'preference_value' => json_encode($inputs),
                'last_used_values' => json_encode($inputs),
                'usage_count' => $this->database->raw('usage_count + 1'),
                'updated_at' => now()
            ]
        );
    }

    /**
     * Smart defaults - kullanıcının geçmiş tercihlerinden
     */
    public function getSmartDefaults(int $userId, int $featureId): array
    {
        $preferences = $this->database->table('ai_user_preferences')
            ->where('user_id', $userId)
            ->where('feature_id', $featureId)
            ->where('preference_key', 'last_inputs')
            ->first();

        if ($preferences) {
            $lastInputs = json_decode($preferences->last_used_values, true);
            // Son kullanılan tone ve length'i default yap
            return [
                'tone' => $lastInputs['tone'] ?? 'professional',
                'length' => $lastInputs['length'] ?? 'orta'
            ];
        }

        return [];
    }

    /**
     * Private helper methods
     */
    private function evaluateConditions(array $conditions, array $context): bool
    {
        foreach ($conditions as $key => $expectedValue) {
            if (!isset($context[$key]) || $context[$key] !== $expectedValue) {
                return false;
            }
        }
        return true;
    }

    private function applyRuleActions(array $inputs, array $actions): array
    {
        foreach ($actions as $action => $value) {
            if ($action === 'add_field') {
                $inputs[$value['key']] = $value['config'];
            } elseif ($action === 'modify_field') {
                if (isset($inputs[$value['key']])) {
                    $inputs[$value['key']] = array_merge($inputs[$value['key']], $value['config']);
                }
            }
        }
        return $inputs;
    }

    private function getBlogCategories(): array
    {
        return [
            'teknoloji' => 'Teknoloji',
            'is-dunyasi' => 'İş Dünyası',
            'saglik' => 'Sağlık',
            'egitim' => 'Eğitim',
            'spor' => 'Spor'
        ];
    }

    private function isRequiredField(string $fieldKey, int $featureId): bool
    {
        $requiredFields = ['topic', 'target_keyword'];
        return in_array($fieldKey, $requiredFields);
    }
}