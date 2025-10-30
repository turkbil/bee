<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Modules\Shop\App\Services\ShopCartService;

class CartController
{
    public function index(ShopCartService $cartService)
    {
        // Sepet bilgilerini al
        $cart = $cartService->getCurrentCart();
        $items = $cartService->getItems();

        return view('shop::front.cart.index', [
            'cart' => $cart,
            'items' => $items,
            'itemCount' => $cartService->getItemCount(),
            'total' => $cartService->getTotal(),
            'subtotal' => (float) ($cart->subtotal ?? 0),
            'taxAmount' => (float) ($cart->tax_amount ?? 0),
        ]);
    }

    public function update(int $itemId, Request $request, ShopCartService $cartService)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:999',
        ]);

        $cartService->updateItemQuantity($itemId, (int) $validated['quantity']);

        return redirect()->route('shop.cart')->with('success', 'Sepet güncellendi');
    }

    public function remove(int $itemId, ShopCartService $cartService)
    {
        $cartService->removeItem($itemId);

        return redirect()->route('shop.cart')->with('success', 'Ürün sepetten kaldırıldı');
    }
}
