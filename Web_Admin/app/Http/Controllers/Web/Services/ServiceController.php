<?php

namespace App\Http\Controllers\Web\Services;

use App\Models\Service;
use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Repositories\ServiceRepository;

class ServiceController extends Controller
{
    public $serviceRepo;
    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepo = $serviceRepository;
    }

    public function index()
    {
        $this->authorizeCatalogAdmin();
        $services = $this->serviceRepo->getAll(true);
        return view('services.index', compact('services'));
    }

    public function create()
    {
        $this->authorizeCatalogAdmin();
        $vendors = collect();

        return view('services.create', compact('vendors'));
    }

    public function store(ServiceRequest $request)
    {
        $this->authorizeCatalogAdmin();
        $this->serviceRepo->storeByRequest($request);

        return redirect()->route('service.index')->with('success', 'تمت إضافة الخدمة بنجاح');
    }

    public function edit(Service $service)
    {
        $this->authorizeCatalogAdmin();
        abort_unless($service->vendor_id === null, 404);
        $vendors = collect();
        return view('services.edit', compact('service', 'vendors'));
    }

    public function update(ServiceRequest $request, Service $service)
    {
        $this->authorizeCatalogAdmin();
        abort_unless($service->vendor_id === null, 404);
        $this->serviceRepo->updateByRequest($request, $service);
        return redirect()->route('service.index')->with('success', 'تم تحديث الخدمة بنجاح');
    }

    public function toggleActivationStatus(Service $service)
    {
        $this->authorizeCatalogAdmin();
        abort_unless($service->vendor_id === null, 404);
        $this->serviceRepo->updateStatusById($service);

        return back();
    }


    public function delete(Service $service)
    {
        $this->authorizeCatalogAdmin();
        abort_unless($service->vendor_id === null, 404);

        try {
            return \Illuminate\Support\Facades\DB::transaction(function () use ($service) {
                // The products FK also includes soft-deleted products. Count
                // them under a row lock so a concurrent product cannot turn a
                // safe validation response into an integrity-error page.
                $linkedProducts = \App\Models\Product::withTrashed()
                    ->where('service_id', $service->id)
                    ->lockForUpdate()
                    ->count();

                if ($linkedProducts > 0) {
                    return back()->with(
                        'error',
                        'لا يمكن حذف الخدمة لوجود منتجات مرتبطة بها، بما فيها المنتجات المؤرشفة ('
                        . $linkedProducts . '). عطّلها من زر الحالة بدلاً من الحذف.'
                    );
                }

                $service->variants()->detach();
                $service->additionals()->detach();
                $service->delete();

                return redirect()->route('service.index')->with('success', 'تم حذف الخدمة بنجاح');
            });
        } catch (\Illuminate\Database\QueryException $exception) {
            report($exception);

            return back()->with(
                'error',
                'تعذر حذف الخدمة لوجود بيانات مرتبطة بها. عطّل الخدمة أو أزل الارتباطات أولاً.'
            );
        }
    }

    protected function authorizeCatalogAdmin(): void
    {
        $user = auth()->user();
        abort_unless($user && (
            $user->hasRole('root')
            || $user->hasRole('admin')
            || ($user->hasRole('Admin') && $user->can('root'))
        ), 403);
    }
}
