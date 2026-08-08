<?php

namespace Tests\Feature\Console;

use App\Enums\UserStatus;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateUserCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_active_user_with_a_role(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->artisan('user:create', [
            '--name' => 'Admin User',
            '--email' => 'ADMIN@example.com',
            '--role' => 'admin',
        ])
            ->expectsQuestion('Password', 'StrongPassword1!')
            ->expectsQuestion('Confirm password', 'StrongPassword1!')
            ->expectsOutput('User [admin@example.com] created with role [admin].')
            ->assertSuccessful();

        $user = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->assertSame(UserStatus::Active, $user->status);
        $this->assertTrue($user->hasRole('admin'));
        $this->assertTrue(Hash::check('StrongPassword1!', $user->password));
    }

    public function test_it_rejects_an_unknown_role(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->artisan('user:create', [
            '--name' => 'Invalid User',
            '--email' => 'invalid@example.com',
            '--role' => 'unknown',
        ])
            ->expectsOutput('Role [unknown] does not exist for the web guard.')
            ->assertFailed();

        $this->assertDatabaseMissing('users', ['email' => 'invalid@example.com']);
    }
}
