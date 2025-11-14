<?php

declare(strict_types=1);

namespace Modules\Cart\App\Listeners;

use Illuminate\Auth\Events\Login;
use Modules\Cart\App\Services\CartService;
use Illuminate\Support\Facades\Log;

/**
 * Merge Guest Cart on Login
 *
 * Kullanıcı login olduğunda misafir sepetini kullanıcı sepetine birleştirir
 */
class MergeGuestCartOnLogin
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Handle the event
     *
     * @param Login $event
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        $sessionId = session()->getId();

        // Misafir sepeti var mı?
        $guestCart = $this->cartService->getCart(null, $sessionId);

        if (!$guestCart || $guestCart->items()->count() === 0) {
            Log::info('No guest cart to merge', [
                'user_id' => $user->id,
                'session_id' => $sessionId,
            ]);
            return; // Misafir sepeti yok, merge gerekmez
        }

        // Kullanıcı sepeti bul/oluştur
        $customerCart = $this->cartService->findOrCreateCart($user->id, null);

        // Misafir sepetini kullanıcı sepetine birleştir
        try {
            $this->cartService->mergeGuestCart($guestCart, $customerCart);

            Log::info('Guest cart merged on login', [
                'user_id' => $user->id,
                'guest_cart_id' => $guestCart->cart_id,
                'customer_cart_id' => $customerCart->cart_id,
                'items_merged' => $guestCart->items()->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to merge guest cart on login', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
