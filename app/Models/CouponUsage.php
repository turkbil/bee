<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CouponUsage extends Model
{
    protected $table = 'coupon_usages';

    protected $fillable = [
        'coupon_id',
        'user_id',
        'usable_type',
        'usable_id',
        'discount_amount',
        'original_amount',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'discount_amount' => 'decimal:2',
            'original_amount' => 'decimal:2',
            'used_at' => 'datetime',
        ];
    }

    /**
     * Get the coupon
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the usable model (subscription, order, etc.)
     */
    public function usable(): MorphTo
    {
        return $this->morphTo();
    }
}
