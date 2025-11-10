<?php

use Illuminate\Support\Facades\Route;
use Modules\Favorite\App\Services\FavoriteService;

// API rotaları - Favorite işlemleri
Route::middleware(['api', 'tenant'])
    ->prefix('api/favorites')
    ->name('api.favorites.')
    ->group(function () {

        // Toggle favorite (ekle/çıkar)
        Route::post('/toggle', function(\Illuminate\Http\Request $request) {
            $service = app(FavoriteService::class);

            $result = $service->toggleFavorite(
                $request->input('model_class'),
                $request->input('model_id'),
                auth()->id()
            );

            return response()->json($result);
        })->middleware('auth:sanctum')->name('toggle');

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
