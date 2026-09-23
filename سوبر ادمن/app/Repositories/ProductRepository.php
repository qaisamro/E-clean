<?php

namespace App\Repositories;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use App\Traits\VendorScoped;

class ProductRepository extends Repository
{
    use VendorScoped;
    private $path = 'images/products/';
    public function model()
    {
        return Product::class;
    }

    public function getAllOrFindBySearch($isLatest = false, $paginate = false, $perPage = 10)
    {
        $products = $this->model()::query()
            ->with('service')
            ->whereNull('product_id');
        $products = $this->scopeVendor($products);

        $searchKey = request('search');

        if ($searchKey) {
            $products->where(function ($query) use ($searchKey) {
                $query->where('name', 'like', "%{$searchKey}%")
                    ->orWhereHas('service', function ($service) use ($searchKey) {
                        $service->where('name', 'like', "%{$searchKey}%");
                    })
                    ->orWhere('price', 'like', "%{$searchKey}%")
                    ->orWhere('discount_price', 'like', "%{$searchKey}%");
            });
        }

        if ($isLatest) {
            $products->latest('id');
        }

        if ($paginate) {
            return $products->paginate($perPage);
        }

        return $products->get();
    }


    public function getProductsByServiceIdAndVariantId($serviceId = null, $variantId = null, $searchKey = null)
    {
        return $this->getProductsByRequest($serviceId, $variantId, null, $searchKey);
    }

    public function getProductsByRequest($serviceId = null, $variantId = null, $vendorId = null, $searchKey = null)
    {
        $products = $this->model()::query()->whereNull('product_id')->with('vendor', 'thumbnail');

        if ($vendorId) {
            $products = $products->where('vendor_id', $vendorId);
        }

        if ($serviceId) {
            $products = $products->where('service_id', $serviceId);
        }

        if ($variantId) {
            $products = $products->where('variant_id', $variantId);
        }

        if ($searchKey) {
            $products = $products->where(function ($query) use ($searchKey) {
                $query->where('name', 'like', "%{$searchKey}%")
                    ->orWhere('price', 'like', "%{$searchKey}%");
            });
        }

        return $products->orderBy('order', 'asc')->isActive()->get();
    }

    public function storeByRequest(ProductRequest $request): Product
    {
        $thumbnail = (new MediaRepository())->storeByRequest(
            $request->image,
            $this->path,
            'this image for product thumbnail',
            'image'
        );
        return Product::create([
            'name' => $request->name,
            'name_bn' => $request->name_bn,
            'slug' => $request->slug,
            'thumbnail_id' => $thumbnail->id,
            'service_id' => $request->service_id,
            'variant_id' => $request->variant_id,
            'discount_price' => $request->discount_price,
            'price' =>  $request->price,
            'description' => $request->description,
            'is_active' => $request->has('active') ? $request->active : 1,
            'vendor_id' => $this->currentVendorId(),
        ]);
    }

    public function updateByRequest(ProductRequest $request, Product $product): Product
    {
        if ($request->hasFile('image')) {
            (new MediaRepository())->updateByRequest(
                $request->image,
                $this->path,
                'image',
                $product->thumbnail
            );
        }
        $product->update([
            'name' => $request->name,
            'name_bn' => $request->name_bn,
            'slug' => $request->slug,
            'service_id' => $request->service_id,
            'variant_id' => $request->variant_id,
            'discount_price' => $request->discount_price,
            'price' =>  $request->price,
            'description' => $request->description,
            'is_active' => $request->has('active')
                ? $request->active
                : (int) (bool) $product->is_active,
        ]);
        return $product;
    }

    public function updateStatusById(Product $product): Product
    {
        $product->update([
            'is_active' => !$product->is_active
        ]);

        return $product;
    }

    public function deleteProductById(Product $product): Product
    {
        foreach ($product->subProducts as $subProduct) {
            $subProduct->forceDelete();
        }

        $thumbnail = $product->thumbnail;

        $product->forceDelete();

        if ($thumbnail) {
            if (Storage::exists($thumbnail->src)) {
                Storage::delete($thumbnail->src);
            }
            $thumbnail->delete();
        }

        return $product;
    }

    public function findById($id): Product
    {
        return $this->model()->find($id);
    }
    public function getByRequest($request)
    {
        $variantId = $request->variant_id;
        $serviceId = $request->service_id;
        $searchKey = $request->search;


        $products = $this->model()::query()
            ->when($serviceId, function ($query) use ($serviceId) {
                $query->where('service_id', $serviceId);
            })
            ->when($variantId, function ($query) use ($variantId) {
                $query->where('variant_id', $variantId);
            })
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where('name', 'like', "%{$searchKey}%")
                    ->orWhere('price', 'like', "%{$searchKey}%");
            });

        return $products->orderBy('order', 'asc')->isActive()->get();
    }
}
