@extends('layouts.admin')

@section('title', 'New category')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-2 text-[12px] font-semibold text-[#6B7280] transition-colors hover:text-[#111827]">
                <x-admin.icon name="arrow-left" class="size-4" />
                Back to categories
            </a>
            <h2 class="mt-4 text-2xl font-bold tracking-tight text-[#111827]">Create a category</h2>
            <p class="mt-2 text-[14px] leading-6 text-[#6B7280]">Add a section that can be assigned to blog articles.</p>
        </div>

        <section class="rounded-xl border border-[#E5E7EB] bg-white p-5 sm:p-6">
            <div class="flex items-start gap-3.5">
                <span class="grid size-10 shrink-0 place-items-center rounded-[10px] bg-[#EEF2FF] text-[#6366F1]">
                    <x-admin.icon name="folder" class="size-5" />
                </span>
                <div>
                    <h3 class="text-[15px] font-bold text-[#111827]">Category information</h3>
                    <p class="mt-1 text-[12px] leading-5 text-[#6B7280]">The slug will be used in the public category URL.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" class="mt-6">
                @csrf
                @include('admin.categories._form', ['category' => null])
            </form>
        </section>
    </div>
@endsection
