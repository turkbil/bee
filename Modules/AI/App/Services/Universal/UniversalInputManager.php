<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Universal;

use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\AIFeatureInput;
use Modules\AI\App\Models\AIPromptTemplate;
use Modules\AI\App\Models\AIUserPreferences;
use Modules\AI\App\Models\AIContextRules;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

readonly class UniversalInputManager
{
    public function __construct(
        private ContextAwareEngine $contextEngine,
        private PromptChainBuilder $promptBuilder
    ) {}

    /**
     * Feature'ın form yapısını getir
     */
    public function getFormStructure(int $featureId, array $context = []): array
    {
        $cacheKey = "universal_form_structure_{$featureId}_" . md5(serialize($context));
        
        return Cache::remember($cacheKey, 300, function () use ($featureId, $context) {
            $feature = AIFeature::with(['inputs.options', 'inputs.group'])->find($featureId);
            
            if (!$feature) {
                throw new \Exception("AI Feature not found: {$featureId}");
            }

            $inputs = $this->buildDynamicInputs($featureId, $context['module_type'] ?? '');
            $contextRules = $this->applyContextRules($inputs, $context);
            
            return [
                'feature' => $feature->toArray(),
                'inputs' => $contextRules,
                'templates' => $this->getAvailableTemplates($feature),
                'context' => $context,
                'validation_rules' => $this->generateValidationRules($contextRules)
            ];
        });
    }

    /**
     * Dinamik input alanları oluştur
     */
    public function buildDynamicInputs(int $featureId, string $moduleType = ''): array
    {
        $inputs = AIFeatureInput::with(['options', 'group'])
            ->where('feature_id', $featureId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return $inputs->groupBy('group.name')->map(function ($groupInputs, $groupName) {
            return [
                'group_name' => $groupName,
                'inputs' => $groupInputs->map(function ($input) {
                    return [
                        'id' => $input->id,
                        'name' => $input->input_name,
                        'type' => $input->input_type,
                        'label' => $input->label,
                        'placeholder' => $input->placeholder,
                        'description' => $input->description,
                        'default_value' => $input->default_value,
                        'validation_rules' => json_decode($input->validation_rules, true),
                        'options' => $input->options->pluck('option_value', 'option_key'),
                        'is_required' => $input->is_required,
                        'conditional_logic' => json_decode($input->conditional_logic, true),
                        'css_classes' => $input->css_classes
                    ];
                })->toArray()
            ];
        })->values()->toArray();
    }

    /**
     * Context kurallarını uygula
     */
    public function applyContextRules(array $inputs, array $context): array
    {
        $rules = AIContextRules::where('is_active', true)
            ->orderBy('priority')
            ->get();

        foreach ($rules as $rule) {
            if ($this->contextEngine->matchesConditions($rule->conditions, $context)) {
                $inputs = $this->applyRuleActions($inputs, json_decode($rule->actions, true));
            }
        }

        return $inputs;
    }

    /**
     * User input'larını prompt'lara map et
     */
    public function mapInputsToPrompts(array $userInputs, int $featureId): array
    {
        $feature = AIFeature::find($featureId);
        $mappings = [];

        foreach ($userInputs as $inputName => $value) {
            $input = AIFeatureInput::where('feature_id', $featureId)
                ->where('input_name', $inputName)
                ->first();

            if ($input && $input->prompt_mapping) {
                $mapping = json_decode($input->prompt_mapping, true);
                $mappings[$mapping['variable']] = $this->processInputValue($value, $input->input_type);
            }
        }

        return $this->promptBuilder->buildChain($featureId, [
            'variables' => $mappings,
            'user_inputs' => $userInputs
        ]);
    }

    /**
     * Input validation kurallarını oluştur
     */
    public function validateInputs(array $inputs, int $featureId): array
    {
        $errors = [];
        
        foreach ($inputs as $inputName => $value) {
            $input = AIFeatureInput::where('feature_id', $featureId)
                ->where('input_name', $inputName)
                ->first();

            if (!$input) continue;

            $rules = json_decode($input->validation_rules, true) ?? [];
            
            // Required validation
            if ($input->is_required && empty($value)) {
                $errors[$inputName][] = "Bu alan zorunludur.";
            }

            // Type specific validation
            $this->validateInputType($inputName, $value, $input->input_type, $rules, $errors);
        }

        return $errors;
    }

    /**
     * Kullanıcı tercihlerini kaydet
     */
    public function saveUserPreferences(int $userId, int $featureId, array $inputs): void
    {
        foreach ($inputs as $inputName => $value) {
            AIUserPreferences::updateOrCreate(
                [
                    'user_id' => $userId,
                    'feature_id' => $featureId,
                    'preference_key' => $inputName
                ],
                [
                    'preference_value' => json_encode($value),
                    'usage_count' => \DB::raw('usage_count + 1'),
                    'last_used_values' => json_encode([
                        'value' => $value,
                        'timestamp' => now()
                    ])
                ]
            );
        }
    }

    /**
     * Akıllı varsayılan değerleri getir
     */
    public function getSmartDefaults(int $userId, int $featureId): array
    {
        $preferences = AIUserPreferences::where('user_id', $userId)
            ->where('feature_id', $featureId)
            ->orderBy('usage_count', 'desc')
            ->get();

        $defaults = [];
        foreach ($preferences as $pref) {
            $lastUsed = json_decode($pref->last_used_values, true);
            if ($lastUsed && isset($lastUsed['value'])) {
                $defaults[$pref->preference_key] = $lastUsed['value'];
            }
        }

        return $defaults;
    }

    /**
     * Private helper methods
     */
    private function getAvailableTemplates(AIFeature $feature): Collection
    {
        return AIPromptTemplate::where('is_active', true)
            ->where(function ($query) use ($feature) {
                $query->where('module_type', $feature->module_type)
                      ->orWhere('category', $feature->category)
                      ->orWhereNull('module_type');
            })
            ->orderBy('usage_count', 'desc')
            ->limit(10)
            ->get();
    }

    private function generateValidationRules(array $inputs): array
    {
        $rules = [];
        
        foreach ($inputs as $group) {
            foreach ($group['inputs'] as $input) {
                $inputRules = [];
                
                if ($input['is_required']) {
                    $inputRules[] = 'required';
                }
                
                if ($input['validation_rules']) {
                    $inputRules = array_merge($inputRules, $input['validation_rules']);
                }
                
                if (!empty($inputRules)) {
                    $rules[$input['name']] = $inputRules;
                }
            }
        }
        
        return $rules;
    }

    private function applyRuleActions(array $inputs, array $actions): array
    {
        foreach ($actions as $action) {
            switch ($action['type']) {
                case 'hide_input':
                    $inputs = $this->hideInput($inputs, $action['target']);
                    break;
                case 'modify_options':
                    $inputs = $this->modifyInputOptions($inputs, $action['target'], $action['options']);
                    break;
                case 'set_default':
                    $inputs = $this->setDefaultValue($inputs, $action['target'], $action['value']);
                    break;
            }
        }
        
        return $inputs;
    }

    private function processInputValue($value, string $inputType)
    {
        return match ($inputType) {
            'number' => (int) $value,
            'boolean' => (bool) $value,
            'json' => is_string($value) ? json_decode($value, true) : $value,
            default => (string) $value
        };
    }

    private function validateInputType(string $inputName, $value, string $type, array $rules, array &$errors): void
    {
        switch ($type) {
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$inputName][] = "Geçerli bir e-posta adresi giriniz.";
                }
                break;
            case 'number':
                if (!is_numeric($value)) {
                    $errors[$inputName][] = "Bu alan sayı olmalıdır.";
                }
                break;
            case 'url':
                if (!filter_var($value, FILTER_VALIDATE_URL)) {
                    $errors[$inputName][] = "Geçerli bir URL giriniz.";
                }
                break;
        }

        // Custom rules validation
        foreach ($rules as $rule => $ruleValue) {
            switch ($rule) {
                case 'min_length':
                    if (strlen($value) < $ruleValue) {
                        $errors[$inputName][] = "En az {$ruleValue} karakter olmalıdır.";
                    }
                    break;
                case 'max_length':
                    if (strlen($value) > $ruleValue) {
                        $errors[$inputName][] = "En fazla {$ruleValue} karakter olmalıdır.";
                    }
                    break;
            }
        }
    }

    private function hideInput(array $inputs, string $targetInput): array
    {
        foreach ($inputs as &$group) {
            $group['inputs'] = array_filter($group['inputs'], function ($input) use ($targetInput) {
                return $input['name'] !== $targetInput;
            });
        }
        
        return $inputs;
    }

    private function modifyInputOptions(array $inputs, string $targetInput, array $newOptions): array
    {
        foreach ($inputs as &$group) {
            foreach ($group['inputs'] as &$input) {
                if ($input['name'] === $targetInput) {
                    $input['options'] = $newOptions;
                }
            }
        }
        
        return $inputs;
    }

    private function setDefaultValue(array $inputs, string $targetInput, $defaultValue): array
    {
        foreach ($inputs as &$group) {
            foreach ($group['inputs'] as &$input) {
                if ($input['name'] === $targetInput) {
                    $input['default_value'] = $defaultValue;
                }
            }
        }
        
        return $inputs;
    }
}