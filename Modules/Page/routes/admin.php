<?php
// Modules/Page/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\Page\App\Http\Livewire\Admin\PageComponent;
use Modules\Page\App\Http\Livewire\Admin\PageManageComponent;

// Admin rotaları
Route::middleware(['admin', 'tenant'])
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
                Route::post('/set-editing-language', function () {
                    return response()->json(['status' => 'success']);
                })->name('set-editing-language');
            });
    });