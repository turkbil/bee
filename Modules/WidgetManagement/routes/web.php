<?php
// Modules/WidgetManagement/routes/web.php

use Illuminate\Support\Facades\Route;
use Modules\WidgetManagement\app\Http\Livewire\WidgetComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetGalleryComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetManageComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetItemComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetItemManageComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetSettingsComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetCategoryComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetCategoryManageComponent;
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
                
                // Widget İçerik Yönetimi (Görüntüleme)
                Route::get('/items/{tenantWidgetId}', WidgetItemComponent::class)
                    ->middleware('module.permission:widgetmanagement,view')
                    ->where('tenantWidgetId', '[0-9]+')
                    ->name('items');
                
                // Widget İçerik Yönetimi (Ekleme/Düzenleme) - Manage Mantığı
                Route::get('/manage/item/{tenantWidgetId}/{itemId?}', WidgetItemManageComponent::class)
                    ->middleware('module.permission:widgetmanagement,update')
                    ->where('tenantWidgetId', '[0-9]+')
                    ->where('itemId', '[0-9]+')
                    ->name('item.manage');
                
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
                
                // Kategori Yönetimi Rotaları
                Route::get('/category', WidgetCategoryComponent::class)
                    ->middleware('module.permission:widgetmanagement,view')
                    ->name('category.index');
                
                Route::get('/category/manage/{id?}', WidgetCategoryManageComponent::class)
                    ->middleware('module.permission:widgetmanagement,update')
                    ->name('category.manage');
            });
    });