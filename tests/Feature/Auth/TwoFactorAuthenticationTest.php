<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Fortify;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_two_factor_settings_require_password_confirmation(): void
    {
        $user = $this->createAdmin();

        $this->actingAs($user)
            ->get('/admin/security')
            ->assertRedirect(route('password.confirm'));

        $this->get(route('password.confirm'))
            ->assertOk()
            ->assertSee('Confirm your password')
            ->assertSee('Back to dashboard')
            ->assertSee('href="'.route('admin.dashboard').'"', false);
    }

    public function test_user_can_enable_and_confirm_two_factor_authentication(): void
    {
        $user = $this->createAdmin();
        $this->actingAs($user)->withSession($this->passwordConfirmedSession());

        $this->from('/admin/security')
            ->post(route('two-factor.enable'))
            ->assertRedirect('/admin/security');

        $user->refresh();

        $this->assertNotNull($user->two_factor_secret);
        $this->assertNull($user->two_factor_confirmed_at);
        $this->get('/admin/security')
            ->assertOk()
            ->assertSee('Scan this QR code');

        $secret = Fortify::currentEncrypter()->decrypt($user->two_factor_secret);
        $code = (new Google2FA)->getCurrentOtp($secret);

        $this->from('/admin/security')
            ->post(route('two-factor.confirm'), ['code' => $code])
            ->assertRedirect('/admin/security');

        $this->assertNotNull($user->refresh()->two_factor_confirmed_at);
        $this->assertCount(8, $user->recoveryCodes());
    }

    public function test_user_can_cancel_pending_two_factor_setup(): void
    {
        $user = $this->createAdmin();
        $this->actingAs($user)->withSession($this->passwordConfirmedSession());

        $this->from('/admin/security')
            ->post(route('two-factor.enable'))
            ->assertRedirect('/admin/security');

        $this->get('/admin/security')
            ->assertOk()
            ->assertSee('Cancel setup');

        $this->from('/admin/security')
            ->delete(route('two-factor.disable'))
            ->assertRedirect('/admin/security');

        $user->refresh();

        $this->assertNull($user->two_factor_secret);
        $this->assertNull($user->two_factor_recovery_codes);
        $this->assertNull($user->two_factor_confirmed_at);
    }

    public function test_two_factor_user_must_complete_the_login_challenge(): void
    {
        [$user, $secret] = $this->createTwoFactorAdmin();

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('two-factor.login'));

        $this->assertGuest();
        $this->get(route('two-factor.login'))
            ->assertOk()
            ->assertSee('Authentication code');

        $this->post(route('two-factor.login.store'), [
            'code' => (new Google2FA)->getCurrentOtp($secret),
        ])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_sign_in_with_a_recovery_code(): void
    {
        [$user] = $this->createTwoFactorAdmin('recovery-code');

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('two-factor.login'));

        $this->post(route('two-factor.login.store'), [
            'recovery_code' => 'recovery-code',
        ])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);
        $this->assertNotContains('recovery-code', $user->refresh()->recoveryCodes());
    }

    private function createAdmin(): User
    {
        $user = User::factory()->create(['password' => 'password']);
        $user->assignRole('admin');

        return $user;
    }

    /**
     * @return array{User, string}
     */
    private function createTwoFactorAdmin(string $recoveryCode = 'unused-recovery-code'): array
    {
        $user = $this->createAdmin();
        $secret = (new Google2FA)->generateSecretKey();

        $user->forceFill([
            'two_factor_secret' => Fortify::currentEncrypter()->encrypt($secret),
            'two_factor_recovery_codes' => Fortify::currentEncrypter()->encrypt(json_encode([$recoveryCode])),
            'two_factor_confirmed_at' => now(),
        ])->save();

        return [$user, $secret];
    }

    /**
     * @return array<string, int>
     */
    private function passwordConfirmedSession(): array
    {
        return ['auth.password_confirmed_at' => time()];
    }
}
