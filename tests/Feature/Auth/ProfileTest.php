<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Fortify;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guest_cannot_view_the_profile_page(): void
    {
        $this->get('/admin/profile')
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_view_profile_information(): void
    {
        $user = $this->createAdmin([
            'name' => 'Jane Editor',
            'email' => 'jane@example.com',
            'last_login_at' => now(),
        ]);

        $this->actingAs($user)
            ->get('/admin/profile')
            ->assertOk()
            ->assertSee('Profile settings')
            ->assertSee('Jane Editor')
            ->assertSee('jane@example.com')
            ->assertSee('Admin')
            ->assertSee('Last sign in')
            ->assertSee('name="name"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="current_password"', false)
            ->assertSee('href="'.route('admin.profile').'"', false);
    }

    public function test_admin_can_update_name_and_login_email(): void
    {
        $user = $this->createAdmin();

        $this->actingAs($user)
            ->from('/admin/profile')
            ->put(route('user-profile-information.update'), [
                'name' => 'Updated Name',
                'email' => 'UPDATED@example.com',
            ])
            ->assertRedirect('/admin/profile')
            ->assertSessionHas('status', Fortify::PROFILE_INFORMATION_UPDATED);

        $user->refresh();

        $this->assertSame('Updated Name', $user->name);
        $this->assertSame('updated@example.com', $user->email);
    }

    public function test_profile_update_rejects_an_email_used_by_another_user(): void
    {
        $user = $this->createAdmin();
        User::factory()->create(['email' => 'existing@example.com']);

        $this->actingAs($user)
            ->from('/admin/profile')
            ->put(route('user-profile-information.update'), [
                'name' => 'Updated Name',
                'email' => 'existing@example.com',
            ])
            ->assertRedirect('/admin/profile')
            ->assertSessionHasErrors('email', null, 'updateProfileInformation');
    }

    public function test_admin_can_update_password(): void
    {
        $user = $this->createAdmin(['password' => 'current-password']);

        $this->actingAs($user)
            ->from('/admin/profile')
            ->put(route('user-password.update'), [
                'current_password' => 'current-password',
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ])
            ->assertRedirect('/admin/profile')
            ->assertSessionHas('status', Fortify::PASSWORD_UPDATED);

        $this->assertTrue(Hash::check('new-secure-password', $user->refresh()->password));
    }

    public function test_password_update_requires_the_current_password(): void
    {
        $user = $this->createAdmin(['password' => 'current-password']);

        $this->actingAs($user)
            ->from('/admin/profile')
            ->put(route('user-password.update'), [
                'current_password' => 'wrong-password',
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ])
            ->assertRedirect('/admin/profile')
            ->assertSessionHasErrors('current_password', null, 'updatePassword');

        $this->assertTrue(Hash::check('current-password', $user->refresh()->password));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createAdmin(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->assignRole('admin');

        return $user;
    }
}
