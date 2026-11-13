<?php

namespace Tests\Feature;

use Tests\TestCase;

class DisabledWebsiteAuthRoutesTest extends TestCase
{
    public function test_sign_in_sign_up_and_super_admin_setup_routes_are_unavailable(): void
    {
        foreach (['/sign-in', '/sign-up', '/create-superadmin'] as $path) {
            $this->get($path)->assertNotFound();
            $this->post($path)->assertNotFound();
        }
    }
}
