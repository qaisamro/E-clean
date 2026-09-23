<?php

namespace App\Http\Controllers\Web\Variants;

use App\Models\Product;
use App\Models\Variant;
use App\Http\Controllers\Controller;
use App\Http\Requests\VariantRequest;
use App\Repositories\VariantRepository;

class VariantController extends Controller
{
    private $variantRepo;
    public function __construct(VariantRepository $variantRepository)
    {
        $this->variantRepo = $variantRepository;
    }

    public function index()
    {
        $variants = $this->variantRepo->getAll();
        return view('variants.index', compact('variants'));
    }

    public function store(VariantRequest $request)
    {
        $this->variantRepo->storeByRequest($request);
        return back()->with('success', 'تمت إضافة المتغير بنجاح');
    }

    public function update(VariantRequest $request, Variant $variant)
    {
        $this->variantRepo->updateByRequest($request, $variant);
        return back()->with('success', 'تم تحديث المتغير بنجاح');
    }

    public function delete(Variant $variant)
    {
        $variant->services()->detach();
        $variant->products()->delete();
        $variant->delete();
        return redirect()->route('variant.index')->with('success', 'تم حذف المتغير بنجاح');
    }

    public function productsVariant(Variant $variant)
    {
        $products = Product::where('variant_id', $variant->id)->orderBy('order', 'asc')->get();
        return view('variants.products', compact('variant', 'products'));
    }
}
