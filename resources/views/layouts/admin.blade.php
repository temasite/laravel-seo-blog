<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">

        <title>@yield('title') · {{ config('app.name', 'Blog') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-stone-100 font-sans text-stone-950 antialiased">
        <header class="border-b border-stone-200 bg-white">
            <div class="mx-auto flex max-w-5xl flex-wrap items-center gap-5 px-6 py-4">
                <a href="{{ route('admin.dashboard') }}" class="mr-auto text-sm font-semibold">{{ config('app.name', 'Blog') }}</a>

                <nav aria-label="Admin navigation" class="flex items-center gap-5 text-sm text-stone-600">
                    <a href="{{ route('admin.dashboard') }}" @class(['font-medium hover:text-stone-950', 'text-stone-950' => request()->routeIs('admin.dashboard')])>Dashboard</a>
                    <a href="{{ route('admin.security') }}" @class(['font-medium hover:text-stone-950', 'text-stone-950' => request()->routeIs('admin.security')])>Security</a>
                </nav>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-stone-600 hover:text-stone-950">Sign out</button>
                </form>
            </div>
        </header>

        <main class="mx-auto max-w-5xl px-6 py-10">
            @yield('content')
        </main>
    </body>
</html>
