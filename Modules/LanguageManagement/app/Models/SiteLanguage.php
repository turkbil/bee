<?php

namespace Modules\LanguageManagement\app\Models;

use Illuminate\Database\Eloquent\Model;

class SiteLanguage extends Model
{
    protected $table = 'site_languages';
    
    protected $fillable = [
        'code',
        'name',
        'native_name',
        'direction',
        'flag_icon',
        'is_active',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Aktif site dilleri
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Varsayılan site dili
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Sıralama
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Code ile site dili bul
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Varsayılan dili güncelle - sadece bir tane olabilir
     */
    public function setAsDefault()
    {
        // Önce tüm dillerin default'unu false yap
        static::query()->update(['is_default' => false]);
        
        // Bu dili default yap
        $this->update(['is_default' => true]);
    }
}