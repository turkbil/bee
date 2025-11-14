<?php
// Modules/Blog/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\Blog\App\Http\Livewire\Admin\BlogComponent;
use Modules\Blog\App\Http\Livewire\Admin\BlogManageComponent;
use Modules\Blog\App\Http\Livewire\Admin\BlogCategoryComponent;
use Modules\Blog\App\Http\Livewire\Admin\BlogCategoryManageComponent;
use Modules\Blog\App\Http\Livewire\Admin\BlogAIDraftComponent;

// Admin rotaları
Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('blog')
            ->name('blog.')
            ->group(function () {
                Route::get('/', BlogComponent::class)
                    ->middleware('module.permission:blog,view')
                    ->name('index');

                Route::get('/manage/{id?}', BlogManageComponent::class)
                    ->middleware('module.permission:blog,update')
                    ->name('manage');

                // AI Taslaklar route
                Route::get('/ai-drafts', BlogAIDraftComponent::class)
                    ->middleware('module.permission:blog,update')
                    ->name('ai-drafts');

                // Kategori route'ları
                Route::prefix('category')
                    ->name('category.')
                    ->group(function () {
                        Route::get('/', BlogCategoryComponent::class)
                            ->middleware('module.permission:blog,view')
                            ->name('index');

                        Route::get('/manage/{id?}', BlogCategoryManageComponent::class)
                            ->middleware('module.permission:blog,update')
                            ->name('manage');
                    });
            });
    });
