<?php

use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/products', [ProductController::class, 'data'])->name('api.product.data');
    Route::delete('/products/{product?}', [ProductController::class, 'destroy'])->name('api.product.destroy');

    Route::get('/product-categories', [ProductCategoryController::class, 'data'])->name('api.product-categories.data');
});
