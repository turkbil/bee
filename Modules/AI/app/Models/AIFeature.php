<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class AIFeature extends Model
{
    use HasFactory;

    protected $table = 'ai_features';

    protected $fillable = [
        'name',
        'slug', 
        'description',
        'emoji',
        'icon',
        'category',
        'response_length',
        'response_format',
        'complexity_level',
        'status',
        'is_system',
        'is_featured',
        'show_in_examples',
        'requires_pro',
        'sort_order',
        'badge_color',
        'requires_input',
        'input_placeholder',
        'button_text',
        'example_inputs',
        'ui_settings',
        'api_settings',
        'validation_rules',
        'meta_title',
        'meta_description',
        'tags',
        'usage_count',
        'last_used_at',
        'avg_rating',
        'rating_count'
    ];

    protected $casts = [
        'example_inputs' => 'array',
        'ui_settings' => 'array',
        'api_settings' => 'array',
        'validation_rules' => 'array',
        'tags' => 'array',
        'is_system' => 'boolean',
        'is_featured' => 'boolean',
        'show_in_examples' => 'boolean',
        'requires_pro' => 'boolean',
        'requires_input' => 'boolean',
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
        return $this->belongsToMany(Prompt::class, 'ai_feature_prompts', 'ai_feature_id', 'ai_prompt_id')
            ->withPivot(['prompt_role', 'priority', 'is_required', 'is_active', 'conditions', 'parameters', 'notes'])
            ->withTimestamps()
            ->orderBy('pivot_priority');
    }

    /**
     * Feature'ın pivot kayıtları
     */
    public function featurePrompts(): HasMany
    {
        return $this->hasMany(AIFeaturePrompt::class, 'ai_feature_id');
    }

    /**
     * Ana prompt'u al
     */
    public function getPrimaryPrompt()
    {
        return $this->prompts()
            ->wherePivot('prompt_role', 'primary')
            ->wherePivot('is_active', true)
            ->first();
    }

    /**
     * Gizli sistem prompt'larını al
     */
    public function getHiddenPrompts()
    {
        return $this->prompts()
            ->wherePivot('prompt_role', 'hidden')
            ->wherePivot('is_active', true)
            ->orderBy('pivot_priority')
            ->get();
    }

    /**
     * Tüm aktif prompt'ları sıralı al
     */
    public function getActivePrompts()
    {
        return $this->prompts()
            ->wherePivot('is_active', true)
            ->orderBy('pivot_priority')
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
     * Kategoriye göre
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
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
     * Kategori ismini Türkçe al
     */
    public function getCategoryName(): string
    {
        $categories = [
            'content' => 'İçerik',
            'creative' => 'Yaratıcı',
            'business' => 'İş Dünyası',
            'technical' => 'Teknik',
            'academic' => 'Akademik',
            'legal' => 'Hukuki',
            'marketing' => 'Pazarlama',
            'analysis' => 'Analiz',
            'communication' => 'İletişim',
            'other' => 'Diğer'
        ];

        return $categories[$this->category] ?? 'Bilinmeyen';
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
}