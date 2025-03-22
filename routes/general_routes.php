<?php

use App\Http\Controllers\Api\HomeController;
use Illuminate\Support\Facades\Route;



Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/deal/{id}', [HomeController::class, 'productDescription']);


Route::get('categories/{slug}', [HomeController::class, 'subcategorybasedproducts'])->name('deals.subcategorybased');
// Route::get('search', [HomeController::class, 'search'])->name('search');
