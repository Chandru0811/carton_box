<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\NewCartController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/contactus', function () {
    return view('contactus');
});


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('deal/{id}', [HomeController::class, 'productDescription']);
Route::get('search', [HomeController::class, 'search'])->name('search');
Route::get('categories/{slug}', [HomeController::class, 'subcategorybasedproducts'])->name('deals.subcategorybased');


Route::get('cart', [NewCartController::class, 'index'])->name('cart.index');
Route::post('addtocart/{slug}', [NewCartController::class, 'addtocart'])->name('cart.add');
Route::get('cart/details', [NewCartController::class, 'cartdetails'])->name('cart.details');
Route::get('/cartSummary/{cart_id}', [CartController::class, 'cartSummary'])->name('cart.address');

Route::post('cart/remove', [CartController::class, 'removeItem'])->name('cart.remove');
Route::post('cart/update', [CartController::class, 'updateCart'])->name('cart.update');
Route::get('get/cartitems', [CartController::class, 'getCartItem'])->name('cartitems.get');



Route::get('/addresses', [AddressController::class, 'index'])->name('address.index');
Route::get('/getAddress/{id}', [AddressController::class, 'show'])->name('address.view');
Route::post('/createAddress', [AddressController::class, 'store'])->name('address.create');
Route::put('/updateAddress', [AddressController::class, 'update'])->name('address.update');
Route::delete('/address/{id}', [AddressController::class, 'destroy'])->name('address.destroy');
Route::post('/selectaddress', [AddressController::class, 'changeSelectedId'])->name('address.change');


Route::get('/orders', [CheckoutController::class, 'getAllOrdersByCustomer'])->name('customer.orders');
Route::get('/order/{id}/{product_id}', [CheckoutController::class, 'showOrderByCustomerId'])->name('customer.orderById');

Route::get('/login', function () {
    return view('auth.login');
});

Route::middleware('guest')->group(function () {
    Route::get('register', [AuthenticatedSessionController::class, 'showRegistrationForm']);
    Route::post('register', [AuthenticatedSessionController::class, 'register'])->name('register');

    Route::get('login', [AuthenticatedSessionController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'login']);
    Route::get('forgot-password', [AuthenticatedSessionController::class, 'showForgotPage'])
        ->name('password.request');
    Route::post('forgot-password', [AuthenticatedSessionController::class, 'store'])
        ->name('password.email');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'logout'])->name('logout');
    Route::get('/home', [HomeController::class, 'home'])->name('index.home');
    Route::get('/checkoutSummary/{product_id}', [CheckoutController::class, 'checkoutsummary'])->name('checkout.summary');
    Route::post('/cartCheckout', [CheckoutController::class, 'cartcheckout'])->name('checkout.cart');
    Route::post('/directCheckout', [CheckoutController::class, 'directcheckout'])->name('checkout.direct');
    Route::post('/checkout', [CheckoutController::class, 'createorder'])->name('checkout.checkout');
});
