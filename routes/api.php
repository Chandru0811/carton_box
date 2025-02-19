<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\Admin\CategoriesController;
use App\Http\Controllers\Api\Admin\CategoryGroupsContorller;
use App\Http\Controllers\Api\Admin\DealCategoryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\SliderController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\ShopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgetpassword']);
    Route::post('/reset-password', [AuthController::class, 'resetpassword']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return response()->json(['user' => $request->user()]);
    });

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

    // Admin Routes
    Route::middleware('role:1')->prefix('admin')->group(function () {});
});
