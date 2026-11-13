<?php

namespace App\Http\Controllers\API\Address;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Repositories\AddressRepository;

class AddressController extends Controller
{
    private $addressRepo;
    public function __construct(AddressRepository $addressRepository)
    {
        $this->addressRepo = $addressRepository;
    }

    public function index()
    {
        return $this->json('address list', [
            'addresses' => AddressResource::collection($this->addressRepo->getAll())
        ]);
    }

    public function store(AddressRequest $request)
    {
        if (!$this->addressRepo->storeByRequest($request)) {
            return $this->json('sorry, you can\'t add more address');
        }

        return $this->json('Address is added successfully', [
            'addresses' => AddressResource::collection($this->addressRepo->getAll())
        ]);
    }

    public function update(AddressRequest $request, Address $address)
    {
        $customerId = $request->user()->customer?->id;
        abort_unless($customerId, 403);

        $address = Address::query()
            ->where('customer_id', $customerId)
            ->findOrFail($address->id);

        $this->addressRepo->updateByRequest($address, $request);

        return $this->json('Address is updated successfully', [
            'addresses' => AddressResource::collection($this->addressRepo->getAll())
        ]);
    }

    public function delete(Address $address)
    {
        $customerId = auth()->user()->customer?->id;
        abort_unless($customerId, 403);

        $address = Address::query()
            ->where('customer_id', $customerId)
            ->findOrFail($address->id);

        $this->addressRepo->deleteOwned($address->id, (int) $customerId);
        return $this->json('Address delete successfully',[
            'addresses' => AddressResource::collection($this->addressRepo->getAll())
        ]);
    }
}
