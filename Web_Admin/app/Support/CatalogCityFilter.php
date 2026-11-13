<?php

namespace App\Support;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CatalogCityFilter
{
    public static function selected(Request $request): ?string
    {
        $request->validate(['city_id' => ['nullable', 'string', 'max:64']]);
        $id = $request->input('city_id');
        if ($id && !app(CityCatalog::class)->find($id)) {
            throw ValidationException::withMessages(['city_id' => 'اختر مدينة معتمدة من القائمة.']);
        }
        return $id ?: null;
    }

    public static function apply(Builder $query, ?string $cityId, bool $vendors = false): Builder
    {
        if (!$cityId) return $query; // Preserve all previous clients without the optional filter.
        $city = app(CityCatalog::class)->find($cityId);
        if (!$city) return $query->whereRaw('1 = 0');
        if ($vendors) return $query->whereIn('city', $city['aliases']);
        $column = $query->getModel()->qualifyColumn('vendor_id');
        return $query->where(function (Builder $inner) use ($column, $city) {
            $inner
                ->whereNull($column)
                ->orWhereIn($column,
                    Vendor::where('is_active', 1)->whereIn('city', $city['aliases'])->select('id'));
        });
    }
}
