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

        // TRY cinsinden toplam hesapla (her item için currency check)
        $subtotalTRY = 0;
        $taxAmountTRY = 0;

        foreach ($items as $item) {
            $exchangeRate = 1;

            // USD veya başka currency ise TRY'ye çevir
            if ($item->currency && $item->currency->code !== 'TRY') {
                $exchangeRate = $item->currency->exchange_rate ?? 1;
            }

            $subtotalTRY += ($item->subtotal ?? 0) * $exchangeRate;
            $taxAmountTRY += ($item->tax_amount ?? 0) * $exchangeRate;
        }

        $totalTRY = $subtotalTRY + $taxAmountTRY;

        return view('shop::front.cart.index', [
            'cart' => $cart,
            'items' => $items,
            'itemCount' => $cartService->getItemCount(),
            'total' => $totalTRY,
            'subtotal' => $subtotalTRY,
            'taxAmount' => $taxAmountTRY,
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
