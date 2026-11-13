<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

/**
 * GET /api/variants?service_id={service_id}
 *
 * Returns the variants assigned to one service (with position), used by the
 * customer app's service picker. Unknown / inactive service returns an empty
 * list instead of an error so the app never breaks.
 */
class VariantController extends Controller
{
    public function index(Request $request)
    {
        $serviceId = (int) $request->query('service_id', 0);
        $service = Service::with('variants')
            ->where('is_active', 1)
            ->find($serviceId);

        if (!$service) {
            return $this->json('variants', ['variants' => []]);
        }

        $variants = $service->variants->map(fn ($v) => [
            'id' => $v->id,
            'name' => $v->name,
            'name_bn' => $v->name_bn,
        ]);

        return $this->json('variants', ['variants' => $variants]);
    }
}
