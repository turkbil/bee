<?php

declare(strict_types=1);

namespace Modules\Shop\App\Services;

use Modules\Shop\App\Models\ShopCart;
use Modules\Shop\App\Models\ShopCartItem;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopProductVariant;
use Modules\Shop\App\Models\ShopCurrency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ShopCartService
{
    /**
     * Mevcut sepeti al veya oluştur
     */
    public function getCurrentCart(): ShopCart
    {
        $sessionId = Session::getId();

        // TODO: Kullanıcı girişi varsa customer_id kullan
        // $customerId = auth()->guard('customer')->id();

        $cart = ShopCart::with('currency')->findOrCreateForSession($sessionId);

        // Eğer sepette currency_id yoksa, default currency'yi ayarla
        if (!$cart->currency_id) {
            $defaultCurrency = ShopCurrency::getDefault();
            if ($defaultCurrency) {
                $cart->currency_id = $defaultCurrency->currency_id;
                $cart->currency = $defaultCurrency->code;
                $cart->save();
                $cart->load('currency'); // Reload currency relationship
            }
        }

        return $cart;
    }

    /**
     * Sepete ürün ekle
     */
    public function addItem(
        int $productId,
        int $quantity = 1,
        ?int $variantId = null,
        array $customizationOptions = []
    ): ShopCartItem {
        $cart = $this->getCurrentCart();
        $product = ShopProduct::findOrFail($productId);

        return DB::transaction(function () use ($cart, $product, $quantity, $variantId, $customizationOptions) {
            // Aynı ürün zaten varsa adet artır
            $existingItem = $cart->items()
                ->where('product_id', $product->product_id)
                ->where('product_variant_id', $variantId)
                ->first();

            if ($existingItem) {
                $existingItem->increaseQuantity($quantity);
                $existingItem->save();
                $item = $existingItem;
            } else {
                // Yeni ürün ekle
                $unitPrice = $this->getProductPrice($product, $variantId);
                $taxRate = 20.0; // KDV %20 (config'den alınabilir)

                $item = new ShopCartItem([
                    'cart_id' => $cart->cart_id,
                    'product_id' => $product->product_id,
                    'product_variant_id' => $variantId,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_amount' => 0,
                    'final_price' => $unitPrice,
                    'subtotal' => $unitPrice * $quantity,
                    'tax_rate' => $taxRate,
                    'tax_amount' => ($unitPrice * $quantity) * ($taxRate / 100),
                    'total' => 0,
                    'currency_id' => $product->currency_id, // Ürünün currency'sini kaydet
                    'customization_options' => $customizationOptions,
                    'in_stock' => true,
                ]);

                $item->total = $item->subtotal + $item->tax_amount;
                $item->save();
            }

            // Sepet toplamlarını güncelle
            $cart->recalculateTotals();
            $cart->touchActivity();

            return $item;
        });
    }

    /**
     * Sepetten ürün çıkar
     */
    public function removeItem(int $cartItemId): bool
    {
        $cart = $this->getCurrentCart();
        $item = ShopCartItem::where('cart_item_id', $cartItemId)
            ->where('cart_id', $cart->cart_id)
            ->firstOrFail();

        return DB::transaction(function () use ($cart, $item) {
            $item->delete();
            $cart->recalculateTotals();
            $cart->touchActivity();

            return true;
        });
    }

    /**
     * Ürün adetini güncelle
     */
    public function updateQuantity(int $cartItemId, int $quantity): ShopCartItem
    {
        $cart = $this->getCurrentCart();
        $item = ShopCartItem::where('cart_item_id', $cartItemId)
            ->where('cart_id', $cart->cart_id)
            ->firstOrFail();

        return DB::transaction(function () use ($cart, $item, $quantity) {
            if ($quantity <= 0) {
                $item->delete();
                $cart->recalculateTotals();
                return $item;
            }

            $item->updateQuantity($quantity);
            $cart->recalculateTotals();
            $cart->touchActivity();

            return $item;
        });
    }

    /**
     * Sepeti boşalt
     */
    public function clearCart(): bool
    {
        $cart = $this->getCurrentCart();

        return DB::transaction(function () use ($cart) {
            $cart->items()->delete();
            $cart->recalculateTotals();
            $cart->touchActivity();

            return true;
        });
    }

    /**
     * Sepet ürün sayısı
     */
    public function getItemCount(): int
    {
        return (int) ($this->getCurrentCart()->items_count ?? 0);
    }

    /**
     * Sepet toplamı
     */
    public function getTotal(): float
    {
        return (float) ($this->getCurrentCart()->total ?? 0);
    }

    /**
     * Sepetteki tüm ürünler
     */
    public function getItems()
    {
        return $this->getCurrentCart()
            ->items()
            ->with(['product.media', 'product.currency', 'variant', 'currency'])
            ->get();
    }

    /**
     * Ürün fiyatını al
     */
    protected function getProductPrice(ShopProduct $product, ?int $variantId = null): float
    {
        // Fiyat talep ediliyorsa
        if ($product->price_on_request) {
            return 0.0;
        }

        // Varyant varsa varyant fiyatı
        if ($variantId) {
            $variant = ShopProductVariant::find($variantId);
            if ($variant && $variant->price) {
                return (float) $variant->price;
            }
        }

        // Temel fiyat
        return (float) $product->base_price;
    }

    /**
     * Sepette ürün var mı?
     */
    public function hasProduct(int $productId, ?int $variantId = null): bool
    {
        $cart = $this->getCurrentCart();

        $query = $cart->items()
            ->where('product_id', $productId);

        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        }

        return $query->exists();
    }

    /**
     * Sepet ID'si al
     */
    public function getCartId(): int
    {
        return $this->getCurrentCart()->cart_id;
    }

    // ============================================
    // ADMIN PANEL METHODS
    // ============================================

    /**
     * Get paginated carts with filters (ADMIN)
     */
    public function getPaginatedCartsForAdmin(array $filters, int $perPage = 15)
    {
        $query = ShopCart::query()
            ->with(['items.product', 'currency']);

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('session_id', 'LIKE', "%{$search}%")
                    ->orWhere('cart_id', 'LIKE', "%{$search}%")
                    ->orWhere('ip_address', 'LIKE', "%{$search}%");
            });
        }

        // Status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Date range filter
        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        // Sorting
        $sortField = $filters['sortField'] ?? 'cart_id';
        $sortDirection = $filters['sortDirection'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Delete cart (ADMIN)
     */
    public function deleteCartAdmin(int $cartId): array
    {
        try {
            $cart = ShopCart::findOrFail($cartId);

            // Delete cart items first
            $cart->items()->delete();

            // Delete cart
            $cart->delete();

            \Log::info('Cart deleted', [
                'cart_id' => $cartId,
                'user_id' => auth()->id(),
            ]);

            return [
                'success' => true,
                'message' => 'Cart deleted successfully',
            ];
        } catch (\Exception $e) {
            \Log::error('Cart deletion failed', [
                'cart_id' => $cartId,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to delete cart: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Bulk delete carts (ADMIN)
     */
    public function bulkDeleteCartsAdmin(array $cartIds): int
    {
        if (empty($cartIds)) {
            return 0;
        }

        try {
            // Delete cart items first
            ShopCart::whereIn('cart_id', $cartIds)
                ->get()
                ->each(function ($cart) {
                    $cart->items()->delete();
                });

            // Delete carts
            $deletedCount = ShopCart::whereIn('cart_id', $cartIds)->delete();

            \Log::info('Bulk cart delete', [
                'deleted_count' => $deletedCount,
                'user_id' => auth()->id(),
            ]);

            return $deletedCount;
        } catch (\Exception $e) {
            \Log::error('Bulk cart delete failed', [
                'error' => $e->getMessage(),
                'cart_ids' => $cartIds,
                'user_id' => auth()->id(),
            ]);

            return 0;
        }
    }

    /**
     * Mark cart as abandoned (ADMIN)
     */
    public function markAsAbandonedAdmin(int $cartId): array
    {
        try {
            $cart = ShopCart::findOrFail($cartId);

            $cart->update([
                'status' => 'abandoned',
                'abandoned_at' => now(),
            ]);

            \Log::info('Cart marked as abandoned', [
                'cart_id' => $cartId,
                'user_id' => auth()->id(),
            ]);

            return [
                'success' => true,
                'message' => 'Cart marked as abandoned',
            ];
        } catch (\Exception $e) {
            \Log::error('Cart mark as abandoned failed', [
                'cart_id' => $cartId,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to mark cart as abandoned',
            ];
        }
    }

    /**
     * Clean old carts (ADMIN)
     */
    public function cleanOldCartsAdmin(int $daysOld = 30): int
    {
        $cutoffDate = now()->subDays($daysOld);

        try {
            $oldCarts = ShopCart::where('status', 'abandoned')
                ->where('updated_at', '<', $cutoffDate)
                ->get();

            // Delete items first
            foreach ($oldCarts as $cart) {
                $cart->items()->delete();
            }

            // Delete carts
            $deletedCount = ShopCart::where('status', 'abandoned')
                ->where('updated_at', '<', $cutoffDate)
                ->delete();

            \Log::info('Old carts cleaned', [
                'deleted_count' => $deletedCount,
                'days_old' => $daysOld,
                'user_id' => auth()->id(),
            ]);

            return $deletedCount;
        } catch (\Exception $e) {
            \Log::error('Clean old carts failed', [
                'error' => $e->getMessage(),
                'days_old' => $daysOld,
                'user_id' => auth()->id(),
            ]);

            return 0;
        }
    }
}
