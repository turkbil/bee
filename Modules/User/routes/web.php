<?php
// Modules/User/routes/web.php
use Illuminate\Support\Facades\Route;
use Modules\User\App\Http\Livewire\UserComponent;
use Modules\User\App\Http\Livewire\UserManageComponent;

Route::middleware(['web', 'auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('user')
            ->name('user.')
            ->group(function () {
                Route::get('/', UserComponent::class)->name('index');
                Route::get('/manage/{id?}', UserManageComponent::class)->name('manage');
            });
    });
