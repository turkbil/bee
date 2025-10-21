<?php

namespace Modules\AI\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIProfileSector extends Model
{
    protected $connection = 'mysql'; // Central DB for all tenants
    protected $table = 'ai_profile_sectors';
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
    }

    protected $fillable = [
        'code',
        'category_id',
        'name',
        'icon',
        'emoji',
        'color',
        'description',
        'keywords',
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
     * Ana kategori (parent) ilişkisi
     */
    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(AIProfileSector::class, 'category_id');
    }

    /**
     * Alt kategoriler (children) ilişkisi
     */
    public function subCategories(): HasMany
    {
        return $this->hasMany(AIProfileSector::class, 'category_id')
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('name');
    }

    /**
     * Ana kategorileri getir (category_id null olanlar)
     */
    public static function getMainCategories()
    {
        return static::whereNull('category_id')
                     ->where('is_active', true)
                     ->orderBy('sort_order')
                     ->orderBy('name')
                     ->get();
    }

    /**
     * Kategorize edilmiş sektörleri getir (ana kategoriler + alt kategorileri)
     */
    public static function getCategorizedSectors()
    {
        return static::with('subCategories')
                     ->whereNull('category_id')
                     ->where('is_active', true)
                     ->orderBy('sort_order')
                     ->orderBy('name')
                     ->get();
    }

    /**
     * Aktif sektörleri getir - Sort order'a göre sıralama (backward compatibility)
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