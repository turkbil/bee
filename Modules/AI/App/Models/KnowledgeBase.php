<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    use HasFactory;

    protected $table = 'tenant_knowledge_base';

    protected $fillable = [
        'category',
        'question',
        'answer',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')
                     ->orderBy('created_at', 'asc');
    }

    // Accessor - Boş cevap kontrolü
    public function getHasAnswerAttribute()
    {
        return !empty($this->answer);
    }

    // Helper - Kategorileri grupla
    public static function getCategories()
    {
        return self::select('category')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
    }

    // Helper - Aktif soru-cevapları al (Bot için)
    public static function getActiveQA()
    {
        return self::active()
            ->whereNotNull('answer')
            ->where('answer', '!=', '')
            ->ordered()
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category,
                    'question' => $item->question,
                    'answer' => $item->answer,
                ];
            })
            ->toArray();
    }
}
