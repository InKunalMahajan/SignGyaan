@extends('layouts.admin')

@section('title', 'Media - SignGyaan Admin')
@section('page-title', 'Media')
@section('description', 'Manage reusable SignGyaan images, ISL videos, documents, audio and external media links.')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Content management</p>
                    <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Media Library</h2>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-sign-muted">Upload and organise reusable images, ISL videos, documents, audio files and external learning links.</p>
                </div>
                <a href="{{ route('admin.media.create') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">
                    + Add Media
                </a>
            </div>

            <div class="mt-7 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">Total media</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $totalAssets }}</p>
                </div>
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">Images</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $imageCount }}</p>
                </div>
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">Videos</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $videoCount }}</p>
                </div>
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">Documents</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $documentCount }}</p>
                </div>
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">Published</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $publishedAssets }}</p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.media.index') }}" class="mt-7 rounded-2xl border border-sign-border bg-white p-4 sm:rounded-3xl sm:p-5" role="search">
                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-[minmax(0,1fr)_11rem_11rem_11rem_auto] xl:items-end">
                    <div>
                        <label for="media-search" class="mb-2 block text-sm font-semibold text-sign-primary">Search media</label>
                        <input id="media-search" type="search" name="q" value="{{ request('q') }}" placeholder="Title, file name, alt text or notes" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                    </div>

                    <div>
                        <label for="media-type" class="mb-2 block text-sm font-semibold text-sign-primary">Type</label>
                        <select id="media-type" name="type" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All types</option>
                            @foreach ($types as $type)
                                <option value="{{ $type }}" @selected(request('type') === $type)>{{ Str::headline($type) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="media-source" class="mb-2 block text-sm font-semibold text-sign-primary">Source</label>
                        <select id="media-source" name="source" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All sources</option>
                            <option value="upload" @selected(request('source') === 'upload')>Uploaded</option>
                            <option value="external" @selected(request('source') === 'external')>External URL</option>
                        </select>
                    </div>

                    <div>
                        <label for="media-status" class="mb-2 block text-sm font-semibold text-sign-primary">Status</label>
                        <select id="media-status" name="status" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All</option>
                            <option value="published" @selected(request('status') === 'published')>Published</option>
                            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Filter</button>
                        @if (request()->hasAny(['q', 'type', 'source', 'status']))
                            <a href="{{ route('admin.media.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Clear</a>
                        @endif
                    </div>
                </div>
            </form>

            @if ($assets->count())
                <div class="mt-7 grid gap-5 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                    @foreach ($assets as $asset)
                        @php($assetUrl = $asset->publicUrl())
                        <article class="flex min-w-0 flex-col overflow-hidden rounded-2xl border border-sign-border bg-white sm:rounded-3xl">
                            <div class="relative aspect-[16/10] overflow-hidden bg-sign-soft">
                                @if ($asset->media_type === 'image' && $assetUrl)
                                    <img src="{{ $assetUrl }}" alt="{{ $asset->alt_text ?: '' }}" class="h-full w-full object-cover" loading="lazy">
                                @else
                                    <div class="flex h-full items-center justify-center p-6">
                                        <div class="text-center">
                                            <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-sign-primary shadow-sm" aria-hidden="true">
                                                @switch($asset->media_type)
                                                    @case('video')
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-8 w-8"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h8.25A3.75 3.75 0 0 0 16.5 15V9a3.75 3.75 0 0 0-3.75-3.75H4.5A2.25 2.25 0 0 0 2.25 7.5v9A2.25 2.25 0 0 0 4.5 18.75Z" /></svg>
                                                        @break
                                                    @case('audio')
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-8 w-8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5m0 4.5 10.5-2.25v9M9 9v8.25a3 3 0 1 1-3-3c.74 0 1.418.269 1.94.714A3.001 3.001 0 0 1 9 17.25Zm10.5-1.5v5.25a3 3 0 1 1-3-3c.74 0 1.418.269 1.94.714a3 3 0 0 1 1.06 2.286Z" /></svg>
                                                        @break
                                                    @case('link')
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-8 w-8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5 21 3m0 0h-5.25M21 3v5.25M10.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V13.5" /></svg>
                                                        @break
                                                    @default
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-8 w-8"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5A3.375 3.375 0 0 0 10.125 2.25H8.25m0 12.75h7.5m-7.5 3h4.5M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.625a9.375 9.375 0 0 0-9.375-9.375Z" /></svg>
                                                @endswitch
                                            </span>
                                            <p class="mt-3 text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">{{ Str::headline($asset->media_type) }}</p>
                                        </div>
                                    </div>
                                @endif

                                <span @class([
                                    'absolute right-3 top-3 rounded-full px-2.5 py-1 text-[11px] font-semibold shadow-sm',
                                    'bg-white text-sign-primary' => $asset->is_published,
                                    'bg-gray-100 text-sign-muted' => ! $asset->is_published,
                                ])>{{ $asset->is_published ? 'Published' : 'Draft' }}</span>
                            </div>

                            <div class="flex flex-1 flex-col p-5">
                                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                                    <span class="rounded-full bg-sign-soft px-2.5 py-1 text-sign-primary">{{ Str::headline($asset->media_type) }}</span>
                                    <span class="text-sign-muted">{{ $asset->source === 'upload' ? 'Uploaded' : 'External' }}</span>
                                    @if ($asset->formattedFileSize())<span class="text-sign-muted">{{ $asset->formattedFileSize() }}</span>@endif
                                </div>

                                <h3 class="mt-4 break-words font-heading text-xl font-semibold text-sign-primary">{{ $asset->title }}</h3>
                                <p class="mt-2 line-clamp-2 text-sm leading-6 text-sign-muted">{{ $asset->caption ?: ($asset->original_name ?: 'Reusable SignGyaan media item.') }}</p>

                                <div class="mt-auto flex flex-wrap gap-2 pt-5">
                                    @if ($assetUrl)
                                        <a href="{{ $assetUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary transition hover:bg-sign-soft">Open</a>
                                    @endif
                                    <a href="{{ route('admin.media.edit', $asset) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary transition hover:bg-sign-soft">Edit</a>
                                    <form method="POST" action="{{ route('admin.media.destroy', $asset) }}" onsubmit="return confirm('Delete this media item? Uploaded files will also be removed from storage.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-50">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="mt-7 rounded-2xl border border-dashed border-sign-border bg-white p-8 text-center sm:rounded-3xl sm:p-12">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-sign-soft text-sign-primary" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-8 w-8"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5A2.25 2.25 0 0 0 22.5 17.25V6.75A2.25 2.25 0 0 0 20.25 4.5H3.75A2.25 2.25 0 0 0 1.5 6.75v10.5A2.25 2.25 0 0 0 3.75 19.5Z" /></svg>
                    </div>
                    <h3 class="mt-4 font-heading text-2xl font-semibold text-sign-primary">No media found</h3>
                    <p class="mt-2 text-sm leading-6 text-sign-muted">Add your first media item or clear the current filters.</p>
                    <a href="{{ route('admin.media.create') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white">Add Media</a>
                </div>
            @endif

            @if ($assets->hasPages())
                <div class="mt-7">{{ $assets->links() }}</div>
            @endif
        </div>
    </section>
@endsection
