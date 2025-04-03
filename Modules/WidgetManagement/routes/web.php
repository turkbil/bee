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
                
                // Widget yönetimi - id parametresi opsiyonel - SADECE ROOT
                Route::get('/manage/{id?}', WidgetManageComponent::class)
                    ->middleware(['module.permission:widgetmanagement,update', 'role:root'])
                    ->name('manage');
                
                // Widget Bölüm Yönetimi - isimli parametreler ile
                Route::get('/section', WidgetSectionComponent::class)
                    ->middleware('module.permission:widgetmanagement,view')
                    ->name('section');
                
                // Konum parametreli route ekleyelim (daha net URL için)
                Route::get('/section/position/{position}', WidgetSectionComponent::class)
                    ->middleware('module.permission:widgetmanagement,view')
                    ->name('section.position');
                    
                // Genel bakış görünümü için route
                Route::get('/section/overview', WidgetSectionComponent::class)
                    ->defaults('overview', true)
                    ->middleware('module.permission:widgetmanagement,view')
                    ->name('section.overview');
                
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
            });
    });