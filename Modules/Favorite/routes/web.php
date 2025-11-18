<?php
// Modules/Favorite/routes/web.php

use Illuminate\Support\Facades\Route;
use Modules\Favorite\App\Services\FavoriteService;

// ⚠️  FRONTEND ROUTES DynamicRouteService TARAFINDAN YÖNETİLİYOR
// Bu dosyada sadece özel route'lar (homefavorite gibi) tanımlanmalı
// Normal content route'ları (index, show) DynamicRouteService'den geliyor

// Ana sayfa route'u routes/web.php'de tanımlı

// Favorite Toggle API (Web middleware ile CSRF + session destekli)
Route::middleware(['web', 'tenant', 'auth'])
    ->prefix('api/favorites')
    ->name('api.favorites.')
    ->group(function () {
        Route::post('/toggle', function(\Illuminate\Http\Request $request) {

            $service = app(FavoriteService::class);

            $result = $service->toggleFavorite(
                $request->input('model_class'),
                $request->input('model_id'),
                auth()->id()
            );

            return response()->json($result);
        })->name('toggle');
    });
