<?php

namespace App\Http\Controllers\Web;

use App\Enum\OrderStatus;
use App\Enum\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\VariantResource;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Repositories\CustomerRepository;
use App\Repositories\DriverRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use App\Repositories\VariantRepository;
use App\Repositories\TransationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Repositories\PaymentGatewayRepository;
use App\Models\PaymentGateway;
use App\Repositories\AddressRepository;

class PosController extends Controller
{
    public function index()
    {
        $repository = new PaymentGatewayRepository();
            $gateways = $repository->query()
            ->where('is_active', 1)
            ->get();

        $data = [
            'paymentGateways' => $gateways,
            'customers' => Customer::all(),
            'services' => Service::all(),

        ];
        return view('pos.index', $data);
    }

    public function sales()
    {
        $orderStatus = OrderStatus::cases();

        $orders = (new OrderRepository())->query()->latest()->get();

        return view('pos.sales', compact('orders', 'orderStatus'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',
            'products' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return $this->json(__('Validation Error'), $validator->errors()->all(), 422);
        }

        $order = (new OrderRepository())->PosStoreByRequest($request);
        (new TransationRepository())->storeForOrder($order);

        if ($order->payment_type != 'cash') {
            $paymentUrl = route('pos.payment', ['order' => $order->id, 'gateway' => $order->payment_type]);

            return $this->json(__('Order Successful'), [
                'message' => __('Order is added successfully'),
                'payment_url' => $paymentUrl,
                'payment_type' => $order->payment_type,
                'orders' => $order,
            ]);
        }

        return $this->json(__('Order Successful'), [
            'message' => __('Order is added successfully'),
            'payment_type' => $order->payment_type,
            'payment_method' => $order->payment_method,
            'orders' => $order,
        ]);
    }

    /**
     * Show the order edit page (POS style) so staff can edit products, quantities,
     * prices, customer, notes and delivery date.
     */
    public function edit(Order $order)
    {
        $data = [
            'paymentGateways' => (new PaymentGatewayRepository())->query()->where('is_active', 1)->get(),
            'customers' => Customer::all(),
            'services' => Service::all(),
            'order' => $order->load(['products', 'address']),
        ];

        return view('orders.edit', $data);
    }

    /**
     * Update an existing order with edited products / quantities / prices / customer / notes.
     */
    public function update(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'products' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return $this->json(__('Validation Error'), $validator->errors()->all(), 422);
        }

        // Only allow price changes if the user has permission
        if (!$request->user()->can('order.edit.price')) {
            $orderProducts = $order->products->keyBy('id');
            $products = collect($request->products)->map(function ($product) use ($orderProducts) {
                $existing = $orderProducts->get($product['id']);
                $product['price'] = $existing ? (float) $existing->pivot->price : (float) $product['price'];
                return $product;
            })->values()->toArray();

            $request->merge(['products' => $products]);
        }

        $order = (new OrderRepository())->PosUpdateByRequest($request, $order);

        return $this->json(__('Order Updated'), [
            'message' => __('Order updated successfully'),
            'orders' => $order,
        ]);
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $status = $request->payment_status ?: 'paid';
        $status = config('enums.payment_status.' . strtolower($status), $status);

        $order->update([
            'payment_status' => $status,
        ]);

        return $this->json(__('Payment Status Updated'), [
            'message' => __('Payment status updated successfully'),
            'payment_status' => $order->payment_status,
        ]);
    }

    public function payment(){
        $stripeGateway  = PaymentGateway::where('name', 'stripe')->first();
        $razorpayGateway = PaymentGateway::where('name', 'razorpay')->first();
        $payStackGateway = PaymentGateway::where('name', 'paystack')->first();
        $stripe_publish_key = optional(json_decode($stripeGateway->config))->published_key ?? '';
        $razorpay_publish_key = optional(json_decode($razorpayGateway->config))->key ?? '';
        $paystack_publish_key = optional(json_decode($payStackGateway->config))->public_key ?? '';

        $data = [
            'stripe_publish_key' => $stripe_publish_key,
            'razorpay_publish_key' => $razorpay_publish_key,
            'paystack_publish_key' => $paystack_publish_key,
        ];
        return view('pos.payment',compact('data'));
    }


    public function storeCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'mobile' => 'required|unique:users,mobile',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->json(__('Validation Error'), $validator->errors(), 422);
        }

        $request['is_active'] = 1;

        $user = (new UserRepository())->registerUser($request);

        $user->assignRole(Roles::CUSTOMER->value);

        (new CustomerRepository())->storeByUser($user);

        return $this->json(__('تم الإنشاء بنجاح'), [
            'user' => (object)[
                'id' => $user->customer->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
            ],
        ], 200);
    }

    public function storeAddress(AddressRequest $request)
    {

        $address = (new AddressRepository())->storeByPos($request);

        return $this->json(__('تم الإنشاء بنجاح'), [
            'address' => (object)[
                'id' => $address->id,
                'address_name' => $address->address_name,
                'road_no' => $address->road_no,
            ],
        ], 200);
    }

    public function fetchVariants()
    {

        $variants = (new VariantRepository())->query()->orderBy('position', 'asc')->get();

        return $this->json(__('variant list'), [
            'variants' => VariantResource::collection($variants)
        ]);
    }

    public function fetchProducts(Request $request)
    {

        $products = (new ProductRepository())->getByRequest($request);

        return $this->json(__('product list'), [
            'products' => ProductResource::collection($products)
        ]);
    }
}