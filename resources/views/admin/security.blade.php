@extends('layouts.admin')

@section('title', 'Security')

@section('content')
    <div class="max-w-2xl">
        <div class="mb-8">
            <h1 class="text-2xl font-semibold tracking-tight">Security</h1>
            <p class="mt-2 text-sm text-stone-600">Protect your account with an authenticator app.</p>
        </div>

        @if ($statusMessage)
            <div role="status" class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ $statusMessage }}
            </div>
        @endif

        <section class="rounded-xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex items-start justify-between gap-6">
                <div>
                    <h2 class="font-semibold">Two-factor authentication</h2>
                    <p class="mt-1 text-sm leading-6 text-stone-600">Require a time-based code when signing in.</p>
                </div>

                <span @class([
                    'rounded-full px-2.5 py-1 text-xs font-medium',
                    'bg-emerald-100 text-emerald-800' => $twoFactorEnabled,
                    'bg-amber-100 text-amber-800' => $twoFactorPending,
                    'bg-stone-100 text-stone-600' => ! $twoFactorEnabled && ! $twoFactorPending,
                ])>
                    {{ $twoFactorEnabled ? 'Enabled' : ($twoFactorPending ? 'Setup required' : 'Disabled') }}
                </span>
            </div>

            @if (! $twoFactorEnabled && ! $twoFactorPending)
                <form method="POST" action="{{ route('two-factor.enable') }}" class="mt-6">
                    @csrf
                    <button type="submit" class="rounded-lg bg-stone-950 px-4 py-2.5 text-sm font-semibold text-white hover:bg-stone-800">Enable 2FA</button>
                </form>
            @endif

            @if ($twoFactorPending)
                <div class="mt-7 border-t border-stone-200 pt-7">
                    <h3 class="text-sm font-semibold">1. Scan this QR code</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-600">Use any TOTP-compatible authenticator app.</p>

                    <div class="mt-5 w-fit rounded-lg border border-stone-200 bg-white p-3" aria-label="Two-factor authentication QR code">
                        {!! $qrCodeSvg !!}
                    </div>

                    <details class="mt-4 text-sm text-stone-600">
                        <summary class="cursor-pointer font-medium text-stone-800">Unable to scan?</summary>
                        <p class="mt-2">Enter this setup key manually:</p>
                        <code class="mt-2 block break-all rounded-lg bg-stone-100 px-3 py-2 font-mono text-xs text-stone-800">{{ $secretKey }}</code>
                    </details>

                    <form method="POST" action="{{ route('two-factor.confirm') }}" class="mt-7 max-w-xs">
                        @csrf
                        <label for="code" class="mb-2 block text-sm font-medium text-stone-800">2. Enter the six-digit code</label>
                        <input
                            id="code"
                            name="code"
                            type="text"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            maxlength="6"
                            required
                            class="block h-11 w-full rounded-lg border border-stone-300 bg-white px-3.5 font-mono text-sm tracking-[0.2em] outline-none focus:border-stone-600 focus:ring-3 focus:ring-stone-950/5"
                        >
                        @error('code', 'confirmTwoFactorAuthentication')
                            <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                        @enderror

                        <button type="submit" class="mt-4 rounded-lg bg-stone-950 px-4 py-2.5 text-sm font-semibold text-white hover:bg-stone-800">Confirm setup</button>
                    </form>

                    <form method="POST" action="{{ route('two-factor.disable') }}" class="mt-3">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-lg px-4 py-2.5 text-sm font-semibold text-stone-600 hover:bg-stone-100 hover:text-stone-950">Cancel setup</button>
                    </form>
                </div>
            @endif

            @if ($twoFactorEnabled)
                <div class="mt-7 border-t border-stone-200 pt-7">
                    <h3 class="text-sm font-semibold">Recovery codes</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-600">Store these codes in a secure place. Each code can be used once.</p>

                    <div class="mt-4 grid gap-2 rounded-lg bg-stone-100 p-4 font-mono text-xs text-stone-800 sm:grid-cols-2">
                        @foreach ($recoveryCodes as $recoveryCode)
                            <code>{{ $recoveryCode }}</code>
                        @endforeach
                    </div>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <form method="POST" action="{{ route('two-factor.regenerate-recovery-codes') }}">
                            @csrf
                            <button type="submit" class="rounded-lg border border-stone-300 bg-white px-4 py-2.5 text-sm font-semibold text-stone-800 hover:bg-stone-50">Generate new codes</button>
                        </form>

                        <form method="POST" action="{{ route('two-factor.disable') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg border border-red-200 bg-white px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-50">Disable 2FA</button>
                        </form>
                    </div>
                </div>
            @endif
        </section>
    </div>
@endsection
