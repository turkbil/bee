<?php

use Illuminate\Support\Facades\Route;

// Database bağımsız schema test sayfası
Route::get('/test-schema', function () {
    // Test page objesi oluştur
    $page = (object) [
        'title' => 'Test Schema Sayfası',
        'excerpt' => 'Bu sayfa Schema.org test için oluşturulmuştur.',
        'updated_at' => now()
    ];
    
    return view('test-schema', compact('page'));
});