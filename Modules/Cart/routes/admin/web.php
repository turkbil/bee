<?php

use Illuminate\Support\Facades\Route;
use Modules\Cart\App\Models\Cart;

// Admin Cart Routes
Route::prefix('admin/cart')
    ->middleware(['web', 'admin', 'tenant'])
    ->name('admin.cart.')
    ->group(function () {
        // Cart Management
        Route::get('/', \Modules\Cart\App\Http\Livewire\Admin\CartComponent::class)->name('index');

        // Cart Detail AJAX
        Route::get('/{cartId}/detail', function ($cartId) {
            $cart = Cart::with(['items', 'currency', 'customer'])->find($cartId);

            if (!$cart) {
                return response()->json(['success' => false, 'message' => 'Sepet bulunamadi']);
            }

            $html = view('cart::livewire.admin.partials.cart-detail', compact('cart'))->render();

            return response()->json([
                'success' => true,
                'cart' => $cart,
                'html' => $html
            ]);
        })->name('detail');
    });

// Admin Orders Routes
Route::prefix('admin/orders')
    ->middleware(['web', 'admin', 'tenant'])
    ->name('admin.orders.')
    ->group(function () {
        // Orders Management
        Route::get('/', \Modules\Cart\App\Http\Livewire\Admin\OrdersComponent::class)->name('index');

        // Order Detail AJAX
        Route::get('/{orderId}/detail', function ($orderId) {
            try {
                $order = \Modules\Cart\App\Models\Order::with(['items', 'payments'])->find($orderId);

                if (!$order) {
                    return response()->json(['success' => false, 'message' => 'Sipariş bulunamadı']);
                }

                $html = view('cart::livewire.admin.partials.order-detail', compact('order'))->render();

                return response()->json([
                    'success' => true,
                    'order' => $order,
                    'html' => $html
                ]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
        })->name('detail');
    });
