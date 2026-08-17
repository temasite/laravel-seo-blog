@extends('layouts.auth')

@section('title', 'Confirm password')

@section('content')
    <main class="flex min-h-screen items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <h1 class="text-2xl font-bold tracking-tight text-[#111827]">Confirm your password</h1>
            <p class="mt-2 text-sm leading-6 text-[#6B7280]">This security setting requires your current password.</p>

            <form method="POST" action="{{ route('password.confirm.store') }}" class="mt-8 space-y-5">
                @csrf

                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-[#374151]">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        autofocus
                        class="block h-11 w-full rounded-lg border border-[#D1D5DB] bg-white px-3.5 text-sm text-[#111827] shadow-sm outline-none transition focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10"
                    >
                    @error('password')
                        <p class="mt-2 text-sm text-[#B91C1C]">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="flex h-11 w-full items-center justify-center rounded-lg bg-[#6366F1] px-4 text-sm font-semibold text-white transition hover:bg-[#5558E6] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#6366F1]">Confirm</button>
            </form>

            <a href="{{ route('admin.dashboard') }}" class="mt-4 flex h-11 w-full items-center justify-center rounded-lg text-sm font-medium text-[#6B7280] transition hover:bg-white hover:text-[#111827]">
                Back to dashboard
            </a>
        </div>
    </main>
@endsection
