<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\Order\OrderController as ApiOrderController;
use App\Http\Controllers\API\PaymentGatewayController as ApiPaymentGatewayController;
use App\Http\Controllers\FCMController;
use App\Http\Controllers\NotificationManageController;
use App\Http\Controllers\Web\AreaController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Banners\BannerController;
use App\Http\Controllers\Web\Contacts\ContactController;
use App\Http\Controllers\Web\Customers\CustomerController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\DeliveryCost\DeliveryCostController;
use App\Http\Controllers\Web\Driver\DriverController;
use App\Http\Controllers\Web\FaqController;
use App\Http\Controllers\Web\InvoiceManageController;
use App\Http\Controllers\Web\LanguageController;
use App\Http\Controllers\Web\MailConfigurationController;
use App\Http\Controllers\Web\MobileAppUrl\MobileAppUrlController;
use App\Http\Controllers\Web\NotificationController;
use App\Http\Controllers\Web\OrderScheduleController;
use App\Http\Controllers\Web\PaymentController;
use App\Http\Controllers\Web\PaymentGatewayController;
use App\Http\Controllers\Web\PosController;
use App\Http\Controllers\Web\Products\CouponController;
use App\Http\Controllers\Web\Products\OrderController;
use App\Http\Controllers\Web\Products\ProductController;
use App\Http\Controllers\Web\Profile\ProfileController;
use App\Http\Controllers\Web\Root\AdminController;
use App\Http\Controllers\Web\Root\CustomerJoinRequestController;
use App\Http\Controllers\Web\Services\ServiceController;
use App\Http\Controllers\Web\Setting\SettingController;
use App\Http\Controllers\Web\SMSGatewaySetupController;
use App\Http\Controllers\Web\Social\SocialController;
use App\Http\Controllers\Web\StripeKeyUpateController;
use App\Http\Controllers\Web\StripePaymentController;
use App\Http\Controllers\Web\WebFaqCategoryController;
use App\Http\Controllers\Web\WebNotificationController;
use App\Http\Controllers\Website\Auth\AuthController as WebsiteAuthController;
use App\Http\Controllers\Website\CheckoutController;
use App\Http\Controllers\Website\ContactController as WebsiteContactController;
use App\Http\Controllers\Website\FaqController as WebsiteFaqController;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Website\policyController;
use App\Http\Controllers\Website\ServiceController as WebsiteServiceController;
use App\Http\Controllers\Website\SettingsController as WebsiteSettingsController;
use App\Http\Controllers\Website\StripeController;
use App\Http\Controllers\Website\WebsiteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Auth
Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'permission_check'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('root');

    // Notifications (ajax polling in layout)
    Route::get('/web-notifications/feed', [WebNotificationController::class, 'feed'])->name('web-notifications.feed');

    // Services
    Route::get('/services', [ServiceController::class, 'index'])->name('service.index');
    Route::get('/services/create', [ServiceController::class, 'create'])->name('service.create');
    Route::post('/services', [ServiceController::class, 'store'])->name('service.store');
    Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('service.edit');
    Route::post('/services/{service}', [ServiceController::class, 'update'])->name('service.update');
    Route::match(['get', 'post'], '/services/{service}/status', [ServiceController::class, 'toggleActivationStatus'])->name('service.status.toggle');
    Route::delete('/services/{service}', [ServiceController::class, 'delete'])->name('service.delete');

    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('product.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('product.create');
    Route::post('/products', [ProductController::class, 'store'])->name('product.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('product.edit');
    Route::post('/products/{product}', [ProductController::class, 'update'])->name('product.update');
    Route::match(['get', 'post'], '/products/{product}/status', [ProductController::class, 'toggleActivationStatus'])->name('product.status.toggle');
    Route::post('/products/{product}/update-order', [ProductController::class, 'orderUpdate'])->name('product.update.order');
    Route::delete('/products/{product}', [ProductController::class, 'delete'])->name('product.delete');

    // Coupons
    Route::get('/coupons', [CouponController::class, 'index'])->name('coupon.index');
    Route::get('/coupons/create', [CouponController::class, 'create'])->name('coupon.create');
    Route::post('/coupons', [CouponController::class, 'store'])->name('coupon.store');
    Route::get('/coupons/{coupon}/edit', [CouponController::class, 'edit'])->name('coupon.edit');
    Route::post('/coupons/{coupon}', [CouponController::class, 'update'])->name('coupon.update');
Route::post('/coupons/{coupon}/destroy', [CouponController::class, 'destroy'])->name('coupon.destroy');

// Super Admin: GLOBAL coupons (apply to every store). Separate from /coupons,
// which stays the vendor-only interface for a store's own coupons.
Route::prefix('admin')->name('coupon.admin.')->group(function () {
    Route::get('/coupons', [\App\Http\Controllers\Web\Root\CouponAdminController::class, 'index'])->name('index');
    Route::get('/coupons/create', [\App\Http\Controllers\Web\Root\CouponAdminController::class, 'create'])->name('create');
    Route::post('/coupons', [\App\Http\Controllers\Web\Root\CouponAdminController::class, 'store'])->name('store');
    Route::get('/coupons/{coupon}/edit', [\App\Http\Controllers\Web\Root\CouponAdminController::class, 'edit'])->name('edit');
    Route::post('/coupons/{coupon}', [\App\Http\Controllers\Web\Root\CouponAdminController::class, 'update'])->name('update');
    Route::post('/coupons/{coupon}/destroy', [\App\Http\Controllers\Web\Root\CouponAdminController::class, 'destroy'])->name('destroy');
});

    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('customer.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customer.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customer.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customer.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customer.edit');
    Route::post('/customers/{customer}', [CustomerController::class, 'update'])->name('customer.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'delete'])->name('customer.delete');
    Route::post('/customers/{user}/status', [CustomerController::class, 'toggleStatus'])->name('user.status.toggle');
    Route::get('/customers/{customerId}/addresses', [CustomerController::class, 'getCustomerAddresses'])->name('customer.addresses');

    // Vendors (Super Admin only)
    Route::get('/vendors', [\App\Http\Controllers\Web\VendorController::class, 'index'])->name('vendors.index');
    Route::get('/vendors/create', [\App\Http\Controllers\Web\VendorController::class, 'create'])->name('vendors.create');
    Route::post('/vendors', [\App\Http\Controllers\Web\VendorController::class, 'store'])->name('vendors.store');
    Route::get('/vendors/{vendor}/edit', [\App\Http\Controllers\Web\VendorController::class, 'edit'])->name('vendors.edit');
    Route::put('/vendors/{vendor}', [\App\Http\Controllers\Web\VendorController::class, 'update'])->name('vendors.update');
    Route::delete('/vendors/{vendor}', [\App\Http\Controllers\Web\VendorController::class, 'destroy'])->name('vendors.destroy');
    Route::post('/vendors/{vendor}/toggle', [\App\Http\Controllers\Web\VendorController::class, 'toggleStatus'])->name('vendors.toggle');
    Route::post('/vendors/{vendor}/toggle-partner', [\App\Http\Controllers\Web\VendorController::class, 'togglePartner'])->name('vendors.togglePartner');

    // Drivers
    Route::get('/drivers', [DriverController::class, 'index'])->name('driver.index');
    Route::get('/drivers/create', [DriverController::class, 'create'])->name('driver.create');
    Route::post('/drivers', [DriverController::class, 'store'])->name('driver.store');
    Route::get('/drivers/{driver}/edit', [DriverController::class, 'edit'])->name('driver.edit');
    Route::post('/drivers/{driver}', [DriverController::class, 'update'])->name('driver.update');
    Route::delete('/drivers/{driver}', [DriverController::class, 'destroy'])->name('driver.destroy');
    Route::get('/drivers/{driver}', [DriverController::class, 'details'])->name('driver.details');
    Route::post('/drivers/{driver}/status', [DriverController::class, 'toggleStatus'])->name('driver.status.toggle');
    Route::post('/drivers/{order}/{driver}/assign', [DriverController::class, 'driverAssign'])->name('driver.assign');

    // شاشة السائق الخاصة (حساب السائق بعد الدخول)
    Route::get('/driver/account', [DriverController::class, 'account'])->name('driver.account');
    Route::post('/driver/orders/{order}/status', [DriverController::class, 'orderStatusUpdate'])->name('driver.order.status.change');

    // Root admins
    Route::get('/admins', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admins/create', [AdminController::class, 'create'])->name('admin.create');
    Route::post('/admins', [AdminController::class, 'store'])->name('admin.store');
    Route::get('/admins/{user}/edit', [AdminController::class, 'edit'])->name('admin.edit');
    Route::post('/admins/{user}', [AdminController::class, 'update'])->name('admin.update');
    Route::post('/admins/{user}/status-update', [AdminController::class, 'toggleStatusUpdate'])->name('admin.status-update');
Route::delete('/admins/{user}', [AdminController::class, 'destroy'])->name('admin.destroy');

    // Customer Join Requests (Super Admin only)
    Route::get('/customer-join-requests', [CustomerJoinRequestController::class, 'index'])->name('customer-join-requests.index');
    Route::get('/customer-join-requests/{joinRequest}', [CustomerJoinRequestController::class, 'show'])->name('customer-join-requests.show');
    Route::post('/customer-join-requests/{joinRequest}/approve', [CustomerJoinRequestController::class, 'approve'])->name('customer-join-requests.approve');
    Route::post('/customer-join-requests/{joinRequest}/reject', [CustomerJoinRequestController::class, 'reject'])->name('customer-join-requests.reject');
    Route::post('/customer-join-requests/{joinRequest}/review', [CustomerJoinRequestController::class, 'review'])->name('customer-join-requests.review');
    Route::post('/customer-join-requests/{joinRequest}/revoke-tracking-token', [CustomerJoinRequestController::class, 'revokeTrackingToken'])->name('customer-join-requests.revoke-tracking-token')->middleware('throttle:10,1');
    Route::post('/customer-join-requests/{joinRequest}/review-again', [CustomerJoinRequestController::class, 'reviewAgain'])->name('customer-join-requests.review-again');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('order.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('order.show');
    Route::get('/orders/{order}/edit', [PosController::class, 'edit'])->name('order.edit');
    Route::post('/orders/{order}/update', [PosController::class, 'update'])->name('order.update');
    Route::post('/orders/{order}/payment-status', [PosController::class, 'updatePaymentStatus'])->name('order.payment-status');
    Route::post('/orders/{order}/status', [OrderController::class, 'statusUpdate'])->name('order.status.change');
    Route::post('/orders/{order}/paid', [OrderController::class, 'orderPaid'])->name('orderIncomplete.paid');
    Route::get('/orders/{order}/print-labels', [OrderController::class, 'printLabels'])->name('order.print.labels');
    Route::get('/orders/{order}/print-invoice', [OrderController::class, 'printInvioce'])->name('order.print.invioce');

    // Banners
    Route::get('/banners', [BannerController::class, 'index'])->name('banner.index');
    Route::get('/banners/promotional', [BannerController::class, 'getPromotional'])->name('banner.promotional');
    Route::get('/banners/wide', [BannerController::class, 'indexWide'])->name('banner.wide');
    Route::post('/banners', [BannerController::class, 'store'])->name('banner.store');
    Route::get('/banners/{banner}/edit', [BannerController::class, 'edit'])->name('banner.edit');
    Route::post('/banners/{banner}', [BannerController::class, 'update'])->name('banner.update');
    Route::post('/banners/{banner}/destroy', [BannerController::class, 'destroy'])->name('banner.destroy');
    Route::match(['get', 'post'], '/banners/{banner}/status', [BannerController::class, 'toggleActivationStatus'])->name('banner.status.toggle');

    // Offers
    Route::get('/offers', [\App\Http\Controllers\Web\Offers\OfferController::class, 'index'])->name('offer.index');
    Route::get('/offers/create', [\App\Http\Controllers\Web\Offers\OfferController::class, 'create'])->name('offer.create');
    Route::post('/offers', [\App\Http\Controllers\Web\Offers\OfferController::class, 'store'])->name('offer.store');
    Route::get('/offers/{offer}/edit', [\App\Http\Controllers\Web\Offers\OfferController::class, 'edit'])->name('offer.edit');
    Route::post('/offers/{offer}', [\App\Http\Controllers\Web\Offers\OfferController::class, 'update'])->name('offer.update');
    Route::post('/offers/{offer}/destroy', [\App\Http\Controllers\Web\Offers\OfferController::class, 'destroy'])->name('offer.destroy');
    Route::match(['get', 'post'], '/offers/{offer}/status', [\App\Http\Controllers\Web\Offers\OfferController::class, 'toggleActivationStatus'])->name('offer.status.toggle');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('profile.change-password.form');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');

    // Settings (legal pages content)
    Route::get('/settings/{slug}', [SettingController::class, 'show'])->name('setting.show');
    Route::get('/settings/{slug}/edit', [SettingController::class, 'edit'])->name('setting.edit');
    Route::post('/settings/{setting}', [SettingController::class, 'update'])->name('setting.update');


    // Schedules
    Route::get('/{type}/scheduls', [OrderScheduleController::class, 'index'])->name('schedule.index')->where('type', 'pickup|delivery');
    Route::post('/schedule/{id}/status', [OrderScheduleController::class, 'updateStatus'])->name('toggole.status.update');
    Route::post('/schedule/{orderSchedule}', [OrderScheduleController::class, 'update'])->name('schedule.update');

    // Notifications (admin)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notification.index');
    Route::post('/notifications/send', [NotificationController::class, 'SendNotification'])->name('notification.send');
    Route::get('/notifications/manage', [NotificationManageController::class, 'index'])->name('notification.manage');
    Route::post('/notifications/manage/{notificationManage}', [NotificationManageController::class, 'update'])->name('notification.manage.update');


    // Website settings editor (admin)
    Route::get('/website-settings', [WebsiteController::class, 'index'])->name('web.setting');
    Route::post('/website-settings/{type}', [WebsiteController::class, 'update'])->name('web.settingUpdate');

    // POS
    Route::get('/vendor/appointments', [\App\Http\Controllers\Web\VendorAppointmentController::class, 'edit'])->name('vendor.appointments.edit');
    Route::post('/vendor/appointments', [\App\Http\Controllers\Web\VendorAppointmentController::class, 'update'])->name('vendor.appointments.update');
    Route::post('/vendor/appointments/orders/{order}', [\App\Http\Controllers\Web\VendorAppointmentController::class, 'reschedule'])->name('vendor.appointments.reschedule');
    Route::get('/vendor/invoice-template', [\App\Http\Controllers\Web\VendorInvoiceTemplateController::class, 'edit'])->name('vendor.invoice-template.edit');
    Route::post('/vendor/invoice-template', [\App\Http\Controllers\Web\VendorInvoiceTemplateController::class, 'update'])->name('vendor.invoice-template.update');
    Route::delete('/vendor/invoice-template', [\App\Http\Controllers\Web\VendorInvoiceTemplateController::class, 'reset'])->name('vendor.invoice-template.reset');
    Route::get('/vendor/invoice-template/preview', [\App\Http\Controllers\Web\VendorInvoiceTemplateController::class, 'preview'])->name('vendor.invoice-template.preview');
    Route::get('/orders/{order}/pos-invoice', [\App\Http\Controllers\Web\VendorInvoiceTemplateController::class, 'print'])->name('order.pos-invoice');
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('/pos/sales', [PosController::class, 'sales'])->name('pos.sales');
    Route::post('/pos/store', [PosController::class, 'store'])->name('pos.store');
    Route::get('/pos/payment', [PosController::class, 'payment'])->name('pos.payment');
    Route::post('/pos/customer-store', [PosController::class, 'storeCustomer'])->name('pos.customerStore');
    Route::post('/pos/address-store', [PosController::class, 'storeAddress'])->name('pos.addressStore');
    Route::get('/pos/fetch/products', [PosController::class, 'fetchProducts'])->name('pos.fetch.products');


    // Vendor dashboard (separate standalone dashboard for each store owner)
    Route::get('/vendor/dashboard', [\App\Http\Controllers\Web\VendorController::class, 'dashboard'])->name('vendor.dashboard');
    // Vendor settings (store owner private settings — separate from global web settings)
    Route::get('/vendor/settings', [\App\Http\Controllers\Web\VendorController::class, 'settings'])->name('vendor.settings');
    Route::post('/vendor/settings', [\App\Http\Controllers\Web\VendorController::class, 'updateSettings'])->name('vendor.settings.update');

    // Vendor store customers and notifications (derived from Delivered orders only)
    Route::get('/vendor/customers', [\App\Http\Controllers\Web\Vendor\VendorStoreCustomerController::class, 'index'])->name('vendor.customers.index');
    Route::get('/vendor/customers/{customer}/edit', [\App\Http\Controllers\Web\Vendor\VendorStoreCustomerController::class, 'edit'])->name('vendor.customers.edit');
    Route::post('/vendor/customers/{customer}', [\App\Http\Controllers\Web\Vendor\VendorStoreCustomerController::class, 'update'])->name('vendor.customers.update');
    Route::get('/vendor/notifications', [\App\Http\Controllers\Web\Vendor\VendorNotificationController::class, 'index'])->name('vendor.notifications.index');
    Route::post('/vendor/notifications', [\App\Http\Controllers\Web\Vendor\VendorNotificationController::class, 'send'])->name('vendor.notifications.send');

    // Sub (child) vendor accounts — managed ONLY by the store owner
    Route::get('/vendor/subs', [\App\Http\Controllers\Web\Vendor\VendorSubController::class, 'index'])->name('vendor.subs.index');
    Route::get('/vendor/subs/create', [\App\Http\Controllers\Web\Vendor\VendorSubController::class, 'create'])->name('vendor.subs.create');
    Route::post('/vendor/subs', [\App\Http\Controllers\Web\Vendor\VendorSubController::class, 'store'])->name('vendor.subs.store');
    Route::get('/vendor/subs/{sub}', [\App\Http\Controllers\Web\Vendor\VendorSubController::class, 'show'])->name('vendor.subs.show');
    Route::get('/vendor/subs/{sub}/edit', [\App\Http\Controllers\Web\Vendor\VendorSubController::class, 'edit'])->name('vendor.subs.edit');
    Route::post('/vendor/subs/{sub}', [\App\Http\Controllers\Web\Vendor\VendorSubController::class, 'update'])->name('vendor.subs.update');
    Route::post('/vendor/subs/{sub}/toggle', [\App\Http\Controllers\Web\Vendor\VendorSubController::class, 'toggleStatus'])->name('vendor.subs.toggle');
    Route::post('/vendor/subs/{sub}/destroy', [\App\Http\Controllers\Web\Vendor\VendorSubController::class, 'destroy'])->name('vendor.subs.destroy');
    Route::post('/vendor/subs/{sub}/set-permission', [\App\Http\Controllers\Web\Vendor\VendorSubController::class, 'setPermission'])->name('vendor.subs.set-permission');

    // Maps reverse geocoding (uses Google Maps when configured, OSM fallback)
    Route::get('/maps/reverse-geocode', [\App\Http\Controllers\Web\MapController::class, 'reverseGeocode'])->name('maps.reverse-geocode');

    // Integration Center (Super Admin)
    Route::get('/integrations', [\App\Http\Controllers\Web\IntegrationCenterController::class, 'index'])->name('integrations.index');
    Route::post('/integrations/{group}', [\App\Http\Controllers\Web\IntegrationCenterController::class, 'update'])->name('integrations.update');
    Route::post('/integrations/{group}/test', [\App\Http\Controllers\Web\IntegrationCenterController::class, 'test'])->name('integrations.test');
    Route::get('/integrations/tools/reverse-geocode', [\App\Http\Controllers\Web\IntegrationCenterController::class, 'reverseGeocode'])->name('integrations.reverse-geocode');
    Route::get('/integrations/templates/list', [\App\Http\Controllers\Web\IntegrationCenterController::class, 'templates'])->name('integrations.templates');
    Route::post('/integrations/templates/store', [\App\Http\Controllers\Web\IntegrationCenterController::class, 'storeTemplate'])->name('integrations.templates.store');
    Route::post('/integrations/templates/{template}/update', [\App\Http\Controllers\Web\IntegrationCenterController::class, 'updateTemplate'])->name('integrations.templates.update');
    Route::delete('/integrations/templates/{template}', [\App\Http\Controllers\Web\IntegrationCenterController::class, 'destroyTemplate'])->name('integrations.templates.destroy');
    Route::get('/integrations/message-logs', [\App\Http\Controllers\Web\IntegrationCenterController::class, 'messageLogs'])->name('integrations.message-logs');
    Route::get('/integrations/webhooks/list', [\App\Http\Controllers\Web\IntegrationCenterController::class, 'webhooks'])->name('integrations.webhooks');
    Route::post('/integrations/webhooks/store', [\App\Http\Controllers\Web\IntegrationCenterController::class, 'storeWebhook'])->name('integrations.webhooks.store');
    Route::post('/integrations/webhooks/{endpoint}/update', [\App\Http\Controllers\Web\IntegrationCenterController::class, 'updateWebhook'])->name('integrations.webhooks.update');
    Route::delete('/integrations/webhooks/{endpoint}', [\App\Http\Controllers\Web\IntegrationCenterController::class, 'deleteWebhook'])->name('integrations.webhooks.destroy');
    Route::post('/integrations/webhooks/{endpoint}/test', [\App\Http\Controllers\Web\IntegrationCenterController::class, 'testWebhook'])->name('integrations.webhooks.test');
    Route::post('/integrations/webhooks/deliveries/{delivery}/retry', [\App\Http\Controllers\Web\IntegrationCenterController::class, 'retryWebhook'])->name('integrations.webhooks.retry');
    Route::get('/integrations/api-keys/list', [\App\Http\Controllers\Web\ApiKeyController::class, 'index'])->name('integrations.api-keys');
    Route::post('/integrations/api-keys/store', [\App\Http\Controllers\Web\ApiKeyController::class, 'store'])->name('integrations.api-keys.store');
    Route::post('/integrations/api-keys/{apiKey}/revoke', [\App\Http\Controllers\Web\ApiKeyController::class, 'revoke'])->name('integrations.api-keys.revoke');
    Route::delete('/integrations/api-keys/{apiKey}', [\App\Http\Controllers\Web\ApiKeyController::class, 'destroy'])->name('integrations.api-keys.destroy');
    Route::get('/integrations/audit-logs', [\App\Http\Controllers\Web\AuditLogController::class, 'index'])->name('integrations.audit-logs');

    // Firebase credential management (Super Admin only, rate-limited).
    Route::post('/integrations/firebase/credentials/upload', [\App\Http\Controllers\Web\IntegrationCenterController::class, 'uploadFirebaseCredentials'])->name('integrations.firebase.credentials.upload')->middleware('throttle:5,1');
    Route::post('/integrations/firebase/credentials/rollback', [\App\Http\Controllers\Web\IntegrationCenterController::class, 'rollbackFirebaseCredentials'])->name('integrations.firebase.credentials.rollback')->middleware('throttle:5,1');
    Route::post('/integrations/firebase/android-config/upload', [\App\Http\Controllers\Web\IntegrationCenterController::class, 'uploadFirebaseAndroidConfig'])->name('integrations.firebase.android-config.upload')->middleware('throttle:5,1');
});

// Website (frontend) public routes - /home removed as requested
// Route::get('/home', [HomeController::class, 'index'])->name('web.home');
// Keep the route NAME so breadcrumbs and auth redirects resolve to the
// site root instead of throwing "Route [web.home] not defined" (500).
Route::get('/home', function () { return redirect('/'); })->name('web.home');

