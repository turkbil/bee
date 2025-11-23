<?php

namespace Modules\Coupon\App\Services;

use Modules\Coupon\App\Models\Coupon;
use Modules\Coupon\App\Models\CouponUsage;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CouponService
{
    /**
     * Validate a coupon code
     */
    public function validate(string $code, User $user, float $amount): array
    {
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return [
                'valid' => false,
                'message' => __('coupon::admin.errors.not_found'),
            ];
        }

        if (!$coupon->isValid()) {
            $message = match($coupon->status) {
                'inactive' => __('coupon::admin.errors.inactive'),
                'expired' => __('coupon::admin.errors.expired'),
                'limit_reached' => __('coupon::admin.errors.limit_reached'),
                'scheduled' => __('coupon::admin.errors.not_started'),
                default => __('coupon::admin.errors.invalid'),
            };

            return [
                'valid' => false,
                'message' => $message,
            ];
        }

        if (!$coupon->isUsableBy($user)) {
            return [
                'valid' => false,
                'message' => __('coupon::admin.errors.user_limit_reached'),
            ];
        }

        if (!$coupon->canApplyTo($amount)) {
            return [
                'valid' => false,
                'message' => __('coupon::admin.errors.min_amount', ['amount' => number_format($coupon->min_amount, 2)]),
            ];
        }

        $discount = $coupon->apply($amount);

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount' => $discount,
            'final_amount' => $amount - $discount,
        ];
    }

    /**
     * Apply coupon and record usage
     */
    public function apply(Coupon $coupon, User $user, Model $usable, float $amount): CouponUsage
    {
        $discount = $coupon->apply($amount);

        $usage = CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id' => $user->id,
            'usable_type' => get_class($usable),
            'usable_id' => $usable->id,
            'discount_amount' => $discount,
        ]);

        $coupon->incrementUsage();

        return $usage;
    }

    /**
     * Get discount amount without recording usage
     */
    public function getDiscount(Coupon $coupon, float $amount): float
    {
        return $coupon->apply($amount);
    }

    /**
     * Generate a unique coupon code
     */
    public function generateCode(int $length = 8): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        do {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[random_int(0, strlen($characters) - 1)];
            }
        } while (Coupon::where('code', $code)->exists());

        return $code;
    }

    /**
     * Get coupon statistics
     */
    public function getStats(): array
    {
        return [
            'total_active' => Coupon::active()->count(),
            'total_expired' => Coupon::expired()->count(),
            'total_usages' => CouponUsage::count(),
            'total_discount_given' => CouponUsage::sum('discount_amount'),
        ];
    }
}
