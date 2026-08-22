@php
    $editing = $category?->exists === true;
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label for="name" class="mb-2 block text-[12px] font-semibold text-[#374151]">Name</label>
        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $category?->name) }}"
            required
            autofocus
            class="block h-11 w-full rounded-lg border border-[#D1D5DB] bg-white px-3.5 text-[13px] text-[#111827] outline-none transition focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10"
        >
        @error('name')
            <p class="mt-2 text-[12px] text-[#B91C1C]">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="slug" class="mb-2 block text-[12px] font-semibold text-[#374151]">Slug</label>
        <input
            id="slug"
            name="slug"
            type="text"
            value="{{ old('slug', $category?->slug) }}"
            placeholder="Generated from the name when empty"
            autocomplete="off"
            class="block h-11 w-full rounded-lg border border-[#D1D5DB] bg-white px-3.5 text-[13px] text-[#111827] outline-none transition placeholder:text-[#9CA3AF] focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10"
        >
        <p class="mt-2 text-[11px] leading-5 text-[#6B7280]">Lowercase letters, numbers, and hyphens only.</p>
        @error('slug')
            <p class="mt-2 text-[12px] text-[#B91C1C]">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-5">
    <label for="description" class="mb-2 block text-[12px] font-semibold text-[#374151]">Description</label>
    <textarea
        id="description"
        name="description"
        rows="6"
        class="block w-full resize-y rounded-lg border border-[#D1D5DB] bg-white px-3.5 py-3 text-[13px] leading-6 text-[#111827] outline-none transition focus:border-[#6366F1] focus:ring-3 focus:ring-[#6366F1]/10"
        data-rich-text-editor
    >{{ old('description', $category?->description) }}</textarea>
    <p class="mt-2 text-[11px] leading-5 text-[#6B7280]">Use the toolbar to format the category description.</p>
    @error('description')
        <p class="mt-2 text-[12px] text-[#B91C1C]">{{ $message }}</p>
    @enderror
</div>

<div class="mt-5" data-banner-cropper>
    <label for="banner" class="mb-2 block text-[12px] font-semibold text-[#374151]">Banner</label>

    <div @class([
        'mb-4 overflow-hidden rounded-[10px] border border-[#E5E7EB] bg-[#F9FAFB]',
        'hidden' => ! ($editing && $category->banner_url),
    ]) data-banner-preview>
        <img
            @if ($editing && $category->banner_url)
                src="{{ $category->banner_url }}"
            @endif
            alt="Banner preview"
            class="aspect-video w-full object-cover"
            data-banner-preview-image
        >
        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-[#E5E7EB] px-3.5 py-3">
            <p class="text-[11px] font-medium text-[#6B7280]" data-banner-status>{{ $editing && $category->banner ? 'Current banner' : '' }}</p>
            <button type="button" class="hidden rounded-md px-2.5 py-1.5 text-[11px] font-semibold text-[#6366F1] transition-colors hover:bg-[#EEF2FF]" data-banner-crop-again>Adjust crop</button>
        </div>
    </div>

    <input
        id="banner"
        name="banner"
        type="file"
        accept="image/jpeg,image/png,image/webp"
        class="block w-full rounded-lg border border-[#D1D5DB] bg-white text-[12px] text-[#6B7280] file:mr-4 file:border-0 file:border-r file:border-[#E5E7EB] file:bg-[#F9FAFB] file:px-4 file:py-3 file:text-[12px] file:font-semibold file:text-[#374151] hover:file:bg-[#F3F4F6]"
        data-banner-input
    >
    <p class="mt-2 text-[11px] leading-5 text-[#6B7280]">JPG, PNG, or WebP up to 5 MB. The crop editor will create a 16:9 banner.</p>
    <p class="mt-2 hidden text-[12px] text-[#B91C1C]" data-banner-file-error></p>
    @error('banner')
        <p class="mt-2 text-[12px] text-[#B91C1C]">{{ $message }}</p>
    @enderror

    @if ($editing && $category->banner)
        <label class="mt-3 inline-flex items-center gap-2 text-[12px] font-medium text-[#6B7280]">
            <input type="checkbox" name="remove_banner" value="1" @checked(old('remove_banner')) class="size-4 rounded border-[#D1D5DB] text-[#6366F1] focus:ring-[#6366F1]" data-remove-banner>
            Remove the current banner
        </label>
    @endif

    <dialog class="fixed inset-0 m-auto max-h-[calc(100vh-2rem)] w-[min(900px,calc(100%-2rem))] overflow-y-auto rounded-2xl border border-[#E5E7EB] bg-white p-0 shadow-2xl" aria-labelledby="banner-crop-title" data-banner-dialog>
        <div class="flex items-start justify-between gap-4 border-b border-[#E5E7EB] px-5 py-4 sm:px-6">
            <div>
                <h3 id="banner-crop-title" class="text-[16px] font-bold text-[#111827]">Crop banner</h3>
                <p class="mt-1 text-[12px] leading-5 text-[#6B7280]">Draw a rectangle or adjust the current selection.</p>
            </div>
            <button type="button" class="grid size-9 shrink-0 place-items-center rounded-lg text-[#6B7280] transition-colors hover:bg-[#F3F4F6] hover:text-[#111827]" aria-label="Close crop editor" data-banner-cancel>
                <x-admin.icon name="x" class="size-5" />
            </button>
        </div>

        <div class="p-4 sm:p-6">
            <div class="flex w-full justify-center overflow-hidden rounded-xl bg-[#111827]">
                <div class="relative inline-block max-w-full touch-none cursor-crosshair overflow-hidden select-none" data-banner-stage>
                    <img alt="" draggable="false" class="pointer-events-none block h-auto max-h-[52vh] w-auto max-w-full select-none" data-banner-image>

                    <div class="absolute cursor-move border-2 border-[#3B82F6]" role="group" aria-label="Selected banner area" data-banner-selection>
                        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                            <span class="absolute inset-y-0 left-1/3 border-l border-dashed border-white/70"></span>
                            <span class="absolute inset-y-0 left-2/3 border-l border-dashed border-white/70"></span>
                            <span class="absolute inset-x-0 top-1/3 border-t border-dashed border-white/70"></span>
                            <span class="absolute inset-x-0 top-2/3 border-t border-dashed border-white/70"></span>
                        </div>

                        <span class="absolute -left-2.5 -top-2.5 size-5 cursor-nwse-resize rounded-sm border-2 border-white bg-[#3B82F6] shadow" data-banner-handle="nw" aria-hidden="true"></span>
                        <span class="absolute -right-2.5 -top-2.5 size-5 cursor-nesw-resize rounded-sm border-2 border-white bg-[#3B82F6] shadow" data-banner-handle="ne" aria-hidden="true"></span>
                        <span class="absolute -bottom-2.5 -right-2.5 size-5 cursor-nwse-resize rounded-sm border-2 border-white bg-[#3B82F6] shadow" data-banner-handle="se" aria-hidden="true"></span>
                        <span class="absolute -bottom-2.5 -left-2.5 size-5 cursor-nesw-resize rounded-sm border-2 border-white bg-[#3B82F6] shadow" data-banner-handle="sw" aria-hidden="true"></span>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex flex-col gap-1 text-[11px] leading-5 text-[#6B7280] sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                <span>Drag inside the rectangle to move it. Drag outside to draw a new one.</span>
                <span class="shrink-0 font-medium text-[#374151]">Always 16:9 · Output 1600 × 900 px</span>
            </div>

            <p class="mt-4 hidden rounded-lg border border-[#FECACA] bg-[#FEF2F2] px-3.5 py-3 text-[12px] text-[#B91C1C]" data-banner-error></p>
        </div>

        <div class="flex flex-col-reverse gap-2 border-t border-[#E5E7EB] bg-[#F9FAFB] px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
            <button type="button" class="rounded-lg px-4 py-2.5 text-[13px] font-semibold text-[#6B7280] transition-colors hover:bg-[#E5E7EB] hover:text-[#111827]" data-banner-cancel>Cancel</button>
            <button type="button" class="rounded-lg bg-[#6366F1] px-4 py-2.5 text-[13px] font-semibold text-white transition-colors hover:bg-[#5558E6] disabled:cursor-wait disabled:opacity-60" data-banner-apply>Apply crop</button>
        </div>
    </dialog>
</div>

<div class="mt-6 flex flex-wrap items-center gap-3 border-t border-[#F3F4F6] pt-5">
    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-[#6366F1] px-4 py-2.5 text-[13px] font-semibold text-white transition-colors hover:bg-[#5558E6]">
        <x-admin.icon name="check" class="size-4" />
        {{ $editing ? 'Save changes' : 'Create category' }}
    </button>
    <a href="{{ route('admin.categories.index') }}" class="rounded-lg px-4 py-2.5 text-[13px] font-semibold text-[#6B7280] transition-colors hover:bg-[#F3F4F6] hover:text-[#111827]">Cancel</a>
</div>
