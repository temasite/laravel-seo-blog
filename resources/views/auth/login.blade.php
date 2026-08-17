@extends('layouts.auth')

@section('title', 'Admin sign in')

@section('content')
    <main class="flex min-h-screen items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <div class="mb-8">
                <h1 class="text-2xl font-bold tracking-tight text-[#111827]">Sign in</h1>
                <p class="mt-2 text-sm text-[#6B7280]">Continue to the administration panel.</p>
            </div>

            @if (session('status'))
                <div role="status" class="mb-5 rounded-lg border border-[#A7F3D0] bg-[#ECFDF5] px-3.5 py-3 text-sm text-[#047857]">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div role="alert" class="mb-5 rounded-lg border border-[#FECACA] bg-[#FEF2F2] px-3.5 py-3 text-sm text-[#B91C1C]">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-[#374151]">Email</label>
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
                            'block h-11 w-full rounded-lg border bg-white px-3.5 text-sm text-[#111827] shadow-sm outline-none transition placeholder:text-[#9CA3AF] focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10',
                            'border-[#FCA5A5]' => $errors->has('email'),
                            'border-[#D1D5DB]' => ! $errors->has('email'),
                        ])
                        placeholder="name@example.com"
                    >
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-[#374151]">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        class="block h-11 w-full rounded-lg border border-[#D1D5DB] bg-white px-3.5 text-sm text-[#111827] shadow-sm outline-none transition placeholder:text-[#9CA3AF] focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10"
                        placeholder="Enter your password"
                    >
                </div>

                <label class="flex w-fit cursor-pointer items-center gap-2.5 text-sm text-[#6B7280]">
                    <input name="remember" type="checkbox" value="1" class="size-4 rounded border-[#D1D5DB] accent-[#6366F1] focus:ring-[#6366F1]/20">
                    <span>Remember me</span>
                </label>

                <button type="submit" class="flex h-11 w-full items-center justify-center rounded-lg bg-[#6366F1] px-4 text-sm font-semibold text-white transition hover:bg-[#5558E6] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#6366F1]">
                    Sign in
                </button>
            </form>
        </div>
    </main>
@endsection
