<?php

namespace App\Http\Controllers\Web\Banners;

use App\Models\Banner;
use App\Http\Controllers\Controller;
use App\Http\Requests\BannerRequest;
use App\Repositories\BannerRepository;

class BannerController extends Controller
{
    public $bannerRepo;
    public function __construct(BannerRepository $bannerRepository)
    {
        $this->bannerRepo = $bannerRepository;
    }

    public function index()
    {
        abort_unless(auth()->user()?->hasRole('root'), 403);
        $banners = $this->bannerRepo->getAllByStatus(true);
        return view('banners.index', compact('banners'));
    }

    public function getPromotional()
    {
        $banners = $this->bannerRepo->getAllByStatus(false);
        return view('banners.index', compact('banners'));
    }

    public function store(BannerRequest $request)
    {
        $this->bannerRepo->storeByRequest($request);

        return redirect()->route('banner.promotional')->with('success', 'A new banner added successfully');
    }

    public function edit(Banner $banner)
    {
        abort_if($this->vendorScopeId() !== null && $banner->vendor_id !== $this->vendorScopeId(), 403);
        return view('banners.edit', [
            'banner' => $banner
        ]);
    }

    public function update(BannerRequest $request, Banner $banner)
    {
        abort_if($this->vendorScopeId() !== null && $banner->vendor_id !== $this->vendorScopeId(), 403);
        $this->bannerRepo->updateByRequest($request, $banner);

        return redirect()->route('banner.promotional')->with('success', 'Banner updated successfully');
    }

    public function destroy(Banner $banner)
    {
        abort_if($this->vendorScopeId() !== null && $banner->vendor_id !== $this->vendorScopeId(), 403);
        $this->bannerRepo->deleteByRequest($banner);

        return back()->with('success', 'Banner deleted successfully');
    }

    public function toggleActivationStatus(Banner $banner)
    {
        abort_if($this->vendorScopeId() !== null && $banner->vendor_id !== $this->vendorScopeId(), 403);
        $this->bannerRepo->toggleActivation($banner);
        return back()->with('success', 'Banner status updated successfully');
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
