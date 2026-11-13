<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // Never let a DATABASE_URL from the workspace override PHPUnit's
        // isolated in-memory database connection.
        if ($app->environment('testing')) {
            $app['config']->set('database.default', 'sqlite');
            $app['config']->set('database.connections.sqlite.url', null);
            $app['config']->set('database.connections.sqlite.database', ':memory:');
        }

        return $app;
    }
}
