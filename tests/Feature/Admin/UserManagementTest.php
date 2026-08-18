<?php

namespace Tests\Feature\Admin;

use App\Enums\UserStatus;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_view_and_filter_users(): void
    {
        $admin = $this->createUserWithRole('admin', ['name' => 'Primary Admin']);
        $activeManager = $this->createUserWithRole('manager', [
            'name' => 'Alpha Manager',
            'email' => 'alpha@example.com',
        ]);
        $suspendedManager = $this->createUserWithRole('manager', [
            'name' => 'Beta Manager',
            'email' => 'beta@example.com',
        ]);
        $suspendedManager->suspend();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Access management')
            ->assertSee('Primary Admin')
            ->assertSee('Alpha Manager')
            ->assertSee('Beta Manager')
            ->assertSee('href="'.route('admin.users.create').'"', false);

        $this->actingAs($admin)
            ->get(route('admin.users.index', [
                'search' => 'Alpha',
                'role' => 'manager',
                'status' => UserStatus::Active->value,
            ]))
            ->assertOk()
            ->assertSee($activeManager->email)
            ->assertDontSee($suspendedManager->email);
    }

    public function test_manager_cannot_access_user_management(): void
    {
        $manager = $this->createUserWithRole('manager');

        $this->actingAs($manager)
            ->get(route('admin.users.index'))
            ->assertForbidden();

        $this->actingAs($manager)
            ->get(route('admin.users.create'))
            ->assertForbidden();

        $managedUser = $this->createUserWithRole('manager');

        $this->actingAs($manager)
            ->delete(route('admin.users.destroy', $managedUser))
            ->assertForbidden();
    }

    public function test_admin_can_open_user_creation_and_edit_forms(): void
    {
        $admin = $this->createUserWithRole('admin');
        $managedUser = $this->createUserWithRole('manager', [
            'name' => 'Managed User',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.create'))
            ->assertOk()
            ->assertSee('Create a user')
            ->assertSee('name="password_confirmation"', false);

        $this->actingAs($admin)
            ->get(route('admin.users.edit', $managedUser))
            ->assertOk()
            ->assertSee('Edit Managed User')
            ->assertSee('Set a new password')
            ->assertSee('Suspend account')
            ->assertSee('Delete user permanently');
    }

    public function test_admin_can_create_an_active_user(): void
    {
        $admin = $this->createUserWithRole('admin');

        $response = $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => '  New Manager  ',
                'email' => 'NEW.MANAGER@example.com',
                'role' => 'manager',
                'password' => 'secure-password',
                'password_confirmation' => 'secure-password',
            ]);

        $user = User::query()->where('email', 'new.manager@example.com')->firstOrFail();

        $response
            ->assertRedirect(route('admin.users.edit', $user))
            ->assertSessionHas('status', 'User has been created.');

        $this->assertSame('New Manager', $user->name);
        $this->assertSame(UserStatus::Active, $user->status);
        $this->assertTrue($user->hasRole('manager'));
        $this->assertTrue(Hash::check('secure-password', $user->password));
    }

    public function test_admin_can_update_user_information_and_role(): void
    {
        $admin = $this->createUserWithRole('admin');
        $managedUser = $this->createUserWithRole('manager');

        $this->actingAs($admin)
            ->from(route('admin.users.edit', $managedUser))
            ->put(route('admin.users.update', $managedUser), [
                'name' => 'Updated User',
                'email' => 'UPDATED.USER@example.com',
                'role' => 'admin',
            ])
            ->assertRedirect(route('admin.users.edit', $managedUser))
            ->assertSessionHas('status', 'User information has been updated.');

        $managedUser->refresh();

        $this->assertSame('Updated User', $managedUser->name);
        $this->assertSame('updated.user@example.com', $managedUser->email);
        $this->assertTrue($managedUser->hasRole('admin'));
        $this->assertFalse($managedUser->hasRole('manager'));

        $this->actingAs($admin)
            ->put(route('admin.users.update', $managedUser), [
                'name' => $managedUser->name,
                'email' => $managedUser->email,
                'role' => 'manager',
            ])
            ->assertSessionHasNoErrors();

        $this->assertTrue($managedUser->refresh()->hasRole('manager'));
    }

    public function test_admin_cannot_change_own_role_suspend_or_delete_own_account(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin)
            ->from(route('admin.users.edit', $admin))
            ->put(route('admin.users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => 'manager',
            ])
            ->assertRedirect(route('admin.users.edit', $admin))
            ->assertSessionHasErrors('role');

        $this->assertTrue($admin->refresh()->hasRole('admin'));

        $this->actingAs($admin)
            ->from(route('admin.users.index'))
            ->patch(route('admin.users.suspend', $admin))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHasErrors('user');

        $this->assertSame(UserStatus::Active, $admin->refresh()->status);

        $this->actingAs($admin)
            ->from(route('admin.users.edit', $admin))
            ->delete(route('admin.users.destroy', $admin))
            ->assertRedirect(route('admin.users.edit', $admin))
            ->assertSessionHasErrors('user');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_can_suspend_and_restore_another_user(): void
    {
        $admin = $this->createUserWithRole('admin');
        $managedUser = $this->createUserWithRole('manager');

        $this->actingAs($admin)
            ->from(route('admin.users.index'))
            ->patch(route('admin.users.suspend', $managedUser))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('status', 'User has been suspended.');

        $managedUser->refresh();

        $this->assertSame(UserStatus::Suspended, $managedUser->status);
        $this->assertNotNull($managedUser->suspended_at);

        $this->actingAs($admin)
            ->from(route('admin.users.edit', $managedUser))
            ->patch(route('admin.users.restore', $managedUser))
            ->assertRedirect(route('admin.users.edit', $managedUser))
            ->assertSessionHas('status', 'User has been restored.');

        $managedUser->refresh();

        $this->assertSame(UserStatus::Active, $managedUser->status);
        $this->assertNull($managedUser->suspended_at);
    }

    public function test_admin_can_set_a_new_user_password(): void
    {
        $admin = $this->createUserWithRole('admin');
        $managedUser = $this->createUserWithRole('manager', [
            'password' => 'old-password',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.users.edit', $managedUser))
            ->put(route('admin.users.password.update', $managedUser), [
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ])
            ->assertRedirect(route('admin.users.edit', $managedUser))
            ->assertSessionHas('status', 'User password has been updated.');

        $this->assertTrue(Hash::check('new-secure-password', $managedUser->refresh()->password));
    }

    public function test_admin_can_permanently_delete_another_user(): void
    {
        $admin = $this->createUserWithRole('admin');
        $managedUser = $this->createUserWithRole('manager');

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $managedUser))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('status', 'User has been permanently deleted.');

        $this->assertDatabaseMissing('users', ['id' => $managedUser->id]);
        $this->assertDatabaseMissing('model_has_roles', [
            'model_type' => User::class,
            'model_id' => $managedUser->id,
        ]);
    }

    public function test_user_with_delete_permission_cannot_delete_the_last_administrator(): void
    {
        $admin = $this->createUserWithRole('admin');
        $manager = $this->createUserWithRole('manager');
        $manager->givePermissionTo('users.delete');

        $this->actingAs($manager)
            ->from(route('admin.users.edit', $admin))
            ->delete(route('admin.users.destroy', $admin))
            ->assertRedirect(route('admin.users.edit', $admin))
            ->assertSessionHasErrors('user');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createUserWithRole(string $role, array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->assignRole($role);

        return $user;
    }
}
