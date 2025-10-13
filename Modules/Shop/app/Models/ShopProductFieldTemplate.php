<?php

declare(strict_types=1);

namespace Modules\Shop\App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopProductFieldTemplate extends Model
{
    protected $table = 'shop_product_field_templates';

    protected $primaryKey = 'template_id';

    protected $fillable = [
        'name',
        'description',
        'fields',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'fields' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope: Sadece aktif şablonlar
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc');
    }

    /**
     * Scope: Sıralı liste
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc');
    }
}
