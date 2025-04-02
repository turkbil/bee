<?php
// Modules/WidgetManagement/routes/web.php

use Illuminate\Support\Facades\Route;
use Modules\WidgetManagement\app\Http\Livewire\WidgetComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetManageComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetSectionComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetItemComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetSettingsComponent;
use Modules\WidgetManagement\app\Http\Controllers\WidgetPreviewController;

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Widget Routes
        Route::prefix('widgetmanagement')
            ->name('widgetmanagement.')
            ->group(function () {
                // Widget listesi
                Route::get('/', WidgetComponent::class)
                    ->middleware('module.permission:widgetmanagement,view')
                    ->name('index');
                
                // Widget yönetimi - id parametresi opsiyonel
                Route::get('/manage/{id?}', WidgetManageComponent::class)
                    ->middleware('module.permission:widgetmanagement,update')
                    ->name('manage');
                
                // Widget Bölüm Yönetimi - tüm parametreler opsiyonel
                Route::get('/section/{pageId?}/{module?}/{position?}', WidgetSectionComponent::class)
                    ->middleware('module.permission:widgetmanagement,view')
                    ->name('section');
                
                // Widget İçerik Yönetimi
                Route::get('/items/{tenantWidgetId}', WidgetItemComponent::class)
                    ->middleware('module.permission:widgetmanagement,update')
                    ->where('tenantWidgetId', '[0-9]+')
                    ->name('items');
                
                // Widget Ayarları
                Route::get('/settings/{tenantWidgetId}', WidgetSettingsComponent::class)
                    ->middleware('module.permission:widgetmanagement,update')
                    ->where('tenantWidgetId', '[0-9]+')
                    ->name('settings');
                
                // Widget Önizleme
                Route::get('/preview/{id}', [WidgetPreviewController::class, 'show'])
                    ->middleware('module.permission:widgetmanagement,view')
                    ->where('id', '[0-9]+')
                    ->name('preview');
                
                // Widget tipine göre listeleme
                Route::get('/types/{type}', WidgetComponent::class)
                    ->middleware('module.permission:widgetmanagement,view')
                    ->name('types');
            });
    });