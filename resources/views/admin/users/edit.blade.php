@extends('layouts.admin')

@section('title', 'Edit user')

@section('content')
    @php
        $currentUser = auth()->user();
        $isCurrentUser = $managedUser->is($currentUser);
        $currentRole = $managedUser->roles->first()?->name;
        $isActive = $managedUser->status === \App\Enums\UserStatus::Active;
    @endphp

    <div class="mx-auto max-w-6xl space-y-6">
        <div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-[12px] font-semibold text-[#6B7280] transition-colors hover:text-[#111827]">
                <x-admin.icon name="arrow-left" class="size-4" />
                Back to users
            </a>
            <h2 class="mt-4 text-2xl font-bold tracking-tight text-[#111827]">Edit {{ $managedUser->name }}</h2>
            <p class="mt-2 text-[14px] leading-6 text-[#6B7280]">{{ $managedUser->email }}</p>
        </div>

        @if (session('status'))
            <div role="status" class="flex items-center gap-3 rounded-[10px] border border-[#A7F3D0] bg-[#ECFDF5] px-4 py-3 text-[13px] font-medium text-[#047857]">
                <span class="grid size-6 shrink-0 place-items-center rounded-full bg-[#D1FAE5]">
                    <x-admin.icon name="check" class="size-4" />
                </span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->has('user'))
            <div role="alert" class="rounded-[10px] border border-[#FECACA] bg-[#FEF2F2] px-4 py-3 text-[13px] font-medium text-[#B91C1C]">
                {{ $errors->first('user') }}
            </div>
        @endif

        <div class="grid items-start gap-4 lg:grid-cols-[minmax(0,1fr)_320px]">
            <div class="space-y-4">
                <section class="rounded-xl border border-[#E5E7EB] bg-white p-5 sm:p-6">
                    <div class="flex items-start gap-3.5">
                        <span class="grid size-10 shrink-0 place-items-center rounded-[10px] bg-[#EEF2FF] text-[#6366F1]">
                            <x-admin.icon name="user" class="size-5" />
                        </span>
                        <div>
                            <h3 class="text-[15px] font-bold text-[#111827]">User information</h3>
                            <p class="mt-1 text-[12px] leading-5 text-[#6B7280]">Update the account name, login email, and role.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.users.update', $managedUser) }}" class="mt-6">
                        @csrf
                        @method('PUT')

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="name" class="mb-2 block text-[12px] font-semibold text-[#374151]">Name</label>
                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    value="{{ old('name', $managedUser->name) }}"
                                    autocomplete="name"
                                    required
                                    class="block h-11 w-full rounded-lg border border-[#D1D5DB] bg-white px-3.5 text-[13px] text-[#111827] outline-none transition focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10"
                                >
                                @error('name')
                                    <p class="mt-2 text-[12px] text-[#B91C1C]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="mb-2 block text-[12px] font-semibold text-[#374151]">Login email</label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email', $managedUser->email) }}"
                                    autocomplete="username"
                                    required
                                    class="block h-11 w-full rounded-lg border border-[#D1D5DB] bg-white px-3.5 text-[13px] text-[#111827] outline-none transition focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10"
                                >
                                @error('email')
                                    <p class="mt-2 text-[12px] text-[#B91C1C]">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-5">
                            <label for="role" class="mb-2 block text-[12px] font-semibold text-[#374151]">Role</label>
                            @if ($isCurrentUser)
                                <input type="hidden" name="role" value="{{ $currentRole }}">
                                <select id="role" disabled class="block h-11 w-full cursor-not-allowed rounded-lg border border-[#E5E7EB] bg-[#F9FAFB] px-3.5 text-[13px] text-[#6B7280]">
                                    <option>{{ ucfirst($currentRole ?? 'No role') }}</option>
                                </select>
                                <p class="mt-2 text-[11px] text-[#6B7280]">You cannot change your own role.</p>
                            @else
                                <select id="role" name="role" required class="block h-11 w-full rounded-lg border border-[#D1D5DB] bg-white px-3.5 text-[13px] text-[#111827] outline-none transition focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}" @selected(old('role', $currentRole) === $role->name)>{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                            @endif
                            @error('role')
                                <p class="mt-2 text-[12px] text-[#B91C1C]">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-[#6366F1] px-4 py-2.5 text-[13px] font-semibold text-white transition-colors hover:bg-[#5558E6]">
                            <x-admin.icon name="check" class="size-4" />
                            Save changes
                        </button>
                    </form>
                </section>

                @can('users.reset-password')
                    <section class="rounded-xl border border-[#E5E7EB] bg-white p-5 sm:p-6">
                        <div class="flex items-start gap-3.5">
                            <span class="grid size-10 shrink-0 place-items-center rounded-[10px] bg-[#EEF2FF] text-[#6366F1]">
                                <x-admin.icon name="key" class="size-5" />
                            </span>
                            <div>
                                <h3 class="text-[15px] font-bold text-[#111827]">Set a new password</h3>
                                <p class="mt-1 text-[12px] leading-5 text-[#6B7280]">The user will use this password on their next sign in.</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.users.password.update', $managedUser) }}" class="mt-6">
                            @csrf
                            @method('PUT')

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="password" class="mb-2 block text-[12px] font-semibold text-[#374151]">New password</label>
                                    <input id="password" name="password" type="password" autocomplete="new-password" required class="block h-11 w-full rounded-lg border border-[#D1D5DB] bg-white px-3.5 text-[13px] text-[#111827] outline-none transition focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10">
                                    @error('password')
                                        <p class="mt-2 text-[12px] text-[#B91C1C]">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="mb-2 block text-[12px] font-semibold text-[#374151]">Confirm password</label>
                                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="block h-11 w-full rounded-lg border border-[#D1D5DB] bg-white px-3.5 text-[13px] text-[#111827] outline-none transition focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10">
                                </div>
                            </div>

                            <button type="submit" class="mt-5 inline-flex items-center gap-2 rounded-lg border border-[#D1D5DB] bg-white px-4 py-2.5 text-[13px] font-semibold text-[#374151] transition-colors hover:bg-[#F9FAFB]">
                                <x-admin.icon name="key" class="size-4 text-[#6366F1]" />
                                Update password
                            </button>
                        </form>
                    </section>
                @endcan
            </div>

            <aside class="space-y-4">
                <section class="rounded-xl border border-[#E5E7EB] bg-white p-5">
                    <div class="flex items-center gap-3">
                        <span class="grid size-11 shrink-0 place-items-center rounded-full bg-[#EEF2FF] text-[13px] font-bold text-[#6366F1]">{{ mb_strtoupper(mb_substr($managedUser->name, 0, 1)) }}</span>
                        <div class="min-w-0">
                            <h3 class="truncate text-[14px] font-bold text-[#111827]">{{ $managedUser->name }}</h3>
                            <p class="mt-0.5 truncate text-[11px] text-[#6B7280]">User #{{ $managedUser->id }}</p>
                        </div>
                    </div>

                    <dl class="mt-5 divide-y divide-[#F3F4F6] border-t border-[#E5E7EB] pt-2">
                        <div class="flex items-center justify-between gap-4 py-3">
                            <dt class="text-[11px] text-[#6B7280]">Status</dt>
                            <dd @class([
                                'rounded-md px-2 py-1 text-[9px] font-bold uppercase tracking-wide',
                                'bg-[#ECFDF5] text-[#047857]' => $isActive,
                                'bg-[#FEF2F2] text-[#B91C1C]' => ! $isActive,
                            ])>{{ ucfirst($managedUser->status->value) }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-3">
                            <dt class="text-[11px] text-[#6B7280]">Role</dt>
                            <dd class="text-[11px] font-semibold text-[#374151]">{{ ucfirst($currentRole ?? 'No role') }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-3">
                            <dt class="text-[11px] text-[#6B7280]">Last sign in</dt>
                            <dd class="text-right text-[11px] font-semibold text-[#374151]">{{ $managedUser->last_login_at?->format('M j, Y · H:i') ?? 'Never' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-3">
                            <dt class="text-[11px] text-[#6B7280]">Created</dt>
                            <dd class="text-right text-[11px] font-semibold text-[#374151]">{{ $managedUser->created_at->format('M j, Y · H:i') }}</dd>
                        </div>
                    </dl>
                </section>

                @if (! $isCurrentUser)
                    <section class="rounded-xl border border-[#E5E7EB] bg-white p-5">
                        <h3 class="text-[14px] font-bold text-[#111827]">Account access</h3>
                        <p class="mt-1 text-[11px] leading-5 text-[#6B7280]">{{ $isActive ? 'Suspending blocks future sign-ins.' : 'Restore this account to allow sign-ins again.' }}</p>

                        @if ($isActive)
                            @can('users.suspend')
                                <form method="POST" action="{{ route('admin.users.suspend', $managedUser) }}" class="mt-4">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg border border-[#FECACA] bg-white px-4 py-2.5 text-[12px] font-semibold text-[#B91C1C] transition-colors hover:bg-[#FEF2F2]">Suspend account</button>
                                </form>
                            @endcan
                        @else
                            @can('users.restore')
                                <form method="POST" action="{{ route('admin.users.restore', $managedUser) }}" class="mt-4">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg border border-[#A7F3D0] bg-white px-4 py-2.5 text-[12px] font-semibold text-[#047857] transition-colors hover:bg-[#ECFDF5]">Restore account</button>
                                </form>
                            @endcan
                        @endif
                    </section>

                    @can('users.delete')
                        <section class="rounded-xl border border-[#FECACA] bg-[#FFFBFB] p-5">
                            <h3 class="text-[14px] font-bold text-[#991B1B]">Permanent deletion</h3>
                            <p class="mt-1 text-[11px] leading-5 text-[#7F1D1D]">Delete this user and all account access permanently. This action cannot be undone.</p>

                            <form
                                method="POST"
                                action="{{ route('admin.users.destroy', $managedUser) }}"
                                class="mt-4"
                                onsubmit="return confirm('Delete this user permanently? This action cannot be undone.')"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg bg-[#B91C1C] px-4 py-2.5 text-[12px] font-semibold text-white transition-colors hover:bg-[#991B1B]">Delete user permanently</button>
                            </form>
                        </section>
                    @endcan
                @endif
            </aside>
        </div>
    </div>
@endsection
