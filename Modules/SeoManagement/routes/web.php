<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => 'admin', 'middleware' => ['admin', 'tenant']], function () {
    Route::get('/seomanagement', [Modules\SeoManagement\App\Http\Controllers\Admin\SeoManagementController::class, 'index'])
        ->name('admin.seomanagement.index');
});