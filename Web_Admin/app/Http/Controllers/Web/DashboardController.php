<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Enum\OrderStatus;
use App\Repositories\OrderRepository;
use App\Repositories\ServiceRepository;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Drivers must never see the dashboard in any case
        if (auth()->user()->hasRole('driver')) {
            return redirect()->route('driver.account');
        }

        // Store owners (and child accounts with dashboard permission) go to
        // the store dashboard; the main dashboard is for the super admin.
        if (\App\Support\VendorAccount::isStoreStaff(auth()->user())) {
            $staff = auth()->user();
            if ($staff->hasRole('vendor_sub') && !$staff->can('vendor.dashboard')) {
                return redirect()->route('profile.index');
            }
            return redirect()->route('vendor.dashboard');
        }

        $customers = Customer::with('user')->get();
        $services = (new ServiceRepository())->getAll();
        $products = Product::with('service')->get();

        $confirmOrder = (new OrderRepository())->getByStatus(OrderStatus::CONFIRM)->count();
        $completeOrder = (new OrderRepository())->getByStatus(OrderStatus::DELIVERED)->count();
        $pendingOrder = (new OrderRepository())->getByStatus(OrderStatus::PENDING)->count();
        $onPregressOrder = (new OrderRepository())->countByStatus([
            OrderStatus::PICKED_UP->value,
            OrderStatus::PROCESSING->value,
            OrderStatus::ON_GOING->value,
        ]);

        $cancelledOrder = (new OrderRepository())->getByStatus(OrderStatus::CANCELLED)->count();

        return view('dashboard.index', compact(
            'customers',
            'services',
            'products',
            'confirmOrder',
            'completeOrder',
            'pendingOrder',
            'onPregressOrder',
            'cancelledOrder'
        ));
    }
}
