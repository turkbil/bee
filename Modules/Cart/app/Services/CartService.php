<?php

declare(strict_types=1);

namespace Modules\Cart\App\Services;

use Modules\Cart\App\Models\Cart;
use Modules\Cart\App\Models\CartItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartService
{
    /**
     * Tenant context kontrolü - Cart sorguları sadece tenant DB'de yapılmalı
     */
    protected function ensureTenantContext(): bool
    {
        // Tenancy initialize edilmemiş veya central tenant ise cart sorgusu yapma
        if (!function_exists('tenant') || !tenant()) {
            Log::warning('CartService: Tenant context not initialized, skipping cart query');
            return false;
        }
        return true;
    }

    /**
     * Aktif sepeti al (session veya customer için)
     */
    public function getCart(?int $customerId = null, ?string $sessionId = null): ?Cart
    {
        // Tenant context yoksa null dön (central DB'ye sorgu atmayı engelle)
        if (!$this->ensureTenantContext()) {
            return null;
        }

        if ($customerId) {
            return Cart::where('customer_id', $customerId)
                ->where('status', 'active')
                ->with(['items' => function ($query) {
                    $query->where('is_active', true);
                }])
                ->first();
        }

        if ($sessionId) {
            return Cart::where('session_id', $sessionId)
                ->where('status', 'active')
                ->with(['items' => function ($query) {
                    $query->where('is_active', true);
                }])
                ->first();
        }

        return null;
    }

    /**
     * Sepet oluştur veya bul
     */
    public function findOrCreateCart(?int $customerId = null, ?string $sessionId = null): ?Cart
    {
        // Tenant context yoksa null dön (central DB'ye sorgu atmayı engelle)
        if (!$this->ensureTenantContext()) {
            return null;
        }

        if ($customerId) {
            return Cart::findOrCreateForCustomer($customerId);
        }

        if ($sessionId) {
            return Cart::findOrCreateForSession($sessionId);
        }

        // Session ID yoksa oluştur
        $newSessionId = session()->getId() ?: \Illuminate\Support\Str::random(40);
        return Cart::findOrCreateForSession($newSessionId);
    }

    /**
     * Sepete item ekle (polymorphic)
     *
     * @param Cart $cart
     * @param mixed $item (ShopProduct, Subscription, Service, etc.)
     * @param int $quantity
     * @param array $options
     */
    public function addItem(Cart $cart, $item, int $quantity = 1, array $options = []): CartItem
    {
        DB::beginTransaction();

        try {
            // Polymorphic type ve id
            $cartableType = get_class($item);
            $cartableId = $item->getKey();

            // Aynı item zaten sepette mi?
            $existingItem = $cart->items()
                ->where('cartable_type', $cartableType)
                ->where('cartable_id', $cartableId)
                ->where('is_active', true)
                ->first();

            if ($existingItem) {
                // Mevcut item'ın miktarını artır
                $existingItem->quantity += $quantity;
                $existingItem->recalculate();
                $cartItem = $existingItem;
            } else {
                // Yeni item ekle
                $cartItem = new CartItem([
                    'cart_id' => $cart->cart_id,
                    'cartable_type' => $cartableType,
                    'cartable_id' => $cartableId,
                    'quantity' => $quantity,
                    'is_active' => true,
                ]);

                // Fiyat bilgilerini al
                $this->setPricing($cartItem, $item, $options);

                $cartItem->save();
            }

            // Sepet toplamlarını güncelle
            $cart->touchActivity();
            $cart->recalculateTotals();

            DB::commit();

            Log::info('Cart item added', [
                'cart_id' => $cart->cart_id,
                'item_type' => $cartableType,
                'item_id' => $cartableId,
                'quantity' => $quantity,
            ]);

            return $cartItem;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to add cart item', [
                'error' => $e->getMessage(),
                'cart_id' => $cart->cart_id,
            ]);
            throw $e;
        }
    }

    /**
     * Item fiyatlandırmasını ayarla
     */
    protected function setPricing(CartItem $cartItem, $item, array $options = []): void
    {
        // Fiyat bilgisini item'dan al
        $unitPrice = $options['unit_price'] ?? $this->getItemPrice($item);
        $discountAmount = $options['discount_amount'] ?? 0;
        $taxRate = $options['tax_rate'] ?? $this->getItemTaxRate($item);

        // Currency dönüşümü (USD/EUR → TRY)
        $originalCurrency = $options['currency'] ?? $this->getItemCurrency($item);
        $originalPrice = $unitPrice;
        $conversionRate = 1.0;

        if ($originalCurrency && $originalCurrency !== 'TRY') {
            $currencyService = app(CurrencyConversionService::class);

            if ($currencyService->needsConversion($originalCurrency)) {
                $converted = $currencyService->convertWithMetadata($unitPrice, $originalCurrency);
                $unitPrice = $converted['converted_amount'];
                $conversionRate = $converted['rate'];

                Log::info('💱 Currency converted for cart item', [
                    'original_amount' => $originalPrice,
                    'original_currency' => $originalCurrency,
                    'converted_amount' => $unitPrice,
                    'rate' => $conversionRate,
                ]);
            }
        }

        $cartItem->unit_price = $unitPrice;
        $cartItem->discount_amount = $discountAmount;
        $cartItem->final_price = $unitPrice - $discountAmount;
        $cartItem->tax_rate = $taxRate;

        // Display bilgileri (migration sonrası aktif)
        if (!empty($options['item_title'])) {
            $cartItem->item_title = $options['item_title'];
        }

        if (!empty($options['item_image'])) {
            $cartItem->item_image = $options['item_image'];
        }

        if (!empty($options['item_sku'])) {
            $cartItem->item_sku = $options['item_sku'];
        }

        // Currency metadata (migration sonrası aktif)
        if ($originalCurrency) {
            $cartItem->original_currency = $originalCurrency;
            $cartItem->original_price = $originalPrice;
            $cartItem->conversion_rate = $conversionRate;
        }

        // Backward compatibility: Shop Product için product_id
        if (method_exists($item, 'getAttribute') && $item->getAttribute('product_id')) {
            $cartItem->product_id = $item->product_id;
        }

        // Customization options
        if (!empty($options['customization_options'])) {
            $cartItem->customization_options = $options['customization_options'];
        }

        if (!empty($options['special_instructions'])) {
            $cartItem->special_instructions = $options['special_instructions'];
        }

        // Metadata (subscription cycle info, vb.)
        if (!empty($options['metadata'])) {
            $cartItem->metadata = $options['metadata'];
        }

        // Item description
        if (!empty($options['item_description'])) {
            $cartItem->item_description = $options['item_description'];
        }

        // İlk hesaplama
        $cartItem->recalculate();
    }

    /**
     * Item fiyatını al
     * 🏷️ ShopProduct için KDV dahil fiyat kullanılır (price_with_tax accessor)
     */
    protected function getItemPrice($item): float
    {
        // Method priority: getPrice() > price_with_tax (ShopProduct) > final_price > base_price > sale_price > price > 0
        if (method_exists($item, 'getPrice')) {
            return (float) $item->getPrice();
        }

        // 🏷️ ShopProduct için price_with_tax accessor (KDV dahil - sepette her zaman KDV dahil gösterilir)
        if (isset($item->price_with_tax) && $item->price_with_tax > 0) {
            return (float) $item->price_with_tax;
        }

        // ShopProduct için final_price accessor (fallback)
        if (isset($item->final_price) && $item->final_price > 0) {
            return (float) $item->final_price;
        }

        // ShopProduct için base_price (fallback - KDV hariç)
        if (isset($item->base_price) && $item->base_price > 0) {
            return (float) $item->base_price;
        }

        // Generic sale_price
        if (isset($item->sale_price) && $item->sale_price > 0) {
            return (float) $item->sale_price;
        }

        // Generic price
        if (isset($item->price) && $item->price > 0) {
            return (float) $item->price;
        }

        return 0.0;
    }

    /**
     * Item vergi oranını al
     */
    protected function getItemTaxRate($item): float
    {
        if (method_exists($item, 'getTaxRate')) {
            return (float) $item->getTaxRate();
        }

        if (isset($item->tax_rate)) {
            return (float) $item->tax_rate;
        }

        // Default KDV %20
        return 20.0;
    }

    /**
     * Item currency bilgisini al
     */
    protected function getItemCurrency($item): string
    {
        // ShopProduct için currency field
        if (isset($item->currency) && $item->currency) {
            return strtoupper($item->currency);
        }

        // Default: TRY
        return 'TRY';
    }

    /**
     * Item miktarını güncelle
     */
    public function updateItemQuantity(CartItem $cartItem, int $quantity): void
    {
        DB::beginTransaction();

        try {
            $cartItem->updateQuantity($quantity);
            $cartItem->cart->recalculateTotals();
            $cartItem->cart->touchActivity();

            DB::commit();

            Log::info('Cart item quantity updated', [
                'cart_item_id' => $cartItem->cart_item_id,
                'new_quantity' => $quantity,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update cart item quantity', [
                'error' => $e->getMessage(),
                'cart_item_id' => $cartItem->cart_item_id,
            ]);
            throw $e;
        }
    }

    /**
     * Item'ı sepetten kaldır
     */
    public function removeItem(CartItem $cartItem): void
    {
        DB::beginTransaction();

        try {
            $cart = $cartItem->cart;
            $cartItem->delete();

            $cart->recalculateTotals();
            $cart->touchActivity();

            DB::commit();

            Log::info('Cart item removed', [
                'cart_item_id' => $cartItem->cart_item_id,
                'cart_id' => $cart->cart_id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to remove cart item', [
                'error' => $e->getMessage(),
                'cart_item_id' => $cartItem->cart_item_id,
            ]);
            throw $e;
        }
    }

    /**
     * Sepeti temizle
     */
    public function clearCart(Cart $cart): void
    {
        DB::beginTransaction();

        try {
            $cart->clear();
            $cart->touchActivity();

            DB::commit();

            Log::info('Cart cleared', ['cart_id' => $cart->cart_id]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to clear cart', [
                'error' => $e->getMessage(),
                'cart_id' => $cart->cart_id,
            ]);
            throw $e;
        }
    }

    /**
     * Kupon uygula
     */
    public function applyCoupon(Cart $cart, string $couponCode, float $discountAmount): void
    {
        DB::beginTransaction();

        try {
            $cart->coupon_code = $couponCode;
            $cart->coupon_discount = $discountAmount;
            $cart->recalculateTotals();
            $cart->touchActivity();
            $cart->save();

            DB::commit();

            Log::info('Coupon applied', [
                'cart_id' => $cart->cart_id,
                'coupon_code' => $couponCode,
                'discount' => $discountAmount,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to apply coupon', [
                'error' => $e->getMessage(),
                'cart_id' => $cart->cart_id,
            ]);
            throw $e;
        }
    }

    /**
     * Kuponu kaldır
     */
    public function removeCoupon(Cart $cart): void
    {
        DB::beginTransaction();

        try {
            $cart->coupon_code = null;
            $cart->coupon_discount = 0;
            $cart->recalculateTotals();
            $cart->touchActivity();
            $cart->save();

            DB::commit();

            Log::info('Coupon removed', ['cart_id' => $cart->cart_id]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to remove coupon', [
                'error' => $e->getMessage(),
                'cart_id' => $cart->cart_id,
            ]);
            throw $e;
        }
    }

    /**
     * Sepeti siparişe dönüştür
     */
    public function convertToOrder(Cart $cart, int $orderId): void
    {
        DB::beginTransaction();

        try {
            $cart->markAsConverted($orderId);

            DB::commit();

            Log::info('Cart converted to order', [
                'cart_id' => $cart->cart_id,
                'order_id' => $orderId,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to convert cart to order', [
                'error' => $e->getMessage(),
                'cart_id' => $cart->cart_id,
            ]);
            throw $e;
        }
    }

    /**
     * Misafir sepetini kayıtlı kullanıcı sepetine merge et
     */
    public function mergeGuestCart(Cart $guestCart, Cart $customerCart): void
    {
        DB::beginTransaction();

        try {
            foreach ($guestCart->items as $guestItem) {
                // Müşteri sepetinde aynı item var mı?
                $existingItem = $customerCart->items()
                    ->where('cartable_type', $guestItem->cartable_type)
                    ->where('cartable_id', $guestItem->cartable_id)
                    ->first();

                if ($existingItem) {
                    // Miktarı birleştir
                    $existingItem->quantity += $guestItem->quantity;
                    $existingItem->recalculate();
                } else {
                    // Item'ı müşteri sepetine taşı
                    $guestItem->cart_id = $customerCart->cart_id;
                    $guestItem->save();
                }
            }

            // Misafir sepetini merged olarak işaretle
            $guestCart->status = 'merged';
            $guestCart->save();

            // Müşteri sepetini güncelle
            $customerCart->recalculateTotals();
            $customerCart->touchActivity();

            DB::commit();

            Log::info('Guest cart merged', [
                'guest_cart_id' => $guestCart->cart_id,
                'customer_cart_id' => $customerCart->cart_id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to merge guest cart', [
                'error' => $e->getMessage(),
                'guest_cart_id' => $guestCart->cart_id,
            ]);
            throw $e;
        }
    }

    /**
     * Terk edilmiş sepetleri işaretle
     */
    public function markAbandonedCarts(int $minutesInactive = 60): int
    {
        $cutoffTime = now()->subMinutes($minutesInactive);

        $count = Cart::where('status', 'active')
            ->where('last_activity_at', '<', $cutoffTime)
            ->whereNotNull('last_activity_at')
            ->update([
                'status' => 'abandoned',
                'abandoned_at' => now(),
            ]);

        Log::info('Abandoned carts marked', [
            'count' => $count,
            'cutoff_minutes' => $minutesInactive,
        ]);

        return $count;
    }
}
