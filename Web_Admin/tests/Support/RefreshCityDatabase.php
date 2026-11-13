<?php

namespace Tests\Support;

use Illuminate\Foundation\Testing\RefreshDatabase;

trait RefreshCityDatabase
{
    use RefreshDatabase;

    protected function migrateFreshUsing(): array
    {
        // An existing unrelated migration uses MySQL MODIFY/ENUM syntax.
        // SQLite represents status as text; leave production migration untouched.
        $paths = array_values(array_filter(glob(database_path('migrations/*.php')),
            fn ($path) => !in_array(basename($path), ['2026_11_13_000002_add_ready_for_pickup_to_orders_status_enum.php', '2026_11_13_000003_add_on_the_way_to_orders_status_enum.php'], true)));
        return ['--path' => $paths, '--realpath' => true];
    }
}
