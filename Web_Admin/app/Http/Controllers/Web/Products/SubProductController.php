<?php

namespace App\Http\Controllers\Web\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Repositories\MediaRepository;
use App\Repositories\ProductRepository;

class SubProductController extends Controller
{
    public function __construct(
        private ProductRepository $productRepo
    ) {
    }

    public function index(Product $product)
    {
        return view('sub-product.index', compact('product'));
    }

    public function create(Product $product)
    {
        return redirect()->route('product.subproduct.index', $product->id);
    }

    public function store(ProductRequest $request, Product $product)
    {

        $this->productRepo->create([
            'name' => $request->name,
            'name_bn' => $request->name_bn,
            'slug' => $request->slug,
            'service_id' => $request->service_id,
            'variant_id' => $request->variant_id,
            'discount_price' => $request->discount_price,
            'price' =>  $request->price,
            'description' => $request->description,
            'is_active' => 1,
            'product_id' => $product->id
        ]);
        return redirect()->route('product.subproduct.index', $product->id)->with('success', 'تم الإنشاء بنجاح');
    }

    public function edit(Product $product)
    {
        $parentId = $product->product_id ?? $product->id;
        return redirect()->route('product.subproduct.index', $parentId);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $this->productRepo->updateByRequest($request, $product);
        return redirect()->route('product.subproduct.index', $product->product_id)->with('success', 'تم التحديث بنجاح');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'تم حذف المنتج الفرعي بنجاح');
    }
}
