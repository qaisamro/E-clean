<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Support\StoreOrderAccess;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class StoreOrderAccessTest extends TestCase
{
    public function test_unrecognized_authenticated_roles_are_denied(): void
    {
        Auth::shouldReceive('user')->andReturn($this->userWithRoles(['customer']));
        $order = new Order();
        $order->id = 8;

        try {
            StoreOrderAccess::assertCanView($order);
            $this->fail('A customer must not pass the store order guard.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }

        Auth::shouldReceive('user')->andReturn($this->userWithRoles(['customer']));
        try {
            StoreOrderAccess::assertCanMutate($order);
            $this->fail('A customer must not mutate an order through the store guard.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
    }

    public function test_root_and_admin_roles_are_allowed_by_the_store_guard(): void
    {
        foreach (['root', 'admin'] as $role) {
            Auth::shouldReceive('user')->andReturn($this->userWithRoles([$role]));
            $order = new Order();
            $order->id = 8;

            StoreOrderAccess::assertCanView($order);

            Auth::shouldReceive('user')->andReturn($this->userWithRoles([$role]));
            StoreOrderAccess::assertCanMutate($order);
        }

        $this->assertTrue(true);
    }

    private function userWithRoles(array $roles): object
    {
        return new class($roles) {
            public $id = 10;
            public $is_active = true;
            private $roles;

            public function __construct(array $roles)
            {
                $this->roles = $roles;
            }

            public function hasRole(string $role): bool
            {
                return in_array($role, $this->roles, true);
            }
        };
    }
}
