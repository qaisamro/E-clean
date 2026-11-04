<?php


namespace App\Repositories;

use App\Http\Requests\BannerRequest;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

class BannerRepository extends Repository
{
    private $path = 'images/banners/';
    public function model()
    {
        return Banner::class;
    }

    public function getAllByStatus($status)
    {
        return $this->model()::where('is_banner', $status)
            ->where(function ($q) {
                $q->whereNull('banner_section')->orWhere('banner_section', '!=', 'wide');
            })
            ->get();
    }

    public function getByBannerStatus($status)
    {
        return $this->model()::where('is_banner', $status)
            ->where(function ($q) {
                $q->whereNull('banner_section')->orWhere('banner_section', '!=', 'wide');
            })
            ->isActive()->get();
    }

    public function getWide()
    {
        return $this->model()::where('banner_section', 'wide')->get();
    }

    public function getActiveWide()
    {
        return $this->model()::where('banner_section', 'wide')->isActive()->get();
    }

    public function storeByRequest(BannerRequest $request): Banner
    {
        $thumbnail = (new MediaRepository())->storeByRequest(
            $request->image,
            $this->path,
            'this image for website slider banner',
            'image'
        );

        $section = $request->banner_section ?? ($request->banner ? 'web' : 'promotional');

        return $this->model()::create([
            'title' => $request->title,
            'description' => $request->description,
            'thumbnail_id' => $thumbnail->id,
            'is_active' => $request->active ?? 0,
            'is_banner' => $section === 'web' ? 1 : 0,
            'banner_section' => $section,
        ]);
    }

    public function updateByRequest(BannerRequest $request, Banner $banner): Banner
    {
        if ($request->hasFile('image')) {
            $mediaRepo = new MediaRepository();
            if ($banner->thumbnail) {
                $mediaRepo->updateByRequest(
                    $request->image,
                    $this->path,
                    'image',
                    $banner->thumbnail
                );
            } else {
                $thumbnail = $mediaRepo->storeByRequest(
                    $request->image,
                    $this->path,
                    'this image for website slider banner',
                    'image'
                );
                $banner->update(['thumbnail_id' => $thumbnail->id]);
            }
        }

        $banner->update([
            'title' => $request->title,
            'description' => $request->description,
            'is_active' => $request->active ?? 0,
        ]);
        return $banner;
    }

    public function deleteByRequest(Banner $banner)
    {
        $thumbnail = $banner->thumbnail;
        if (Storage::exists($thumbnail->src)) {
            Storage::delete($thumbnail->src);
        }

        $banner->delete();
        $thumbnail->delete();
        return;
    }

    public function toggleActivation(Banner $banner): Banner
    {
        $banner->update([
            'is_active' => !$banner->is_active
        ]);
        return $banner;
    }
}
