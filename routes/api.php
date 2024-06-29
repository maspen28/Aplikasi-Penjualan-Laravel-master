<?php

use App\Http\Controllers\API\AuthenticationController;
use App\Http\Controllers\API\CheckoutController;
use App\Http\Controllers\API\LocationController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ShippingController;
use App\Http\Controllers\API\HistoryController;
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

Route::get('/user', function (Request $request) {
  return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthenticationController::class, 'login']);
Route::post('/register', [AuthenticationController::class, 'register']);

Route::get('/city/{provinceId}', [LocationController::class, 'getCity']);
Route::get('/district/province/{provinceId}/city/{cityId}/', [LocationController::class, 'getDistrict']);
Route::get('/provinces', [LocationController::class, 'getProvinces']);

Route::get('/product', [ProductController::class, 'index']);
Route::get('/product/{id}', [ProductController::class, 'detail']);

Route::get('/cart', [ProductController::class, 'cart']);
Route::post('/cart', [ProductController::class, 'addToCart']);
Route::delete('/cart', [ProductController::class, 'removeFromCart']);

Route::post('/checkout', [CheckoutController::class, 'checkout']);

// Payment and Shipping Routes
Route::post('/payment/create/{orderId}', [PaymentController::class, 'createPayment'])->name('payment.create');
Route::post('/payment/notification', [PaymentController::class, 'notificationHandler']);
Route::post('/shipping/cost', [ShippingController::class, 'calculateShippingCost']);

Route::get('/history', [HistoryController::class, 'getHistory']);
?>
