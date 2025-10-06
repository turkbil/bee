<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AIFeature extends Model
{
    use HasFactory;
    protected $connection = 'central';

    protected $table = 'ai_features';
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
    }

    protected $fillable = [
        'name',
        'slug', 
        'description',
        'emoji',
        'icon',
        'ai_feature_category_id',
        'helper_function',
        'helper_examples',
        'helper_parameters',
        'helper_description',
        'helper_returns',
        'hybrid_system_type',
        'has_custom_prompt',
        'has_related_prompts',
        'quick_prompt',
        'response_template',
        'custom_prompt',
        'additional_config',
        'usage_examples',
        'input_validation',
        'settings',
        'error_messages',
        'success_messages',
        'token_cost',
        'response_length',
        'response_format',
        'complexity_level',
        'status',
        'is_system',
        'is_featured',
        'show_in_examples',
        'sort_order',
        'badge_color',
        'requires_input',
        'input_placeholder',
        'button_text',
        'example_inputs',
        'usage_count',
        'last_used_at',
        'avg_rating',
        'rating_count'
    ];

    protected $casts = [
        'response_template' => 'array',
        'additional_config' => 'array',
        'usage_examples' => 'array',
        'input_validation' => 'array',
        'settings' => 'array',
        'error_messages' => 'array',
        'success_messages' => 'array',
        'token_cost' => 'array',
        'example_inputs' => 'array',
        'helper_examples' => 'array',
        'helper_parameters' => 'array',
        'helper_returns' => 'array',
        'is_system' => 'boolean',
        'is_featured' => 'boolean',
        'show_in_examples' => 'boolean',
        'requires_input' => 'boolean',
        'has_custom_prompt' => 'boolean',
        'has_related_prompts' => 'boolean',
        'last_used_at' => 'datetime',
        'avg_rating' => 'decimal:2',
        'usage_count' => 'integer',
        'rating_count' => 'integer',
        'sort_order' => 'integer'
    ];

    /**
     * Feature'a bağlı prompt'lar (many-to-many)
     */
    public function prompts(): BelongsToMany
    {
        return $this->belongsToMany(Prompt::class, 'ai_feature_prompt_relations', 'feature_id', 'prompt_id', 'id', 'prompt_id')
            ->withPivot(['role', 'priority', 'is_active', 'conditions', 'notes'])
            ->withTimestamps()
            ->orderBy('priority');
    }

    /**
     * Feature'ın pivot kayıtları
     */
    public function featurePrompts(): HasMany
    {
        return $this->hasMany(\Modules\AI\App\Models\AIFeaturePromptRelation::class, 'feature_id');
    }

    /**
     * Ana prompt'u al
     */
    public function getPrimaryPrompt()
    {
        return $this->prompts()
            ->wherePivot('role', 'primary')
            ->wherePivot('is_active', true)
            ->first();
    }

    /**
     * Gizli sistem prompt'larını al
     */
    public function getHiddenPrompts()
    {
        return $this->prompts()
            ->wherePivot('role', 'hidden')
            ->wherePivot('is_active', true)
            ->orderBy('priority')
            ->get();
    }

    /**
     * Tüm aktif prompt'ları sıralı al
     */
    public function getActivePrompts()
    {
        return $this->prompts()
            ->wherePivot('is_active', true)
            ->orderBy('priority')
            ->get();
    }

    /**
     * Kullanım sayısını artır
     */
    public function incrementUsage()
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Puan ver
     */
    public function addRating(float $rating)
    {
        $newCount = $this->rating_count + 1;
        $newAvg = (($this->avg_rating * $this->rating_count) + $rating) / $newCount;
        
        $this->update([
            'avg_rating' => round($newAvg, 2),
            'rating_count' => $newCount
        ]);
    }

    // ==================== SCOPE'LAR ====================

    /**
     * Sadece aktif özellikler
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Examples sayfasında gösterilenler
     */
    public function scopeForExamples(Builder $query): Builder
    {
        return $query->where('show_in_examples', true)
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    /**
     * Kategoriye göre (ai_feature_category_id ile)
     */
    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('ai_feature_category_id', $categoryId);
    }

    /**
     * Öne çıkan özellikler
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Sistem özellikleri (silinemez)
     */
    public function scopeSystem(Builder $query): Builder
    {
        return $query->where('is_system', true);
    }

    /**
     * Pro gerektirmeyen özellikler
     */
    public function scopeFree(Builder $query): Builder
    {
        return $query->where('requires_pro', false);
    }

    // ==================== HELPER METODLAR ====================

    /**
     * Badge rengini Bootstrap class olarak al
     */
    public function getBadgeClass(): string
    {
        return 'badge bg-' . $this->badge_color;
    }

    /**
     * Kategori ismini relationship üzerinden al
     */
    public function getCategoryName(): string
    {
        // Manuel kategori ilişkisi - relationship çalışmıyor
        if ($this->ai_feature_category_id) {
            $category = \Modules\AI\App\Models\AIFeatureCategory::where('ai_feature_category_id', $this->ai_feature_category_id)->first();
            return $category ? $category->title : 'Kategorisiz';
        }
        return 'Kategorisiz';
    }

    /**
     * Complexity level'ı Türkçe al
     */
    public function getComplexityName(): string
    {
        $levels = [
            'beginner' => 'Başlangıç',
            'intermediate' => 'Orta',
            'advanced' => 'İleri',
            'expert' => 'Uzman'
        ];

        return $levels[$this->complexity_level] ?? 'Orta';
    }

    /**
     * Silinebilir mi kontrol et
     */
    public function canBeDeleted(): bool
    {
        return !$this->is_system;
    }

    /**
     * Örnek input'ları formatla
     */
    public function getFormattedExamples(): array
    {
        if (!$this->example_inputs || !is_array($this->example_inputs)) {
            return [];
        }

        return collect($this->example_inputs)->map(function ($example) {
            return [
                'text' => $example['text'] ?? '',
                'label' => $example['label'] ?? 'Örnek'
            ];
        })->toArray();
    }

    // ==================== BADGE SİSTEMİ ====================

    /**
     * Feature için özel badge'lar al
     */
    public function getBadges(): array
    {
        $badges = [];

        // Yeni feature (son 30 günde oluşturulmuş)
        if ($this->created_at && $this->created_at->diffInDays(now()) <= 30) {
            $badges[] = [
                'text' => 'YENİ',
                'class' => 'badge bg-success',
                'icon' => 'fas fa-star'
            ];
        }

        // Popüler (usage_count > 100)
        if ($this->usage_count > 100) {
            $badges[] = [
                'text' => 'POPÜLER',
                'class' => 'badge bg-warning',
                'icon' => 'fas fa-fire'
            ];
        }

        // Öne çıkan
        if ($this->is_featured) {
            $badges[] = [
                'text' => 'ÖNERİLEN',
                'class' => 'badge bg-primary',
                'icon' => 'fas fa-thumbs-up'
            ];
        }

        // Yüksek puanlı (avg_rating >= 4.5)
        if ($this->avg_rating >= 4.5 && $this->rating_count >= 10) {
            $badges[] = [
                'text' => 'ÜST PUAN',
                'class' => 'badge bg-info',
                'icon' => 'fas fa-medal'
            ];
        }

        // Beta
        if ($this->status === 'beta') {
            $badges[] = [
                'text' => 'BETA',
                'class' => 'badge bg-secondary',
                'icon' => 'fas fa-flask'
            ];
        }

        // Kategori badge'ları relationship üzerinden
        if ($this->category) {
            $badges[] = [
                'text' => strtoupper($this->category->title),
                'class' => 'badge bg-gradient-secondary',
                'icon' => $this->category->icon ?? 'fas fa-tag'
            ];
        }

        return $badges;
    }

    /**
     * Badge HTML'ini render et
     */
    public function renderBadges(): string
    {
        $badges = $this->getBadges();
        $html = '';

        foreach ($badges as $badge) {
            $html .= sprintf(
                '<span class="%s me-1"><i class="%s me-1"></i>%s</span>',
                $badge['class'],
                $badge['icon'],
                $badge['text']
            );
        }

        return $html;
    }

    // ==================== YENİ TEMPLATE SİSTEMİ ====================
    
    /**
     * Quick prompt var mı?
     */
    public function hasQuickPrompt(): bool
    {
        return !empty($this->quick_prompt);
    }
    
    /**
     * Response template var mı?
     */
    public function hasResponseTemplate(): bool
    {
        return !empty($this->response_template) && is_array($this->response_template);
    }
    
    /**
     * Template'i AI için formatla
     */
    public function getFormattedTemplate(): string
    {
        if (!$this->hasResponseTemplate()) {
            return '';
        }
        
        $template = $this->response_template;
        
        if (isset($template['sections']) && is_array($template['sections'])) {
            $formatted = "YANIT FORMATI (Bu formatı kesinlikle takip et):\n\n";
            
            foreach ($template['sections'] as $section) {
                $formatted .= $section . "\n";
            }
            
            // Özel formatlar
            if (isset($template['format'])) {
                $formatted .= "\nÖZEL FORMAT: " . $template['format'] . "\n";
            }
            
            if (isset($template['scoring']) && $template['scoring']) {
                $formatted .= "\nMUTLAKA PUANLAMA EKLE: Sonunda % puanı ver\n";
            }
            
            return $formatted;
        }
        
        return '';
    }
    
    /**
     * Yeni sistem için final prompt oluştur
     */
    public function buildNewSystemPrompt(array $userInput = []): string
    {
        $promptParts = [];
        
        // 1. Quick Prompt (NE yapacağı)
        if ($this->hasQuickPrompt()) {
            $promptParts[] = "=== GÖREV TANIMI ===\n" . $this->quick_prompt;
        }
        
        // 2. Response Template (NASIL görünecek)
        if ($this->hasResponseTemplate()) {
            $promptParts[] = "=== YANIT FORMATI ===\n" . $this->getFormattedTemplate();
        }
        
        // 3. Expert Prompt'lar (NASIL yapacağı - priority sırasına göre)
        $expertPrompts = $this->prompts()
            ->wherePivot('is_active', true)
            ->where('prompt_type', 'standard')
            ->orderBy('ai_feature_prompts.priority', 'asc')
            ->get();
            
        foreach ($expertPrompts as $prompt) {
            $role = $prompt->pivot->role ?? 'primary';
            $promptParts[] = "=== UZMAN BİLGİSİ ({$role}) ===\n" . $prompt->content;
        }
        
        // 4. Final prompt birleştir
        $finalPrompt = implode("\n\n" . str_repeat("-", 50) . "\n\n", $promptParts);
        
        // 5. User input placeholders
        if (!empty($userInput['content'])) {
            $finalPrompt .= "\n\n=== KULLANICI GİRDİSİ ===\n" . $userInput['content'];
        }
        
        return $finalPrompt;
    }

    // ==================== CUSTOM PROMPT SİSTEMİ ====================

    /**
     * Custom prompt var mı kontrol et
     */
    public function hasCustomPrompt(): bool
    {
        return !empty($this->custom_prompt);
    }

    /**
     * Template seçimi gerekiyor mu?
     */
    public function requiresTemplateSelection(): bool
    {
        return isset($this->additional_config['template_selection']) && $this->additional_config['template_selection'] === true;
    }

    /**
     * Dil seçimi gerekiyor mu?
     */
    public function requiresLanguageSelection(): bool
    {
        return isset($this->additional_config['language_selection']) && $this->additional_config['language_selection'] === true;
    }

    /**
     * Template seçeneklerini al
     */
    public function getTemplateOptions(): array
    {
        return $this->additional_config['template_options'] ?? [];
    }

    /**
     * Helper function'ı çalıştırılabilir mi?
     */
    public function hasHelperFunction(): bool
    {
        return !empty($this->helper_function) && function_exists($this->helper_function);
    }

    // ==================== HYBRID PROMPT SYSTEM ====================

    /**
     * Hybrid Prompt Builder - En önemli metod!
     * Hem custom_prompt hem de ilişkili prompts'ları akıllıca birleştirir
     */
    public function buildFinalPrompt(array $conditions = [], array $userInput = []): string
    {
        $promptChain = [];
        
        // 1. CUSTOM PROMPT (Basit ve hızlı)
        if (!empty($this->custom_prompt)) {
            $promptChain[] = [
                'role' => 'custom',
                'priority' => 0,
                'content' => $this->custom_prompt
            ];
        }
        
        // 2. İLİŞKİLİ PROMPTS (Gelişmiş ve detaylı)
        $relatedPrompts = $this->prompts()
            ->wherePivot('is_active', true)
            ->orderBy('priority', 'asc')
            ->get();
            
        foreach ($relatedPrompts as $prompt) {
            $pivotData = $prompt->pivot;
            
            // Şartları kontrol et
            if ($this->checkPromptConditions($pivotData->conditions, $conditions, $userInput)) {
                $promptChain[] = [
                    'role' => $pivotData->prompt_role,
                    'priority' => $pivotData->priority,
                    'content' => $prompt->content,
                    'parameters' => $pivotData->parameters,
                    'is_required' => $pivotData->is_required
                ];
            }
        }
        
        // 3. PRİORİTY SIRASINA GÖRE SIRALA
        usort($promptChain, function($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });
        
        // 4. PROMPT CHAIN'İ BİRLEŞTİR
        $finalPromptParts = [];
        
        foreach ($promptChain as $promptItem) {
            $content = $promptItem['content'];
            
            // Role bazlı özel işlemler
            switch ($promptItem['role']) {
                case 'system':
                    $finalPromptParts[] = "=== SYSTEM DIRECTIVE ===\n" . $content;
                    break;
                case 'custom':
                    $finalPromptParts[] = "=== FEATURE PROMPT ===\n" . $content;
                    break;
                case 'primary':
                    $finalPromptParts[] = "=== PRIMARY INSTRUCTION ===\n" . $content;
                    break;
                case 'formatting':
                    $finalPromptParts[] = "=== OUTPUT FORMAT ===\n" . $content;
                    break;
                case 'validation':
                    $finalPromptParts[] = "=== VALIDATION RULES ===\n" . $content;
                    break;
                default:
                    $finalPromptParts[] = $content;
            }
        }
        
        // 5. FİNAL PROMPT'U OLUŞTUR
        $finalPrompt = implode("\n\n" . str_repeat("-", 50) . "\n\n", $finalPromptParts);
        
        // 6. USER INPUT PLACEHOLDER'LARI DEĞİŞTİR
        $finalPrompt = $this->replacePlaceholders($finalPrompt, $userInput);
        
        return $finalPrompt;
    }
    
    /**
     * Prompt şartlarını kontrol et
     */
    private function checkPromptConditions($conditions, array $contextConditions, array $userInput): bool
    {
        if (empty($conditions)) {
            return true; // Şart yoksa her zaman çalışır
        }
        
        $conditions = is_string($conditions) ? json_decode($conditions, true) : $conditions;
        
        if (!is_array($conditions)) {
            return true;
        }
        
        // Input length kontrolü
        if (isset($conditions['input_length'])) {
            $inputLength = isset($userInput['content']) ? strlen($userInput['content']) : 0;
            
            if (isset($conditions['input_length']['min']) && $inputLength < $conditions['input_length']['min']) {
                return false;
            }
            
            if (isset($conditions['input_length']['max']) && $inputLength > $conditions['input_length']['max']) {
                return false;
            }
        }
        
        // Language kontrolü
        if (isset($conditions['language']) && isset($contextConditions['language'])) {
            if (!in_array($contextConditions['language'], $conditions['language'])) {
                return false;
            }
        }
        
        // User level kontrolü
        if (isset($conditions['user_level']) && isset($contextConditions['user_level'])) {
            if (!in_array($contextConditions['user_level'], $conditions['user_level'])) {
                return false;
            }
        }
        
        // Content type kontrolü
        if (isset($conditions['content_type']) && isset($contextConditions['content_type'])) {
            if (!in_array($contextConditions['content_type'], $conditions['content_type'])) {
                return false;
            }
        }
        
        return true; // Tüm şartları geçti
    }
    
    /**
     * Placeholder'ları değiştir
     */
    private function replacePlaceholders(string $prompt, array $userInput): string
    {
        $placeholders = [
            '{USER_INPUT}' => $userInput['content'] ?? '',
            '{USER_LANGUAGE}' => $userInput['language'] ?? 'Turkish',
            '{USER_LEVEL}' => $userInput['level'] ?? 'intermediate',
            '{CONTENT_TYPE}' => $userInput['type'] ?? 'general',
            '{TARGET_AUDIENCE}' => $userInput['audience'] ?? 'general',
            '{WORD_COUNT}' => $userInput['word_count'] ?? '500',
            '{TONE}' => $userInput['tone'] ?? 'professional'
        ];
        
        return str_replace(array_keys($placeholders), array_values($placeholders), $prompt);
    }

    /**
     * Prompt zincirinin önizlemesini al (debug için)
     */
    public function getPromptChainPreview(): array
    {
        $chain = [];
        
        // Custom prompt
        if (!empty($this->custom_prompt)) {
            $chain[] = [
                'type' => 'Custom Prompt',
                'priority' => 0,
                'length' => strlen($this->custom_prompt),
                'preview' => substr($this->custom_prompt, 0, 100) . '...'
            ];
        }
        
        // Related prompts
        $relatedPrompts = $this->prompts()
            ->wherePivot('is_active', true)
            ->orderBy('priority', 'asc')
            ->get();
            
        foreach ($relatedPrompts as $prompt) {
            $chain[] = [
                'type' => 'Related Prompt (' . $prompt->pivot->prompt_role . ')',
                'priority' => $prompt->pivot->priority,
                'length' => strlen($prompt->content),
                'preview' => substr($prompt->content, 0, 100) . '...',
                'required' => $prompt->pivot->is_required
            ];
        }
        
        return $chain;
    }

    /**
     * Bu feature'ın toplam prompt gücünü hesapla
     */
    public function getPromptPowerScore(): int
    {
        $score = 0;
        
        // Custom prompt varsa +50
        if (!empty($this->custom_prompt)) {
            $score += 50;
        }
        
        // Her related prompt için +100
        $relatedCount = $this->prompts()->wherePivot('is_active', true)->count();
        $score += $relatedCount * 100;
        
        return $score;
    }

    /**
     * Prompt system aktif mi?
     */
    public function hasAdvancedPromptSystem(): bool
    {
        return $this->prompts()->wherePivot('is_active', true)->exists();
    }

    // ==================== HELPER SYSTEM METHODS ====================

    /**
     * Helper fonksiyon çalışır durumda mı?
     */
    public function isHelperFunctionAvailable(): bool
    {
        return !empty($this->helper_function) && function_exists($this->helper_function);
    }

    /**
     * Helper çalıştırma örneği al
     */
    public function getHelperExample(string $type = 'basic'): ?array
    {
        if (!$this->helper_examples) {
            return null;
        }

        return $this->helper_examples[$type] ?? $this->helper_examples['basic'] ?? null;
    }

    /**
     * Helper parametrelerini al
     */
    public function getHelperParameters(): array
    {
        return $this->helper_parameters ?? [];
    }

    /**
     * Hybrid sistem tipi bilgisi
     */
    public function getHybridSystemInfo(): array
    {
        return [
            'type' => $this->hybrid_system_type,
            'has_custom_prompt' => $this->has_custom_prompt,
            'has_related_prompts' => $this->has_related_prompts,
            'is_advanced' => $this->hybrid_system_type === 'advanced',
            'prompt_power' => $this->getPromptPowerScore()
        ];
    }

    /**
     * Helper kullanım kılavuzu oluştur
     */
    public function generateHelperGuide(): array
    {
        $guide = [
            'function_name' => $this->helper_function,
            'description' => $this->helper_description,
            'parameters' => $this->getHelperParameters(),
            'examples' => $this->helper_examples ?? [],
            'returns' => $this->helper_returns ?? [],
            'token_info' => $this->token_cost ?? [],
            'hybrid_info' => $this->getHybridSystemInfo()
        ];

        return $guide;
    }

    /**
     * Helper örnek kodunu formatla
     */
    public function getFormattedHelperExample(string $type = 'basic'): ?string
    {
        $example = $this->getHelperExample($type);
        if (!$example) {
            return null;
        }

        $formatted = "// {$example['description']}\n";
        $formatted .= "// Tahmini token: {$example['estimated_tokens']}\n";
        $formatted .= $example['code'];

        return $formatted;
    }

    /**
     * Tüm helper örneklerini al
     */
    public function getAllHelperExamples(): array
    {
        if (!$this->helper_examples) {
            return [];
        }

        $formatted = [];
        foreach ($this->helper_examples as $type => $example) {
            $formatted[$type] = $this->getFormattedHelperExample($type);
        }

        return $formatted;
    }

    /**
     * Feature'ın kategori ilişkisi
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(AIFeatureCategory::class, 'ai_feature_category_id', 'ai_feature_category_id');
    }

    // ==================== UNIVERSAL INPUT SYSTEM ====================

    /**
     * Feature'a ait input'ları al
     */
    public function inputs(): HasMany
    {
        return $this->hasMany(AIFeatureInput::class, 'feature_id')->orderBy('sort_order');
    }

    /**
     * Ana input'u al (accordion dışında gösterilen)
     */
    public function primaryInput(): HasOne
    {
        return $this->hasOne(AIFeatureInput::class, 'feature_id')->where('is_primary', true);
    }

    /**
     * Gruplandırılmış input'ları al (accordion içindekiler)
     */
    public function groupedInputs(): HasMany
    {
        return $this->hasMany(AIFeatureInput::class, 'feature_id')->whereNotNull('group_id')->with('group');
    }

    /**
     * Gerekli input'ları al
     */
    public function requiredInputs(): HasMany
    {
        return $this->hasMany(AIFeatureInput::class, 'feature_id')->where('is_required', true);
    }

    /**
     * Feature'ın form yapısını al (Universal Input System)
     */
    public function getFormStructure(): array
    {
        $primaryInput = $this->primaryInput()->with('options', 'dynamicSource')->first();
        $groupedInputs = $this->groupedInputs()->with(['options', 'dynamicSource', 'group'])->get();
        
        // Input'ları gruplara göre organize et
        $groups = [];
        foreach ($groupedInputs as $input) {
            $groupKey = $input->group_key;
            if (!isset($groups[$groupKey])) {
                $groups[$groupKey] = [
                    'group' => $input->group,
                    'inputs' => []
                ];
            }
            $groups[$groupKey]['inputs'][] = $input;
        }

        return [
            'feature' => [
                'id' => $this->id,
                'name' => $this->name,
                'description' => $this->description,
                'quick_prompt' => $this->quick_prompt
            ],
            'primary_input' => $primaryInput ? [
                'id' => $primaryInput->id,
                'input_key' => $primaryInput->input_key,
                'input_type' => $primaryInput->input_type,
                'label' => $primaryInput->label,
                'placeholder' => $primaryInput->placeholder,
                'help_text' => $primaryInput->help_text,
                'is_required' => $primaryInput->is_required,
                'validation_rules' => $primaryInput->validation_rules,
                'default_value' => $primaryInput->default_value,
                'options' => $primaryInput->options,
                'dynamic_source' => $primaryInput->dynamicSource
            ] : null,
            'groups' => collect($groups)->map(function($group, $key) {
                return [
                    'key' => $key,
                    'name' => $group['group']->group_name ?? $key,
                    'description' => $group['group']->description ?? null,
                    'icon' => $group['group']->getIconClass() ?? 'ti ti-folder',
                    'is_collapsible' => $group['group']->is_collapsible ?? true,
                    'is_collapsed_by_default' => $group['group']->is_collapsed_by_default ?? false,
                    'inputs' => collect($group['inputs'])->map(function($input) {
                        return [
                            'id' => $input->id,
                            'input_key' => $input->input_key,
                            'input_type' => $input->input_type,
                            'label' => $input->label,
                            'placeholder' => $input->placeholder,
                            'help_text' => $input->help_text,
                            'is_required' => $input->is_required,
                            'validation_rules' => $input->validation_rules,
                            'default_value' => $input->default_value,
                            'depends_on' => $input->depends_on,
                            'display_order' => $input->display_order,
                            'options' => $input->options,
                            'dynamic_source' => $input->dynamicSource
                        ];
                    })->sortBy('display_order')->values()->toArray()
                ];
            })->sortBy('group.display_order')->values()->toArray(),
            'validation_rules' => $this->collectValidationRules()
        ];
    }

    /**
     * Tüm validation kurallarını topla
     */
    private function collectValidationRules(): array
    {
        $rules = [];
        
        foreach ($this->inputs as $input) {
            if ($input->validation_rules) {
                $rules[$input->input_key] = $input->validation_rules;
            }
        }

        return $rules;
    }

    /**
     * Feature'ın form yapısında değişiklik oldu mu kontrol et
     */
    public function hasFormStructureChanged(): bool
    {
        return $this->inputs()->exists() && $this->updated_at > $this->inputs()->max('updated_at');
    }

    /**
     * Universal Input System için cache key oluştur
     */
    public function getFormCacheKey(): string
    {
        return "ai_form_structure_{$this->id}";
    }

    /**
     * Form cache'ini temizle
     */
    public function clearFormCache(): void
    {
        \Illuminate\Support\Facades\Cache::forget($this->getFormCacheKey());
        \Illuminate\Support\Facades\Cache::forget("ai_prompt_mappings_{$this->id}");
        \Illuminate\Support\Facades\Cache::forget("ai_validation_rules_{$this->id}");
    }
}