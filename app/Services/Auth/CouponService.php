<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Database\Eloquent\Model;

class CouponService
{
    /**
     * Validate a coupon code for user
     */
    public function validate(string $code, User $user): array
    {
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return ['valid' => false, 'message' => 'Kupon bulunamadı'];
        }

        if (!$coupon->is_active) {
            return ['valid' => false, 'message' => 'Kupon aktif değil'];
        }

        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            return ['valid' => false, 'message' => 'Kupon henüz geçerli değil'];
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return ['valid' => false, 'message' => 'Kuponun süresi dolmuş'];
        }

        if ($coupon->usage_limit && $coupon->usage_count >= $coupon->usage_limit) {
            return ['valid' => false, 'message' => 'Kupon kullanım limiti dolmuş'];
        }

        if ($coupon->per_user_limit) {
            $userUsageCount = $coupon->usages()->where('user_id', $user->id)->count();
            if ($userUsageCount >= $coupon->per_user_limit) {
                return ['valid' => false, 'message' => 'Bu kuponu daha fazla kullanamazsınız'];
            }
        }

        return ['valid' => true, 'coupon' => $coupon, 'message' => 'Kupon geçerli'];
    }

    /**
     * Apply coupon discount to amount
     */
    public function apply(Coupon $coupon, float $amount): array
    {
        $discount = $coupon->calculateDiscount($amount);
        $finalAmount = $amount - $discount;

        return [
            'original' => $amount,
            'discount' => $discount,
            'final' => $finalAmount,
        ];
    }

    /**
     * Mark coupon as used
     */
    public function markAsUsed(Coupon $coupon, User $user, Model $model): CouponUsage
    {
        // Create usage record
        $usage = CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id' => $user->id,
            'usable_type' => get_class($model),
            'usable_id' => $model->id,
            'discount_amount' => $coupon->calculateDiscount($model->getPayableAmount()),
            'original_amount' => $model->getPayableAmount(),
            'used_at' => now(),
        ]);

        // Increment coupon usage count
        $coupon->incrementUsage();

        return $usage;
    }

    /**
     * Get coupon by code
     */
    public function getByCode(string $code): ?Coupon
    {
        return Coupon::where('code', $code)->first();
    }
}
