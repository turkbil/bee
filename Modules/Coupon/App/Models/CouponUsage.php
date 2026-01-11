<?php

namespace Modules\Coupon\App\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CouponUsage extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'coupon_id',
        'user_id',
        'usable_type',
        'usable_id',
        'discount_amount',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
    ];

    // Relationships
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function usable()
    {
        return $this->morphTo();
    }
}
