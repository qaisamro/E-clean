<?php


namespace App\Repositories;

use App\Http\Requests\AddressRequest;
use App\Models\Address;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
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

    public function storeByRequest(AddressRequest $request): ?Address
    {
        $customerId = (int) auth()->user()->customer->id;
        return DB::transaction(function () use ($request, $customerId) {
            $this->lockCustomer($customerId);
            if ($request->boolean('is_default')) {
                $this->clearDefault($customerId);
            }
            return $this->create([
                'customer_id' => $customerId,
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
                'is_default' => $request->boolean('is_default'),
            ]);
        }, 3);
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
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address_line' => $request->address_line,
            'delivery_note' => $request->delivery_note,
        ]);
    }

    public function updateByRequest(Address $address, AddressRequest $request): Address
    {
        $customerId = (int) auth()->user()->customer->id;
        return DB::transaction(function () use ($address, $request, $customerId) {
            $this->lockCustomer($customerId);
            $owned = $this->query()->where('customer_id', $customerId)->findOrFail($address->id);
            // Only supplied fields change: a default-only update must preserve
            // the address text, coordinates and existing order references.
            $fields = $request->only([
                'address_name', 'road_no', 'house_no', 'house_name', 'flat_no',
                'block', 'area', 'sub_district_id', 'district_id', 'address_line',
                'address_line2', 'delivery_note', 'post_code', 'latitude', 'longitude',
            ]);
            if ($request->has('is_default')) {
                if ($request->boolean('is_default')) {
                    $this->clearDefault($customerId);
                    $owned->refresh();
                }
                $fields['is_default'] = $request->boolean('is_default');
            }
            $owned->update($fields);
            return $owned;
        }, 3);
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
        return DB::transaction(function () use ($addressId, $customerId) {
            $this->lockCustomer($customerId);
            $address = $this->query()->where('customer_id', $customerId)->findOrFail($addressId);
            $this->clearDefault($customerId);
            $address->refresh();
            $address->update(['is_default' => true]);
            return $address;
        }, 3);
    }

    public function deleteOwned(int $addressId, int $customerId): void
    {
        DB::transaction(function () use ($addressId, $customerId) {
            $this->lockCustomer($customerId);
            $address = $this->query()->where('customer_id', $customerId)->findOrFail($addressId);
            // Preserve the existing soft-delete policy. Choosing a replacement
            // is explicit; deleting an address never rewrites an order.
            $address->delete();
        }, 3);
    }

    private function lockCustomer(int $customerId): void
    {
        // Lock the stable parent row, even when the customer has no addresses.
        // Web and API default changes take the same lock before writing.
        Customer::query()->whereKey($customerId)->lockForUpdate()->firstOrFail();
    }

    private function clearDefault(int $customerId): void
    {
        $this->query()->where('customer_id', $customerId)
            ->where('is_default', true)->update(['is_default' => false]);
    }
}
