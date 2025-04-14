<?php
// Modules/Studio/routes/web.php
use Illuminate\Support\Facades\Route;

// Ön yüz rotaları
Route::middleware(['web', 'tenant'])
    ->group(function () {
        // Şimdilik bir şey yok
    });