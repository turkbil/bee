<?php

declare(strict_types=1);

namespace Modules\Cart\App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Cart\App\Services\CartService;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Services\ShopCartBridge;

class CartApiController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected ShopCartBridge $shopCartBridge
    ) {}

    /**
     * Sepete ürün ekle
     */
    public function addItem(Request $request): JsonResponse
    {
        \Log::info('🛒 CartAPI: addItem called', $request->all());

        $request->validate([
            'product_id' => 'required|integer|exists:shop_products,product_id',
            'quantity' => 'integer|min:1',
            'variant_id' => 'nullable|integer',
            'cart_id' => 'nullable|integer',  // localStorage'dan gelen cart_id
        ]);

        try {
            $product = ShopProduct::findOrFail($request->product_id);
            $quantity = $request->quantity ?? 1;

            // 🔍 STOK KONTROLÜ - ShopCartBridge kullan
            if (!$this->shopCartBridge->canAddToCart($product, $quantity)) {
                $errors = $this->shopCartBridge->getCartItemErrors($product, $quantity);
                return response()->json([
                    'success' => false,
                    'message' => implode(' ', $errors),
                ], 400);
            }

            // Önce cart_id parametresine bak (localStorage'dan geliyorsa)
            $cart = null;
            if ($request->cart_id) {
                $cart = \Modules\Cart\App\Models\Cart::find($request->cart_id);
                \Log::info('🛒 CartAPI: Cart loaded by ID', [
                    'cart_id' => $request->cart_id,
                    'found' => $cart ? 'yes' : 'no',
                ]);
            }

            // Cart bulunamadıysa session/customer ile bul
            if (!$cart) {
                $sessionId = session()->getId();
                $customerId = auth()->check() ? auth()->id() : null;

                \Log::info('🛒 CartAPI: Session info', [
                    'session_id' => $sessionId,
                    'customer_id' => $customerId,
                    'product_id' => $product->product_id,
                    'quantity' => $quantity,
                ]);

                $cart = $this->cartService->findOrCreateCart($customerId, $sessionId);
            }

            \Log::info('🛒 CartAPI: Cart loaded', [
                'cart_id' => $cart->cart_id,
            ]);

            // 🎯 ShopCartBridge ile display bilgileri ve currency hazırla
            $options = $this->shopCartBridge->prepareProductForCart($product, $quantity);

            // Variant ID varsa ekle
            if ($request->variant_id) {
                $options['customization_options'] = ['variant_id' => $request->variant_id];
            }

            $cartItem = $this->cartService->addItem($cart, $product, $quantity, $options);

            \Log::info('🛒 CartAPI: Item added', [
                'cart_item_id' => $cartItem->cart_item_id,
                'unit_price' => $cartItem->unit_price,
                'total' => $cartItem->total,
            ]);

            // Güncel sepet bilgilerini al
            $cart->refresh();
            $itemCount = $cart->items()->where('is_active', true)->sum('quantity');

            return response()->json([
                'success' => true,
                'message' => 'Ürün sepete eklendi!',
                'data' => [
                    'cart_id' => $cart->cart_id,
                    'item_count' => $itemCount,
                    'total' => $cart->total,
                    'subtotal' => $cart->subtotal,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('🛒 CartAPI: ERROR', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ürün sepete eklenirken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sepet item miktarını güncelle
     */
    public function updateItem(Request $request): JsonResponse
    {
        $request->validate([
            'cart_item_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $cartItem = \Modules\Cart\App\Models\CartItem::findOrFail($request->cart_item_id);
            $this->cartService->updateItemQuantity($cartItem, $request->quantity);

            $cart = $cartItem->cart;
            $itemCount = $cart->items()->where('is_active', true)->sum('quantity');

            return response()->json([
                'success' => true,
                'message' => 'Sepet güncellendi',
                'data' => [
                    'item_count' => $itemCount,
                    'total' => $cart->total,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sepet güncellenirken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sepetten ürün kaldır
     */
    public function removeItem(Request $request): JsonResponse
    {
        $request->validate([
            'cart_item_id' => 'required|integer',
        ]);

        try {
            $cartItem = \Modules\Cart\App\Models\CartItem::findOrFail($request->cart_item_id);
            $cart = $cartItem->cart;

            $this->cartService->removeItem($cartItem);

            $cart->refresh();
            $itemCount = $cart->items()->where('is_active', true)->sum('quantity');

            return response()->json([
                'success' => true,
                'message' => 'Ürün sepetten kaldırıldı',
                'data' => [
                    'item_count' => $itemCount,
                    'total' => $cart->total,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün kaldırılırken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sepetteki ürün sayısını getir
     */
    public function getCount(Request $request): JsonResponse
    {
        try {
            // Önce localStorage'dan gelen cart_id'yi kontrol et
            $cart = null;
            if ($request->cart_id) {
                $cart = \Modules\Cart\App\Models\Cart::find($request->cart_id);
                \Log::info('🛒 CartAPI: getCount by cart_id', [
                    'cart_id' => $request->cart_id,
                    'found' => $cart ? 'yes' : 'no',
                ]);
            }

            // Cart bulunamadıysa session ile bul VEYA OLUŞTUR
            if (!$cart) {
                $sessionId = session()->getId();
                $customerId = auth()->check() ? auth()->id() : null;

                // ÖNCE mevcut cart'ı bul
                $cart = $this->cartService->getCart($customerId, $sessionId);

                // Yoksa YENİ OLUŞTUR
                if (!$cart) {
                    \Log::info('🛒 CartAPI: Creating new cart for session', [
                        'session_id' => $sessionId,
                        'customer_id' => $customerId,
                    ]);
                    $cart = $this->cartService->findOrCreateCart($customerId, $sessionId);
                }

                // Geçersiz cart_id varsa temizlendi bilgisi
                if ($request->cart_id && $cart->cart_id != $request->cart_id) {
                    \Log::warning('🛒 CartAPI: Invalid cart_id cleared, new cart created', [
                        'old_cart_id' => $request->cart_id,
                        'new_cart_id' => $cart->cart_id,
                    ]);
                }
            }

            if ($cart) {
                $itemCount = $cart->items()->where('is_active', true)->sum('quantity');
                return response()->json([
                    'success' => true,
                    'data' => [
                        'item_count' => $itemCount,
                        'total' => $cart->total,
                        'cart_id' => $cart->cart_id,  // Frontend için cart_id dön
                    ],
                ]);
            }

            // Cart yok, localStorage'ı temizle
            return response()->json([
                'success' => true,
                'data' => [
                    'item_count' => 0,
                    'total' => 0,
                    'cart_id' => null,  // null dönünce frontend temizleyecek
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sepet bilgisi alınırken hata oluştu',
            ], 500);
        }
    }
}
