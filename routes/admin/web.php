<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use App\Http\Middleware\InitializeTenancy;

Route::middleware(['web', 'auth', 'admin.access', 'tenant'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        $currentTenant = tenancy()->tenant;
        
        if (!$currentTenant) {
            return view('admin.index');
        }

        // Redis'te tenant bazlı önbellekleme
        $redisKey = "tenant:{$currentTenant->id}:stats";
        $tenantStats = Cache::store('redis')->remember($redisKey, 3600, function () use ($currentTenant) {
            return [
                'id' => $currentTenant->id,
                'name' => $currentTenant->name ?? 'Varsayılan',
                'domain' => request()->getHost(),
                'created_at' => $currentTenant->created_at?->format('d.m.Y H:i:s') ?? 'Belirtilmemiş'
            ];
        });

        return view('admin.index', compact('tenantStats'));
    })->name('dashboard');
});