<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\InitializeTenancy;
use App\Http\Middleware\RedirectIfAuthenticatedExceptDeviceLimit;

Route::middleware([InitializeTenancy::class, RedirectIfAuthenticatedExceptDeviceLimit::class])
    ->withoutMiddleware([\Modules\LanguageManagement\app\Http\Middleware\SiteSetLocaleMiddleware::class])
    ->group(function () {
    // 🔄 Register GET → Login'e yönlendir (tab=register ile)
    Route::get('register', function() {
        return redirect('/login?tab=register');
    })->name('register');

    Route::post('register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:3,5'); // Max 3 registration per 5 minutes

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:60,1'); // Max 60 attempt per minute

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email')
        ->middleware('throttle:3,1'); // Max 3 password reset per minute

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

// 📧 Email Verification Route (No auth required - signed URL is enough)
Route::middleware([InitializeTenancy::class])
    ->get('verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::middleware([InitializeTenancy::class, 'auth'])->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // Logout - POST (actual logout)
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Logout - GET (redirect to auto-submit form)
    Route::get('logout', function () {
        return view('auth.logout-form');
    })->name('logout.form');
});
