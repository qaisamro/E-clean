<?php

namespace App\Http\Controllers\Web\Products;

use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Repositories\ProductRepository;
use App\Repositories\ServiceRepository;
use Illuminate\Http\Request;

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
        $services = (new ServiceRepository())->getAll();
        return view('products.create', compact('services'));
    }

    public function store(ProductRequest $request)
    {
        if (($request->old_price != '') && ($request->old_price < $request->price)) {
            return back()->with('error', 'يجب أن يكون سعر الخصم أقل من سعر المنتج');
        }
        $this->productRepo->storeByRequest($request);

        return redirect()->route('product.index')->with('success', 'تمت إضافة المنتج بنجاح');
    }

    public function edit(Product $product)
    {
        $variants = $product->service->variants;
        $services = (new ServiceRepository())->getAll();
        return view('products.edit', compact('product', 'services', 'variants'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        if (($request->old_price != '') && ($request->old_price < $request->price)) {
            return back()->with('error', 'يجب أن يكون سعر المنتج أكبر من سعر الخصم');
        }
        $this->productRepo->updateByRequest($request, $product);
        return redirect()->route('product.index')->with('success', 'تم تحديث المنتج بنجاح');
    }

    public function toggleActivationStatus(Product $product)
    {
        $this->productRepo->updateStatusById($product);

        return back()->with('success', 'تم تحديث حالة المنتج');
    }

    public function orderUpdate(Request $request, Product $product)
    {

        $product->update([
            'order' => $request->position ?? 0
        ]);

        return back();
    }

    public function delete(Product $product)
    {
        $this->productRepo->deleteProductById($product);
        return redirect()->route('product.index')->with('success', 'تم حذف المنتج بنجاح');
    }
}
