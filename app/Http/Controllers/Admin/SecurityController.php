<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;

class SecurityController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $hasTwoFactorSecret = filled($user->two_factor_secret);
        $twoFactorEnabled = $user->hasEnabledTwoFactorAuthentication();

        return view('admin.security', [
            'twoFactorEnabled' => $twoFactorEnabled,
            'twoFactorPending' => $hasTwoFactorSecret && ! $twoFactorEnabled,
            'qrCodeSvg' => $hasTwoFactorSecret && ! $twoFactorEnabled
                ? $user->twoFactorQrCodeSvg()
                : null,
            'secretKey' => $hasTwoFactorSecret && ! $twoFactorEnabled
                ? Fortify::currentEncrypter()->decrypt($user->two_factor_secret)
                : null,
            'recoveryCodes' => $twoFactorEnabled ? $user->recoveryCodes() : [],
            'statusMessage' => $this->statusMessage((string) session('status')),
        ]);
    }

    private function statusMessage(string $status): ?string
    {
        return match ($status) {
            Fortify::TWO_FACTOR_AUTHENTICATION_ENABLED => 'Two-factor authentication setup started.',
            Fortify::TWO_FACTOR_AUTHENTICATION_CONFIRMED => 'Two-factor authentication is now active.',
            Fortify::TWO_FACTOR_AUTHENTICATION_DISABLED => 'Two-factor authentication has been disabled.',
            Fortify::RECOVERY_CODES_GENERATED => 'New recovery codes have been generated.',
            default => null,
        };
    }
}
