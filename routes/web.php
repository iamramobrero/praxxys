<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
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

Route::group(['middleware' => ['guest']], function () {
    Route::get('/', [AuthController::class, 'showLoginPage'])->name('showLoginPage');
    Route::post('/login', [AuthController::class, 'doLogin'])->name('doLogin');
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', [DashboardController::class, 'showDashboard'])->name('showDashboard');
    Route::get('/logout', [AuthController::class, 'doLogout'])->name('doLogout');
});
