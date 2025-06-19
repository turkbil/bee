<?php
// Modules/TenantManagement/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\TenantManagement\App\Http\Livewire\TenantComponent;

Route::middleware(['web', 'auth', 'tenant', 'root.access'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('tenantmanagement')
            ->name('tenantmanagement.')
            ->group(function () {
                Route::get('/', TenantComponent::class)->name('index');
            });
    });