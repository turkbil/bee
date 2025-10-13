<?php

namespace Modules\SettingManagement\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * AI Knowledge Base Model
 *
 * Tenant-specific FAQ/Q&A bilgi bankası
 * Her tenant kendi soru-cevaplarını yönetir
 */
class AIKnowledgeBase extends Model
{
    protected $table = 'ai_knowledge_base';

    protected $fillable = [
        'tenant_id',
        'category',
        'question',
        'answer',
        'metadata',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Boot method - Otomatik tenant_id ekle
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->tenant_id) {
                $model->tenant_id = tenant('id');
            }
        });

        // Default scope: Sadece mevcut tenant'ın kayıtları
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (tenant('id')) {
                $builder->where('tenant_id', tenant('id'));
            }
        });
    }

    /**
     * Scope: Sadece aktif kayıtlar
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Kategoriye göre
     */
    public function scopeCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Sıralı
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Metadata getter - Güvenli erişim
     */
    public function getMetadataAttribute($value)
    {
        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }

        return $value ?? [];
    }

    /**
     * Internal note (metadata'dan)
     */
    public function getInternalNoteAttribute(): ?string
    {
        return $this->metadata['internal_note'] ?? null;
    }

    /**
     * Tags (metadata'dan)
     */
    public function getTagsAttribute(): array
    {
        return $this->metadata['tags'] ?? [];
    }

    /**
     * Icon (metadata'dan)
     */
    public function getIconAttribute(): ?string
    {
        return $this->metadata['icon'] ?? 'fas fa-question-circle';
    }

    /**
     * Priority (metadata'dan)
     */
    public function getPriorityAttribute(): string
    {
        return $this->metadata['priority'] ?? 'normal';
    }

    /**
     * Tüm kategorileri getir (unique)
     */
    public static function getCategories(): array
    {
        return static::query()
            ->select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category')
            ->toArray();
    }

    /**
     * Kategori bazında group by
     */
    public static function groupByCategory(): array
    {
        $items = static::active()->ordered()->get();

        $grouped = [];
        foreach ($items as $item) {
            $category = $item->category ?? 'Genel';
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = $item;
        }

        return $grouped;
    }
}
