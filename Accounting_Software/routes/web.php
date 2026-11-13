<?php

use App\Livewire\Order\EditOrder;
use App\Livewire\Order\OrderList;
use App\Livewire\Order\CreateOrder;
use App\Livewire\Salary\SalaryList;
use Illuminate\Support\Facades\Route;
use App\Livewire\Customer\CustomerList;
use App\Livewire\Customer\CustomerCreate;
use App\Livewire\Customer\CustomerPaymentCreate;
use App\Livewire\Customer\CustomerDebtList;
use App\Livewire\Expenses\ExpensesList;
use App\Livewire\Order\OrderByCustomer;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InvoiceTemplateController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OverviewController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ItemServiceController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\CustomerStatementController;
use App\Livewire\Inventory\CategoryList;
use App\Livewire\Inventory\ProductList;
use App\Livewire\Inventory\CreateProduct;
use App\Livewire\Inventory\EditProduct;
use App\Livewire\Inventory\MovementList;
use App\Livewire\Inventory\CreateMovement;
use App\Livewire\Inventory\ShortagesList;
use App\Livewire\Inventory\InventoryReport;
use App\Livewire\Suppliers\SupplierList;
use App\Livewire\Suppliers\SupplierCreate;
use App\Livewire\Suppliers\SupplierForm;
use App\Livewire\Suppliers\SupplierShow;
use App\Livewire\Suppliers\BillList;
use App\Livewire\Suppliers\BillCreate;
use App\Livewire\Suppliers\BillShow;
use App\Livewire\Suppliers\PaymentList;
use App\Livewire\Suppliers\PaymentCreate;
use App\Http\Controllers\SupplierStatementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the "web" middleware group and assigned to the
| "web" middleware group from the RouteServiceProvider.
|------------------------------------------------------------------------------
*/

Route::middleware(['auth:web', 'check-status'])->group(function () {
    Route::redirect('/', 'overview');

    Route::get('overview', OverviewController::class)->name('overview');

    ####################################### Customers & Orders (granular) #######################################
    Route::get('customers', CustomerList::class)->middleware('permission:customers.view')->name('customers.index');
    Route::get('customers/create', CustomerCreate::class)->middleware('permission:customers.manage')->name('customers.create');
    Route::get('customer-payments/create', CustomerPaymentCreate::class)->middleware('permission:customers.manage')->name('customer-payments.create');
    Route::get('customers-debts', CustomerDebtList::class)->middleware('permission:customers.view')->name('customers.debts');
    Route::get('customers/{customer}/statement', [CustomerStatementController::class, 'show'])->middleware('permission:customers.view')->name('customers.statement');
    Route::get('customers/{customer}/statement/print', [CustomerStatementController::class, 'print'])->middleware('permission:customers.view')->name('customers.statement.print');
    Route::get('customers/{customer}', [CustomerController::class, 'show'])->middleware('permission:customers.view')->name('customers.show');
    Route::get('customers/{customer}/edit', [CustomerController::class, 'edit'])->middleware('permission:customers.manage')->name('customers.edit');
    Route::put('customers/{customer}', [CustomerController::class, 'update'])->middleware('permission:customers.manage')->name('customers.update');

    Route::get('orders', OrderList::class)->middleware('permission:orders.view')->name('orders.index');
    Route::get('orders/ordered-by-customer', OrderByCustomer::class)->middleware('permission:orders.view')->name('orders.customer.index');
    Route::get('orders/create', CreateOrder::class)->middleware('permission:orders.create')->name('orders.create');
    Route::get('orders/{order}/edit', EditOrder::class)->middleware('permission:orders.edit')->name('orders.edit');
    Route::get('orders/{order}/stickers', [OrderController::class, 'stickers'])->middleware('permission:orders.view')->name('orders.stickers');
    Route::get('orders/{order}', [OrderController::class, 'show'])->middleware('permission:orders.view')->name('orders.show');
    Route::get('orders/{order}/invoice', [InvoiceTemplateController::class, 'show'])->middleware('permission:orders.view')->name('orders.invoice');
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])->middleware('permission:orders.delete')->name('orders.destroy');

    ####################################### Admin modules (granular) #######################################
    Route::get('item-prices/{item}', [ItemServiceController::class, 'show'])->middleware('permission:item_services.view')->name('itemPrices.show');
    Route::get('itemServices', [ItemServiceController::class, 'index'])->middleware('permission:item_services.view')->name('itemServices.index');
    Route::resource('itemServices', ItemServiceController::class)->except(['index', 'show'])->middleware('permission:item_services.manage');

    Route::get('services', [ServiceController::class, 'index'])->middleware('permission:services.view')->name('services.index');
    Route::resource('services', ServiceController::class)->except(['index', 'show'])->middleware('permission:services.manage');

    // The items module was merged into the unified products inventory
    // inventory — keep old links alive by redirecting.
    Route::get('items', fn () => redirect()->route('inventory.products.index', ['kind' => 'laundry']))->middleware('permission:inventory.view')->name('items.index');
    Route::get('items/create', fn () => redirect()->route('inventory.products.create', ['kind' => 'laundry']))->middleware('permission:inventory.manage')->name('items.create');
    Route::get('items/{item}/edit', fn () => redirect()->route('inventory.products.index', ['kind' => 'laundry']))->middleware('permission:inventory.manage')->name('items.edit');
    Route::post('items', fn () => redirect()->route('inventory.products.index', ['kind' => 'laundry']))->middleware('permission:inventory.manage')->name('items.store');
    Route::put('items/{item}', fn () => redirect()->route('inventory.products.index'))->middleware('permission:inventory.manage')->name('items.update');
    Route::delete('items/{item}', fn () => redirect()->route('inventory.products.index'))->middleware('permission:inventory.manage')->name('items.destroy');

    Route::post('customers/reset-default-password/{customer}', [CustomerController::class, 'resetPassword'])->middleware('permission:customers.manage')->name('customers.password-reset');

    ####################################### Inventory #######################################
    Route::get('inventory', fn () => redirect()->route('inventory.products.index'))->middleware('permission:inventory.view')->name('inventory.index');
    Route::get('inventory/products', ProductList::class)->middleware('permission:inventory.view')->name('inventory.products.index');
    Route::get('inventory/products/create', CreateProduct::class)->middleware('permission:inventory.manage')->name('inventory.products.create');
    Route::get('inventory/products/{product}/edit', EditProduct::class)->middleware('permission:inventory.manage')->name('inventory.products.edit');
    Route::get('inventory/movements', MovementList::class)->middleware('permission:inventory.view')->name('inventory.movements.index');
    Route::get('inventory/movements/create', CreateMovement::class)->middleware('permission:inventory.manage')->name('inventory.movements.create');
    Route::get('inventory/shortages', ShortagesList::class)->middleware('permission:inventory.view')->name('inventory.shortages');
    Route::get('inventory/reports', InventoryReport::class)->middleware('permission:inventory.view')->name('inventory.reports');
    Route::get('inventory/categories', CategoryList::class)->middleware('permission:inventory.manage')->name('inventory.categories.index');

    ####################################### Expenses & Salaries #######################################
    Route::get('expenses', ExpensesList::class)->middleware('permission:expenses.view')->name('expenses.index');
    Route::resource('expenses', ExpenseController::class)->except('index')->middleware('permission:expenses.manage');
    Route::get('salaries', SalaryList::class)->middleware('permission:salaries.view')->name('salaries.index');
    Route::resource('salaries', SalaryController::class)->except('index')->middleware('permission:salaries.manage');

    ####################################### Suppliers #######################################
    Route::get('suppliers', SupplierList::class)->middleware('permission:suppliers.view')->name('suppliers.index');
    Route::get('suppliers/create', SupplierCreate::class)->middleware('permission:suppliers.manage')->name('suppliers.create');
    Route::get('suppliers/{supplier}', SupplierShow::class)->middleware('permission:suppliers.view')->name('suppliers.show');
    Route::get('suppliers/{supplier}/edit', SupplierForm::class)->middleware('permission:suppliers.manage')->name('suppliers.edit');
    Route::get('suppliers/{supplier}/statement/print', [SupplierStatementController::class, 'print'])->middleware('permission:suppliers.view')->name('suppliers.statement.print');
    Route::get('supplier-bills', BillList::class)->middleware('permission:suppliers.view')->name('supplier-bills.index');
    Route::get('supplier-bills/create', BillCreate::class)->middleware('permission:suppliers.manage')->name('supplier-bills.create');
    Route::get('supplier-bills/{bill}', BillShow::class)->middleware('permission:suppliers.view')->name('supplier-bills.show');
    Route::get('supplier-payments', PaymentList::class)->middleware('permission:suppliers.view')->name('supplier-payments.index');
    Route::get('supplier-payments/create', PaymentCreate::class)->middleware('permission:suppliers.manage')->name('supplier-payments.create');

    ####################################### Reports #######################################
    Route::get('reports', [ReportController::class, 'index'])->middleware('permission:reports.view')->name('reports.index');

    ###################################### SuperAdmin only #####################################
    Route::middleware('is-superAdmin')->group(function () {
        Route::post('users/toggle-suspension/{user}', [UserController::class, 'toggleSuspension'])->name('users.toggle-suspension');
        Route::post('users/reset-default-password/{user}', [UserController::class, 'resetPassword'])->name('users.password-reset');
        Route::resource('users', UserController::class);


        Route::get('invoice-template', [InvoiceTemplateController::class, 'edit'])->name('invoice-template.edit');
        Route::post('invoice-template', [InvoiceTemplateController::class, 'update'])->name('invoice-template.update');
        Route::delete('invoice-template', [InvoiceTemplateController::class, 'reset'])->name('invoice-template.reset');
        Route::get('invoice-template/preview', [InvoiceTemplateController::class, 'preview'])->name('invoice-template.preview');
    });
    ##################################### End SuperAdmin only ########################################

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware('auth:customer')->as('customer.')->prefix('customer')->group(function () {
    Route::get('profile', [CustomerProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [CustomerProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile/update', [CustomerProfileController::class, 'updateProfileInfo'])->name('profile.info.update');
    Route::put('profile/password', [CustomerProfileController::class, 'updateProfilePassword'])->name('profile.password.update');
});

require __DIR__ . '/auth.php';
