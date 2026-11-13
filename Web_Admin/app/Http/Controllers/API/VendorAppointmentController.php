<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Services\VendorAppointments;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class VendorAppointmentController extends Controller
{
    public function index(Request $request, Vendor $vendor, VendorAppointments $appointments)
    {
        abort_unless($vendor->is_active, 404);
        $v = $request->validate(['date' => 'sometimes|date_format:Y-m-d',
            'pick_date' => 'nullable|required_with:pick_hour|date_format:Y-m-d',
            'pick_hour' => 'nullable|required_with:pick_date|date_format:H:i']);
        return $this->json('Store appointments', $appointments->availability($vendor->id,
            $v['date'] ?? CarbonImmutable::now(VendorAppointments::ZONE)->format('Y-m-d'),
            $v['pick_date'] ?? null, $v['pick_hour'] ?? null));
    }
}
