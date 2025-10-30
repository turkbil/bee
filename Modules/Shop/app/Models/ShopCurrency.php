<?php

declare(strict_types=1);

namespace Modules\Shop\App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopCurrency extends BaseModel
{
    use HasFactory;

    protected $table = 'shop_currencies';
    protected $primaryKey = 'currency_id';

    protected $fillable = [
        'code',
        'symbol',
        'name',
        'name_translations',
        'exchange_rate',
        'is_active',
        'is_default',
        'decimal_places',
        'format',
    ];

    protected $casts = [
        'name_translations' => 'array',
        'exchange_rate' => 'decimal:4',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'decimal_places' => 'integer',
    ];

    /**
     * Default currency'yi getir
     */
    public static function getDefault(): ?self
    {
        return static::where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Code ile currency bul
     */
    public static function findByCode(string $code): ?self
    {
        return static::where('code', $code)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Fiyatı formatla
     */
    public function formatPrice(float $price): string
    {
        $formattedNumber = number_format($price, $this->decimal_places, ',', '.');

        if ($this->format === 'symbol_before') {
            return $this->symbol . ' ' . $formattedNumber;
        }

        return $formattedNumber . ' ' . $this->symbol;
    }

    /**
     * Translated name
     */
    public function getTranslatedName(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        if ($this->name_translations && isset($this->name_translations[$locale])) {
            return $this->name_translations[$locale];
        }

        return $this->name;
    }

    /**
     * Scope: Aktif currency'ler
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Default currency
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
