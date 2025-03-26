<?php
// Modules/ModuleManagement/routes/web.php
use Illuminate\Support\Facades\Route;
use Modules\ModuleManagement\App\Http\Livewire\ModuleComponent;
use Modules\ModuleManagement\App\Http\Livewire\ModuleManageComponent;

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('modulemanagement')
            ->name('modulemanagement.')
            ->group(function () {
                Route::get('/', ModuleComponent::class)
                    ->middleware('module.permission:modulemanagement,view')
                    ->name('index');
                Route::get('/manage/{id?}', ModuleManageComponent::class)
                    ->middleware('module.permission:modulemanagement,update')
                    ->name('manage');
            });
    });