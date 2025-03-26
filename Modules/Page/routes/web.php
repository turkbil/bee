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
                    
                // Yeni sayfa oluşturma - create izni kontrolü
                Route::get('/manage', PageManageComponent::class)
                    ->middleware('module.permission:page,create')
                    ->name('create');
                    
                // Mevcut sayfayı düzenleme - update izni kontrolü 
                Route::get('/manage/{id}', PageManageComponent::class)
                    ->middleware('module.permission:page,update')
                    ->where('id', '[0-9]+')
                    ->name('edit');
            });
    });