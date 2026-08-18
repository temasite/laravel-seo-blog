@extends('layouts.admin')

@section('title', 'New user')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-[12px] font-semibold text-[#6B7280] transition-colors hover:text-[#111827]">
                <x-admin.icon name="arrow-left" class="size-4" />
                Back to users
            </a>
            <h2 class="mt-4 text-2xl font-bold tracking-tight text-[#111827]">Create a user</h2>
            <p class="mt-2 text-[14px] leading-6 text-[#6B7280]">Add an administrator or manager account.</p>
        </div>

        <section class="rounded-xl border border-[#E5E7EB] bg-white p-5 sm:p-6">
            <div class="flex items-start gap-3.5">
                <span class="grid size-10 shrink-0 place-items-center rounded-[10px] bg-[#EEF2FF] text-[#6366F1]">
                    <x-admin.icon name="user" class="size-5" />
                </span>
                <div>
                    <h3 class="text-[15px] font-bold text-[#111827]">Account information</h3>
                    <p class="mt-1 text-[12px] leading-5 text-[#6B7280]">The account will be active immediately.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="mt-6">
                @csrf

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="name" class="mb-2 block text-[12px] font-semibold text-[#374151]">Name</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            autocomplete="name"
                            required
                            autofocus
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
                            value="{{ old('email') }}"
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
                    <select id="role" name="role" required class="block h-11 w-full rounded-lg border border-[#D1D5DB] bg-white px-3.5 text-[13px] text-[#111827] outline-none transition focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10">
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" @selected(old('role', 'manager') === $role->name)>{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <p class="mt-2 text-[12px] text-[#B91C1C]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="password" class="mb-2 block text-[12px] font-semibold text-[#374151]">Password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="new-password"
                            required
                            class="block h-11 w-full rounded-lg border border-[#D1D5DB] bg-white px-3.5 text-[13px] text-[#111827] outline-none transition focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10"
                        >
                        @error('password')
                            <p class="mt-2 text-[12px] text-[#B91C1C]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-[12px] font-semibold text-[#374151]">Confirm password</label>
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

                <div class="mt-6 flex flex-wrap items-center gap-3 border-t border-[#E5E7EB] pt-5">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-[#6366F1] px-4 py-2.5 text-[13px] font-semibold text-white transition-colors hover:bg-[#5558E6]">
                        <x-admin.icon name="plus" class="size-4" />
                        Create user
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="rounded-lg px-4 py-2.5 text-[13px] font-semibold text-[#6B7280] transition-colors hover:bg-[#F3F4F6] hover:text-[#111827]">Cancel</a>
                </div>
            </form>
        </section>
    </div>
@endsection
