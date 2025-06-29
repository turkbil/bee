<?php

namespace Modules\LanguageManagement\app\Models;

use Illuminate\Database\Eloquent\Model;

class TenantLanguage extends Model
{
    protected $table = 'tenant_languages';
    
    protected $fillable = [
        'code',
        'name',
        'native_name',
        'direction',
        'flag_icon',
        'is_active',
        // is_default kaldırıldı - artık tenants.tenant_default_locale'de
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        // is_default kaldırıldı
        'sort_order' => 'integer',
    ];

    /**
     * Aktif tenant dilleri
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Varsayılan dil artık tenants tablosunda tutulduğu için bu scope kaldırıldı

    /**
     * Sıralama
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Code ile tenant dili bul
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    // setAsDefault metodu kaldırıldı - varsayılan dil artık tenants.tenant_default_locale'de
}