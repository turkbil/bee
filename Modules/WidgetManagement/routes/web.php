<?php
// Modules/WidgetManagement/routes/web.php

use Illuminate\Support\Facades\Route;
use Modules\WidgetManagement\app\Http\Livewire\WidgetComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetGalleryComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetManageComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetSectionComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetItemComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetItemManageComponent;
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
                // Bileşen Yönetimi Ana Sayfa (Aktif Bileşenler)
                Route::get('/', WidgetComponent::class)
                    ->middleware('module.permission:widgetmanagement,view')
                    ->name('index');
                
                // Bileşen Galerisi Sayfası
                Route::get('/gallery', WidgetGalleryComponent::class)
                    ->middleware('module.permission:widgetmanagement,view')
                    ->name('gallery');
                    
                // Widget Şablonu Yönetimi - SADECE ROOT
                Route::get('/manage/{id?}', WidgetManageComponent::class)
                    ->middleware(['role:root'])
                    ->name('manage');
                
                // Bölüm Yönetimi
                Route::get('/section', WidgetSectionComponent::class)
                    ->middleware('module.permission:widgetmanagement,view')
                    ->name('section');
                
                // Konum parametreli route
                Route::get('/section/position/{position}', WidgetSectionComponent::class)
                    ->middleware('module.permission:widgetmanagement,view')
                    ->name('section.position');
                    
                // Genel bakış görünümü için route
                Route::get('/section/overview', WidgetSectionComponent::class)
                    ->defaults('overview', true)
                    ->middleware('module.permission:widgetmanagement,view')
                    ->name('section.overview');
                
                // Widget İçerik Yönetimi (Görüntüleme)
                Route::get('/items/{tenantWidgetId}', WidgetItemComponent::class)
                    ->middleware('module.permission:widgetmanagement,view')
                    ->where('tenantWidgetId', '[0-9]+')
                    ->name('items');
                
                // Widget İçerik Ekleme
                Route::get('/content/create/{tenantWidgetId}', WidgetItemManageComponent::class)
                    ->middleware('module.permission:widgetmanagement,update')
                    ->where('tenantWidgetId', '[0-9]+')
                    ->name('content.create');
                
                // Widget İçerik Düzenleme
                Route::get('/content/edit/{itemId}', WidgetItemManageComponent::class)
                    ->middleware('module.permission:widgetmanagement,update')
                    ->where('itemId', '[0-9]+')
                    ->name('content.edit');
                
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