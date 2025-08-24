<?php
// Modules/TenantManagement/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\TenantManagement\App\Http\Livewire\TenantComponent;
use Modules\TenantManagement\App\Http\Livewire\TenantMonitoringComponent;
use Modules\TenantManagement\App\Http\Livewire\TenantLimitsComponent;
use Modules\TenantManagement\App\Http\Livewire\TenantRateLimitComponent;
use Modules\TenantManagement\App\Http\Livewire\TenantCacheComponent;
use Modules\TenantManagement\App\Http\Livewire\Admin\TenantPoolMonitoringComponent;
use Modules\TenantManagement\App\Http\Livewire\Admin\TenantHealthCheckComponent;

Route::middleware(['web', 'auth', 'tenant', 'root.access'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('tenantmanagement')
            ->name('tenantmanagement.')
            ->group(function () {
                // Tenant Listesi
                Route::get('/', TenantComponent::class)->name('index');
                
                // Monitoring Dashboard
                Route::get('/monitoring', TenantMonitoringComponent::class)->name('monitoring');
                
                // Resource Limits
                Route::get('/limits', TenantLimitsComponent::class)->name('limits');
                
                // Rate Limiting
                Route::get('/rate-limits', TenantRateLimitComponent::class)->name('rate-limits');
                
                // Cache Management
                Route::get('/cache', TenantCacheComponent::class)->name('cache');
                
                // Database Pool Monitoring
                Route::get('/pool-monitoring', TenantPoolMonitoringComponent::class)->name('pool-monitoring');
                
                // Auto Scaling
                Route::get('/auto-scaling', \Modules\TenantManagement\App\Http\Livewire\TenantAutoScalingComponent::class)->name('auto-scaling');
                
                // Health Check Dashboard
                Route::get('/health-check', TenantHealthCheckComponent::class)->name('health-check');
            });
    });