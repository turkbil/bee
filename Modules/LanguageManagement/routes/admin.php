<?php

use Illuminate\Support\Facades\Route;
use Modules\LanguageManagement\app\Http\Livewire\Admin\LanguageSettingsComponent;
use Modules\LanguageManagement\app\Http\Livewire\Admin\AdminLanguageComponent;
use Modules\LanguageManagement\app\Http\Livewire\Admin\AdminLanguageManageComponent;
use Modules\LanguageManagement\app\Http\Livewire\Admin\TenantLanguageComponent;
use Modules\LanguageManagement\app\Http\Livewire\Admin\TenantLanguageManageComponent;
use Modules\LanguageManagement\app\Http\Livewire\Admin\TranslationManageComponent;

// Admin rotaları
Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('languagemanagement')
            ->name('languagemanagement.')
            ->group(function () {
                // Ana sayfa (dil ayarları)
                Route::get('/', LanguageSettingsComponent::class)
                    ->middleware('module.permission:languagemanagement,view')
                    ->name('index');
                

                // Sistem dilleri (Admin paneli) - Sadece central domain
                Route::prefix('system')
                    ->name('system.')
                    ->middleware('central.domain')
                    ->group(function () {
                        Route::get('/', AdminLanguageComponent::class)
                            ->middleware('module.permission:languagemanagement,view')
                            ->name('index');
                        
                        Route::get('/manage/{id?}', AdminLanguageManageComponent::class)
                            ->middleware('module.permission:languagemanagement,create')
                            ->name('manage');
                    });

                // Site dilleri (Frontend içerik)
                Route::prefix('site')
                    ->name('site.')
                    ->group(function () {
                        Route::get('/', TenantLanguageComponent::class)
                            ->middleware('module.permission:languagemanagement,view')
                            ->name('index');
                        
                        Route::get('/manage/{id?}', TenantLanguageManageComponent::class)
                            ->middleware('module.permission:languagemanagement,create')
                            ->name('manage');
                    });

                // Çeviri yönetimi
                Route::get('/translations', TranslationManageComponent::class)
                    ->middleware('module.permission:languagemanagement,edit')
                    ->name('translations');
            });
    });