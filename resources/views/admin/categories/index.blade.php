@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
    <div class="mx-auto max-w-7xl space-y-6">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <p class="text-[13px] font-medium text-[#6366F1]">Content structure</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-[#111827]">Categories</h2>
                <p class="mt-2 max-w-2xl text-[14px] leading-6 text-[#6B7280]">Organize articles into clear sections for readers and search engines.</p>
            </div>

            @can('categories.create')
                <a href="{{ route('admin.categories.create') }}" class="inline-flex w-fit items-center gap-2 rounded-lg bg-[#6366F1] px-4 py-2.5 text-[13px] font-semibold text-white transition-colors hover:bg-[#5558E6]">
                    <x-admin.icon name="plus" class="size-4" />
                    New category
                </a>
            @endcan
        </div>

        @if (session('status'))
            <div role="status" class="flex items-center gap-3 rounded-[10px] border border-[#A7F3D0] bg-[#ECFDF5] px-4 py-3 text-[13px] font-medium text-[#047857]">
                <span class="grid size-6 shrink-0 place-items-center rounded-full bg-[#D1FAE5]">
                    <x-admin.icon name="check" class="size-4" />
                </span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <form method="GET" action="{{ route('admin.categories.index') }}" class="rounded-xl border border-[#E5E7EB] bg-white p-4">
            <div class="flex flex-col gap-3 sm:flex-row">
                <div class="relative min-w-0 flex-1">
                    <label for="search" class="sr-only">Search categories</label>
                    <x-admin.icon name="search" class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-[#9CA3AF]" />
                    <input id="search" name="search" type="search" value="{{ request('search') }}" placeholder="Search name, slug, or description" class="block h-10 w-full rounded-lg border border-[#D1D5DB] bg-white pl-9 pr-3 text-[12px] text-[#111827] outline-none transition placeholder:text-[#9CA3AF] focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex h-10 flex-1 items-center justify-center rounded-lg bg-[#111827] px-5 text-[12px] font-semibold text-white transition-colors hover:bg-[#1F2937]">Search</button>
                    @if (request()->filled('search'))
                        <a href="{{ route('admin.categories.index') }}" class="flex h-10 items-center justify-center rounded-lg px-3 text-[12px] font-semibold text-[#6B7280] transition-colors hover:bg-[#F3F4F6] hover:text-[#111827]">Clear</a>
                    @endif
                </div>
            </div>
        </form>

        @if ($categories->isEmpty())
            <div class="rounded-xl border border-[#E5E7EB] bg-white px-6 py-14 text-center">
                <span class="mx-auto grid size-11 place-items-center rounded-[10px] bg-[#EEF2FF] text-[#6366F1]">
                    <x-admin.icon name="folder" class="size-5" />
                </span>
                <h3 class="mt-4 text-[14px] font-bold text-[#111827]">No categories found</h3>
                <p class="mt-1 text-[12px] text-[#6B7280]">Create the first category or change your search.</p>
            </div>
        @else
            <div class="hidden overflow-hidden rounded-xl border border-[#E5E7EB] bg-white md:block">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="border-b border-[#E5E7EB] bg-[#F9FAFB]">
                            <tr class="text-left text-[10px] font-semibold uppercase tracking-wider text-[#6B7280]">
                                <th scope="col" class="px-5 py-3">Category</th>
                                <th scope="col" class="px-5 py-3">Slug</th>
                                <th scope="col" class="px-5 py-3">Description</th>
                                <th scope="col" class="px-5 py-3">Updated</th>
                                <th scope="col" class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F3F4F6]">
                            @foreach ($categories as $category)
                                <tr class="text-[12px]">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            @if ($category->banner_url)
                                                <img src="{{ $category->banner_url }}" alt="" class="size-11 shrink-0 rounded-lg object-cover">
                                            @else
                                                <span class="grid size-11 shrink-0 place-items-center rounded-lg bg-[#EEF2FF] text-[#6366F1]"><x-admin.icon name="folder" class="size-5" /></span>
                                            @endif
                                            <p class="font-semibold text-[#111827]">{{ $category->name }}</p>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 font-mono text-[11px] text-[#6B7280]">/{{ $category->slug }}</td>
                                    <td class="max-w-sm px-5 py-4 text-[#6B7280]">
                                        <p class="max-h-10 overflow-hidden leading-5">{{ $category->description ?: 'No description' }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-[#6B7280]">{{ $category->updated_at->format('M j, Y · H:i') }}</td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-end gap-1">
                                            @can('categories.update')
                                                <a href="{{ route('admin.categories.edit', $category) }}" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-2 font-semibold text-[#4B5563] transition-colors hover:bg-[#F3F4F6] hover:text-[#111827]"><x-admin.icon name="edit" class="size-3.5" /> Edit</a>
                                            @endcan
                                            @can('categories.delete')
                                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category permanently?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="rounded-lg px-2.5 py-2 font-semibold text-[#B91C1C] transition-colors hover:bg-[#FEF2F2]">Delete</button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-3 md:hidden">
                @foreach ($categories as $category)
                    <article class="rounded-xl border border-[#E5E7EB] bg-white p-4">
                        <div class="flex items-start gap-3">
                            @if ($category->banner_url)
                                <img src="{{ $category->banner_url }}" alt="" class="size-12 shrink-0 rounded-lg object-cover">
                            @else
                                <span class="grid size-12 shrink-0 place-items-center rounded-lg bg-[#EEF2FF] text-[#6366F1]"><x-admin.icon name="folder" class="size-5" /></span>
                            @endif
                            <div class="min-w-0 flex-1">
                                <h3 class="truncate text-[13px] font-semibold text-[#111827]">{{ $category->name }}</h3>
                                <p class="mt-1 truncate font-mono text-[10px] text-[#6B7280]">/{{ $category->slug }}</p>
                            </div>
                        </div>
                        @if ($category->description)
                            <p class="mt-3 max-h-10 overflow-hidden text-[11px] leading-5 text-[#6B7280]">{{ $category->description }}</p>
                        @endif
                        <div class="mt-4 flex items-center justify-end gap-1 border-t border-[#F3F4F6] pt-3">
                            @can('categories.update')
                                <a href="{{ route('admin.categories.edit', $category) }}" class="rounded-lg px-2.5 py-2 text-[11px] font-semibold text-[#4B5563] hover:bg-[#F3F4F6]">Edit</a>
                            @endcan
                            @can('categories.delete')
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category permanently?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg px-2.5 py-2 text-[11px] font-semibold text-[#B91C1C] hover:bg-[#FEF2F2]">Delete</button>
                                </form>
                            @endcan
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($categories->hasPages())
                <div>{{ $categories->links() }}</div>
            @endif
        @endif
    </div>
@endsection
