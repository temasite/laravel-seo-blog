@extends('layouts.admin')

@section('title', 'Security')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div>
            <p class="text-[13px] font-medium text-[#6366F1]">Account protection</p>
            <h2 class="mt-1 text-2xl font-bold tracking-tight text-[#111827]">Sign-in security</h2>
            <p class="mt-2 max-w-2xl text-[14px] leading-6 text-[#6B7280]">Add an extra verification step to keep your administration account protected.</p>
        </div>

        @if ($statusMessage)
            <div role="status" class="flex items-center gap-3 rounded-[10px] border border-[#A7F3D0] bg-[#ECFDF5] px-4 py-3 text-[13px] font-medium text-[#047857]">
                <span class="grid size-6 shrink-0 place-items-center rounded-full bg-[#D1FAE5]">
                    <x-admin.icon name="check" class="size-4" />
                </span>
                <span>{{ $statusMessage }}</span>
            </div>
        @endif

        <div class="grid items-start gap-4 lg:grid-cols-[minmax(0,1fr)_320px]">
            <section class="rounded-xl border border-[#E5E7EB] bg-white p-5 sm:p-6">
                <div class="flex items-start justify-between gap-5">
                    <div class="flex items-start gap-3.5">
                        <span @class([
                            'grid size-10 shrink-0 place-items-center rounded-[10px]',
                            'bg-[#ECFDF5] text-[#059669]' => $twoFactorEnabled,
                            'bg-[#FFF7ED] text-[#EA580C]' => $twoFactorPending,
                            'bg-[#EEF2FF] text-[#6366F1]' => ! $twoFactorEnabled && ! $twoFactorPending,
                        ])>
                            <x-admin.icon name="shield" class="size-5" />
                        </span>
                        <div>
                            <h3 class="text-[15px] font-bold text-[#111827]">Two-factor authentication</h3>
                            <p class="mt-1 text-[12px] leading-5 text-[#6B7280]">Require a time-based code when signing in.</p>
                        </div>
                    </div>

                    <span @class([
                        'shrink-0 rounded-md px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide',
                        'bg-[#ECFDF5] text-[#047857]' => $twoFactorEnabled,
                        'bg-[#FFF7ED] text-[#C2410C]' => $twoFactorPending,
                        'bg-[#F3F4F6] text-[#6B7280]' => ! $twoFactorEnabled && ! $twoFactorPending,
                    ])>
                        {{ $twoFactorEnabled ? 'Enabled' : ($twoFactorPending ? 'Setup required' : 'Disabled') }}
                    </span>
                </div>

                @if (! $twoFactorEnabled && ! $twoFactorPending)
                    <div class="mt-6 border-t border-[#E5E7EB] pt-6">
                        <div class="rounded-[10px] bg-[#F9FAFB] p-4">
                            <div class="flex items-start gap-3">
                                <x-admin.icon name="info" class="mt-0.5 size-[18px] text-[#6366F1]" />
                                <p class="text-[12px] leading-5 text-[#6B7280]">You will need a TOTP-compatible authenticator app. After scanning the setup code, sign-ins will require both your password and a six-digit code.</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('two-factor.enable') }}" class="mt-5">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-[#6366F1] px-4 py-2.5 text-[13px] font-semibold text-white transition-colors hover:bg-[#5558E6]">
                                <x-admin.icon name="shield" class="size-4" />
                                Enable 2FA
                            </button>
                        </form>
                    </div>
                @endif

                @if ($twoFactorPending)
                    <div class="mt-6 border-t border-[#E5E7EB] pt-6">
                        <div class="grid gap-7 md:grid-cols-[220px_minmax(0,1fr)]">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wider text-[#6B7280]">Step 1</p>
                                <h4 class="mt-1 text-[14px] font-bold text-[#111827]">Scan this QR code</h4>
                                <p class="mt-1 text-[11px] leading-5 text-[#6B7280]">Use any TOTP-compatible authenticator app.</p>

                                <div class="mt-4 aspect-square w-full max-w-[196px] rounded-[10px] border border-[#E5E7EB] bg-white p-3 [&>svg]:h-full [&>svg]:w-full" aria-label="Two-factor authentication QR code">
                                    {!! $qrCodeSvg !!}
                                </div>
                            </div>

                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wider text-[#6B7280]">Step 2</p>
                                <h4 class="mt-1 text-[14px] font-bold text-[#111827]">Verify the connection</h4>
                                <p class="mt-1 text-[11px] leading-5 text-[#6B7280]">Enter the six-digit code generated by your app.</p>

                                <form method="POST" action="{{ route('two-factor.confirm') }}" class="mt-5 max-w-xs">
                                    @csrf
                                    <label for="code" class="mb-2 block text-[12px] font-semibold text-[#374151]">Authentication code</label>
                                    <input
                                        id="code"
                                        name="code"
                                        type="text"
                                        inputmode="numeric"
                                        autocomplete="one-time-code"
                                        maxlength="6"
                                        required
                                        placeholder="000000"
                                        class="block h-11 w-full rounded-lg border border-[#D1D5DB] bg-white px-3.5 font-mono text-[14px] tracking-[0.2em] text-[#111827] outline-none transition focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10"
                                    >
                                    @error('code', 'confirmTwoFactorAuthentication')
                                        <p class="mt-2 text-[12px] text-[#B91C1C]">{{ $message }}</p>
                                    @enderror

                                    <button type="submit" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-[#6366F1] px-4 py-2.5 text-[13px] font-semibold text-white transition-colors hover:bg-[#5558E6]">
                                        <x-admin.icon name="check" class="size-4" />
                                        Confirm setup
                                    </button>
                                </form>

                                <details class="mt-5 max-w-md rounded-lg border border-[#E5E7EB] bg-[#F9FAFB] px-3.5 py-3 text-[12px] text-[#6B7280]">
                                    <summary class="cursor-pointer font-semibold text-[#374151]">Unable to scan?</summary>
                                    <p class="mt-3">Enter this setup key manually:</p>
                                    <code class="mt-2 block break-all rounded-md bg-white px-3 py-2 font-mono text-[11px] text-[#111827]">{{ $secretKey }}</code>
                                </details>

                                <form method="POST" action="{{ route('two-factor.disable') }}" class="mt-3">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg px-3 py-2 text-[12px] font-semibold text-[#6B7280] transition-colors hover:bg-[#F3F4F6] hover:text-[#111827]">Cancel setup</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($twoFactorEnabled)
                    <div class="mt-6 border-t border-[#E5E7EB] pt-6">
                        <div class="flex items-start gap-3">
                            <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-[#FFF7ED] text-[#EA580C]">
                                <x-admin.icon name="key" class="size-[18px]" />
                            </span>
                            <div>
                                <h4 class="text-[14px] font-bold text-[#111827]">Recovery codes</h4>
                                <p class="mt-1 text-[11px] leading-5 text-[#6B7280]">Store these codes in a secure place. Each code can be used once.</p>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-x-6 gap-y-2 rounded-[10px] bg-[#F9FAFB] p-4 font-mono text-[12px] text-[#374151] sm:grid-cols-2">
                            @foreach ($recoveryCodes as $recoveryCode)
                                <code>{{ $recoveryCode }}</code>
                            @endforeach
                        </div>

                        <div class="mt-5 flex flex-wrap gap-3">
                            <form method="POST" action="{{ route('two-factor.regenerate-recovery-codes') }}">
                                @csrf
                                <button type="submit" class="rounded-lg border border-[#D1D5DB] bg-white px-4 py-2.5 text-[12px] font-semibold text-[#374151] transition-colors hover:bg-[#F9FAFB]">Generate new codes</button>
                            </form>

                            <form method="POST" action="{{ route('two-factor.disable') }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg border border-[#FECACA] bg-white px-4 py-2.5 text-[12px] font-semibold text-[#B91C1C] transition-colors hover:bg-[#FEF2F2]">Disable 2FA</button>
                            </form>
                        </div>
                    </div>
                @endif
            </section>

            <aside class="rounded-xl border border-[#E5E7EB] bg-white p-5">
                <h3 class="text-[15px] font-bold text-[#111827]">Security overview</h3>
                <p class="mt-1 text-[12px] leading-5 text-[#6B7280]">How your account is protected.</p>

                <div class="mt-5 space-y-4">
                    <div class="flex items-start gap-3">
                        <span class="grid size-8 shrink-0 place-items-center rounded-lg bg-[#EEF2FF] text-[#6366F1]">
                            <x-admin.icon name="lock" class="size-4" />
                        </span>
                        <div>
                            <p class="text-[12px] font-semibold text-[#111827]">Password confirmation</p>
                            <p class="mt-0.5 text-[11px] leading-4 text-[#6B7280]">Required before changing sensitive settings.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <span class="grid size-8 shrink-0 place-items-center rounded-lg bg-[#EEF2FF] text-[#6366F1]">
                            <x-admin.icon name="shield" class="size-4" />
                        </span>
                        <div>
                            <p class="text-[12px] font-semibold text-[#111827]">Authenticator codes</p>
                            <p class="mt-0.5 text-[11px] leading-4 text-[#6B7280]">Time-based codes refresh every 30 seconds.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <span class="grid size-8 shrink-0 place-items-center rounded-lg bg-[#EEF2FF] text-[#6366F1]">
                            <x-admin.icon name="key" class="size-4" />
                        </span>
                        <div>
                            <p class="text-[12px] font-semibold text-[#111827]">Emergency access</p>
                            <p class="mt-0.5 text-[11px] leading-4 text-[#6B7280]">Recovery codes provide one-time access if your device is unavailable.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-5 rounded-[10px] bg-[#F9FAFB] p-4">
                    <p class="text-[11px] leading-5 text-[#6B7280]">Never share authentication or recovery codes. Blog administrators will not ask you for them.</p>
                </div>
            </aside>
        </div>
    </div>
@endsection
