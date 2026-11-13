<?php

namespace Tests\Unit;

use App\Http\Middleware\CheckPermission;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CheckPermissionTest extends TestCase
{
    public function test_customer_role_cannot_access_an_admin_route(): void
    {
        $request = $this->requestFor('service.index', ['customer']);

        try {
            (new CheckPermission())->handle($request, fn () => 'allowed');
            $this->fail('Expected admin access to be denied.');
        } catch (AccessDeniedHttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
    }

    public function test_admin_roles_can_access_administration_routes(): void
    {
        foreach (['root', 'admin'] as $role) {
            $request = $this->requestFor('order.index', [$role]);

            $this->assertSame(
                'allowed',
                (new CheckPermission())->handle($request, fn () => 'allowed')
            );
        }
    }

    public function test_visitor_requires_the_route_permission(): void
    {
        $request = $this->requestFor('service.index', ['visitor'], ['service.index']);
        $response = (new CheckPermission())->handle($request, fn () => 'allowed');

        $this->assertSame('allowed', $response);
    }

    public function test_visitor_without_route_permission_is_denied(): void
    {
        $request = $this->requestFor('service.index', ['visitor']);

        $this->expectException(AccessDeniedHttpException::class);
        (new CheckPermission())->handle($request, fn () => 'allowed');
    }

    public function test_driver_can_only_enter_the_driver_account_and_status_routes(): void
    {
        $allowedRequest = $this->requestFor('driver.account', ['driver']);
        $this->assertSame(
            'allowed',
            (new CheckPermission())->handle($allowedRequest, fn () => 'allowed')
        );

        $deniedRequest = $this->requestFor('order.index', ['driver']);
        try {
            (new CheckPermission())->handle($deniedRequest, fn () => 'allowed');
            $this->fail('Drivers must not access administration order routes.');
        } catch (AccessDeniedHttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }

        $statusRequest = $this->requestFor('driver.order.status.change', ['driver']);
        $this->assertSame(
            'allowed',
            (new CheckPermission())->handle($statusRequest, fn () => 'allowed')
        );

        $adminRequest = $this->requestFor('order.index', ['driver']);
        $this->expectException(AccessDeniedHttpException::class);
        (new CheckPermission())->handle($adminRequest, fn () => 'allowed');
    }

    private function requestFor(string $routeName, array $roles, array $permissions = []): Request
    {
        $user = new class($roles, $permissions) {
            public function __construct(
                private array $roles,
                private array $permissions
            ) {
            }

            public function hasRole(string $role): bool
            {
                return in_array($role, $this->roles, true);
            }

            public function can(string $permission): bool
            {
                return in_array($permission, $this->permissions, true);
            }
        };

        $route = new Route(['GET'], '/', static fn () => null);
        $route->name($routeName);

        $request = Request::create('/', 'GET');
        $request->setUserResolver(static fn () => $user);
        $request->setRouteResolver(static fn () => $route);

        return $request;
    }
}
