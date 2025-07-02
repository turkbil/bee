<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AITokenPackage extends Model
{
    protected $table = 'ai_token_packages';
    
    protected $fillable = [
        'name',
        'token_amount',
        'price',
        'currency',
        'description',
        'is_active',
        'is_popular',
        'features',
        'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
        'features' => 'array',
        'token_amount' => 'integer',
        'sort_order' => 'integer'
    ];

    /**
     * Scope for active packages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered packages
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('price', 'asc');
    }

    /**
     * Get all purchases of this package
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(AITokenPurchase::class, 'package_id');
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2) . ' ' . $this->currency;
    }

    /**
     * Get formatted token amount
     */
    public function getFormattedTokenAmountAttribute(): string
    {
        return number_format($this->token_amount) . ' Token';
    }
}