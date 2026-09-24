<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\Additional\AdditionalServiceController;
use App\Http\Controllers\API\Address\AddressController;
use App\Http\Controllers\API\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\API\Admin\dashboard\dashboardController;
use App\Http\Controllers\API\Admin\order\OrderController as AdminOrderController;
use App\Http\Controllers\API\AreaController;
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\ForgotPasswordController;
use App\Http\Controllers\API\Banner\BannerController;
use App\Http\Controllers\API\Contacts\ContactController;
use App\Http\Controllers\API\Coupon\CouponController;
use App\Http\Controllers\API\Customers\CardController;
use App\Http\Controllers\API\Customers\CustomerController;
use App\Http\Controllers\API\Driver\Auth\LoginController as DriverLoginController;
use App\Http\Controllers\API\Driver\Dashboard\DashboardController as DriverDashboardController;
use App\Http\Controllers\API\Driver\Notifications\DriverNotificationController;
use App\Http\Controllers\API\Master\masterController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\API\Notifications\NotificationsController;
use App\Http\Controllers\API\Order\OrderController;
use App\Http\Controllers\API\Order\PosController as ApiPosController;
use App\Http\Controllers\API\Payment\PaymentController;
use App\Http\Controllers\API\PaymentGatewayController;
use App\Http\Controllers\API\PostCode\PostCodeController;
use App\Http\Controllers\API\Product\ProductController;
use App\Http\Controllers\API\Promotion\PromotionController;
use App\Http\Controllers\API\Rating\RatingController;
use App\Http\Controllers\API\Service\ServiceController;
use App\Http\Controllers\API\Setting\SettingController;
use App\Http\Controllers\API\Social\SocialLinkController;
use App\Http\Controllers\API\User\UserController;
use App\Http\Controllers\API\Variant\VariantController;
use App\Http\Controllers\API\VendorController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Guest / public endpoints
Route::post('/register', [AuthController::class, 'register']);
Route::post('/contact/verify', [AuthController::class, 'mobileVerify']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/resend/otp', [AuthController::class, 'resendOTP']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
Route::post('/forgot-password/otp/verify', [ForgotPasswordController::class, 'verifyOtp']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);

Route::get('/promotions', [PromotionController::class, 'index']);
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/variants', [VariantController::class, 'index']);
Route::get('/offers', [\App\Http\Controllers\API\Offer\OfferController::class, 'index']);
// Multi-vendor public endpoints (under same API group, no auth required)
Route::get('/vendors', [VendorController::class, 'index']);
Route::get('/vendors/{id}', [VendorController::class, 'show']);
Route::get('/vendors/{id}/services', [VendorController::class, 'services']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/vendors/{id}/products', [ProductController::class, 'byVendor']);
Route::get('/master', [masterController::class, 'index']);
Route::get('/banner', [BannerController::class, 'index']);
Route::get('/area', [AreaController::class, 'index']);
Route::get('/additional-service', [AdditionalServiceController::class, 'index']);
Route::get('/post-code', [PostCodeController::class, 'index']);
Route::get('/social-link', [SocialLinkController::class, 'index']);
Route::get('/legal-pages/{page}', [SettingController::class, 'show']);
Route::post('/contact', [ContactController::class, 'store']);

Route::get('/setting/privacy-show', [SettingController::class, 'privacyShow']);
Route::get('/setting/terms/{page}', [SettingController::class, 'termsShow']);
Route::get('/setting/{page}', [SettingController::class, 'show']);

Route::get('/payment-gateway', [PaymentGatewayController::class, 'index']);
Route::get('/payment-gateway/get-by-id', [PaymentGatewayController::class, 'getByID']);
Route::get('/payment-gateway/success', [PaymentGatewayController::class, 'success']);

// POS (customer app)
Route::get('/pos/customer', [ApiPosController::class, 'posCustomer']);
Route::get('/pos/service', [ApiPosController::class, 'posService']);
Route::post('/pos/store', [ApiPosController::class, 'posStore']);
Route::get('/pos/variants', [ApiPosController::class, 'fetchVariants']);
Route::get('/pos/products', [ApiPosController::class, 'fetchProducts']);
Route::get('/pos/payment', [ApiPosController::class, 'payment']);

// Customer authenticated endpoints
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::post('/addresses/{address}', [AddressController::class, 'update']);
    Route::delete('/addresses/{address}', [AddressController::class, 'delete']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::put('/orders/{order}', [OrderController::class, 'update']);
    Route::get('/orders/new-order', [OrderController::class, 'newOrder']);
    Route::get('/orders/{id}/details', [OrderController::class, 'show']);

    Route::post('/coupons/{couponCode}/apply', [CouponController::class, 'apply'])->name('coupon.apply');

    Route::get('/customer', [CustomerController::class, 'show']);
    Route::post('/customer/{user}/toggle-status', [CustomerController::class, 'toggleStatus']);

    Route::get('/card-list', [CardController::class, 'index']);
    Route::post('/cards', [CardController::class, 'store']);

    Route::get('/notification', [NotificationsController::class, 'index']);
    Route::post('/notification', [NotificationsController::class, 'store']);
    Route::post('/notification/{notification}', [NotificationsController::class, 'update']);
    Route::delete('/notification/{notification}', [NotificationsController::class, 'delete']);

    Route::get('/rating', [RatingController::class, 'index']);
    Route::post('/rating', [RatingController::class, 'store']);

    Route::post('/users/update', [UserController::class, 'update']);
    Route::post('/users/profile-photo/update', [UserController::class, 'updateProfilePhoto']);
    Route::post('/users/change-password', [UserController::class, 'changePassword']);

    Route::get('/get/messages', [MessageController::class, 'index']);
});

// Pick / delivery schedules (used by customer app, no auth required)
Route::get('/pick-schedules/{date}', [OrderController::class, 'pickSchedule']);
Route::get('/delivery-schedules/{date}', [OrderController::class, 'deliverySchedule']);

// Payment
Route::post('/payment', [PaymentController::class, 'store']);
Route::post('/payment-gateway/process-order/{order}', [PaymentGatewayController::class, 'processOrder']);

// Admin API
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminLoginController::class, 'login']);
    Route::post('/logout', [AdminLoginController::class, 'logout']);
    Route::get('/find-key/{key}', [AdminLoginController::class, 'findByKey']);

    Route::middleware('auth:api')->group(function () {
        Route::get('/dashboard', [dashboardController::class, 'index']);
        Route::get('/dashboard/status', [dashboardController::class, 'status']);
        Route::get('/orders', [AdminOrderController::class, 'index']);
        Route::get('/orders/{id}', [AdminOrderController::class, 'orderDetails']);
        Route::post('/orders/{order}/status', [AdminOrderController::class, 'statusUpdate']);
    });
});

// Driver API
Route::prefix('driver')->group(function () {
    Route::post('/login', [DriverLoginController::class, 'login']);
    Route::post('/logout', [DriverLoginController::class, 'logout']);
    Route::post('/change-password', [DriverLoginController::class, 'changePassword']);
    Route::get('/profile', [DriverLoginController::class, 'show']);
    Route::delete('/user/{user}', [DriverLoginController::class, 'delete']);
    Route::get('/find-key/{key}', [DriverLoginController::class, 'findByKey']);

    Route::middleware('auth:api')->group(function () {
        Route::get('/dashboard/today', [DriverDashboardController::class, 'today']);
        Route::get('/dashboard/today-pending', [DriverDashboardController::class, 'todayPending']);
        Route::get('/dashboard/today-orders', [DriverDashboardController::class, 'todayOrders']);
        Route::get('/dashboard/order/{id}', [DriverDashboardController::class, 'orderDetails']);
        Route::post('/dashboard/order/{order}/status', [DriverDashboardController::class, 'statusUpdate']);
        Route::post('/dashboard/order/{order}/accept', [DriverDashboardController::class, 'acceptOrder']);
        Route::get('/dashboard/this-week', [DriverDashboardController::class, 'thisWeek']);
        Route::get('/dashboard/last-week', [DriverDashboardController::class, 'lastWeek']);
        Route::get('/dashboard/total-order', [DriverDashboardController::class, 'totalOrder']);
        Route::get('/dashboard/history', [DriverDashboardController::class, 'history']);

        Route::get('/notification', [DriverNotificationController::class, 'index']);
        Route::post('/notification', [DriverNotificationController::class, 'store']);
        Route::post('/notification/{notification}', [DriverNotificationController::class, 'update']);
        Route::delete('/notification/{notification}', [DriverNotificationController::class, 'delete']);
    });
});
