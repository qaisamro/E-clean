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
        $services = $this->serviceRepo->getAll(true);
        return view('services.index', compact('services'));
    }

    public function create()
    {
        $variants = (new VariantRepository())->getAll(true);

        return view('services.create', compact('variants'));
    }

    public function store(ServiceRequest $request)
    {
        $this->serviceRepo->storeByRequest($request);

        return redirect()->route('service.index')->with('success', 'تمت إضافة الخدمة بنجاح');
    }

    public function edit(Service $service)
    {
        $variants = (new VariantRepository())->getAll(true);
        $additionals = (new AdditionalRepository())->getAllByActive();
        return view('services.edit', compact('service', 'variants', 'additionals'));
    }

    public function update(ServiceRequest $request, Service $service)
    {
        $this->serviceRepo->updateByRequest($request, $service);
        $service->additionals()->sync($request->additional);

        return redirect()->route('service.index')->with('success', 'تم تحديث الخدمة بنجاح');
    }

    public function toggleActivationStatus(Service $service)
    {
        $this->serviceRepo->updateStatusById($service);

        return back();
    }


    public function delete(Service $service)
    {
        // Products reference this service (FK) — refuse with a clear
        // message instead of a 500 integrity-violation page.
        $linkedProducts = \App\Models\Product::where('service_id', $service->id)->count();
        if ($linkedProducts > 0) {
            return back()->with('error', 'لا يمكن حذف الخدمة لوجود منتجات مرتبطة بها (' . $linkedProducts . '). عطّلها من زر الحالة بدلاً من الحذف.');
        }
        $service->variants()->detach();
        $service->delete();
        return redirect()->route("service.index")->with("success", "Service deleted successfully");
    }

    public function getVariant(Service $service)
    {
        // Store owners may only read their own store's services
        if (auth()->check() && auth()->user()->hasRole('vendor_admin') && auth()->user()->vendor) {
            $vendorId = auth()->user()->vendor->id;
            if ($service->vendor_id && (int) $service->vendor_id !== (int) $vendorId) {
                abort(403);
            }
        }
        $variants = $service->variants;
        // Fallback to the full catalog so product creation is never
        // blocked by a service that has no variants linked yet.
        if ($variants->isEmpty()) {
            $variants = \App\Models\Variant::orderBy('position', 'asc')->get();
        }
        return response()->json($variants);
    }
}
