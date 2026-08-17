@extends('layouts.auth')

@section('title', 'Two-factor authentication')

@section('content')
    <main class="flex min-h-screen items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <a href="{{ route('login') }}" class="mb-10 inline-block text-sm font-semibold text-[#111827]">
                {{ config('app.name', 'Blog') }}
            </a>

            <h1 class="text-2xl font-bold tracking-tight text-[#111827]">Authentication code</h1>
            <p class="mt-2 text-sm leading-6 text-[#6B7280]">Enter the six-digit code from your authenticator app.</p>

            <form method="POST" action="{{ route('two-factor.login.store') }}" class="mt-8 space-y-5">
                @csrf

                <div>
                    <label for="code" class="mb-2 block text-sm font-medium text-[#374151]">Code</label>
                    <input
                        id="code"
                        name="code"
                        type="text"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        maxlength="6"
                        autofocus
                        class="block h-11 w-full rounded-lg border border-[#D1D5DB] bg-white px-3.5 font-mono text-sm tracking-[0.2em] text-[#111827] shadow-sm outline-none transition focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10"
                    >
                    @error('code')
                        <p class="mt-2 text-sm text-[#B91C1C]">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="flex h-11 w-full items-center justify-center rounded-lg bg-[#6366F1] px-4 text-sm font-semibold text-white transition hover:bg-[#5558E6] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#6366F1]">Continue</button>
            </form>

            <details class="mt-7 border-t border-[#E5E7EB] pt-6">
                <summary class="cursor-pointer text-sm font-medium text-[#4B5563]">Use a recovery code</summary>

                <form method="POST" action="{{ route('two-factor.login.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label for="recovery_code" class="mb-2 block text-sm font-medium text-[#374151]">Recovery code</label>
                        <input
                            id="recovery_code"
                            name="recovery_code"
                            type="text"
                            autocomplete="one-time-code"
                            class="block h-11 w-full rounded-lg border border-[#D1D5DB] bg-white px-3.5 font-mono text-sm text-[#111827] shadow-sm outline-none transition focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10"
                        >
                        @error('recovery_code')
                            <p class="mt-2 text-sm text-[#B91C1C]">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="flex h-11 w-full items-center justify-center rounded-lg border border-[#D1D5DB] bg-white px-4 text-sm font-semibold text-[#374151] transition hover:bg-[#F9FAFB]">Use recovery code</button>
                </form>
            </details>
        </div>
    </main>
@endsection
