<?php

namespace App\Http\Controllers\Website;

use App\Enum\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\Website\ProfileUpdateRequest;
use App\Repositories\AddressRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\UserRepository;
use App\Models\WebSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class SettingsController extends Controller
{
    public function __construct(
        private UserRepository $userRepository,
        private CustomerRepository $customerRepository,
        private OrderRepository $orderRepository,
        private AddressRepository $addressRepository
    ) {}

    public function index()
    {
        $user = Auth::user();
        return view('website.pages.settings', compact('user'));
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = Auth::user();

        if (!$user) {
            return back()->with('error', 'المستخدم غير موجود');
        }

        $this->userRepository->updateWebsiteProfileByRequest($request, (object)$user);

        return back()->with('success', 'تم تحديث الحساب بنجاح');
    }

    public function orders($orderId)
    {
        $order = $this->orderRepository->getOrderDetailsById($orderId);

        if (!$order) {
            abort(404, 'Order not found');
        }

        return view('website.pages.order-details', compact('order'));
    }

    public function payOrder($orderId)
    {
        $user = Auth::user();
        $order = $this->orderRepository->getOrderDetailsById($orderId);

        if (!$order) {
            abort(404, 'Order not found');
        }

        $customer = $this->customerRepository->findByUserId($user->id);
        if (!$customer) {
            $customer = $this->customerRepository->storeByUser((object)$user);
        }

        if (!in_array($order->customer_id, [$customer->id, $user->id], true)) {
            abort(403, 'Unauthorized payment attempt');
        }

        if ($order->payment_status === config('enums.payment_status.paid')) {
            return redirect()->route('web.orders', $order->id)->with('info', 'الطلب مدفوع بالفعل.');
        }

        $webSetting = WebSetting::first();
        $currency = strtolower($webSetting->currency_name ?? 'usd');
        if ($currency === 'bdt') {
            $currency = 'usd';
        }

        try {
            $paymentUrl = (new PaymentRepository())->stripePaymentURL([
                'currency' => $currency,
                'amount' => $order->total_amount,
                'product_data' => "Order #{$order->order_code}",
            ], $order->id);

            return redirect($paymentUrl);
        } catch (\Exception $e) {
            return redirect()->route('web.orders', $order->id)
                ->with('error', 'تعذر بدء الدفع عبر Stripe: ' . $e->getMessage());
        }
    }

    public function myOrders()
    {
        $user = Auth::user();

        $customer = $this->customerRepository->findByUserId($user->id);
        if (!$customer) {
            $customer = $this->customerRepository->storeByUser((object)$user);
        }

        $customerIds = [$customer->id];
        if ($this->orderRepository->existsByCustomerId($user->id) && $user->id !== $customer->id) {
            $customerIds[] = $user->id;
        }

        $orders = $this->orderRepository->query()
            ->whereIn('customer_id', array_unique($customerIds))
            ->with([
                'products.service',
                'products.variant',
                'address',
                'customer',
                'payment',
                'transaction',
                'coupon'
            ])
            ->latest()
            ->get();

        return view('website.pages.my-orders', compact('orders'));
    }

    public function favourite()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Get order statistics if user is authenticated
        $orderStats = [
            'total' => 0,
            'in_progress' => 0,
            'completed' => 0,
            'cancelled' => 0,
        ];

        $customer = $this->customerRepository->findByUserId($user->id);

        // Create customer record if not exists
        if (!$customer) {
            $customer = $this->customerRepository->storeByUser((object)$user);
        }

        // Get customer ID for querying orders
        $customerId = $customer->id;

        // Check if there are orders stored with user_id as customer_id (legacy data)
        $legacyOrders = $this->orderRepository->existsByCustomerId($user->id);
        if ($legacyOrders) {
            // Use user->id for legacy orders compatibility
            $customerId = $user->id;
        }

        // Get order statistics using repository
        $orderStats = $this->orderRepository->getStatsByCustomerId($customerId, $user->id);

        // Get favorite stores (using orders directly)
        $allOrders = $this->orderRepository->getByCustomerIdWithAddress($customerId);

        // Group orders by address to create favorite stores list
        $favoriteStores = $allOrders->groupBy('address_id')->map(function ($orders) {
            $firstOrder = $orders->first();
            return [
                'store' => (object)[
                    'name' => $firstOrder->address ? ($firstOrder->address->label ?? 'Address #' . $firstOrder->address_id) : 'Store Order'
                ],
                'order_count' => $orders->count(),
                'rating' => 5.0,
            ];
        })->filter(function ($item) {
            return $item['store']->name !== null;
        })->values();

        return view('website.pages.favorite', compact('user', 'orderStats', 'favoriteStores'));
    }


    public function overview()
    {
        $user = Auth::user();

        $customer = $this->customerRepository->findByUserId($user->id);
        if (!$customer) {
            $customer = $this->customerRepository->storeByUser((object)$user);
        }

        $orders = $this->orderRepository->getByCustomerIdWithAddress($customer->id);
        $addresses = $this->addressRepository->getByCustomerId($customer->id);
        return view('website.pages.overview', compact('user', 'orders', 'addresses'));
    }

    public function addresses()
    {
        $user = Auth::user();

        $customer = $this->customerRepository->findByUserId($user->id);
        if (!$customer) {
            $customer = $this->customerRepository->storeByUser((object)$user);
        }

        $addresses = $this->addressRepository->getByCustomerId($customer->id)->sortByDesc('is_default');

        return view('website.pages.addresses', compact('user', 'addresses'));
    }

    public function storeAddress(AddressRequest $request)
    {
        $user = Auth::user();

        if (!$user) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Please login first'], 401);
            }
            return redirect()->route('login');
        }

        $customer = $this->customerRepository->findByUserId($user->id);

        if (!$customer) {
            $customer = $this->customerRepository->storeByUser((object)$user);
        }

        $addressCount = $this->addressRepository->countByCustomerId($customer->id);
        if ($addressCount >= 3) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => "عذراً، لا يمكنك إضافة أكثر من 3 عناوين"]);
            }
            return back()->with('error', 'عذراً، لا يمكنك إضافة أكثر من 3 عناوين');
        }

        $this->addressRepository->storeByWebsiteRequest($request, $customer->id);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'تمت إضافة العنوان بنجاح!']);
        }

        return back()->with('success', 'تمت إضافة العنوان بنجاح');
    }

    public function getAddresses(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Please login first'], 401);
        }

        $customer = $this->customerRepository->findByUserId($user->id);

        if (!$customer) {
            return response()->json(['success' => true, 'addresses' => []]);
        }

        $addresses = $this->addressRepository->getByCustomerId($customer->id);

        return response()->json(['success' => true, 'addresses' => $addresses]);
    }

    public function updateAddress(AddressRequest $request, $id)
    {
        $user = Auth::user();

        if (!$user) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Please login first'], 401);
            }
            return redirect()->route('login');
        }

        $customer = $this->customerRepository->findByUserId($user->id);

        if (!$customer) {
            return back()->with('error', 'العميل غير موجود');
        }

        // Check for legacy data
        $customerId = $customer->id;
        $legacyAddress = $this->addressRepository->existsByIdAndCustomerId($id, $user->id);
        if ($legacyAddress) {
            $customerId = $user->id;
        }

        $address = $this->addressRepository->findByIdAndCustomerId($id, $customerId);

        if (!$address) {
            return back()->with('error', 'العنوان غير موجود');
        }

        $this->addressRepository->updateByRequest($address, $request);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'تم تحديث العنوان بنجاح!']);
        }

        return back()->with('success', 'تم تحديث العنوان بنجاح');
    }

    public function destroyAddress(\Illuminate\Http\Request $request, $id)
    {
        $user = Auth::user();

        if (!$user) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Please login first'], 401);
            }
            return redirect()->route('login');
        }

        $customer = $this->customerRepository->findByUserId($user->id);

        if (!$customer) {
            return back()->with('error', 'العميل غير موجود');
        }

        // Check for legacy data
        $customerId = $customer->id;
        $legacyAddress = $this->addressRepository->existsByIdAndCustomerId($id, $user->id);
        if ($legacyAddress) {
            $customerId = $user->id;
        }

        $address = $this->addressRepository->findByIdAndCustomerId($id, $customerId);

        if (!$address) {
            return back()->with('error', 'العنوان غير موجود');
        }

        $this->addressRepository->delete($id);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'تم حذف العنوان بنجاح!']);
        }

        return back()->with('success', 'تم حذف العنوان بنجاح');
    }

    public function setDefaultAddress(\Illuminate\Http\Request $request, $id)
    {
        $user = Auth::user();

        if (!$user) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Please login first'], 401);
            }
            return redirect()->route('login');
        }

        $customer = $this->customerRepository->findByUserId($user->id);

        if (!$customer) {
            return back()->with('error', 'العميل غير موجود');
        }

        // Check for legacy data
        $customerId = $customer->id;
        $legacyAddress = $this->addressRepository->existsByIdAndCustomerId($id, $user->id);
        if ($legacyAddress) {
            $customerId = $user->id;
        }

        $address = $this->addressRepository->findByIdAndCustomerId($id, $customerId);

        if (!$address) {
            return back()->with('error', 'العنوان غير موجود');
        }

        $this->addressRepository->setDefault($id, $customerId);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'تم تعيين العنوان الافتراضي بنجاح!']);
        }

        return back()->with('success', 'تم تعيين العنوان الافتراضي بنجاح');
    }
}
