@extends('layouts.admin')

@section('title', 'Edit category')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-2 text-[12px] font-semibold text-[#6B7280] transition-colors hover:text-[#111827]">
                <x-admin.icon name="arrow-left" class="size-4" />
                Back to categories
            </a>
            <h2 class="mt-4 text-2xl font-bold tracking-tight text-[#111827]">Edit {{ $category->name }}</h2>
            <p class="mt-2 text-[14px] leading-6 text-[#6B7280]">/{{ $category->slug }}</p>
        </div>

        @if (session('status'))
            <div role="status" class="flex items-center gap-3 rounded-[10px] border border-[#A7F3D0] bg-[#ECFDF5] px-4 py-3 text-[13px] font-medium text-[#047857]">
                <span class="grid size-6 shrink-0 place-items-center rounded-full bg-[#D1FAE5]">
                    <x-admin.icon name="check" class="size-4" />
                </span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <section class="rounded-xl border border-[#E5E7EB] bg-white p-5 sm:p-6">
            <div class="flex items-start gap-3.5">
                <span class="grid size-10 shrink-0 place-items-center rounded-[10px] bg-[#EEF2FF] text-[#6366F1]">
                    <x-admin.icon name="folder" class="size-5" />
                </span>
                <div>
                    <h3 class="text-[15px] font-bold text-[#111827]">Category information</h3>
                    <p class="mt-1 text-[12px] leading-5 text-[#6B7280]">Update the title, public URL, description, and banner.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data" class="mt-6">
                @csrf
                @method('PUT')
                @include('admin.categories._form', ['category' => $category])
            </form>
        </section>

        @can('categories.delete')
            <section class="rounded-xl border border-[#FECACA] bg-[#FFFBFB] p-5 sm:p-6">
                <h3 class="text-[14px] font-bold text-[#991B1B]">Delete category</h3>
                <p class="mt-1 text-[12px] leading-5 text-[#7F1D1D]">The category and its banner will be permanently deleted. This action cannot be undone.</p>
                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="mt-4" onsubmit="return confirm('Delete this category permanently?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-lg bg-[#B91C1C] px-4 py-2.5 text-[12px] font-semibold text-white transition-colors hover:bg-[#991B1B]">Delete category</button>
                </form>
            </section>
        @endcan
    </div>
@endsection
