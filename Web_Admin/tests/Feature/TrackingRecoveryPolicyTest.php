<?php

namespace Tests\Feature;

use App\Services\IntegrationSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingRecoveryPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_master_exposes_recovery_policy_without_changing_other_fields(): void
    {
        $settings = app(IntegrationSettingsService::class);
        $settings->set('otp.enabled', '1', ['group' => 'otp']);
        $enabled = $this->getJson('/api/master')->assertOk()
            ->assertJsonPath('data.tracking_recovery_available', true)->json();
        $settings->set('otp.enabled', '0', ['group' => 'otp']);
        $disabled = $this->getJson('/api/master')->assertOk()
            ->assertJsonPath('data.tracking_recovery_available', false)->json();
        unset($enabled['data']['tracking_recovery_available'], $disabled['data']['tracking_recovery_available']);
        $this->assertSame($enabled, $disabled);
    }
}
