<?php

use App\Http\Controllers\Api\HomeController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => '{country_code?}', 'middleware' => 'country'], function () {
    Route::get('/', [HomeController::class, 'index'])->name('country.home');
    Route::get('/deal/{id}', [HomeController::class, 'productDescription'])->name('product.description');

    Route::get('categories/{slug}', [HomeController::class, 'subcategorybasedproducts'])->name('deals.subcategorybased');
});
