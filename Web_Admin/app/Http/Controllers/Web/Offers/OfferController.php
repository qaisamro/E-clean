<?php

namespace App\Http\Controllers\Web\Offers;

use App\Models\Offer;
use App\Models\Vendor;
use App\Http\Controllers\Controller;
use App\Http\Requests\OfferRequest;
use App\Repositories\OfferRepository;

class OfferController extends Controller
{
    public $offerRepo;

    public function __construct(OfferRepository $offerRepository)
    {
        $this->offerRepo = $offerRepository;
    }

    public function index()
    {
        $offers = $this->offerRepo->getAllOrFindBySearch();
        return view('offers.index', compact('offers'));
    }

    public function create()
    {
        $vendors = Vendor::where('is_active', true)->get();
        $products = $this->offerRepo->getAvailableProducts();
        return view('offers.create', compact('vendors', 'products'));
    }

    public function store(OfferRequest $request)
    {
        $this->offerRepo->storeByRequest($request);
        return redirect()->route('offer.index')->with('success', 'تمت إضافة العرض بنجاح');
    }

    protected function assertOfferScope(Offer $offer): void
    {
        if (\App\Support\VendorAccount::isStoreStaff(auth()->user())) {
            $vendorId = \App\Support\VendorAccount::idFor(auth()->user());
            abort_unless($vendorId && (int) $offer->vendor_id === (int) $vendorId, 404);
        }
    }

    public function edit(Offer $offer)
    {
        $this->assertOfferScope($offer);
        $vendors = Vendor::where('is_active', true)->get();
        $products = $this->offerRepo->getAvailableProducts();
        return view('offers.edit', compact('offer', 'vendors', 'products'));
    }

    public function update(OfferRequest $request, Offer $offer)
    {
        $this->assertOfferScope($offer);
        $this->offerRepo->updateByRequest($request, $offer);
        return redirect()->route('offer.index')->with('success', 'تم تحديث العرض بنجاح');
    }

    public function destroy(Offer $offer)
    {
        $this->assertOfferScope($offer);
        $this->offerRepo->deleteByRequest($offer);
        return back()->with('success', 'تم حذف العرض بنجاح');
    }

    public function toggleActivationStatus(Offer $offer)
    {
        $this->assertOfferScope($offer);
        $this->offerRepo->toggleActivation($offer);
        return back()->with('success', 'تم تحديث حالة العرض بنجاح');
    }
}