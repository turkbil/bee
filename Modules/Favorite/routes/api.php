<?php

use Illuminate\Support\Facades\Route;
use Modules\Favorite\App\Services\FavoriteService;

// API rotaları - Favorite işlemleri
Route::middleware(['tenant'])
    ->prefix('favorites')
    ->name('favorites.')
    ->group(function () {

        // Toggle favorite (ekle/çıkar)
        Route::post('/toggle', function(\Illuminate\Http\Request $request) {
            // Auth kontrolü - web session veya sanctum token
            if (!auth()->check() && !auth('sanctum')->check()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $service = app(FavoriteService::class);
            $userId = auth()->id() ?? auth('sanctum')->id();

            $result = $service->toggleFavorite(
                $request->input('model_class'),
                $request->input('model_id'),
                $userId
            );

            return response()->json($result);
        })->name('toggle');

        // Kullanıcının favorileri
        Route::get('/my-favorites', function(\Illuminate\Http\Request $request) {
            $service = app(FavoriteService::class);

            $favorites = $service->getUserFavorites(
                auth()->id(),
                $request->input('model_type'),
                $request->input('per_page', 15)
            );

            return response()->json($favorites);
        })->middleware('auth:sanctum')->name('my-favorites');
    });
