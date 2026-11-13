<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\CityLocationResolver;
use App\Support\CityCatalog;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(CityCatalog $catalog)
    {
        return $this->json('City list', ['cities' => array_map([CityCatalog::class, 'publicData'], $catalog->all())]);
    }

    public function resolve(Request $request, CityLocationResolver $resolver)
    {
        $data = $request->validate(['latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'], 'accuracy' => ['nullable', 'numeric', 'between:0,100000']]);
        $city = $resolver->resolve((float) $data['latitude'], (float) $data['longitude'], (float) ($data['accuracy'] ?? 0));
        return $this->json($city ? 'City resolved' : 'اختر المدينة يدويًا لتعذر التحقق من حدود موقعك.', ['city' => $city]);
    }
}
