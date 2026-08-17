<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow">

        <title>@yield('title') · Admin · {{ config('app.name', 'Blog') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    @php
        $adminUser = auth()->user();
        $adminRole = ucfirst($adminUser?->getRoleNames()->first() ?? 'User');
        $adminInitial = mb_strtoupper(mb_substr($adminUser?->name ?? 'A', 0, 1));
    @endphp
    <body class="h-screen overflow-hidden bg-[#F5F5F7] font-sans text-[#111827] antialiased">
        <div class="flex h-full min-h-0" data-admin-shell>
            <button
                type="button"
                class="fixed inset-0 z-40 hidden bg-slate-950/55 backdrop-blur-[1px] lg:hidden"
                data-sidebar-overlay
                aria-label="Close navigation"
            ></button>

            <aside
                class="fixed inset-y-0 left-0 z-50 flex w-[260px] -translate-x-full flex-col overflow-hidden bg-[#0F172A] transition-transform duration-200 ease-out lg:static lg:translate-x-0"
                data-admin-sidebar
                aria-label="Admin navigation"
            >
                <div class="flex h-16 shrink-0 items-center gap-3 px-6">
                    <a href="{{ route('admin.dashboard') }}" class="min-w-0">
                        <span class="block min-w-0 leading-tight">
                            <span class="block truncate text-[17px] font-bold text-white">{{ config('app.name', 'Blog') }}</span>
                            <span class="block truncate text-[11px] text-[#94A3B8]">Administration panel</span>
                        </span>
                    </a>

                    <button
                        type="button"
                        class="ml-auto grid size-9 shrink-0 place-items-center rounded-lg text-[#94A3B8] hover:bg-[#1E293B] hover:text-white lg:hidden"
                        data-sidebar-close
                        aria-label="Close navigation"
                    >
                        <x-admin.icon name="x" class="size-5" />
                    </button>
                </div>

                <nav class="flex min-h-0 flex-1 flex-col overflow-y-auto px-3 pb-4 pt-5">
                    <p class="mb-2 px-3 text-[10px] font-semibold tracking-[0.16em] text-[#64748B]">MANAGEMENT</p>

                    <a
                        href="{{ route('admin.dashboard') }}"
                        @class([
                            'flex items-center gap-3 rounded-lg px-3 py-2.5 text-[14px] transition-colors',
                            'bg-[#1E293B] font-semibold text-white' => request()->routeIs('admin.dashboard'),
                            'text-[#94A3B8] hover:bg-[#1E293B]/60 hover:text-white' => ! request()->routeIs('admin.dashboard'),
                        ])
                    >
                        <x-admin.icon name="dashboard" @class(['size-[18px]', 'text-[#818CF8]' => request()->routeIs('admin.dashboard')]) />
                        <span>Dashboard</span>
                    </a>

                    @if (Route::has('admin.articles.index') && $adminUser?->can('articles.view'))
                        <a href="{{ route('admin.articles.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-[14px] text-[#94A3B8] transition-colors hover:bg-[#1E293B]/60 hover:text-white">
                            <x-admin.icon name="file" class="size-[18px]" />
                            <span>Articles</span>
                        </a>
                    @endif

                    @if (Route::has('admin.categories.index') && $adminUser?->can('categories.view'))
                        <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-[14px] text-[#94A3B8] transition-colors hover:bg-[#1E293B]/60 hover:text-white">
                            <x-admin.icon name="folder" class="size-[18px]" />
                            <span>Categories</span>
                        </a>
                    @endif

                    @if (Route::has('admin.users.index') && $adminUser?->can('users.view'))
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-[14px] text-[#94A3B8] transition-colors hover:bg-[#1E293B]/60 hover:text-white">
                            <x-admin.icon name="users" class="size-[18px]" />
                            <span>Managers</span>
                        </a>
                    @endif

                    <p class="mb-2 mt-7 px-3 text-[10px] font-semibold tracking-[0.16em] text-[#64748B]">ACCOUNT</p>

                    <a
                        href="{{ route('admin.security') }}"
                        @class([
                            'flex items-center gap-3 rounded-lg px-3 py-2.5 text-[14px] transition-colors',
                            'bg-[#1E293B] font-semibold text-white' => request()->routeIs('admin.security'),
                            'text-[#94A3B8] hover:bg-[#1E293B]/60 hover:text-white' => ! request()->routeIs('admin.security'),
                        ])
                    >
                        <x-admin.icon name="shield" @class(['size-[18px]', 'text-[#818CF8]' => request()->routeIs('admin.security')]) />
                        <span>Security</span>
                    </a>
                </nav>

                <div class="shrink-0 border-t border-[#1E293B] p-3">
                    <a
                        href="{{ route('admin.profile') }}"
                        @class([
                            'mb-1 flex items-center gap-3 rounded-lg px-3 py-2 transition-colors hover:bg-[#1E293B]',
                            'bg-[#1E293B]' => request()->routeIs('admin.profile'),
                        ])
                    >
                        <span class="grid size-9 shrink-0 place-items-center rounded-full bg-[#6366F1] text-xs font-bold text-white">{{ $adminInitial }}</span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-[13px] font-semibold text-white">{{ $adminUser?->name }}</span>
                            <span class="block truncate text-[11px] text-[#94A3B8]">{{ $adminRole }}</span>
                        </span>
                        <x-admin.icon name="arrow-right" class="size-4 text-[#64748B]" />
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-[13px] text-[#94A3B8] transition-colors hover:bg-[#1E293B] hover:text-white">
                            <x-admin.icon name="log-out" class="size-[18px]" />
                            <span>Sign out</span>
                        </button>
                    </form>
                </div>
            </aside>

            <div class="flex min-w-0 flex-1 flex-col">
                <header class="flex h-16 shrink-0 items-center justify-between gap-4 border-b border-[#E5E7EB] bg-white px-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <button
                            type="button"
                            class="-ml-1 grid size-9 shrink-0 place-items-center rounded-lg text-[#6B7280] hover:bg-[#F3F4F6] hover:text-[#111827] lg:hidden"
                            data-sidebar-open
                            aria-label="Open navigation"
                            aria-expanded="false"
                        >
                            <x-admin.icon name="menu" class="size-5" />
                        </button>
                        <h1 class="truncate text-[18px] font-bold text-[#111827] sm:text-[20px]">@yield('title')</h1>
                    </div>

                    <a href="{{ url('/') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-[13px] font-semibold text-[#6B7280] transition-colors hover:bg-[#F3F4F6] hover:text-[#111827]">
                        <span class="hidden sm:inline">View blog</span>
                        <x-admin.icon name="external-link" class="size-4" />
                    </a>
                </header>

                <main class="min-h-0 flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
