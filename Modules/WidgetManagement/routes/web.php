<?php
// Modules/WidgetManagement/routes/web.php

use Illuminate\Support\Facades\Route;
use Modules\WidgetManagement\app\Http\Livewire\WidgetComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetGalleryComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetManageComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetCodeEditorComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetItemComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetItemManageComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetSettingsComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetCategoryComponent;
use Modules\WidgetManagement\app\Http\Controllers\WidgetPreviewController;
use Modules\WidgetManagement\app\Http\Livewire\FileWidgetListComponent;
use Modules\WidgetManagement\app\Http\Livewire\ModuleWidgetListComponent;
use Modules\WidgetManagement\app\Http\Controllers\WidgetFormBuilderController;
use Modules\WidgetManagement\app\Http\Livewire\WidgetFormBuilderComponent;

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
                
                // Widget Kod Editörü - SADECE ROOT
                Route::get('/code-editor/{id}', WidgetCodeEditorComponent::class)
                    ->middleware(['role:root'])
                    ->name('code-editor');
                
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
                
                Route::get('/preview/embed/{tenantWidgetId}', [WidgetPreviewController::class, 'embed'])
                    ->middleware('module.permission:widgetmanagement,view')
                    ->where('tenantWidgetId', '[0-9]+')
                    ->name('preview.embed');
                
                // JSON embed endpoint for Studio loader
                Route::get('/preview/embed/json/{tenantWidgetId}', [WidgetPreviewController::class, 'embedJson'])
                    ->middleware('module.permission:widgetmanagement,view')
                    ->where('tenantWidgetId', '[0-9]+')
                    ->name('preview.embed.json');
                
                // Module widget için JSON API - YENİ EKLENEN ENDPOINT
                Route::get('/api/module/{moduleId}', [WidgetPreviewController::class, 'moduleJson'])
                    ->middleware('module.permission:widgetmanagement,view')
                    ->where('moduleId', '[0-9]+')
                    ->name('api.module');
                
                // Kategori Yönetimi Rotaları
                Route::get('/category', WidgetCategoryComponent::class)
                    ->middleware('module.permission:widgetmanagement,view')
                    ->name('category.index');

                // File Widget Routes (sadece root yetkileri)
                Route::get('/file-widgets', FileWidgetListComponent::class)
                    ->middleware(['role:root'])
                    ->name('files');

                Route::get('/file-widgets/preview/{id}', [WidgetPreviewController::class, 'showFile'])
                    ->middleware(['role:root'])
                    ->where('id', '[0-9]+')
                    ->name('file.preview');
                    
                Route::get('/modules', ModuleWidgetListComponent::class)
                    ->middleware(['role:root'])
                    ->name('modules');
                
                Route::get('/modules/preview/{id}', [WidgetPreviewController::class, 'showModule'])
                    ->middleware(['role:root'])
                    ->where('id', '[0-9]+')
                    ->name('modules.preview');

                // Widget Form Builder API endpoints - ÖNCELİKLİ ROUTE'LAR
                Route::get('/form-builder/{widgetId}/load/{schemaType}', [WidgetFormBuilderController::class, 'load'])
                    ->middleware('module.permission:widgetmanagement,view')
                    ->name('form-builder.load');

                Route::post('/form-builder/{widgetId}/save/{schemaType}', [WidgetFormBuilderController::class, 'save'])
                    ->middleware('module.permission:widgetmanagement,update')
                    ->name('form-builder.save');

                // Widget Form Builder routes - Livewire yaklaşımıyla
                Route::get('/form-builder/{widgetId}/{schemaType}', WidgetFormBuilderComponent::class)
                    ->middleware('module.permission:widgetmanagement,update')
                    ->where('schemaType', 'items|settings')
                    ->name('form-builder.edit');
            });
    });