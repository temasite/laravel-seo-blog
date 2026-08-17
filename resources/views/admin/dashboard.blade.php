@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    @php
        $user = auth()->user();
    @endphp

    <div class="mx-auto max-w-7xl space-y-6">
        <div>
            <p class="text-[13px] font-medium text-[#6366F1]">Welcome back, {{ $user->name }}</p>
            <h2 class="mt-1 text-2xl font-bold tracking-tight text-[#111827]">Your publishing workspace</h2>
            <p class="mt-2 max-w-2xl text-[14px] leading-6 text-[#6B7280]">Manage the blog, your team, and account security from one place.</p>
        </div>

        <section class="rounded-xl border border-[#E5E7EB] bg-white p-5 sm:p-6">
            <div>
                <h3 class="text-[15px] font-bold text-[#111827]">Blog management</h3>
                <p class="mt-1 text-[12px] leading-5 text-[#6B7280]">Your permissions are ready for the next administration modules.</p>
            </div>

            <div class="mt-5 divide-y divide-[#F3F4F6]">
                <div class="flex items-center gap-3 py-4 first:pt-0">
                    <span class="grid size-10 shrink-0 place-items-center rounded-[10px] bg-[#EEF2FF] text-[#6366F1]">
                        <x-admin.icon name="file" class="size-5" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-[13px] font-semibold text-[#111827]">Articles</p>
                        <p class="mt-0.5 text-[11px] text-[#6B7280]">Create, edit, publish, and restore posts.</p>
                    </div>
                    <span class="rounded-md bg-[#F3F4F6] px-2 py-1 text-[10px] font-semibold text-[#6B7280]">{{ $user->can('articles.view') ? 'Access granted' : 'Restricted' }}</span>
                </div>

                <div class="flex items-center gap-3 py-4">
                    <span class="grid size-10 shrink-0 place-items-center rounded-[10px] bg-[#EEF2FF] text-[#6366F1]">
                        <x-admin.icon name="folder" class="size-5" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-[13px] font-semibold text-[#111827]">Categories</p>
                        <p class="mt-0.5 text-[11px] text-[#6B7280]">Organize articles into clear sections.</p>
                    </div>
                    <span class="rounded-md bg-[#F3F4F6] px-2 py-1 text-[10px] font-semibold text-[#6B7280]">{{ $user->can('categories.view') ? 'Access granted' : 'Restricted' }}</span>
                </div>

                @if ($user->can('users.view'))
                    <div class="flex items-center gap-3 py-4 last:pb-0">
                        <span class="grid size-10 shrink-0 place-items-center rounded-[10px] bg-[#EEF2FF] text-[#6366F1]">
                            <x-admin.icon name="users" class="size-5" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-[13px] font-semibold text-[#111827]">Managers</p>
                            <p class="mt-0.5 text-[11px] text-[#6B7280]">Control access for the editorial team.</p>
                        </div>
                        <span class="rounded-md bg-[#F3F4F6] px-2 py-1 text-[10px] font-semibold text-[#6B7280]">Access granted</span>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
