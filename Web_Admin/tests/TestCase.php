<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // API feature tests authenticate a model directly; use the session
        // driver in testing so Passport's on-disk signing keys are untouched.
        config([
            'auth.guards.api.driver' => 'session',
            'auth.guards.api.provider' => 'users',
        ]);
    }
}
