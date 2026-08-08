<?php

namespace Tests\Feature\Models;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UserStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_are_active_by_default(): void
    {
        $user = User::factory()->create();

        $this->assertSame(UserStatus::Active, $user->status);
        $this->assertNull($user->last_login_at);
        $this->assertNull($user->suspended_at);
    }

    public function test_status_and_user_activity_dates_are_cast(): void
    {
        $user = User::factory()->create([
            'status' => UserStatus::Suspended,
            'last_login_at' => now(),
            'suspended_at' => now(),
        ]);

        $this->assertSame(UserStatus::Suspended, $user->status);
        $this->assertInstanceOf(Carbon::class, $user->last_login_at);
        $this->assertInstanceOf(Carbon::class, $user->suspended_at);
    }

    public function test_user_can_be_suspended(): void
    {
        $suspendedAt = Carbon::parse('2026-08-08 12:00:00');
        $this->travelTo($suspendedAt);

        $user = User::factory()->create();

        $user->suspend();

        $this->assertSame(UserStatus::Suspended, $user->status);
        $this->assertTrue($suspendedAt->equalTo($user->suspended_at));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => UserStatus::Suspended->value,
            'suspended_at' => $suspendedAt->format('Y-m-d H:i:s'),
        ]);
    }

    public function test_suspending_an_already_suspended_user_preserves_the_original_date(): void
    {
        $originalSuspendedAt = Carbon::parse('2026-08-08 12:00:00');
        $user = User::factory()->create([
            'status' => UserStatus::Suspended,
            'suspended_at' => $originalSuspendedAt,
        ]);

        $this->travelTo($originalSuspendedAt->copy()->addDay());

        $user->suspend();

        $this->assertTrue($originalSuspendedAt->equalTo($user->suspended_at));
    }

    public function test_activating_a_user_clears_the_suspension_date(): void
    {
        $user = User::factory()->create([
            'status' => UserStatus::Suspended,
            'suspended_at' => now(),
        ]);

        $user->activate();

        $this->assertSame(UserStatus::Active, $user->status);
        $this->assertNull($user->suspended_at);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => UserStatus::Active->value,
            'suspended_at' => null,
        ]);
    }
}
