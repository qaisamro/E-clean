<?php

namespace App\Http\Controllers\API\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Service;
use App\Repositories\OrderRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\VariantRepository;
use App\Http\Requests\OrderRequest;
use App\Repositories\ProductRepository;
use App\Http\Resources\ProductResource;
use App\Models\Variant;
use Illuminate\Validation\Rules\Enum;
use App\Models\PaymentGateway;

class PosController extends Controller
{
    public function posCustomer(){

        $customers = Customer::all();

        return $this->json('Customer Info',[
           'customers' => $customers
        ]);

    }
    public function posService(){

        $services = Service::all();
        return $this->json('Service Info',[
            'services' => $services
        ]);

    }

    public function posStore(Request $request)
    {
        $request->validate([
            'customer_id' => 'exists:customers,id',
            'service_id' => 'exists:services,id',
            'variant_id' => 'exists:variants,id',
            'payment_type' => ['required', new Enum(PaymentGateway::class)],
        ]);
        $order = (new OrderRepository())->PosStoreByRequest($request);

        (new TransactionRepository())->storeForOrder($order);


        if($order->payment_type != 'cash'){

            $paymentUrl = route('pos.payment', ['order' => $order->id, 'gateway' => $order->payment_type]);

        return $this->json('Order Successful',[
            'message' => 'Order is added successfully',
            'payment_url' => $paymentUrl,
            'payment_type' => $order->payment_type,
            'orders' =>$order,
        ]);

        }else{
            return $this->json('Order Successful',[
                'message' => 'Order is added successfully',

            ]);
        }


    }

    public function fetchVariants()
    {
        $variants = Variant::all();
        return $this->json('Variant Info',[
            'variants' => $variants
        ]);



    }

    public function fetchProducts(Request $request)
    {
        // $store = auth()->user()->store;

        // if ($store) {
        //     $request->merge(['store_id' => $store?->id]);
        // }

        $products = (new ProductRepository())->getByRequest($request);

        return $this->json('product list', [
            'products' => ProductResource::collection($products)
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
        return view('pos.order-payment',compact('data'));
    }
}
