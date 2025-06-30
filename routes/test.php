<?php

use Illuminate\Support\Facades\Route;

Route::get('/test/language-selectors', function () {
    return view('test.language-selectors');
})->name('test.language-selectors');