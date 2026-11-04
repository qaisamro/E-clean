<?php


namespace App\Repositories;

use App\Http\Requests\AddressRequest;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;

class AddressRepository extends Repository
{
    public function model()
    {
        return Address::class;
    }

    public function getAll()
    {
        $customer = auth()->user()->customer;
        return $this->query()->where('customer_id', $customer->id)->get();
    }

    public function storeByRequest(AddressRequest $request): Address
    {
        return $this->create([
            'customer_id' => auth()->user()->customer->id,
            'address_name' => $request->address_name,
            'road_no' => $request->road_no,
            'house_no' => $request->house_no,
            'house_name' => $request->house_name,
            'flat_no' => $request->flat_no,
            'block' => $request->block,
            'area' => $request->area,
            'sub_district_id' => $request->sub_district_id,
            'district_id' => $request->district_id,
            'address_line' => $request->address_line,
            'address_line2' => $request->address_line2,
            'delivery_note' => $request->delivery_note,
            'post_code' => $request->post_code,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);
    }

    public function storeByPos(AddressRequest $request): Address
    {
        return $this->create([
            'customer_id' => $request->customer_id,
            'address_name' => $request->address_name,
            'road_no' => $request->road_no,
            'house_no' => $request->house_no,
            'house_name' => $request->house_name,
            'area' => $request->area,
        ]);
    }

    public function updateByRequest(Address $address, AddressRequest $request): Address
    {
        $address->update([
            'customer_id' => auth()->user()->customer->id,
            'address_name' => $request->address_name,
            'road_no' => $request->road_no,
            'house_no' => $request->house_no,
            'house_name' => $request->house_name,
            'flat_no' => $request->flat_no,
            'block' => $request->block,
            'area' => $request->area,
            'sub_district_id' => $request->sub_district_id,
            'district_id' => $request->district_id,
            'address_line' => $request->address_line,
            'address_line2' => $request->address_line2,
            'delivery_note' => $request->delivery_note,
            'post_code' => $request->post_code,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);
        return $address;
    }

    /**
     * Get addresses by customer ID
     */
    public function getByCustomerId(int $customerId)
    {
        return $this->query()->where('customer_id', $customerId)->get();
    }

    /**
     * Count addresses by customer ID
     */
    public function countByCustomerId(int $customerId): int
    {
        return $this->query()->where('customer_id', $customerId)->count();
    }

    /**
     * Store address for website (with customer_id from request)
     */
    public function storeByWebsiteRequest(AddressRequest $request, int $customerId): Address
    {
        return $this->create([
            'customer_id' => $customerId,
            'address_name' => $request->address_name,
            'road_no' => $request->road_no,
            'house_no' => $request->house_no,
            'flat_no' => $request->flat_no,
            'block' => $request->block,
            'area' => $request->area,
            'address_line' => $request->address_line,
            'delivery_note' => $request->delivery_note,
            'post_code' => $request->post_code,
            'phone' => $request->phone,
        ]);
    }

    /**
     * Find address by ID and customer ID
     */
    public function findByIdAndCustomerId(int $id, int $customerId): ?Address
    {
        return $this->query()->where('id', $id)->where('customer_id', $customerId)->first();
    }

    /**
     * Check if address exists by ID and customer ID
     */
    public function existsByIdAndCustomerId(int $id, int $customerId): bool
    {
        return $this->query()->where('id', $id)->where('customer_id', $customerId)->exists();
    }

    /**
     * Delete address by ID
     */
    public function deleteById(int $id): bool
    {
        return $this->query()->where('id', $id)->delete();
    }

    /**
     * Set default address
     */
    public function setDefault(int $addressId, int $customerId): Address
    {
        $this->query()->where('customer_id', $customerId)->update(['is_default' => false]);
        $address = $this->find($addressId);
        $address->update(['is_default' => true]);
        return $address;
    }
}
