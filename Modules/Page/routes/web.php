<?php
// Modules/Page/routes/web.php
use Illuminate\Support\Facades\Route;
use Modules\Page\App\Http\Livewire\PageComponent;
use Modules\Page\App\Http\Livewire\PageManageComponent;

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('page')
            ->name('page.')
            ->group(function () {
                Route::get('/', PageComponent::class)
                    ->middleware('module.permission:page,view')
                    ->name('index');
                Route::get('/manage/{id?}', PageManageComponent::class)
                    ->middleware('module.permission:page,update')
                    ->name('manage');
            });
    });