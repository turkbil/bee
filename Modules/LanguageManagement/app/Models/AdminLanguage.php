<?php

namespace Modules\LanguageManagement\app\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLanguage extends Model
{
    protected $connection = 'mysql'; // Always use central database
    protected $table = 'admin_languages';
    
    protected $fillable = [
        'code',
        'name',
        'native_name',
        'direction',
        'flag_icon',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Aktif admin panel dilleri
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Sıralama
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Code ile admin dili bul
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }
}