@extends('layouts.admin')

@section('title', 'Users')

@section('content')
    @php
        $currentUser = auth()->user();
    @endphp

    <div class="mx-auto max-w-7xl space-y-6">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <p class="text-[13px] font-medium text-[#6366F1]">Access management</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-[#111827]">Users</h2>
                <p class="mt-2 max-w-2xl text-[14px] leading-6 text-[#6B7280]">Manage administrator and manager accounts.</p>
            </div>

            @can('users.create')
                <a href="{{ route('admin.users.create') }}" class="inline-flex w-fit items-center gap-2 rounded-lg bg-[#6366F1] px-4 py-2.5 text-[13px] font-semibold text-white transition-colors hover:bg-[#5558E6]">
                    <x-admin.icon name="plus" class="size-4" />
                    New user
                </a>
            @endcan
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

        <form method="GET" action="{{ route('admin.users.index') }}" class="rounded-xl border border-[#E5E7EB] bg-white p-4">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-[minmax(240px,1fr)_180px_180px_auto]">
                <div class="relative">
                    <label for="search" class="sr-only">Search users</label>
                    <x-admin.icon name="search" class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-[#9CA3AF]" />
                    <input
                        id="search"
                        name="search"
                        type="search"
                        value="{{ request('search') }}"
                        placeholder="Search name or email"
                        class="block h-10 w-full rounded-lg border border-[#D1D5DB] bg-white pl-9 pr-3 text-[12px] text-[#111827] outline-none transition placeholder:text-[#9CA3AF] focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10"
                    >
                </div>

                <div>
                    <label for="role" class="sr-only">Role</label>
                    <select id="role" name="role" class="block h-10 w-full rounded-lg border border-[#D1D5DB] bg-white px-3 text-[12px] text-[#374151] outline-none transition focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10">
                        <option value="">All roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" @selected(request('role') === $role->name)>{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="sr-only">Status</label>
                    <select id="status" name="status" class="block h-10 w-full rounded-lg border border-[#D1D5DB] bg-white px-3 text-[12px] text-[#374151] outline-none transition focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10">
                        <option value="">All statuses</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ ucfirst($status->value) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex h-10 flex-1 items-center justify-center rounded-lg bg-[#111827] px-4 text-[12px] font-semibold text-white transition-colors hover:bg-[#1F2937]">Filter</button>
                    @if (request()->hasAny(['search', 'role', 'status']))
                        <a href="{{ route('admin.users.index') }}" class="flex h-10 items-center justify-center rounded-lg px-3 text-[12px] font-semibold text-[#6B7280] transition-colors hover:bg-[#F3F4F6] hover:text-[#111827]">Clear</a>
                    @endif
                </div>
            </div>
        </form>

        @if ($users->isEmpty())
            <div class="rounded-xl border border-[#E5E7EB] bg-white px-6 py-14 text-center">
                <span class="mx-auto grid size-11 place-items-center rounded-[10px] bg-[#EEF2FF] text-[#6366F1]">
                    <x-admin.icon name="users" class="size-5" />
                </span>
                <h3 class="mt-4 text-[14px] font-bold text-[#111827]">No users found</h3>
                <p class="mt-1 text-[12px] text-[#6B7280]">Try changing the filters or create a new account.</p>
            </div>
        @else
            <div class="hidden overflow-hidden rounded-xl border border-[#E5E7EB] bg-white md:block">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="border-b border-[#E5E7EB] bg-[#F9FAFB]">
                            <tr class="text-left text-[10px] font-semibold uppercase tracking-wider text-[#6B7280]">
                                <th scope="col" class="px-5 py-3">User</th>
                                <th scope="col" class="px-5 py-3">Role</th>
                                <th scope="col" class="px-5 py-3">Status</th>
                                <th scope="col" class="px-5 py-3">Last sign in</th>
                                <th scope="col" class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F3F4F6]">
                            @foreach ($users as $managedUser)
                                @php
                                    $role = $managedUser->roles->first()?->name;
                                    $isActive = $managedUser->status === \App\Enums\UserStatus::Active;
                                @endphp
                                <tr class="text-[12px]">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="grid size-9 shrink-0 place-items-center rounded-full bg-[#EEF2FF] text-[11px] font-bold text-[#6366F1]">{{ mb_strtoupper(mb_substr($managedUser->name, 0, 1)) }}</span>
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <p class="truncate font-semibold text-[#111827]">{{ $managedUser->name }}</p>
                                                    @if ($managedUser->is($currentUser))
                                                        <span class="rounded bg-[#F3F4F6] px-1.5 py-0.5 text-[9px] font-semibold text-[#6B7280]">You</span>
                                                    @endif
                                                </div>
                                                <p class="mt-0.5 truncate text-[11px] text-[#6B7280]">{{ $managedUser->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="rounded-md bg-[#EEF2FF] px-2 py-1 text-[10px] font-semibold text-[#6366F1]">{{ $role ? ucfirst($role) : 'No role' }}</span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span @class([
                                            'rounded-md px-2 py-1 text-[10px] font-bold uppercase tracking-wide',
                                            'bg-[#ECFDF5] text-[#047857]' => $isActive,
                                            'bg-[#FEF2F2] text-[#B91C1C]' => ! $isActive,
                                        ])>{{ ucfirst($managedUser->status->value) }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-[#6B7280]">
                                        {{ $managedUser->last_login_at?->format('M j, Y · H:i') ?? 'Never' }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            @can('users.update')
                                                <a href="{{ route('admin.users.edit', $managedUser) }}" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-2 font-semibold text-[#4B5563] transition-colors hover:bg-[#F3F4F6] hover:text-[#111827]">
                                                    <x-admin.icon name="edit" class="size-3.5" />
                                                    Edit
                                                </a>
                                            @endcan

                                            @if (! $managedUser->is($currentUser))
                                                @if ($isActive)
                                                    @can('users.suspend')
                                                        <form method="POST" action="{{ route('admin.users.suspend', $managedUser) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="rounded-lg px-2.5 py-2 font-semibold text-[#B91C1C] transition-colors hover:bg-[#FEF2F2]">Suspend</button>
                                                        </form>
                                                    @endcan
                                                @else
                                                    @can('users.restore')
                                                        <form method="POST" action="{{ route('admin.users.restore', $managedUser) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="rounded-lg px-2.5 py-2 font-semibold text-[#047857] transition-colors hover:bg-[#ECFDF5]">Restore</button>
                                                        </form>
                                                    @endcan
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-3 md:hidden">
                @foreach ($users as $managedUser)
                    @php
                        $role = $managedUser->roles->first()?->name;
                        $isActive = $managedUser->status === \App\Enums\UserStatus::Active;
                    @endphp
                    <article class="rounded-xl border border-[#E5E7EB] bg-white p-4">
                        <div class="flex items-start gap-3">
                            <span class="grid size-10 shrink-0 place-items-center rounded-full bg-[#EEF2FF] text-[12px] font-bold text-[#6366F1]">{{ mb_strtoupper(mb_substr($managedUser->name, 0, 1)) }}</span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="truncate text-[13px] font-semibold text-[#111827]">{{ $managedUser->name }}</h3>
                                    @if ($managedUser->is($currentUser))
                                        <span class="rounded bg-[#F3F4F6] px-1.5 py-0.5 text-[9px] font-semibold text-[#6B7280]">You</span>
                                    @endif
                                </div>
                                <p class="mt-0.5 truncate text-[11px] text-[#6B7280]">{{ $managedUser->email }}</p>
                            </div>
                            <span @class([
                                'rounded-md px-2 py-1 text-[9px] font-bold uppercase tracking-wide',
                                'bg-[#ECFDF5] text-[#047857]' => $isActive,
                                'bg-[#FEF2F2] text-[#B91C1C]' => ! $isActive,
                            ])>{{ ucfirst($managedUser->status->value) }}</span>
                        </div>

                        <div class="mt-4 flex items-center justify-between border-t border-[#F3F4F6] pt-3">
                            <div>
                                <span class="text-[10px] text-[#9CA3AF]">Role</span>
                                <p class="mt-0.5 text-[11px] font-semibold text-[#374151]">{{ $role ? ucfirst($role) : 'No role' }}</p>
                            </div>
                            <div class="flex items-center gap-1">
                                @can('users.update')
                                    <a href="{{ route('admin.users.edit', $managedUser) }}" class="rounded-lg px-2.5 py-2 text-[11px] font-semibold text-[#4B5563] hover:bg-[#F3F4F6]">Edit</a>
                                @endcan

                                @if (! $managedUser->is($currentUser))
                                    @if ($isActive)
                                        @can('users.suspend')
                                            <form method="POST" action="{{ route('admin.users.suspend', $managedUser) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="rounded-lg px-2.5 py-2 text-[11px] font-semibold text-[#B91C1C] hover:bg-[#FEF2F2]">Suspend</button>
                                            </form>
                                        @endcan
                                    @else
                                        @can('users.restore')
                                            <form method="POST" action="{{ route('admin.users.restore', $managedUser) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="rounded-lg px-2.5 py-2 text-[11px] font-semibold text-[#047857] hover:bg-[#ECFDF5]">Restore</button>
                                            </form>
                                        @endcan
                                    @endif
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($users->hasPages())
                <div>
                    {{ $users->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
