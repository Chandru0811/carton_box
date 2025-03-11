<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\Admin\ApprovalController;
use App\Http\Controllers\Api\Admin\CategoriesController;
use App\Http\Controllers\Api\Admin\CategoryGroupsContorller;
use App\Http\Controllers\Api\Admin\CountryController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\DealCategoryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AppController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\Admin\SliderController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\ShopController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\CheckoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgetpassword']);
    Route::post('/reset-password', [AuthController::class, 'resetpassword']);

    // user 
    Route::get('appHome', [AppController::class, 'homepage']);
    Route::get('get/{id}/categories', [AppController::class, 'categories']);
    Route::get('deals/{category_id}', [AppController::class, 'getDeals']);
    Route::get('deal/details/{id}', [AppController::class, 'dealDescription']);
    Route::get('search', [AppController::class, 'search']);
    Route::get('categories/{id}', [AppController::class, 'subcategorybasedproductsformobile']);



    //cart
    Route::post('addtocart/{slug}', [CartController::class, 'addtoCart']);
    Route::get('cart', [CartController::class, 'getCart']);
    Route::post('cart/remove', [CartController::class, 'removeItem']);
    Route::post('cart/update', [CartController::class, 'updateCart']);
    Route::get('cart/totalitems', [CartController::class, 'totalItems']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return response()->json(['user' => $request->user()]);
    });

    // Admin Routes
    Route::middleware('role:1')->prefix('admin')->group(function () {
        // Sliders
        Route::get('sliders', [SliderController::class, 'index']);
        Route::post('slider', [SliderController::class, 'store']);
        Route::get('slider/{id}', [SliderController::class, 'show']);
        Route::put('slider/update/{id}', [SliderController::class, 'update']);
        Route::delete('slider/delete/{id}', [SliderController::class, 'destroy']);


        // Category Groups
        Route::get('categoryGroup', [CategoryGroupsContorller::class, 'index']);
        Route::post('categoryGroup', [CategoryGroupsContorller::class, 'store']);
        Route::get('categoryGroup/{id}', [CategoryGroupsContorller::class, 'show']);
        Route::put('categoryGroup/update/{id}', [CategoryGroupsContorller::class, 'update']);
        Route::delete('categoryGroup/{id}', [CategoryGroupsContorller::class, 'delete']);


        // Categories
        Route::get('categories', [CategoriesController::class, 'index']);
        Route::post('categories', [CategoriesController::class, 'store']);
        Route::get('categories/{id}', [CategoriesController::class, 'show']);
        Route::put('categories/update/{id}', [CategoriesController::class, 'update']);
        Route::delete('categories/{id}', [CategoriesController::class, 'destroy']);
        Route::post('categories/restore/{id}', [CategoriesController::class, 'restore']);
        // Route::post('category/{id}/approve', [ApprovalController::class, 'approveCategory']);

        // Deal Category
        Route::get('dealCategory', [DealCategoryController::class, 'index']);
        Route::post('dealCategory', [DealCategoryController::class, 'store']);
        Route::get('dealCategory/{id}', [DealCategoryController::class, 'show']);
        Route::put('dealCategory/update/{id}', [DealCategoryController::class, 'update']);
        Route::delete('dealCategory/remove/{id}', [DealCategoryController::class, 'delete']);
        Route::post('dealCategory/restore/{id}', [DealCategoryController::class, 'restore']);


        //Product
        Route::get('product/{shop_id}', [ProductController::class, 'index']);
        Route::post('product', [ProductController::class, 'store']);
        Route::post('product/restore/{id}', [ProductController::class, 'restore']);
        Route::get('product/{id}/get', [ProductController::class, 'show']);
        Route::put('product/{id}/update', [ProductController::class, 'update']);
        Route::delete('product/{id}/delete', [ProductController::class, 'destroy']);
        Route::delete('product/media/{id}/delete', [ProductController::class, 'destroyProductMedia']);

        // Category Group and Categories
        Route::get('categorygroups', [ProductController::class, 'getAllCategoryGroups']);
        Route::get('categories/categorygroups/{id}', [ProductController::class, 'getAllCategoriesByCategoryGroupId']);
        Route::post('categories/create', [ProductController::class, 'categoriesCreate']);


        // Order
        Route::get('orders/{shop_id}', [ShopController::class, 'getAllOrdersByShop']);
        Route::get('order/{order_id}/{product_id}', [ShopController::class, 'showOrderById']);

        // address
        Route::get('/address', [AddressController::class, 'index']);
        Route::post('/address', [AddressController::class, 'store']);
        Route::get('/address/{id}', [AddressController::class, 'show']);
        Route::put('/address/update/{id}', [AddressController::class, 'update']);
        Route::delete('/address/{id}', [AddressController::class, 'destroy']);


        // address
        Route::get('/country', [CountryController::class, 'index']);
        Route::post('/country', [CountryController::class, 'store']);
        Route::get('/country/{id}', [CountryController::class, 'show']);
        Route::put('/country/update/{id}', [CountryController::class, 'update']);
        Route::delete('/country/{id}', [CountryController::class, 'destroy']);


        // User
        Route::get('users', [UserController::class, 'getAllUser']);
        Route::get('user/{id}', [UserController::class, 'userShow']);


        Route::post('deal/{id}/approve', [ApprovalController::class, 'approveProduct']);
        Route::post('deal/{id}/disapprove', [ApprovalController::class, 'disapproveProduct']);


        Route::get('dashboard', [DashboardController::class, 'index']);
        Route::post('dashboard', [DashboardController::class, 'graphdata']);
    });


    //Customer
    Route::middleware('role:3')->prefix('customer')->group(function () {
        Route::get('/cartSummary/{cart_id}', [CartController::class, 'cartSummary']);
        Route::get('/checkout/{cart_id}', [CheckoutController::class, 'cartcheckout']);
        Route::get('/checkoutSummary/{product_id}', [CheckoutController::class, 'checkoutsummary']);
        Route::get('/directCheckout', [CheckoutController::class, 'directCheckout']);
        Route::post('/checkout', [CheckoutController::class, 'createorder']);
        Route::get('/orders', [CheckoutController::class, 'getAllOrdersByCustomer']);
        Route::get('/order/{id}/{product_id}', [CheckoutController::class, 'showOrderByCustomerId']);
        Route::put('/updateUser', [AppController::class, 'updateUser']);
        Route::get('/getUser', [AppController::class, 'getUser']);
        Route::delete('/deleteUser', [AppController::class, 'softDeleteUser']);

        Route::get('/address', [AddressController::class, 'index']);
        Route::post('/address', [AddressController::class, 'store']);
        Route::get('/address/{id}', [AddressController::class, 'show']);
        Route::put('/address/update/{id}', [AddressController::class, 'update']);
        Route::delete('/address/{id}', [AddressController::class, 'destroy']);
        Route::post('/review', [AppController::class, 'createReview']);
    });
});
