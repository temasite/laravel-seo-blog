<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_login_page_is_available_under_the_admin_prefix(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('Sign in')
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false)
            ->assertSee('action="'.route('login.store').'"', false);

        $this->get('/login')->assertNotFound();
    }

    public function test_public_registration_is_disabled(): void
    {
        $this->get('/register')->assertNotFound();
        $this->get('/admin/register')->assertNotFound();
    }

    public function test_active_admin_can_sign_in(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);
        $user->assignRole('admin');

        $this->post('/admin/login', [
            'email' => 'ADMIN@example.com',
            'password' => 'password',
        ])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->refresh()->last_login_at);
    }

    public function test_active_manager_can_sign_in(): void
    {
        $user = User::factory()->create([
            'email' => 'manager@example.com',
            'password' => 'password',
        ]);
        $user->assignRole('manager');

        $this->post('/admin/login', [
            'email' => 'manager@example.com',
            'password' => 'password',
        ])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);
    }

    public function test_suspended_user_cannot_sign_in(): void
    {
        $user = User::factory()->create([
            'email' => 'suspended@example.com',
            'password' => 'password',
        ]);
        $user->assignRole('admin');
        $user->suspend();

        $this->post('/admin/login', [
            'email' => 'suspended@example.com',
            'password' => 'password',
        ])
            ->assertSessionHasErrors('email')
            ->assertRedirect();

        $this->assertGuest();
    }

    public function test_user_without_admin_access_cannot_sign_in(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $this->post('/admin/login', [
            'email' => 'user@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_authenticated_user_is_redirected_away_from_login_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)
            ->get('/admin/login')
            ->assertRedirect('/admin');
    }
}
