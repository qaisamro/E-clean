<?php

namespace App\Repositories;

use App\Repositories\MediaRepository;
use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use App\Traits\VendorScoped;

class ServiceRepository extends Repository
{
    use VendorScoped;
    private $path = 'images/services/';
    public function model()
    {
        return Service::class;
    }
    public function getActive($paginate = false, $perPage = 4, $search = null, $category = null, $sort = 'recommended')
    {
        $query = Service::query()
            ->where('services.is_active', 1);

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('services.name', 'like', "%{$search}%")
                    ->orWhere('services.description', 'like', "%{$search}%");
            });
        }

        // Apply category filter (removed - category column doesn't exist in database)
        // if ($category && $category !== 'all') {
        //     $query->where('services.category', $category);
        // }

        // Apply sorting
        switch ($sort) {
            case 'price_low':
                $query->orderByRaw("(SELECT COALESCE(MIN(price), 0) FROM products WHERE service_id = services.id AND product_id IS NULL AND is_active = 1) ASC");
                break;
            case 'price_high':
                $query->orderByRaw("(SELECT COALESCE(MIN(price), 0) FROM products WHERE service_id = services.id AND product_id IS NULL AND is_active = 1) DESC");
                break;
            case 'recommended':
            default:
                $query->orderBy('services.id', 'desc');
                break;
        }

        if ($paginate) {
            // For paginated results with complex joins, we need to handle differently
            if (in_array($sort, ['price_low', 'price_high', 'rating'])) {
                $query->distinct();
            }
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function getActiveWithLowestProduct()
    {
        return Service::query()
            ->where('is_active', 1)
            ->with('lowestProduct')
            ->get();
    }


    public function getAll($isLatest = false)
    {
        $category = $this->model()::query();
        $category = $this->scopeVendor($category);
        if ($isLatest) {
            $category->latest('id');
        }
        return $category->get();
    }

    public function getActiveServices()
    {
        return Service::isActive()->get();
    }

    public function storeByRequest(ServiceRequest $request)
    {
        $variantIds = $request->variant_ids;
        $thumbnail = (new MediaRepository())->storeByRequest(
            $request->image,
            $this->path,
            'this image for service thumbnail',
            'image'
        );

        $service = $this->model()::create([
            'name' => $request->name,
            'description' => $request->description,
            'name_bn' => $request->name_bn,
            'description_bn' => $request->description_bn,
            'thumbnail_id' => $thumbnail->id,
            'vendor_id' => $this->currentVendorId(),
        ]);

        $service->variants()->sync($variantIds);

        return $service;
    }

    public function updateByRequest(ServiceRequest $request, Service $service): Service
    {
        $variantIds = $request->variant_ids ? $request->variant_ids : $service->variants->pluck('id')->toArray();
        if ($request->hasFile('image')) {
            (new MediaRepository())->updateByRequest(
                $request->image,
                $this->path,
                'image',
                $service->thumbnail
            );
        }

        $service->update([
            'name' => $request->name,
            'description' => $request->description,
            'name_bn' => $request->name_bn,
            'description_bn' => $request->description_bn,
            'amount' => $request->amount,
        ]);

        $service->variants()->sync($variantIds);

        return $service;
    }

    public function updateStatusById(Service $service): Service
    {
        $service->update([
            'is_active' => !$service->is_active
        ]);

        return $service;
    }

    public function findOrFailById($serviceId): Service
    {
        $service = $this->model()::findOrFail($serviceId);

        return $service;
    }

    public function getCategories()
    {
        // Category column doesn't exist in the database, returning empty array
        return [];
    }
}
