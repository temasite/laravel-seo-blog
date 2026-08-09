@extends('layouts.auth')

@section('title', 'Confirm password')

@section('content')
    <main class="flex min-h-screen items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <h1 class="text-2xl font-semibold tracking-tight">Confirm your password</h1>
            <p class="mt-2 text-sm leading-6 text-stone-500">This security setting requires your current password.</p>

            <form method="POST" action="{{ route('password.confirm.store') }}" class="mt-8 space-y-5">
                @csrf

                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-stone-800">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        autofocus
                        class="block h-11 w-full rounded-lg border border-stone-300 bg-white px-3.5 text-sm outline-none focus:border-stone-600 focus:ring-3 focus:ring-stone-950/5"
                    >
                    @error('password')
                        <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="flex h-11 w-full items-center justify-center rounded-lg bg-stone-950 px-4 text-sm font-semibold text-white hover:bg-stone-800">Confirm</button>
            </form>
        </div>
    </main>
@endsection
