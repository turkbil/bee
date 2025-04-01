<?php

use Illuminate\Support\Facades\Route;
use Modules\WidgetManagement\app\Http\Livewire\WidgetComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetManageComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetSectionComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetItemComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetSettingsComponent;

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin/widgetmanagement')
    ->name('admin.widgetmanagement.')
    ->group(function () {
        // Widget Yönetimi
        Route::get('/', WidgetComponent::class)
            ->middleware('module.permission:widgetmanagement,view')
            ->name('index');
        
        // Widget Oluşturma/Düzenleme
        Route::get('/manage/{id?}', WidgetManageComponent::class)
            ->middleware('module.permission:widgetmanagement,update')
            ->name('manage');
        
        // Widget Sayfa/Modül Widget Bölümleri
        Route::get('/section/{pageId?}/{module?}/{position?}', WidgetSectionComponent::class)
            ->middleware('module.permission:widgetmanagement,view')
            ->name('section');
        
        // Widget Öğeleri
        Route::get('/items/{tenantWidgetId}', WidgetItemComponent::class)
            ->middleware('module.permission:widgetmanagement,update')
            ->name('items');
        
        // Widget Ayarları
        Route::get('/settings/{tenantWidgetId}', WidgetSettingsComponent::class)
            ->middleware('module.permission:widgetmanagement,update')
            ->name('settings');
    });