<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['admin', 'tenant'])
    ->prefix('admin/mail')
    ->name('admin.mail.')
    ->group(function () {
        Route::get('/', fn() => view('mail::admin.index'))->name('index');
    });
