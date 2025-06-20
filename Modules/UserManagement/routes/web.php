<?php

use Modules\UserManagement\App\Http\Controllers\Front\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile/avatar', [ProfileController::class, 'avatar'])->name('profile.avatar');
});