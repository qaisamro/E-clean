<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Enum\OrderStatus;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ServiceRepository;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        $services = (new ServiceRepository())->getAll();
        $products = (new ProductRepository())->getAll();
        $income = (new OrderRepository())->getByStatus('Delivered')->sum('amount');

        $revenues = (new OrderRepository())->getRevenueReport();

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
            'revenues',
            'income',
            'confirmOrder',
            'completeOrder',
            'pendingOrder',
            'onPregressOrder',
            'cancelledOrder'
        ));
    }
}
