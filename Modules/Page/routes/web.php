<?php
// Modules/Page/routes/web.php
use Illuminate\Support\Facades\Route;
use Modules\Page\App\Http\Livewire\PageComponent;
use Modules\Page\App\Http\Livewire\PageManageComponent;

Route::middleware(['web', 'auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('page')
            ->name('page.')
            ->group(function () {
                Route::get('/', PageComponent::class)->name('index');
                Route::get('/manage/{id?}', PageManageComponent::class)->name('manage');
            });
    });
