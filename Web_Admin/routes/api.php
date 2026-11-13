<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\Address\AddressController;
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\ForgotPasswordController;
use App\Http\Controllers\API\Banner\BannerController;
use App\Http\Controllers\API\Contacts\ContactController;
use App\Http\Controllers\API\Coupon\CouponController;
use App\Http\Controllers\API\Customers\CardController;
use App\Http\Controllers\API\Customers\CustomerController;
use App\Http\Controllers\API\Master\masterController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\API\Notifications\NotificationsController;
use App\Http\Controllers\API\Order\OrderController;
use App\Http\Controllers\API\PostCode\PostCodeController;
use App\Http\Controllers\API\Product\ProductController;
use App\Http\Controllers\API\VariantController;

use App\Http\Controllers\API\Promotion\PromotionController;
use App\Http\Controllers\API\Rating\RatingController;
use App\Http\Controllers\API\Service\ServiceController;
use App\Http\Controllers\API\Setting\SettingController;
use App\Http\Controllers\API\User\UserController;
use App\Http\Controllers\API\VendorController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Guest / public endpoints
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::post('/contact/verify', [AuthController::class, 'mobileVerify'])->middleware('throttle:5,1');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/login/otp/request', [AuthController::class, 'requestLoginOtp'])->middleware('throttle:5,1');
Route::post('/login/otp/verify', [AuthController::class, 'verifyLoginOtp'])->middleware('throttle:5,1');
Route::post('/resend/otp', [AuthController::class, 'resendOTP'])->middleware('throttle:5,1');
Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword'])->middleware('throttle:5,1');
Route::post('/forgot-password/otp/verify', [ForgotPasswordController::class, 'verifyOtp'])->middleware('throttle:5,1');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->middleware('throttle:5,1');

Route::get('/promotions', [PromotionController::class, 'index']);
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/variants', [VariantController::class, 'index']);
Route::get('/offers', [\App\Http\Controllers\API\Offer\OfferController::class, 'index']);
// Multi-vendor public endpoints (under same API group, no auth required)
Route::get('/cities', [\App\Http\Controllers\API\CityController::class, 'index']);
Route::post('/cities/resolve', [\App\Http\Controllers\API\CityController::class, 'resolve'])->middleware('throttle:10,1');
Route::get('/vendors', [VendorController::class, 'index']);
Route::get('/vendors/{id}/services', [VendorController::class, 'services']);
Route::get('/services/{service}/vendors', [VendorController::class, 'serviceVendors']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/vendors/{id}/products', [ProductController::class, 'byVendor']);
Route::get('/master', [masterController::class, 'index']);
Route::get('/banners/wide', [BannerController::class, 'getWideBanners']);
Route::get('/post-code', [PostCodeController::class, 'index']);
Route::get('/legal-pages/{page}', [SettingController::class, 'show']);
Route::post('/contact', [ContactController::class, 'store']);

// Maps: public configuration (no secrets) and rate-limited reverse geocoding.
Route::get('/maps/config', [\App\Http\Controllers\API\MapController::class, 'config']);
Route::get('/maps/reverse-geocode', [\App\Http\Controllers\API\MapController::class, 'reverseGeocode'])->middleware('throttle:20,1');

// WhatsApp Cloud API webhook (signature verified in the controller).
Route::get('/webhooks/whatsapp', [\App\Http\Controllers\API\WhatsAppWebhookController::class, 'verify'])->middleware('throttle:120,1');
Route::post('/webhooks/whatsapp', [\App\Http\Controllers\API\WhatsAppWebhookController::class, 'handle'])->middleware('throttle:120,1');

// Partner API protected by hashed API keys, scopes and idempotency.
Route::get('/partner/ping', [\App\Http\Controllers\API\Partner\PartnerController::class, 'ping'])
    ->middleware(['api.key:ping.read']);
Route::post('/partner/echo', [\App\Http\Controllers\API\Partner\PartnerController::class, 'echo'])
    ->middleware(['api.key:ping.read', 'idempotency']);
Route::post('/partner/webhooks/test', [\App\Http\Controllers\API\Partner\PartnerController::class, 'testWebhook'])
    ->middleware(['api.key:webhooks.read', 'idempotency']);

// Customer join-request status by tracking token (public, throttled, no PII exposed).
Route::post('/customer/join-request-status', [\App\Http\Controllers\API\Auth\AuthController::class, 'joinRequestStatusByToken'])->middleware('throttle:10,1');

// Recovery: request a fresh tracking token via OTP to the registered contact.
Route::post('/customer/join-request-recovery', [\App\Http\Controllers\API\Auth\AuthController::class, 'joinRequestRecovery'])->middleware('throttle:3,1');
Route::post('/customer/join-request-recovery/verify', [\App\Http\Controllers\API\Auth\AuthController::class, 'joinRequestRecoveryVerify'])->middleware('throttle:5,1');

// Customer authenticated endpoints
Route::middleware(['auth:api', 'role:customer,api', 'ensure.customer.approved'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/users/device-key', [AuthController::class, 'storeDeviceKey']);

    Route::get('/customer/join-request-status', [\App\Http\Controllers\API\Auth\AuthController::class, 'joinRequestStatusMe']);

    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::post('/addresses/{address}', [AddressController::class, 'update']);
    Route::delete('/addresses/{address}', [AddressController::class, 'delete']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::put('/orders/{order}', [OrderController::class, 'update']);
    Route::get('/orders/{id}/details', [OrderController::class, 'show']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);
    Route::post('/orders/{order}/customer-edit', [OrderController::class, 'customerEdit']);

    Route::post('/coupons/{couponCode}/apply', [CouponController::class, 'apply'])->name('coupon.apply');

    Route::get('/customer', [CustomerController::class, 'show']);
    Route::delete('/customer/account', [CustomerController::class, 'destroyAccount']);

    Route::get('/card-list', [CardController::class, 'index']);
    Route::post('/cards', [CardController::class, 'store']);

    Route::get('/notification', [NotificationsController::class, 'index']);
    Route::post('/notification/{notification}', [NotificationsController::class, 'update']);
    Route::delete('/notification/{notification}', [NotificationsController::class, 'delete']);

    // Alias for Flutter app endpoint (plural form used by client)
    Route::post('/ratings', [RatingController::class, 'store']);

    Route::post('/users/update', [UserController::class, 'update']);
    Route::post('/users/profile-photo/update', [UserController::class, 'updateProfilePhoto']);
    Route::post('/users/change-password', [UserController::class, 'changePassword']);
    Route::post('/users/phone/request', [UserController::class, 'requestPhoneChange'])->middleware('throttle:5,1');
    Route::post('/users/phone/confirm', [UserController::class, 'confirmPhoneChange'])->middleware('throttle:5,1');

    Route::get('/get/messages', [MessageController::class, 'index']);
});

Route::get('/vendors/{vendor}/appointments', [\App\Http\Controllers\API\VendorAppointmentController::class, 'index']);

// Pick / delivery schedules (used by customer app, no auth required)
Route::get('/pick-schedules/{date}', [OrderController::class, 'pickSchedule']);
Route::get('/delivery-schedules/{date}', [OrderController::class, 'deliverySchedule']);

// Payment
// The former client-reported payment endpoint was removed. Payment status
// must be confirmed by the payment provider on the server, never by the app.
