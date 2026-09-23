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
        return view('offers.create', compact('vendors'));
    }

    public function store(OfferRequest $request)
    {
        $this->offerRepo->storeByRequest($request);
        return redirect()->route('offer.index')->with('success', 'تمت إضافة العرض بنجاح');
    }

    public function edit(Offer $offer)
    {
        $vendors = Vendor::where('is_active', true)->get();
        return view('offers.edit', compact('offer', 'vendors'));
    }

    public function update(OfferRequest $request, Offer $offer)
    {
        $this->offerRepo->updateByRequest($request, $offer);
        return redirect()->route('offer.index')->with('success', 'تم تحديث العرض بنجاح');
    }

    public function destroy(Offer $offer)
    {
        $this->offerRepo->deleteByRequest($offer);
        return back()->with('success', 'تم حذف العرض بنجاح');
    }

    public function toggleActivationStatus(Offer $offer)
    {
        $this->offerRepo->toggleActivation($offer);
        return back()->with('success', 'تم تحديث حالة العرض بنجاح');
    }
}