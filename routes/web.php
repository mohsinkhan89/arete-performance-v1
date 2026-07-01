<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackendController;
use App\Http\Controllers\FrontendController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [FrontendController::class, 'index'])->name('frontend.index');
Route::get('my-cart', [FrontendController::class, 'myCart'])->name('frontend.my-cart');
Route::get('checkout', [FrontendController::class, 'checkout'])->name('frontend.checkout');
Route::get('order-success', [FrontendController::class, 'orderSuccess'])->name('frontend.order-success');
Route::get('product-details', [FrontendController::class, 'productDetails'])->name('frontend.product-details');
Route::get('search', [FrontendController::class, 'search'])->name('frontend.search');
Route::get('shop', [FrontendController::class, 'shop'])->name('frontend.shop');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::prefix('admin')->name('backend.')->middleware('auth')->group(function () {
    Route::redirect('/', '/admin/dashboard');
    Route::get('dashboard', [BackendController::class, 'dashboard'])->name('dashboard');
    Route::get('profile', [BackendController::class, 'profile'])->name('profile');
    Route::get('profile/edit', [BackendController::class, 'editProfile'])->name('profile.edit');
    Route::put('profile', [BackendController::class, 'updateProfile'])->name('profile.update');
    Route::get('{resource}/create', [BackendController::class, 'create'])->name('resource.create');
    Route::post('{resource}', [BackendController::class, 'store'])->name('resource.store');
    Route::get('{resource}/{id}/edit', [BackendController::class, 'edit'])->name('resource.edit');
    Route::put('{resource}/{id}', [BackendController::class, 'update'])->name('resource.update');
    Route::delete('{resource}/{id}', [BackendController::class, 'destroy'])->name('resource.destroy');
    Route::patch('{resource}/{id}/status', [BackendController::class, 'toggleStatus'])->name('resource.status');
    Route::get('{page}', [BackendController::class, 'page'])->name('page');
});
