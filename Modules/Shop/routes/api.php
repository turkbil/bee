<?php

use Illuminate\Support\Facades\Route;
use Modules\Shop\App\Http\Controllers\Api\ShopApiController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::prefix('v1/shops')->group(function () {
    Route::get('/', [ShopApiController::class, 'index'])->name('api.shops.index');
    Route::get('{slug}', [ShopApiController::class, 'show'])->name('api.shops.show');
});

// Cart API
Route::prefix('cart')->middleware(['web', 'tenant'])->group(function () {
    Route::post('add', function (\Illuminate\Http\Request $request) {
        try {
            $cartService = app(\Modules\Shop\App\Services\ShopCartService::class);

            $item = $cartService->addItem(
                $request->input('product_id'),
                $request->input('quantity', 1),
                $request->input('variant_id')
            );

            return response()->json([
                'success' => true,
                'message' => 'Ürün sepete eklendi',
                'cart_item_id' => $item->cart_item_id,
                'cart_count' => $cartService->getItemCount(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    })->name('api.cart.add');
});
