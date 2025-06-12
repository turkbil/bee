<?php

// Test route'u
Route::middleware([App\Http\Middleware\InitializeTenancy::class])->get('/test-slug-debug', function() {
    $currentDatabase = \DB::connection()->getDatabaseName();
    $currentDomain = request()->getHost();
    
    try {
        // Domain tablosuna bak
        $domainRecord = \DB::connection('mysql')->table('domains')->where('domain', $currentDomain)->first();
        
        $settings = \App\Models\ModuleTenantSetting::all();
        $pageSlug = \App\Services\ModuleSlugService::getSlug('Page', 'index');
        
        return response()->json([
            'domain' => $currentDomain,
            'database_name' => $currentDatabase,
            'domain_record' => $domainRecord,
            'tenancy_initialized' => app()->bound('tenancy') ? app('tenancy')->initialized : 'not_bound',
            'settings_count' => $settings->count(),
            'settings' => $settings->toArray(),
            'page_index_slug' => $pageSlug,
            'url_test' => url('/'),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'domain' => $currentDomain,
            'database_name' => $currentDatabase,
            'error' => $e->getMessage(),
            'url_test' => url('/'),
        ]);
    }
});

// Basit test route'u - middleware olmadan
Route::get('/simple-test', function() {
    return response()->json([
        'message' => 'Route çalışıyor',
        'domain' => request()->getHost(),
        'database' => \DB::connection()->getDatabaseName(),
    ]);
});