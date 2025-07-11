<?php

use Illuminate\Support\Facades\Route;

Route::get('/test/language-selectors', function () {
    return view('test.language-selectors');
})->name('test.language-selectors');

// AI Profil test sayfası
Route::get('/ai-profil', function () {
    // AI Profil index.php sayfasını göster
    $indexPath = base_path('ai-profil/index.php');
    
    if (file_exists($indexPath)) {
        // PHP dosyasını include et ve çıktısını yakala
        ob_start();
        include $indexPath;
        $content = ob_get_clean();
        
        return response($content)->header('Content-Type', 'text/html');
    }
    
    return response('AI Profil dosyası bulunamadı! Path: ' . $indexPath, 404);
})->name('ai-profil.demo');