<?php

declare(strict_types=1);

namespace Modules\Shop\App\Services;

use Modules\Shop\App\Models\ShopCart;
use Modules\Shop\App\Models\ShopCartItem;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopProductVariant;
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

        return ShopCart::findOrCreateForSession($sessionId);
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
        return $this->getCurrentCart()->items_count;
    }

    /**
     * Sepet toplamı
     */
    public function getTotal(): float
    {
        return (float) $this->getCurrentCart()->total;
    }

    /**
     * Sepetteki tüm ürünler
     */
    public function getItems()
    {
        return $this->getCurrentCart()
            ->items()
            ->with(['product.media', 'variant'])
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
}
