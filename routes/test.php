<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Auth test endpoint
Route::get('/test-auth-status', function () {
    return response()->json([
        'auth_check' => Auth::check(),
        'user' => Auth::check() ? [
            'id' => Auth::user()->id,
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
        ] : null,
        'session_id' => session()->getId(),
        'session_driver' => config('session.driver'),
        'cookie' => request()->cookie(config('session.cookie')),
    ]);
})->middleware('web');
