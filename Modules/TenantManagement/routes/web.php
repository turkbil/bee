<?php
// Modules/TenantManagement/routes/web.php
use Illuminate\Support\Facades\Route;
use Modules\TenantManagement\App\Http\Livewire\TenantComponent;

Route::middleware(['web', 'auth', 'tenant', 'root.access'])
    ->prefix('admin/tenantmanagement')
    ->name('admin.tenantmanagement.')
    ->group(function () {
        Route::get('/', TenantComponent::class)->name('index');
    });