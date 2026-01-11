<?php

declare(strict_types=1);

namespace Modules\Mail\App\Models;

use Illuminate\Database\Eloquent\Model;

class MailTemplate extends Model
{
    protected $fillable = [
        'key',
        'name',
        'subject',
        'content',
        'variables',
        'category',
        'is_active',
    ];

    protected $casts = [
        'subject' => 'array',
        'content' => 'array',
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Belirli bir dil için konu başlığını al
     */
    public function getSubjectForLocale(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $this->subject[$locale] ?? $this->subject['tr'] ?? reset($this->subject) ?? '';
    }

    /**
     * Belirli bir dil için içeriği al
     */
    public function getContentForLocale(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $this->content[$locale] ?? $this->content['tr'] ?? reset($this->content) ?? '';
    }

    /**
     * Scope: Aktif şablonlar
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Kategoriye göre filtrele
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Key'e göre şablon bul
     */
    public static function findByKey(string $key): ?self
    {
        return static::where('key', $key)->first();
    }
}
