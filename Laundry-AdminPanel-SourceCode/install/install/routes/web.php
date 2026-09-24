<?php

use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\Banners\BannerController;
use App\Http\Controllers\Web\Root\AdminController;
use App\Http\Controllers\Web\Services\ServiceController;
use App\Http\Controllers\Web\Vendors\VendorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect('/dashboard')
        : redirect('/login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('form-login');
});

Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/privacy-policy', [LoginController::class, 'privacyPolicy'])->name('web.privacy');
Route::get('/terms-condition', [LoginController::class, 'termsCondition'])->name('web.terms');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('root');
    Route::get('/admins', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admins/create', [AdminController::class, 'create'])->name('admin.create');
    Route::post('/admins', [AdminController::class, 'store'])->name('admin.store');
    Route::get('/admins/{user}', [AdminController::class, 'show'])->name('admin.show');
    Route::get('/admins/{user}/edit', [AdminController::class, 'edit'])->name('admin.edit');
    Route::post('/admins/{user}', [AdminController::class, 'update'])->name('admin.update');
    Route::post('/admins/{user}/set-permission', [AdminController::class, 'setPermission'])->name('admin.set-permission');
    Route::post('/admins/{user}/status-update', [AdminController::class, 'toggleStatusUpdate'])->name('admin.status-update');

    Route::get('/services', [ServiceController::class, 'index'])->name('service.index');
    Route::get('/services/create', [ServiceController::class, 'create'])->name('service.create');
    Route::post('/services', [ServiceController::class, 'store'])->name('service.store');
    Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('service.edit');
    Route::match(['put', 'patch'], '/services/{service}', [ServiceController::class, 'update'])->name('service.update');
    Route::get('/services/{service}/status', [ServiceController::class, 'toggleActivationStatus'])->name('service.status.toggle');
    Route::get('/stores', [VendorController::class, 'index'])->name('vendor.index');
    Route::get('/stores/{vendor}/status', [VendorController::class, 'toggleActivationStatus'])->name('vendor.status.toggle');
    Route::get('/banners', [BannerController::class, 'index'])->name('banner.index');
    Route::get('/banners/promotional', [BannerController::class, 'getPromotional'])->name('banner.promotional');
    Route::post('/banners', [BannerController::class, 'store'])->name('banner.store');
    Route::get('/banners/{banner}/edit', [BannerController::class, 'edit'])->name('banner.edit');
    Route::match(['put', 'patch'], '/banners/{banner}', [BannerController::class, 'update'])->name('banner.update');
    Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('banner.destroy');
    Route::get('/banners/{banner}/status', [BannerController::class, 'toggleActivationStatus'])->name('banner.status.toggle');

    Route::get('/notifications/new', function () {
        return response()->json(['data' => ['orders' => []]]);
    })->name('new.orders');

    Route::get('/locale', function () {
        return redirect('/dashboard');
    })->name('change.local');

    $layoutRouteNames = [
        'order.index',
        'variant.index',
        'product.index',
        'coupon.index',
        'pos.index',
        'pos.sales',
        'notification.index',
        'revenue.index',
        'customer.index',
        'driver.index',
        'contact',
        'web.setting',
        'web.faq.list',
        'web.faq.category.index',
        'setting.show',
        'deliveryCost',
        'mobileApp',
        'socialLink.index',
        'schedule.index',
        'webSetting.index',
        'payment-gateway.index',
        'stripeKey.index',
        'sms-gateway.index',
        'invoiceManage.index',
        'notification.manage',
        'fcm.index',
        'mail-config.index',
        'areas.index',
        'profile.index',
        'language.index',
        'revenue.generate.pdf',
        'order.show',
    ];

    foreach ($layoutRouteNames as $routeName) {
        Route::get('/admin-placeholder/' . str_replace('.', '-', $routeName), function () {
            return redirect()->route('root');
        })->name($routeName);
    }
});