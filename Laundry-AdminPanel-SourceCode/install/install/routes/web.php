<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\Order\OrderController as ApiOrderController;
use App\Http\Controllers\API\PaymentGatewayController as ApiPaymentGatewayController;
use App\Http\Controllers\CreateSuperAdmin;
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
use App\Http\Controllers\Web\Products\SubProductController;
use App\Http\Controllers\Web\Profile\ProfileController;
use App\Http\Controllers\Web\Revenues\RevenueController;
use App\Http\Controllers\Web\Root\AdminController;
use App\Http\Controllers\Web\Services\AdditionalServiceController;
use App\Http\Controllers\Web\Services\ServiceController;
use App\Http\Controllers\Web\Setting\SettingController;
use App\Http\Controllers\Web\SMSGatewaySetupController;
use App\Http\Controllers\Web\Social\SocialController;
use App\Http\Controllers\Web\StripeKeyUpateController;
use App\Http\Controllers\Web\StripePaymentController;
use App\Http\Controllers\Web\Variants\VariantController;
use App\Http\Controllers\Web\WebFaqCategoryController;
use App\Http\Controllers\Web\WebSettingController;
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

// Language change
Route::get('/change-local', function (Request $request) {
    if ($request->filled('ln')) {
        session(['local' => $request->ln]);
    }

    return back();
})->name('change.local');

// Auth
Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/form-login', [LoginController::class, 'login'])->name('form-login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Super admin (first run)
Route::get('/create-superadmin', [CreateSuperAdmin::class, 'index'])->name('create.superadmin.form');
Route::post('/create-superadmin', [CreateSuperAdmin::class, 'store'])->name('create.superadmin');

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('root');

    // Notifications (ajax polling in layout)
    Route::get('/orders/new', [ApiOrderController::class, 'newOrder'])->name('new.orders');

    // Areas
    Route::get('/areas', [AreaController::class, 'index'])->name('areas.index');
    Route::post('/areas', [AreaController::class, 'store'])->name('areas.store');
    Route::post('/areas/{area}', [AreaController::class, 'update'])->name('areas.update');
    Route::post('/areas/{area}/toggle', [AreaController::class, 'toggle'])->name('areas.toggle');
    Route::delete('/areas/{area}', [AreaController::class, 'delete'])->name('areas.delete');

    // Services
    Route::get('/services', [ServiceController::class, 'index'])->name('service.index');
    Route::get('/services/create', [ServiceController::class, 'create'])->name('service.create');
    Route::post('/services', [ServiceController::class, 'store'])->name('service.store');
    Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('service.edit');
    Route::post('/services/{service}', [ServiceController::class, 'update'])->name('service.update');
    Route::post('/services/{service}/status', [ServiceController::class, 'toggleActivationStatus'])->name('service.status.toggle');
    Route::get('/services/{service}/variants', [ServiceController::class, 'getVariant'])->name('service.get.variant');
    Route::delete('/services/{service}', [ServiceController::class, 'delete'])->name('service.delete');

    // Additional services
    Route::get('/additional-services', [AdditionalServiceController::class, 'index'])->name('additional.index');
    Route::get('/additional-services/create', [AdditionalServiceController::class, 'create'])->name('additional.create');
    Route::post('/additional-services', [AdditionalServiceController::class, 'store'])->name('additional.store');
    Route::get('/additional-services/{additional}/edit', [AdditionalServiceController::class, 'edit'])->name('additional.edit');
    Route::post('/additional-services/{additional}', [AdditionalServiceController::class, 'update'])->name('additional.update');
    Route::post('/additional-services/{additional}/status', [AdditionalServiceController::class, 'toggleActivationStatus'])->name('additional.status.toggle');

    // Variants
    Route::get('/variants', [VariantController::class, 'index'])->name('variant.index');
    Route::post('/variants', [VariantController::class, 'store'])->name('variant.store');
    Route::post('/variants/{variant}', [VariantController::class, 'update'])->name('variant.update');
    Route::get('/variants/{variant}/products', [VariantController::class, 'productsVariant'])->name('variant.products');
    Route::delete('/variants/{variant}', [VariantController::class, 'delete'])->name('variant.delete');

    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('product.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('product.create');
    Route::post('/products', [ProductController::class, 'store'])->name('product.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('product.edit');
    Route::post('/products/{product}', [ProductController::class, 'update'])->name('product.update');
    Route::post('/products/{product}/status', [ProductController::class, 'toggleActivationStatus'])->name('product.status.toggle');
    Route::post('/products/{product}/update-order', [ProductController::class, 'orderUpdate'])->name('product.update.order');
    Route::delete('/products/{product}', [ProductController::class, 'delete'])->name('product.delete');

    // Sub products
    Route::get('/products/{product}/subproducts', [SubProductController::class, 'index'])->name('product.subproduct.index');
    Route::get('/products/{product}/subproducts/create', [SubProductController::class, 'create'])->name('product.subproduct.create');
    Route::post('/products/{product}/subproducts', [SubProductController::class, 'store'])->name('product.subproduct.store');
    Route::get('/sub-product/{product}/edit', [SubProductController::class, 'edit'])->name('product.subproduct.edit');
    Route::post('/sub-product/{product}', [SubProductController::class, 'update'])->name('product.subproduct.update');
    Route::delete('/sub-product/{product}', [SubProductController::class, 'destroy'])->name('product.subproduct.delete');

    // Coupons
    Route::get('/coupons', [CouponController::class, 'index'])->name('coupon.index');
    Route::get('/coupons/create', [CouponController::class, 'create'])->name('coupon.create');
    Route::post('/coupons', [CouponController::class, 'store'])->name('coupon.store');
    Route::get('/coupons/{coupon}/edit', [CouponController::class, 'edit'])->name('coupon.edit');
    Route::post('/coupons/{coupon}', [CouponController::class, 'update'])->name('coupon.update');

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
    Route::get('/vendors/{vendor}/orders', [\App\Http\Controllers\Web\VendorController::class, 'orders'])->name('vendors.orders');
    Route::get('/vendors/{vendor}/account', [\App\Http\Controllers\Web\VendorController::class, 'account'])->name('vendors.account');

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
    Route::get('/admins/{user}', [AdminController::class, 'show'])->name('admin.show');
    Route::get('/admins/{user}/edit', [AdminController::class, 'edit'])->name('admin.edit');
    Route::post('/admins/{user}', [AdminController::class, 'update'])->name('admin.update');
    Route::post('/admins/{user}/set-permission', [AdminController::class, 'setPermission'])->name('admin.set-permission');
    Route::post('/admins/{user}/status-update', [AdminController::class, 'toggleStatusUpdate'])->name('admin.status-update');

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
    Route::post('/banners', [BannerController::class, 'store'])->name('banner.store');
    Route::get('/banners/{banner}/edit', [BannerController::class, 'edit'])->name('banner.edit');
    Route::post('/banners/{banner}', [BannerController::class, 'update'])->name('banner.update');
    Route::post('/banners/{banner}/destroy', [BannerController::class, 'destroy'])->name('banner.destroy');
    Route::post('/banners/{banner}/status', [BannerController::class, 'toggleActivationStatus'])->name('banner.status.toggle');

    // Offers
    Route::get('/offers', [\App\Http\Controllers\Web\Offers\OfferController::class, 'index'])->name('offer.index');
    Route::get('/offers/create', [\App\Http\Controllers\Web\Offers\OfferController::class, 'create'])->name('offer.create');
    Route::post('/offers', [\App\Http\Controllers\Web\Offers\OfferController::class, 'store'])->name('offer.store');
    Route::get('/offers/{offer}/edit', [\App\Http\Controllers\Web\Offers\OfferController::class, 'edit'])->name('offer.edit');
    Route::post('/offers/{offer}', [\App\Http\Controllers\Web\Offers\OfferController::class, 'update'])->name('offer.update');
    Route::post('/offers/{offer}/destroy', [\App\Http\Controllers\Web\Offers\OfferController::class, 'destroy'])->name('offer.destroy');
    Route::post('/offers/{offer}/status', [\App\Http\Controllers\Web\Offers\OfferController::class, 'toggleActivationStatus'])->name('offer.status.toggle');

    // Contacts
    Route::get('/contacts', [ContactController::class, 'index'])->name('contact');

    // Revenue / reports
    Route::get('/revenues', [RevenueController::class, 'index'])->name('revenue.index');
    Route::get('/revenues/pdf', [RevenueController::class, 'generatePDF'])->name('revenue.generate.pdf');
    Route::get('/reports/pdf', [RevenueController::class, 'generateInvoicePDF'])->name('report.generate.pdf');

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

    // Delivery cost / mobile app / social links / web settings
    Route::get('/delivery-cost', [DeliveryCostController::class, 'index'])->name('deliveryCost');
    Route::post('/delivery-cost', [DeliveryCostController::class, 'updateOrCreate'])->name('deliveryCost.store');
    Route::get('/mobile-app', [MobileAppUrlController::class, 'index'])->name('mobileApp');
    Route::post('/mobile-app', [MobileAppUrlController::class, 'updateOrCreate'])->name('mobileApp.store');

    Route::get('/social-links', [SocialController::class, 'index'])->name('socialLink.index');
    Route::post('/social-links', [SocialController::class, 'store'])->name('socialLink.store');
    Route::post('/social-links/{socialLink}', [SocialController::class, 'update'])->name('socialLink.update');
    Route::delete('/social-links/{socialLink}', [SocialController::class, 'delete'])->name('socialLink.delete');

    Route::get('/web-setting', [WebSettingController::class, 'index'])->name('webSetting.index');
    Route::post('/web-setting/{webSetting}', [WebSettingController::class, 'update'])->name('webSetting.update');

    // SMS gateway / mail config / fcm / stripe key
    Route::get('/sms-gateway', [SMSGatewaySetupController::class, 'index'])->name('sms-gateway.index');
    Route::post('/sms-gateway', [SMSGatewaySetupController::class, 'update'])->name('sms-gateway.update');
    Route::get('/mail-config', [MailConfigurationController::class, 'index'])->name('mail-config.index');
    Route::post('/mail-config', [MailConfigurationController::class, 'update'])->name('mail-config.update');
    Route::get('/fcm', [FCMController::class, 'index'])->name('fcm.index');
    Route::post('/fcm', [FCMController::class, 'update'])->name('fcm.update');
    Route::get('/stripe-key', [StripeKeyUpateController::class, 'index'])->name('stripeKey.index');
    Route::post('/stripe-key', [StripeKeyUpateController::class, 'update'])->name('stripeKey.update');

    // Payment gateways (admin)
    Route::get('/payment-gateway', [PaymentGatewayController::class, 'index'])->name('payment-gateway.index');
    Route::post('/payment-gateway/{paymentGateway}', [PaymentGatewayController::class, 'update'])->name('payment-gateway.update');
    Route::get('/payment-gateway/{paymentGateway}/toggle', [PaymentGatewayController::class, 'toggle'])->name('payment-gateway.toggle');

    // Invoice manage
    Route::get('/invoice-manage', [InvoiceManageController::class, 'index'])->name('invoiceManage.index');
    Route::post('/invoice-manage/{invoiceManage}', [InvoiceManageController::class, 'update'])->name('invoiceManage.update');

    // Schedules
    Route::get('/{type}/scheduls', [OrderScheduleController::class, 'index'])->name('schedule.index')->where('type', 'pickup|delivery');
    Route::post('/schedule/{id}/status', [OrderScheduleController::class, 'updateStatus'])->name('toggole.status.update');
    Route::post('/schedule/{orderSchedule}', [OrderScheduleController::class, 'update'])->name('schedule.update');

    // Notifications (admin)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notification.index');
    Route::post('/notifications/send', [NotificationController::class, 'SendNotification'])->name('notification.send');
    Route::get('/notifications/manage', [NotificationManageController::class, 'index'])->name('notification.manage');
    Route::post('/notifications/manage/{notificationManage}', [NotificationManageController::class, 'update'])->name('notification.manage.update');

    // Languages
    Route::get('/languages', [LanguageController::class, 'index'])->name('language.index');
    Route::get('/languages/create', [LanguageController::class, 'create'])->name('language.create');
    Route::post('/languages', [LanguageController::class, 'store'])->name('language.store');
    Route::get('/languages/{language}/edit', [LanguageController::class, 'edit'])->name('language.edit');
    Route::post('/languages/{language}', [LanguageController::class, 'update'])->name('language.update');
    Route::delete('/languages/{language}', [LanguageController::class, 'delete'])->name('language.delete');

    // Website FAQ (admin CRUD)
    Route::get('/faqs', [FaqController::class, 'index'])->name('web.faq.list');
    Route::get('/faqs/create', [FaqController::class, 'create'])->name('web.faq.create');
    Route::post('/faqs', [FaqController::class, 'store'])->name('web.faq.store');
    Route::get('/faqs/{slug}/edit', [FaqController::class, 'edit'])->name('web.faq.edit');
    Route::post('/faqs/{slug}', [FaqController::class, 'update'])->name('web.faq.update');
    Route::post('/faqs/{slug}/delete', [FaqController::class, 'delete'])->name('web.faq.delete');
    Route::post('/faqs/{id}/status', [FaqController::class, 'toggleStatus'])->name('web.faq.status.toggle');

    // Website FAQ categories (admin CRUD)
    Route::get('/faq-categories', [WebFaqCategoryController::class, 'index'])->name('web.faq.category.index');
    Route::get('/faq-categories/create', [WebFaqCategoryController::class, 'create'])->name('web.faq.category.create');
    Route::post('/faq-categories', [WebFaqCategoryController::class, 'store'])->name('web.faq.category.store');
    Route::get('/faq-categories/{id}/edit', [WebFaqCategoryController::class, 'edit'])->name('web.faq.category.edit');
    Route::post('/faq-categories/{id}', [WebFaqCategoryController::class, 'update'])->name('web.faq.category.update');
    Route::post('/faq-categories/{id}/delete', [WebFaqCategoryController::class, 'delete'])->name('web.faq.category.delete');
    Route::post('/faq-categories/{id}/status', [WebFaqCategoryController::class, 'toggleStatus'])->name('web.faq.category.status.toggle');

    // Website settings editor (admin)
    Route::get('/website-settings', [WebsiteController::class, 'index'])->name('web.setting');
    Route::post('/website-settings/{type}', [WebsiteController::class, 'update'])->name('web.settingUpdate');

    // POS
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('/pos/sales', [PosController::class, 'sales'])->name('pos.sales');
    Route::post('/pos/store', [PosController::class, 'store'])->name('pos.store');
    Route::get('/pos/payment', [PosController::class, 'payment'])->name('pos.payment');
    Route::post('/pos/customer-store', [PosController::class, 'storeCustomer'])->name('pos.customerStore');
    Route::post('/pos/address-store', [PosController::class, 'storeAddress'])->name('pos.addressStore');
    Route::get('/pos/fetch/variants', [PosController::class, 'fetchVariants'])->name('pos.fetch.variants');
    Route::get('/pos/fetch/products', [PosController::class, 'fetchProducts'])->name('pos.fetch.products');

    // Payment (web checkout / POS)
    Route::get('/web/test', [PaymentController::class, 'testIndex'])->name('web.test');
    Route::get('/payment/{enryptedOrderId}/{enryptCardId}', [PaymentController::class, 'payment'])->name('payment');
    Route::get('/payment/intent/{customer}/{card}/{amount}/{order}', [PaymentController::class, 'intent'])->name('payment.intent');
    Route::post('/payment/{id}/update', [PaymentController::class, 'updatePayment'])->name('payment.update');
    Route::post('/payment/process/{order}', [PaymentGatewayController::class, 'process'])->name('payment.process');
    Route::post('/payment/process-order/{order}', [ApiPaymentGatewayController::class, 'processOrder'])->name('payment.process.order');
    Route::get('/payment/success', [ApiPaymentGatewayController::class, 'success'])->name('payment.success');

    // Stripe (web payment page)
    Route::get('/stripe/payment', [StripePaymentController::class, 'index'])->name('stripe.payment');
    Route::get('/stripe/charge', [StripePaymentController::class, 'charge'])->name('charge');
});

// Website (frontend) public routes - /home removed as requested
// Route::get('/home', [HomeController::class, 'index'])->name('web.home');
Route::get('/website/services', [WebsiteServiceController::class, 'index'])->name('web.services');
Route::get('/website/service', [WebsiteServiceController::class, 'index'])->name('web.service');
Route::get('/store/{vendor}', [WebsiteServiceController::class, 'vendorStore'])->name('web.vendor.store');
// Route::get('/stores', function(){ return redirect()->route('web.home'); })->name('web.stores');
Route::get('/faq', [WebsiteFaqController::class, 'website'])->name('web.faq');
Route::get('/website/contact', [WebsiteContactController::class, 'index'])->name('web.contact');
Route::post('/website/contact', [WebsiteContactController::class, 'submit'])->name('web.contact.submit');
Route::get('/cart', [CheckoutController::class, 'cart'])->name('web.cart');
Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('web.checkout');
Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('checkout.placeOrder');
Route::post('/cart/{id}/update', [CheckoutController::class, 'update'])->name('cart.update');
Route::post('/cart/{id}/remove', [CheckoutController::class, 'remove'])->name('cart.remove');
Route::post('/cart/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('cart.applyCoupon');
Route::get('/order-success/{id}', [CheckoutController::class, 'orderSuccess'])->name('order.success');
Route::get('/order-details/{id}', [CheckoutController::class, 'orderDetails'])->name('order.details');

Route::get('/terms', [policyController::class, 'terms'])->name('web.terms');
Route::get('/privacy', [policyController::class, 'privacy'])->name('web.privacy');

Route::get('/sign-in', [WebsiteAuthController::class, 'showLogin'])->name('web.showLogin');
Route::get('/sign-up', [WebsiteAuthController::class, 'showRegister'])->name('web.showRegister');
Route::post('/sign-in', [WebsiteAuthController::class, 'login'])->name('web.login');
Route::post('/sign-up', [WebsiteAuthController::class, 'register'])->name('web.register');
Route::post('/sign-out', [WebsiteAuthController::class, 'logout'])->name('web.logout');

// Stripe callbacks
Route::get('/stripe/success/{order}', [StripeController::class, 'stripeSuccess'])->name('stripe.success');
Route::get('/stripe/cancel/{order}', [StripeController::class, 'stripeCancel'])->name('stripe.cancel');
Route::get('/stripe/failure/{order}', [StripeController::class, 'orderFailure'])->name('stripe.failure');

// Website customer account
Route::middleware('auth')->group(function () {
    Route::get('/account', [WebsiteSettingsController::class, 'index'])->name('web.settings');
    Route::post('/account', [WebsiteSettingsController::class, 'update'])->name('web.settings.update');
    Route::get('/account/overview', [WebsiteSettingsController::class, 'overview'])->name('web.overview');
    Route::get('/account/my-orders', [WebsiteSettingsController::class, 'myOrders'])->name('web.my.orders');
    Route::get('/account/orders/{orderId}', [WebsiteSettingsController::class, 'orders'])->name('web.orders');
    Route::get('/account/orders/{orderId}/payment', [WebsiteSettingsController::class, 'payOrder'])->name('web.order.payment');
    Route::get('/account/addresses', [WebsiteSettingsController::class, 'addresses'])->name('web.addresses');
    Route::post('/account/addresses', [WebsiteSettingsController::class, 'storeAddress'])->name('web.addresses.store');
    Route::get('/account/addresses/list', [WebsiteSettingsController::class, 'getAddresses'])->name('web.addresses.list');
    Route::post('/account/addresses/{id}/destroy', [WebsiteSettingsController::class, 'destroyAddress'])->name('web.addresses.destroy');
    Route::post('/account/addresses/{id}/default', [WebsiteSettingsController::class, 'setDefaultAddress'])->name('web.addresses.setDefault');
    Route::get('/account/favourite', [WebsiteSettingsController::class, 'favourite'])->name('web.favourite');
});
