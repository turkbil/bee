<?php
// Modules/Studio/routes/web.php
use Illuminate\Support\Facades\Route;

// Front-end rotaları
Route::middleware(['web', 'tenant'])
    ->group(function () {
        // Şimdilik bir şey yok
    });