@extends('layouts.auth')

@section('title', 'Admin sign in')

@section('content')
    <main class="flex min-h-screen items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <div class="mb-8">
                <h1 class="text-2xl font-semibold tracking-tight text-stone-950">Sign in</h1>
            </div>

            @if (session('status'))
                <div role="status" class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-3.5 py-3 text-sm text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div role="alert" class="mb-5 rounded-lg border border-red-200 bg-red-50 px-3.5 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-stone-800">Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        inputmode="email"
                        required
                        autofocus
                        @class([
                            'block h-11 w-full rounded-lg border bg-white px-3.5 text-sm text-stone-950 shadow-sm outline-none transition placeholder:text-stone-400 focus:border-stone-600 focus:ring-3 focus:ring-stone-950/5',
                            'border-red-300' => $errors->has('email'),
                            'border-stone-300' => ! $errors->has('email'),
                        ])
                        placeholder="name@example.com"
                    >
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-stone-800">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        class="block h-11 w-full rounded-lg border border-stone-300 bg-white px-3.5 text-sm text-stone-950 shadow-sm outline-none transition placeholder:text-stone-400 focus:border-stone-600 focus:ring-3 focus:ring-stone-950/5"
                        placeholder="Enter your password"
                    >
                </div>

                <label class="flex w-fit cursor-pointer items-center gap-2.5 text-sm text-stone-600">
                    <input name="remember" type="checkbox" value="1" class="size-4 rounded border-stone-300 accent-stone-950 focus:ring-stone-950/20">
                    <span>Remember me</span>
                </label>

                <button type="submit" class="flex h-11 w-full items-center justify-center rounded-lg bg-stone-950 px-4 text-sm font-semibold text-white transition hover:bg-stone-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-stone-950">
                    Sign in
                </button>
            </form>
        </div>
    </main>
@endsection
