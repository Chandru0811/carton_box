<?php

use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('deal/{id}', [HomeController::class, 'productDescription']);
Route::get('search', [HomeController::class, 'search'])->name('search');
// Route::get('/checkoutSummary/{product_id}', [CheckoutController::class, 'checkoutsummary'])->name('checkout.summary');
// Route::post('/directCheckout', [CheckoutController::class, 'directcheckout'])->name('checkout.direct');



Route::get('/login', function () {
    return view('auth.login');
});

// Route::get('/description', function () {
//     return view('productDescription');
// });

Route::get('/checkoutsummary', function () {
    return view('summary');
});

Route::get('/directCheckout', function () {
    return view('checkout');
});

// Route::get('/search', function () {
//     return view('productfilter');
// });

Route::middleware('guest')->group(function () {
    Route::get('register', [AuthenticatedSessionController::class, 'showRegistrationForm']);
    Route::post('register', [AuthenticatedSessionController::class, 'register'])->name('register');

    Route::get('login', [AuthenticatedSessionController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'logout'])->name('logout');
    Route::get('/home', function () {
        return view('home');
    })->name('home');
});
