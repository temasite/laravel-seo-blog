@extends('layouts.auth')

@section('title', 'Two-factor authentication')

@section('content')
    <main class="flex min-h-screen items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <a href="{{ route('login') }}" class="mb-10 inline-flex items-center gap-2.5 text-sm font-semibold text-stone-950">
                <span class="grid size-8 place-items-center rounded-lg bg-stone-950 text-white">2</span>
                <span>{{ config('app.name', 'Blog') }}</span>
            </a>

            <h1 class="text-2xl font-semibold tracking-tight">Authentication code</h1>
            <p class="mt-2 text-sm leading-6 text-stone-500">Enter the six-digit code from your authenticator app.</p>

            <form method="POST" action="{{ route('two-factor.login.store') }}" class="mt-8 space-y-5">
                @csrf

                <div>
                    <label for="code" class="mb-2 block text-sm font-medium text-stone-800">Code</label>
                    <input
                        id="code"
                        name="code"
                        type="text"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        maxlength="6"
                        autofocus
                        class="block h-11 w-full rounded-lg border border-stone-300 bg-white px-3.5 font-mono text-sm tracking-[0.2em] outline-none focus:border-stone-600 focus:ring-3 focus:ring-stone-950/5"
                    >
                    @error('code')
                        <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="flex h-11 w-full items-center justify-center rounded-lg bg-stone-950 px-4 text-sm font-semibold text-white hover:bg-stone-800">Continue</button>
            </form>

            <details class="mt-7 border-t border-stone-200 pt-6">
                <summary class="cursor-pointer text-sm font-medium text-stone-700">Use a recovery code</summary>

                <form method="POST" action="{{ route('two-factor.login.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label for="recovery_code" class="mb-2 block text-sm font-medium text-stone-800">Recovery code</label>
                        <input
                            id="recovery_code"
                            name="recovery_code"
                            type="text"
                            autocomplete="one-time-code"
                            class="block h-11 w-full rounded-lg border border-stone-300 bg-white px-3.5 font-mono text-sm outline-none focus:border-stone-600 focus:ring-3 focus:ring-stone-950/5"
                        >
                        @error('recovery_code')
                            <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="flex h-11 w-full items-center justify-center rounded-lg border border-stone-300 bg-white px-4 text-sm font-semibold text-stone-800 hover:bg-stone-50">Use recovery code</button>
                </form>
            </details>
        </div>
    </main>
@endsection
