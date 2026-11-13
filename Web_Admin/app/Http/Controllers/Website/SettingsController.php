<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\Website\ProfileUpdateRequest;
use App\Models\Customer;
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
        $order = $this->findOwnedOrder($orderId, Auth::user());
        abort_unless($order, 404, 'Order not found');

        return view('website.pages.order-details', compact('order'));
    }

    public function payOrder($orderId)
    {
        $user = Auth::user();
        $order = $this->findOwnedOrder($orderId, $user);
        abort_unless($order, 404, 'Order not found');

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
                'product_data' => "Order {$order->order_code}",
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

        $orders = $this->ownedOrdersQuery($user)
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

        $allOrders = $this->ownedOrdersQuery($user)->with('address')->get();
        $inProgressStatuses = [
            config('enums.order_status.pending'),
            config('enums.order_status.order_confirmed'),
            config('enums.order_status.picked_order'),
            config('enums.order_status.processing'),
            config('enums.order_status.on_the_way'),
        ];
        $orderStats = [
            'total' => $allOrders->count(),
            'in_progress' => $allOrders->whereIn('order_status', $inProgressStatuses)->count(),
            'completed' => $allOrders->where('order_status', config('enums.order_status.delivered'))->count(),
            'ready_for_pickup' => $allOrders->where('order_status', config('enums.order_status.ready_for_pickup'))->count(),
            'on_the_way' => $allOrders->where('order_status', config('enums.order_status.on_the_way'))->count(),
            'cancelled' => $allOrders->where('order_status', config('enums.order_status.cancelled'))->count(),
        ];

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

        $orders = $this->ownedOrdersQuery($user)->with('address')->latest()->get();
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

        $address = $this->findOwnedAddress($id, $user, $customer);

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

        $address = $this->findOwnedAddress($id, $user, $customer);

        if (!$address) {
            return back()->with('error', 'العنوان غير موجود');
        }

        $address->delete();

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

        $address = $this->findOwnedAddress($id, $user, $customer);

        if (!$address) {
            return back()->with('error', 'العنوان غير موجود');
        }

        $this->addressRepository->setDefault($address->id, (int) $address->customer_id);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'تم تعيين العنوان الافتراضي بنجاح!']);
        }

        return back()->with('success', 'تم تعيين العنوان الافتراضي بنجاح');
    }

    private function ownedOrdersQuery($user)
    {
        return $this->orderRepository->query()
            ->where(function ($query) use ($user) {
                $query->whereHas('customer', function ($customerQuery) use ($user) {
                    $customerQuery->where('user_id', $user->id);
                })->orWhere(function ($legacyQuery) use ($user) {
                    $legacyQuery->where('customer_id', $user->id)
                        ->whereDoesntHave('customer');
                });
            });
    }

    private function findOwnedOrder($orderId, $user)
    {
        $order = $this->ownedOrdersQuery($user)->whereKey($orderId)->first();

        return $order
            ? $this->orderRepository->getOrderDetailsById($order->id)
            : null;
    }

    private function findOwnedAddress($addressId, $user, $customer)
    {
        $address = $this->addressRepository
            ->findByIdAndCustomerId((int) $addressId, (int) $customer->id);

        if ($address) {
            return $address;
        }

        // Legacy rows sometimes stored the user ID in customer_id. Only use
        // that fallback when it cannot resolve to another customer's record.
        $legacyCustomer = Customer::query()->find($user->id);
        if ($legacyCustomer && (int) $legacyCustomer->user_id !== (int) $user->id) {
            return null;
        }

        return $this->addressRepository
            ->findByIdAndCustomerId((int) $addressId, (int) $user->id);
    }
}
