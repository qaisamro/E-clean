<?php

namespace App\Repositories;

use App\Http\Requests\OfferRequest;
use App\Models\Offer;
use App\Traits\VendorScoped;
use Illuminate\Support\Facades\Storage;

class OfferRepository extends Repository
{
    use VendorScoped;
    private $path = 'images/offers/';

    public function model()
    {
        return Offer::class;
    }

    public function getAllOrFindBySearch($paginate = false, $perPage = 15)
    {
        $offers = $this->model()::query()->with(['vendor', 'thumbnail']);
        $offers = $this->scopeVendor($offers);

        $searchKey = request('search');

        if ($searchKey) {
            $offers->where(function ($query) use ($searchKey) {
                $query->where('title', 'like', "%{$searchKey}%")
                    ->orWhere('description', 'like', "%{$searchKey}%")
                    ->orWhereHas('vendor', function ($vendor) use ($searchKey) {
                        $vendor->where('name', 'like', "%{$searchKey}%");
                    });
            });
        }

        if (auth()->check() && auth()->user()->hasRole('root')) {
            $offers->latest('id');
        } else {
            $offers->latest('id');
        }

        if ($paginate) {
            return $offers->paginate($perPage);
        }

        return $offers->get();
    }

    public function getActive()
    {
        return $this->model()::query()
            ->with(['vendor', 'thumbnail'])
            ->whereHas('vendor', function ($vendor) {
                $vendor->where('is_active', true);
            })
            ->isActive()
            ->latest('id')
            ->get();
    }

    public function storeByRequest(OfferRequest $request): Offer
    {
        $thumbnailId = null;
        if ($request->hasFile('image')) {
            $thumbnail = (new MediaRepository())->storeByRequest(
                $request->image,
                $this->path,
                'this image for offers',
                'image'
            );
            $thumbnailId = $thumbnail->id;
        }

        return $this->model()::create([
            'title' => $request->title,
            'description' => $request->description,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'thumbnail_id' => $thumbnailId,
            'is_active' => $request->active ?? 0,
            'vendor_id' => $this->currentVendorId() ?? $request->vendor_id,
        ]);
    }

    public function updateByRequest(OfferRequest $request, Offer $offer): Offer
    {
        if ($request->hasFile('image')) {
            $thumbnail = (new MediaRepository())->storeByRequest(
                $request->image,
                $this->path,
                'this image for offers',
                'image'
            );
            $oldThumbnail = $offer->thumbnail;

            $offer->update([
                'thumbnail_id' => $thumbnail->id,
            ]);

            if ($oldThumbnail && Storage::exists($oldThumbnail->src)) {
                Storage::delete($oldThumbnail->src);
            }
            $oldThumbnail?->delete();
        }

        $offer->update([
            'title' => $request->title,
            'description' => $request->description,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'is_active' => $request->has('active') ? $request->active : $offer->is_active,
        ]);

        return $offer;
    }

    public function deleteByRequest(Offer $offer)
    {
        $thumbnail = $offer->thumbnail;
        if ($thumbnail && Storage::exists($thumbnail->src)) {
            Storage::delete($thumbnail->src);
        }

        $offer->delete();
        $thumbnail?->delete();

        return;
    }

    public function toggleActivation(Offer $offer): Offer
    {
        $offer->update([
            'is_active' => !$offer->is_active
        ]);
        return $offer;
    }
}