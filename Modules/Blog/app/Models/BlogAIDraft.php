<?php

namespace Modules\Blog\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Blog AI Draft Model
 *
 * AI tarafından oluşturulan blog taslakları
 * Admin bu taslakları seçer, seçilenler tam blog yazısına dönüştürülür
 */
class BlogAIDraft extends Model
{
    protected $table = 'blog_ai_drafts';

    protected $fillable = [
        'topic_keyword',
        'category_suggestions',
        'seo_keywords',
        'outline',
        'meta_description',
        'is_selected',
        'is_generated',
        'generated_blog_id',
    ];

    protected $casts = [
        'category_suggestions' => 'array',
        'seo_keywords' => 'array',
        'outline' => 'array',
        'is_selected' => 'boolean',
        'is_generated' => 'boolean',
    ];

    /**
     * İlişki: Oluşturulan blog
     */
    public function generatedBlog(): BelongsTo
    {
        return $this->belongsTo(Blog::class, 'generated_blog_id', 'blog_id');
    }

    /**
     * Accessor: Topic başlığı
     */
    public function getTopicAttribute()
    {
        return $this->topic_keyword;
    }

    /**
     * Scope: Seçilen taslaklar
     */
    public function scopeSelected($query)
    {
        return $query->where('is_selected', true);
    }

    /**
     * Scope: Henüz blog yazılmamış seçili taslaklar
     */
    public function scopePending($query)
    {
        return $query->where('is_selected', true)
            ->where('is_generated', false);
    }

    /**
     * Scope: Blog yazılmış taslaklar
     */
    public function scopeGenerated($query)
    {
        return $query->where('is_generated', true);
    }

    /**
     * Scope: Seçilmemiş taslaklar (yeni taslaklar)
     */
    public function scopeUnselected($query)
    {
        return $query->where('is_selected', false);
    }
}
