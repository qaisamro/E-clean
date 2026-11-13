<?php

namespace App\Http\Controllers\API\Offer;

use App\Http\Controllers\Controller;
use App\Http\Resources\OfferResource;
use App\Repositories\OfferRepository;

class OfferController extends Controller
{
    public function index()
    {
        $offers = (new OfferRepository())->getActive(\App\Support\CatalogCityFilter::selected(request()));

        return $this->json('offer list', [
            'offers' => OfferResource::collection($offers)
        ]);
    }
}