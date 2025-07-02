<?php

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
use Modules\WidgetManagement\App\Http\Livewire\WidgetFormBuilderComponent;

Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('widgetmanagement')
            ->name('widgetmanagement.')
            ->group(function () {
                Route::get('/', WidgetComponent::class)
                    ->middleware('module.permission:widgetmanagement,view')
                    ->name('index');
                
                Route::get('/gallery', WidgetGalleryComponent::class)
                    ->middleware('module.permission:widgetmanagement,view')
                    ->name('gallery');
                    
                Route::get('/manage/{id?}', WidgetManageComponent::class)
                    ->middleware(['role:root'])
                    ->name('manage');
                
                Route::get('/code-editor/{id}', WidgetCodeEditorComponent::class)
                    ->middleware(['role:root'])
                    ->name('code-editor');
                
                Route::get('/items/{tenantWidgetId}', WidgetItemComponent::class)
                    ->middleware('module.permission:widgetmanagement,view')
                    ->where('tenantWidgetId', '[0-9]+')
                    ->name('items');
                
                Route::get('/manage/item/{tenantWidgetId}/{itemId?}', WidgetItemManageComponent::class)
                    ->middleware('module.permission:widgetmanagement,update')
                    ->where('tenantWidgetId', '[0-9]+')
                    ->where('itemId', '[0-9]+')
                    ->name('item.manage');
                
                Route::get('/settings/{tenantWidgetId}', WidgetSettingsComponent::class)
                    ->middleware('module.permission:widgetmanagement,update')
                    ->where('tenantWidgetId', '[0-9]+')
                    ->name('settings');
                
                Route::get('/preview/template/{widgetId}', [WidgetPreviewController::class, 'showTemplate'])
                    ->middleware('module.permission:widgetmanagement,view')
                    ->where('widgetId', '[0-9]+')
                    ->name('preview.template');
                
                Route::get('/preview/instance/{tenantWidgetId}', [WidgetPreviewController::class, 'showInstance'])
                    ->middleware('module.permission:widgetmanagement,view')
                    ->where('tenantWidgetId', '[0-9]+')
                    ->name('preview.instance');
                
                Route::get('/preview/embed/{tenantWidgetId}', [WidgetPreviewController::class, 'embed'])
                    ->middleware('module.permission:widgetmanagement,view')
                    ->where('tenantWidgetId', '[0-9]+')
                    ->name('preview.embed');
                
                Route::get('/preview/embed/json/{tenantWidgetId}', [WidgetPreviewController::class, 'embedJson'])
                    ->middleware('module.permission:widgetmanagement,view')
                    ->where('tenantWidgetId', '[0-9]+')
                    ->name('preview.embed.json');
                
                Route::get('/api/module/{moduleId}', [WidgetPreviewController::class, 'moduleJson'])
                    ->middleware('module.permission:widgetmanagement,view')
                    ->where('moduleId', '[0-9]+')
                    ->name('api.module');
                
                Route::get('/category', WidgetCategoryComponent::class)
                    ->middleware('module.permission:widgetmanagement,view')
                    ->name('category.index');

                Route::get('/file-widgets', FileWidgetListComponent::class)
                    ->middleware(['role:root'])
                    ->name('files');

                Route::get('/modules', ModuleWidgetListComponent::class)
                    ->middleware(['role:root'])
                    ->name('modules');

                Route::get('/form-builder/{widgetId}/load/{schemaType}', [WidgetFormBuilderController::class, 'load'])
                    ->middleware('module.permission:widgetmanagement,view')
                    ->name('form-builder.load');

                Route::post('/form-builder/{widgetId}/save/{schemaType}', [WidgetFormBuilderController::class, 'save'])
                    ->middleware('module.permission:widgetmanagement,update')
                    ->name('form-builder.save');

                Route::get('/form-builder/{widgetId}/{schemaType}', WidgetFormBuilderComponent::class)
                    ->middleware('module.permission:widgetmanagement,update')
                    ->where('schemaType', 'items|settings')
                    ->name('form-builder.edit');
            });
    });