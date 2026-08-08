<?php

namespace Tests\Feature\Seeders;

use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolesAndPermissionsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_roles_and_assigns_their_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = Role::findByName('admin');
        $manager = Role::findByName('manager');

        $this->assertCount(Permission::count(), $admin->permissions);
        $this->assertTrue($manager->hasPermissionTo('admin.access'));
        $this->assertTrue($manager->hasPermissionTo('articles.publish'));
        $this->assertTrue($manager->hasPermissionTo('categories.delete'));
        $this->assertFalse($manager->hasPermissionTo('users.view'));
        $this->assertFalse($manager->hasPermissionTo('settings.manage'));
    }

    public function test_it_can_be_run_more_than_once(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->assertSame(2, Role::count());
        $this->assertSame(19, Permission::count());
    }
}
