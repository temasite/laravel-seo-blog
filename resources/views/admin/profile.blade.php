@extends('layouts.admin')

@section('title', 'Profile')

@section('content')
    @php
        $user = auth()->user();
        $roles = $user->getRoleNames()
            ->map(fn (string $role): string => ucfirst($role))
            ->join(', ');
        $twoFactorEnabled = $user->hasEnabledTwoFactorAuthentication();
    @endphp

    <div class="mx-auto max-w-6xl space-y-6">
        <div>
            <p class="text-[13px] font-medium text-[#6366F1]">Personal account</p>
            <h2 class="mt-1 text-2xl font-bold tracking-tight text-[#111827]">Profile settings</h2>
            <p class="mt-2 max-w-2xl text-[14px] leading-6 text-[#6B7280]">Review your account details and update the credentials used to sign in.</p>
        </div>

        @if (session('status') === \Laravel\Fortify\Fortify::PROFILE_INFORMATION_UPDATED)
            <div role="status" class="flex items-center gap-3 rounded-[10px] border border-[#A7F3D0] bg-[#ECFDF5] px-4 py-3 text-[13px] font-medium text-[#047857]">
                <span class="grid size-6 shrink-0 place-items-center rounded-full bg-[#D1FAE5]">
                    <x-admin.icon name="check" class="size-4" />
                </span>
                <span>Profile information has been updated.</span>
            </div>
        @endif

        @if (session('status') === \Laravel\Fortify\Fortify::PASSWORD_UPDATED)
            <div role="status" class="flex items-center gap-3 rounded-[10px] border border-[#A7F3D0] bg-[#ECFDF5] px-4 py-3 text-[13px] font-medium text-[#047857]">
                <span class="grid size-6 shrink-0 place-items-center rounded-full bg-[#D1FAE5]">
                    <x-admin.icon name="check" class="size-4" />
                </span>
                <span>Password has been updated.</span>
            </div>
        @endif

        <div class="grid items-start gap-4 lg:grid-cols-[320px_minmax(0,1fr)]">
            <div class="space-y-4">
                <section class="rounded-xl border border-[#E5E7EB] bg-white p-5">
                    <div class="flex items-center gap-4">
                        <span class="grid size-14 shrink-0 place-items-center rounded-full bg-[#6366F1] text-lg font-bold text-white">
                            {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                        </span>
                        <div class="min-w-0">
                            <h3 class="truncate text-[16px] font-bold text-[#111827]">{{ $user->name }}</h3>
                            <p class="mt-0.5 truncate text-[12px] text-[#6B7280]">{{ $user->email }}</p>
                            <p class="mt-1 text-[11px] font-semibold text-[#6366F1]">{{ $roles ?: 'No role assigned' }}</p>
                        </div>
                    </div>

                    <div class="mt-5 border-t border-[#E5E7EB] pt-5">
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-[12px] text-[#6B7280]">Account status</span>
                            <span @class([
                                'rounded-md px-2 py-1 text-[10px] font-bold uppercase tracking-wide',
                                'bg-[#ECFDF5] text-[#047857]' => $user->status->value === 'active',
                                'bg-[#FEF2F2] text-[#B91C1C]' => $user->status->value !== 'active',
                            ])>{{ ucfirst($user->status->value) }}</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between gap-4">
                            <span class="text-[12px] text-[#6B7280]">Two-factor authentication</span>
                            <a href="{{ route('admin.security') }}" @class([
                                'text-[11px] font-semibold',
                                'text-[#059669]' => $twoFactorEnabled,
                                'text-[#EA580C]' => ! $twoFactorEnabled,
                            ])>{{ $twoFactorEnabled ? 'Enabled' : 'Disabled' }}</a>
                        </div>
                    </div>
                </section>

                <section class="rounded-xl border border-[#E5E7EB] bg-white p-5">
                    <h3 class="text-[15px] font-bold text-[#111827]">Account details</h3>

                    <dl class="mt-4 divide-y divide-[#F3F4F6]">
                        <div class="flex items-center justify-between gap-4 py-3 first:pt-0">
                            <dt class="text-[11px] text-[#6B7280]">User ID</dt>
                            <dd class="font-mono text-[11px] font-semibold text-[#374151]">#{{ $user->id }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-3">
                            <dt class="text-[11px] text-[#6B7280]">Last sign in</dt>
                            <dd class="text-right text-[11px] font-semibold text-[#374151]">{{ $user->last_login_at?->format('M j, Y · H:i') ?? 'Never' }}</dd>
                        </div>
                        @if ($user->suspended_at)
                            <div class="flex items-center justify-between gap-4 py-3">
                                <dt class="text-[11px] text-[#6B7280]">Suspended at</dt>
                                <dd class="text-right text-[11px] font-semibold text-[#B91C1C]">{{ $user->suspended_at->format('M j, Y · H:i') }}</dd>
                            </div>
                        @endif
                        <div class="flex items-center justify-between gap-4 py-3">
                            <dt class="text-[11px] text-[#6B7280]">Created</dt>
                            <dd class="text-right text-[11px] font-semibold text-[#374151]">{{ $user->created_at->format('M j, Y · H:i') }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-3 last:pb-0">
                            <dt class="text-[11px] text-[#6B7280]">Last updated</dt>
                            <dd class="text-right text-[11px] font-semibold text-[#374151]">{{ $user->updated_at->format('M j, Y · H:i') }}</dd>
                        </div>
                    </dl>
                </section>
            </div>

            <div class="space-y-4">
                <section class="rounded-xl border border-[#E5E7EB] bg-white p-5 sm:p-6">
                    <div class="flex items-start gap-3.5">
                        <span class="grid size-10 shrink-0 place-items-center rounded-[10px] bg-[#EEF2FF] text-[#6366F1]">
                            <x-admin.icon name="user" class="size-5" />
                        </span>
                        <div>
                            <h3 class="text-[15px] font-bold text-[#111827]">Profile information</h3>
                            <p class="mt-1 text-[12px] leading-5 text-[#6B7280]">Your email address is also your login.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('user-profile-information.update') }}" class="mt-6">
                        @csrf
                        @method('PUT')

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="name" class="mb-2 block text-[12px] font-semibold text-[#374151]">Name</label>
                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    value="{{ old('name', $user->name) }}"
                                    autocomplete="name"
                                    required
                                    class="block h-11 w-full rounded-lg border border-[#D1D5DB] bg-white px-3.5 text-[13px] text-[#111827] outline-none transition focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10"
                                >
                                @error('name', 'updateProfileInformation')
                                    <p class="mt-2 text-[12px] text-[#B91C1C]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="mb-2 block text-[12px] font-semibold text-[#374151]">Login email</label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email', $user->email) }}"
                                    autocomplete="username"
                                    required
                                    class="block h-11 w-full rounded-lg border border-[#D1D5DB] bg-white px-3.5 text-[13px] text-[#111827] outline-none transition focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10"
                                >
                                @error('email', 'updateProfileInformation')
                                    <p class="mt-2 text-[12px] text-[#B91C1C]">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="mt-5 inline-flex items-center gap-2 rounded-lg bg-[#6366F1] px-4 py-2.5 text-[13px] font-semibold text-white transition-colors hover:bg-[#5558E6]">
                            <x-admin.icon name="check" class="size-4" />
                            Save profile
                        </button>
                    </form>
                </section>

                <section class="rounded-xl border border-[#E5E7EB] bg-white p-5 sm:p-6">
                    <div class="flex items-start gap-3.5">
                        <span class="grid size-10 shrink-0 place-items-center rounded-[10px] bg-[#EEF2FF] text-[#6366F1]">
                            <x-admin.icon name="lock" class="size-5" />
                        </span>
                        <div>
                            <h3 class="text-[15px] font-bold text-[#111827]">Change password</h3>
                            <p class="mt-1 text-[12px] leading-5 text-[#6B7280]">Use a strong password that you do not reuse elsewhere.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('user-password.update') }}" class="mt-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="current_password" class="mb-2 block text-[12px] font-semibold text-[#374151]">Current password</label>
                            <input
                                id="current_password"
                                name="current_password"
                                type="password"
                                autocomplete="current-password"
                                required
                                class="block h-11 w-full rounded-lg border border-[#D1D5DB] bg-white px-3.5 text-[13px] text-[#111827] outline-none transition focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10"
                            >
                            @error('current_password', 'updatePassword')
                                <p class="mt-2 text-[12px] text-[#B91C1C]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="password" class="mb-2 block text-[12px] font-semibold text-[#374151]">New password</label>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    autocomplete="new-password"
                                    required
                                    class="block h-11 w-full rounded-lg border border-[#D1D5DB] bg-white px-3.5 text-[13px] text-[#111827] outline-none transition focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10"
                                >
                                @error('password', 'updatePassword')
                                    <p class="mt-2 text-[12px] text-[#B91C1C]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="mb-2 block text-[12px] font-semibold text-[#374151]">Confirm new password</label>
                                <input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    autocomplete="new-password"
                                    required
                                    class="block h-11 w-full rounded-lg border border-[#D1D5DB] bg-white px-3.5 text-[13px] text-[#111827] outline-none transition focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10"
                                >
                            </div>
                        </div>

                        <button type="submit" class="mt-5 inline-flex items-center gap-2 rounded-lg border border-[#D1D5DB] bg-white px-4 py-2.5 text-[13px] font-semibold text-[#374151] transition-colors hover:bg-[#F9FAFB]">
                            <x-admin.icon name="key" class="size-4 text-[#6366F1]" />
                            Update password
                        </button>
                    </form>
                </section>
            </div>
        </div>
    </div>
@endsection
