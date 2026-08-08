<?php

namespace Tests\Feature\Middleware;

use Illuminate\Contracts\Http\Kernel;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Tests\TestCase;

class PermissionMiddlewareAliasesTest extends TestCase
{
    public function test_spatie_permission_middleware_aliases_are_registered(): void
    {
        $middleware = app(Kernel::class)->getMiddlewareAliases();

        $this->assertSame(RoleMiddleware::class, $middleware['role']);
        $this->assertSame(PermissionMiddleware::class, $middleware['permission']);
        $this->assertSame(RoleOrPermissionMiddleware::class, $middleware['role_or_permission']);
    }
}
