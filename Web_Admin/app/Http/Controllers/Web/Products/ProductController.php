<?php

namespace App\Http\Controllers\Web\Products;

use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Repositories\ProductRepository;
use App\Repositories\ServiceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    private $productRepo;
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepo = $productRepository;
    }

    public function index()
    {
        $products = $this->productRepo->getAllOrFindBySearch(true);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $services = \App\Models\Service::query()->global()->isActive()->orderBy('name')->get();
        return view('products.create', compact('services'));
    }

    public function store(ProductRequest $request)
    {
        if (\App\Support\VendorAccount::isStoreStaff(auth()->user())) {
            $service = \App\Models\Service::findOrFail($request->service_id);
            $vendorId = \App\Support\VendorAccount::idFor(auth()->user());
            abort_unless($vendorId && $service->vendor_id === null && $service->is_active, 404);
        }
        if (($request->old_price != '') && ($request->old_price < $request->price)) {
            return back()->with('error', 'يجب أن يكون سعر الخصم أقل من سعر المنتج');
        }
        $this->productRepo->storeByRequest($request);

        return redirect()->route('product.index')->with('success', 'تمت إضافة المنتج بنجاح');
    }

    public function edit(Product $product)
    {
        $this->assertVendorOwns($product);
        $services = \App\Models\Service::query()->global()->isActive()->orderBy('name')->get();
        return view('products.edit', compact('product', 'services'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $this->assertVendorOwns($product);
        if (\App\Support\VendorAccount::isStoreStaff(auth()->user())) {
            $service = \App\Models\Service::findOrFail($request->service_id);
            $vendorId = \App\Support\VendorAccount::idFor(auth()->user());
            abort_unless($vendorId && $service->vendor_id === null && $service->is_active, 404);
        }
        if (($request->old_price != '') && ($request->old_price < $request->price)) {
            return back()->with('error', 'يجب أن يكون سعر المنتج أكبر من سعر الخصم');
        }
        $this->productRepo->updateByRequest($request, $product);
        return redirect()->route('product.index')->with('success', 'تم تحديث المنتج بنجاح');
    }

    public function toggleActivationStatus(Product $product)
    {
        $this->assertVendorOwns($product);
        $this->productRepo->updateStatusById($product);

        return back()->with('success', 'تم تحديث حالة المنتج');
    }

    public function orderUpdate(Request $request, Product $product)
    {
        $this->assertVendorOwns($product);

        $product->update([
            'order' => $request->position ?? 0
        ]);

        return back();
    }

    public function delete(Product $product)
    {
        $this->assertVendorOwns($product);

        // Products that are tied to past orders can never be hard-deleted:
        // order_products.product_id is a restricting FK, and a raw delete
        // crashes with a 500. Offer deactivation instead of a broken page.
        $hasOrders = DB::table('order_products')
            ->where('product_id', $product->id)
            ->exists();

        if ($hasOrders) {
            return back()->with('error', 'هذا المنتج مرتبط بطلبات سابقة، لا يمكن حذفه. يمكنك تعطيله بدلًا من ذلك.');
        }

        $this->productRepo->deleteProductById($product);
        return redirect()->route('product.index')->with('success', 'تم حذف المنتج بنجاح');
    }

    protected function assertVendorOwns(Product $product): void
    {
        if (\App\Support\VendorAccount::isStoreStaff(auth()->user())) {
            $vendorId = \App\Support\VendorAccount::idFor(auth()->user());
            abort_unless($vendorId && (int) $product->vendor_id === (int) $vendorId, 404);
        }
    }
}
