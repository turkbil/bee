<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\FormBuilder;

use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\AIFeatureInput;
use Modules\AI\App\Models\Prompt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

readonly class UniversalInputManager
{
    /**
     * Feature için tüm form yapısını getir - Multi-layer caching
     */
    public function getFormStructure(int $featureId): array
    {
        // L1 Cache: Memory cache (5 minutes)
        $cacheKey = "ai_form_structure_{$featureId}";
        
        return Cache::remember($cacheKey, 300, function() use ($featureId) {
            // L2 Cache: Feature-level cache with invalidation tracking
            $feature = $this->getCachedFeature($featureId);
            
            // L3 Cache: Structure cache with dependency tracking
            return $this->getCachedFormStructure($feature);
        });
    }
    
    /**
     * Cache feature with invalidation tracking
     */
    private function getCachedFeature(int $featureId): AIFeature
    {
        $cacheKey = "ai_feature_with_inputs_{$featureId}";
        $cacheTime = 1800; // 30 minutes
        
        return Cache::remember($cacheKey, $cacheTime, function() use ($featureId) {
            return AIFeature::with([
                'inputs' => function($query) {
                    $query->orderBy('sort_order')->orderBy('id');
                },
                'inputs.options' => function($query) {
                    $query->orderBy('sort_order')->orderBy('id');
                },
                'inputs.group' => function($query) {
                    $query->orderBy('sort_order');
                },
                'inputs.dynamicSource'
            ])->findOrFail($featureId);
        });
    }
    
    /**
     * Cache formatted structure with dependency tracking
     */
    private function getCachedFormStructure(AIFeature $feature): array
    {
        $cacheKey = "ai_form_formatted_{$feature->id}";
        $cacheTime = 3600; // 1 hour
        
        // Dependency tracking - cache depends on last update of inputs
        $lastUpdate = $feature->inputs->max('updated_at');
        $dependencyKey = "ai_form_dependency_{$feature->id}";
        
        $cachedDependency = Cache::get($dependencyKey);
        
        // If inputs updated, invalidate structure cache
        if ($cachedDependency !== $lastUpdate?->timestamp) {
            Cache::forget($cacheKey);
            Cache::put($dependencyKey, $lastUpdate?->timestamp, $cacheTime);
        }
        
        return Cache::remember($cacheKey, $cacheTime, function() use ($feature) {
            return $this->formatFormStructure($feature);
        });
    }
    
    /**
     * Kullanıcı inputlarını prompt chain'e çevir
     */
    public function mapInputsToPrompts(array $userInputs, int $featureId): array
    {
        $promptIds = [];
        
        foreach ($userInputs as $inputKey => $value) {
            $promptId = $this->getPromptIdForInput($featureId, $inputKey, $value);
            if ($promptId) {
                $promptIds[] = $promptId;
            }
        }
        
        return $this->sortPromptsByPriority($promptIds);
    }
    
    /**
     * Form yapısını formatla
     */
    private function formatFormStructure(AIFeature $feature): array
    {
        return [
            'feature' => [
                'id' => $feature->id,
                'name' => $feature->name,
                'description' => $feature->description,
                'quick_prompt' => $feature->quick_prompt
            ],
            'primary_input' => $this->formatInput($feature->primaryInput),
            'groups' => $this->groupInputsByCategory($feature->inputs),
            'validation_rules' => $this->collectValidationRules($feature->inputs)
        ];
    }
    
    /**
     * Input'u formatla
     */
    private function formatInput($input): ?array
    {
        if (!$input) {
            return null;
        }
        
        return [
            'id' => $input->id,
            'input_key' => $input->slug,
            'input_type' => $input->input_type,
            'label' => $input->label,
            'placeholder' => $input->placeholder,
            'help_text' => $input->help_text,
            'is_required' => $input->is_required,
            'validation_rules' => $input->validation_rules,
            'default_value' => $input->default_value,
            'depends_on' => $input->depends_on,
            'options' => $input->options?->map(function($option) {
                return [
                    'value' => $option->option_value,
                    'label' => $option->option_label,
                    'prompt_id' => $option->prompt_id,
                    'is_default' => $option->is_default,
                    'conditions' => $option->conditions
                ];
            })->toArray(),
            'dynamic_source' => $input->dynamicSource ? [
                'source_type' => $input->dynamicSource->source_type,
                'data' => $input->dynamicSource->getDataOptions()
            ] : null
        ];
    }
    
    /**
     * Input'ları gruplara göre organize et
     */
    private function groupInputsByCategory(Collection $inputs): array
    {
        $grouped = $inputs->whereNotNull('group_key')->groupBy('group_key');
        
        return $grouped->map(function($groupInputs, $groupKey) {
            $firstInput = $groupInputs->first();
            $group = $firstInput->group;
            
            return [
                'key' => $groupKey,
                'name' => $group?->group_name ?? $groupKey,
                'description' => $group?->description,
                'icon' => $group?->getIconClass() ?? 'ti ti-folder',
                'is_collapsible' => $group?->is_collapsible ?? true,
                'is_collapsed_by_default' => $group?->is_collapsed_by_default ?? false,
                'inputs' => $groupInputs->map(function($input) {
                    return $this->formatInput($input);
                })->sortBy('display_order')->values()->toArray()
            ];
        })->sortBy(function($group) {
            return $group['key'] === 'basic' ? 0 : 1;
        })->values()->toArray();
    }
    
    /**
     * Validation kurallarını topla
     */
    private function collectValidationRules(Collection $inputs): array
    {
        $rules = [];
        
        foreach ($inputs as $input) {
            if ($input->validation_rules) {
                $rules[$input->slug] = $input->validation_rules;
            }
        }
        
        return $rules;
    }
    
    /**
     * Input için prompt ID'sini al
     */
    private function getPromptIdForInput(int $featureId, string $inputKey, $value): ?int
    {
        // Input'u bul
        $input = AIFeatureInput::where('feature_id', $featureId)
            ->where('slug', $inputKey)
            ->with('options')
            ->first();
            
        if (!$input || !$input->options) {
            return null;
        }
        
        // Seçilen değere göre prompt ID'sini bul
        foreach ($input->options as $option) {
            if ($option->option_value === $value) {
                // Şartları kontrol et
                if ($option->hasConditions()) {
                    // Şartlı kontrol implementasyonu
                    return $option->prompt_id;
                }
                
                return $option->prompt_id;
            }
        }
        
        return null;
    }
    
    /**
     * Prompt'ları priority'ye göre sırala
     */
    private function sortPromptsByPriority(array $promptIds): array
    {
        if (empty($promptIds)) {
            return [];
        }
        
        $prompts = Prompt::whereIn('id', $promptIds)
            ->orderBy('priority')
            ->get();
            
        return $prompts->pluck('id')->toArray();
    }
    
    /**
     * Form validation kurallarını al
     */
    public function getValidationRules(int $featureId): array
    {
        return Cache::remember("ai_validation_rules_{$featureId}", 3600, function() use ($featureId) {
            $inputs = AIFeatureInput::where('feature_id', $featureId)->get();
            $rules = [];
            
            foreach ($inputs as $input) {
                if ($input->validation_rules) {
                    $rules[$input->slug] = $input->validation_rules;
                }
            }
            
            return $rules;
        });
    }
    
    /**
     * User input'larını validate et
     */
    public function validateInputs(array $userInputs, int $featureId): array
    {
        $rules = $this->getValidationRules($featureId);
        $errors = [];
        
        foreach ($rules as $inputKey => $inputRules) {
            $value = $userInputs[$inputKey] ?? null;
            
            // Validation rules array olmalı
            if (!is_array($inputRules)) {
                $inputRules = is_string($inputRules) ? json_decode($inputRules, true) : [$inputRules];
            }
            
            if (is_array($inputRules)) {
                foreach ($inputRules as $rule) {
                    if (!$this->validateSingleRule($value, (string)$rule)) {
                        $errors[$inputKey][] = $this->getErrorMessage($inputKey, (string)$rule);
                    }
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Tek bir kuralı validate et
     */
    private function validateSingleRule($value, string $rule): bool
    {
        switch ($rule) {
            case 'required':
                return !empty($value);
            
            case str_starts_with($rule, 'min:'):
                $min = (int) substr($rule, 4);
                return strlen((string)$value) >= $min;
            
            case str_starts_with($rule, 'max:'):
                $max = (int) substr($rule, 4);
                return strlen((string)$value) <= $max;
            
            case 'email':
                return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
            
            case 'url':
                return filter_var($value, FILTER_VALIDATE_URL) !== false;
            
            default:
                return true;
        }
    }
    
    /**
     * Hata mesajını al
     */
    private function getErrorMessage(string $inputKey, string $rule): string
    {
        $messages = [
            'required' => "{$inputKey} alanı zorunludur.",
            'email' => "{$inputKey} geçerli bir e-posta adresi olmalıdır.",
            'url' => "{$inputKey} geçerli bir URL olmalıdır.",
        ];
        
        if (str_starts_with($rule, 'min:')) {
            $min = substr($rule, 4);
            return "{$inputKey} en az {$min} karakter olmalıdır.";
        }
        
        if (str_starts_with($rule, 'max:')) {
            $max = substr($rule, 4);
            return "{$inputKey} en fazla {$max} karakter olmalıdır.";
        }
        
        return $messages[$rule] ?? "{$inputKey} geçersiz.";
    }
    
    /**
     * Smart default değerleri al
     */
    public function getSmartDefaults(int $featureId, array $context = []): array
    {
        $inputs = AIFeatureInput::where('feature_id', $featureId)
            ->whereNotNull('default_value')
            ->get();
            
        $defaults = [];
        
        foreach ($inputs as $input) {
            $defaultValue = $input->default_value;
            
            // Context'e göre smart default hesapla
            if (!empty($context)) {
                $defaultValue = $this->calculateSmartDefault($input, $context);
            }
            
            $defaults[$input->slug] = $defaultValue;
        }
        
        return $defaults;
    }
    
    /**
     * Smart default hesapla
     */
    private function calculateSmartDefault(AIFeatureInput $input, array $context): mixed
    {
        // Kullanıcı geçmişine göre smart default
        if (isset($context['user_id'])) {
            $userHistory = Cache::get("user_{$context['user_id']}_preferences");
            if ($userHistory && isset($userHistory[$input->slug])) {
                return $userHistory[$input->slug];
            }
        }
        
        // Feature'a özel smart defaults
        $featureDefaults = [
            'writing_style' => 'professional',
            'content_length' => 'medium',
            'tone' => 'professional',
            'language' => 'tr',
            'seo_optimization' => 'auto'
        ];
        
        return $featureDefaults[$input->slug] ?? $input->default_value;
    }
    
    /**
     * Form cache'ini temizle - Multi-layer cache clearing
     */
    public function clearFormCache(int $featureId): void
    {
        // L1 Cache - Form structure
        Cache::forget("ai_form_structure_{$featureId}");
        
        // L2 Cache - Feature with inputs
        Cache::forget("ai_feature_with_inputs_{$featureId}");
        
        // L3 Cache - Formatted structure and dependencies
        Cache::forget("ai_form_formatted_{$featureId}");
        Cache::forget("ai_form_dependency_{$featureId}");
        
        // Related caches
        Cache::forget("ai_prompt_mappings_{$featureId}");
        Cache::forget("ai_validation_rules_{$featureId}");
        
        // Clear dynamic data source caches that might be used by this feature
        $this->clearDynamicSourceCaches($featureId);
    }
    
    /**
     * Dynamic data source cache'lerini temizle
     */
    private function clearDynamicSourceCaches(int $featureId): void
    {
        // Feature'ın kullandığı dynamic data source'ları bul
        $dynamicSources = AIFeatureInput::where('feature_id', $featureId)
            ->whereNotNull('dynamic_data_source_id')
            ->with('dynamicSource')
            ->get()
            ->pluck('dynamicSource')
            ->filter();
            
        foreach ($dynamicSources as $source) {
            if ($source) {
                Cache::forget("dynamic_data_{$source->slug}");
                Cache::forget("dynamic_data_processed_{$source->id}");
            }
        }
    }
    
    /**
     * Tüm feature'ların form cache'ini temizle
     */
    public function clearAllFormCaches(): void
    {
        $features = AIFeature::pluck('id');
        
        foreach ($features as $featureId) {
            $this->clearFormCache($featureId);
        }
    }
}