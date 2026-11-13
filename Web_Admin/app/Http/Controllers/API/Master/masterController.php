<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentGatewayResource;
use App\Models\DeliveryCost;
use App\Models\MobileAppUrl;
use App\Models\PaymentGateway;
use App\Models\WebSetting;
use Illuminate\Support\Facades\Cache;

class masterController extends Controller
{
    /**
     * Whether the super admin wants the partners section visible in the app.
     * Edited at /website-settings (الصفحات والسياسات → إعدادات التطبيق). Cached 1h.
     */
    protected function partnersSectionEnabled(): bool
    {
        return Cache::remember('app_partners_section_enabled', 3600, function () {
            $row = \App\Models\Website\WebsiteSetting::query()->where('key', 'app_partners_section')->first();
            return $row ? (bool) ($row->value['enabled'] ?? true) : true;
        });
    }

    public function index()
    {

        $websetting = WebSetting::first();
        $currency = $websetting?->currency ?? config('enums.currency');
        $paymentGateway = PaymentGateway::where('is_active', true)->get();

        $cost = DeliveryCost::first();
        $costFee = $cost ? $cost->cost : 00.0;
        $fee_cost = $cost ? $cost->fee_cost : 100;
        $mini_cost = $cost ? $cost->minimum_cost : 00;

        $mobileAppLink = MobileAppUrl::first();

        return $this->json('',[
            'tracking_recovery_available' => app(\App\Services\OtpService::class)->enabled(),
            'show_partners_section' => $this->partnersSectionEnabled(),
            'currency' => $currency,
            'currency_position' => config('app.currency_position'),
            'delivery_cost' => $costFee,
            'fee_cost' => $fee_cost,
            'minimum_cost' => $mini_cost,
            'tax_rate' => $websetting?->tax_rate ?? 0,
            'payment_gateway' => PaymentGatewayResource::collection($paymentGateway),
            'post_code' => config('enums.post_code'),
            'android_url' => $mobileAppLink ? $mobileAppLink->android_url : '',
            'ios_url' => $mobileAppLink ? $mobileAppLink->ios_url : ''
        ]);

    }
}
