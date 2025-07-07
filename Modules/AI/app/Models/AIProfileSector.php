<?php

namespace Modules\AI\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AIProfileSector extends Model
{
    protected $table = 'ai_profile_sectors';

    protected $fillable = [
        'code',
        'name',
        'icon',
        'description',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Bu sektöre ait sorular
     */
    public function questions(): HasMany
    {
        return $this->hasMany(AIProfileQuestion::class, 'sector_code', 'code')
                    ->where('is_active', true)
                    ->orderBy('step')
                    ->orderBy('sort_order');
    }

    /**
     * Aktif sektörleri getir - Sort order'a göre sıralama
     */
    public static function getActive()
    {
        return static::where('is_active', true)
                     ->orderBy('sort_order')  // Sort order'a göre sıralama
                     ->orderBy('name')        // Aynı sort_order'da alfabetik
                     ->get();
    }

    /**
     * Sektör koduna göre bul
     */
    public static function findByCode(string $code)
    {
        return static::where('code', $code)->first();
    }
}