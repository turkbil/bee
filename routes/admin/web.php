<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use App\Http\Middleware\InitializeTenancy;

// Genel admin rotaları - sadece roller tablosunda kaydı olan kullanıcılar için
Route::middleware(['web', 'auth', 'tenant'])->prefix('admin')->name('admin.')->group(function () {
    
    // Admin dashboard rotası - TÜM yetkilendirilmiş kullanıcılar için (editor, admin, root)
    Route::get('/dashboard', function () {
        // Rol kontrolü
        if (!auth()->user()->hasAnyRole(['admin', 'root', 'editor'])) {
            abort(403, 'Bu alana erişim yetkiniz bulunmamaktadır.');
        }
        
        $currentTenant = null;
        
        if (app(\Stancl\Tenancy\Tenancy::class)->initialized) {
            $currentTenant = tenant();
        }
        
        if (!$currentTenant) {
            return view('admin.index');
        }
        
        // Redis'te tenant bazlı önbellekleme
        $redisKey = "tenant:{$currentTenant->id}:stats";
        $tenantStats = Cache::store('redis')->remember($redisKey, 3600, function () use ($currentTenant) {
            return [
                'id' => $currentTenant->id,
                'name' => $currentTenant->title ?? 'Varsayılan',
                'domain' => request()->getHost(),
                'created_at' => $currentTenant->created_at?->format('d.m.Y H:i:s') ?? 'Belirtilmemiş'
            ];
        });
        
        return view('admin.index', compact('tenantStats'));
    })->name('dashboard');
    
    // Profil düzenleme sayfası - tüm yetkilendirilmiş kullanıcılar için
    Route::get('/profile', function () {
        // Rol kontrolü
        if (!auth()->user()->hasAnyRole(['admin', 'root', 'editor'])) {
            abort(403, 'Bu alana erişim yetkiniz bulunmamaktadır.');
        }
        
        return view('admin.profile');
    })->name('profile');
    
    // Yetkisiz erişim sayfası - özel 403 sayfasına yönlendirilecek
    Route::get('/access-denied', function() {
        abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
    })->name('access.denied');
});

// Diğer admin routes - spesifik modül erişimleri için admin.access middleware'i kullanabilirsiniz
Route::middleware(['web', 'auth', 'tenant', 'admin.access'])->prefix('admin')->name('admin.')->group(function () {
    // Burada spesifik admin kontrolleri gerektiren rotaları tanımlayabilirsiniz
});