<?php

namespace App\Http\Controllers\Web\Services;

use App\Models\Service;
use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Repositories\AdditionalRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\VariantRepository;

class ServiceController extends Controller
{
    public $serviceRepo;
    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepo = $serviceRepository;
    }

    public function index()
    {
        $services = $this->serviceRepo->query()
            ->with('vendor')
            ->when($this->vendorScopeId() !== null, fn ($query) => $query->where('vendor_id', $this->vendorScopeId()))
            ->latest('id')
            ->get();
        return view('services.index', compact('services'));
    }

    public function create()
    {
        $variants = (new VariantRepository())->getAll(true);

        return view('services.create', compact('variants'));
    }

    public function store(ServiceRequest $request)
    {
        $service = $this->serviceRepo->storeByRequest($request);
        if ($this->vendorScopeId() !== null) {
            $service->update(['vendor_id' => $this->vendorScopeId()]);
        }

        return redirect()->route('service.index')->with('success', 'A service added success');
    }

    public function edit(Service $service)
    {
        abort_if($this->vendorScopeId() !== null && $service->vendor_id !== $this->vendorScopeId(), 403);
        $variants = (new VariantRepository())->getAll(true);
        $additionals = (new AdditionalRepository())->getAllByActive();
        return view('services.edit', compact('service', 'variants', 'additionals'));
    }

    public function update(ServiceRequest $request, Service $service)
    {
        abort_if($this->vendorScopeId() !== null && $service->vendor_id !== $this->vendorScopeId(), 403);
        $this->serviceRepo->updateByRequest($request, $service);
        $service->additionals()->sync($request->additional);

        return redirect()->route('service.index')->with('success', 'Service updated successfully');
    }

    public function toggleActivationStatus(Service $service)
    {
        abort_if($this->vendorScopeId() !== null && $service->vendor_id !== $this->vendorScopeId(), 403);
        $this->serviceRepo->updateStatusById($service);

        return back();
    }

    public function getVariant(Service $service)
    {
        return response()->json($service->variants);
    }

    private function vendorScopeId(): ?int
    {
        $user = auth()->user();
        if (!$user || $user->hasRole('root')) {
            return null;
        }

        return $user->vendor?->id;
    }
}
