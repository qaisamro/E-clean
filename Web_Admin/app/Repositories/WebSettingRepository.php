<?php

namespace App\Repositories;

use App\Models\WebSetting;
use App\Repositories\MediaRepository;
use Illuminate\Http\Request;

class WebSettingRepository extends Repository
{
    public function model()
    {
        return WebSetting::class;
    }

    public function index()
    {
        return $this->first() ?? new WebSetting();
    }

    public function updateOrCreate(Request $request, $webSetting = null, MediaRepository $mediaRepo = null)
    {
        $data = [
            'name' => $request->name,
            'title' => $request->title,
            'city' => $request->city,
            'address' => $request->address,
            'road' => $request->road,
            'area' => $request->area,
            'mobile' => $request->mobile,
            'currency' => $request->currency,
            'currency_name' => $request->currency_name,
            'tax_rate' => $request->tax_rate,
         ];

        // Handle logo
        if ($request->hasFile('logo')) {
            $thumbnail = $webSetting?->websiteLogo;
            if (!$thumbnail) {
                $thumbnail = (new MediaRepository())->storeByRequest(
                    $request->logo,
                    'images/webs/',
                    'website logo',
                    'image'
                );
            } else {
                $thumbnail = (new MediaRepository())->updateByRequest(
                    $request->logo,
                    'images/webs/',
                    'image',
                    $thumbnail
                );
            }
            $data['logo'] = $thumbnail->id;
        }

        // Handle favicon
        if ($request->hasFile('fav_icon')) {
            $thumbnail = $webSetting?->websiteFavicon;
            if (!$thumbnail) {
                $thumbnail = (new MediaRepository())->storeByRequest(
                    $request->fav_icon,
                    'images/webs/',
                    'website favicon',
                    'image'
                );
            } else {
                $thumbnail = (new MediaRepository())->updateByRequest(
                    $request->fav_icon,
                    'images/webs/',
                    'image',
                    $thumbnail
                );
            }
            $data['fav_icon'] = $thumbnail->id;
        }

        // Handle signature
        if ($request->hasFile('signature')) {
            $thumbnail = $webSetting?->signature;
            if (!$thumbnail) {
                $thumbnail = (new MediaRepository())->storeByRequest(
                    $request->signature,
                    'images/webs/',
                    'website signature',
                    'image'
                );
            } else {
                $thumbnail = (new MediaRepository())->updateByRequest(
                    $request->signature,
                    'images/webs/',
                    'image',
                    $thumbnail
                );
            }
            $data['signature_id'] = $thumbnail->id;
        }

        $id = $webSetting?->id ?? null;

        return $this->query()->updateOrCreate(
            ['id' => $id],
            $data
        );
    }

    public function getAllZones()
    {
        $zones = [];
        $timestamp = time();
        foreach (timezone_identifiers_list() as $key => $zone) {
            date_default_timezone_set($zone);
            $zones[$key]['zone'] = $zone;
            $zones[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
        }
        return $zones;
    }
}
