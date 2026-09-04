@extends('layouts.admin')

@section('title', 'Practice & Resources - SignGyaan Admin')
@section('page-title', 'Practice & Resources')
@section('description', 'Manage SignGyaan practice activities and supporting lesson resources.')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Content management</p>
                    <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Practice & Resources</h2>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-sign-muted">Attach exercises, quizzes, worksheets, notes and useful links to specific lessons.</p>
                </div>
                <a href="{{ route('admin.practice.create') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">+ Add Item</a>
            </div>

            <div class="mt-7 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ([
                    ['label' => 'Total items', 'value' => $totalItems],
                    ['label' => 'Practice', 'value' => $practiceCount],
                    ['label' => 'Resources', 'value' => $resourceCount],
                    ['label' => 'Published', 'value' => $publishedCount],
                ] as $stat)
                    <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                        <p class="text-sm font-semibold text-sign-muted">{{ $stat['label'] }}</p>
                        <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $stat['value'] }}</p>
                    </div>
                @endforeach
            </div>

            <form method="GET" action="{{ route('admin.practice.index') }}" class="mt-7 rounded-2xl border border-sign-border bg-white p-4 sm:rounded-3xl sm:p-5" role="search">
                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-[minmax(0,1fr)_12rem_13rem_12rem_11rem_auto] xl:items-end">
                    <div>
                        <label for="practice-search" class="mb-2 block text-sm font-semibold text-sign-primary">Search</label>
                        <input id="practice-search" type="search" name="q" value="{{ request('q') }}" placeholder="Title, slug or instructions" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                    </div>
                    <div>
                        <label for="practice-kind" class="mb-2 block text-sm font-semibold text-sign-primary">Kind</label>
                        <select id="practice-kind" name="kind" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All</option>
                            <option value="practice" @selected(request('kind') === 'practice')>Practice</option>
                            <option value="resource" @selected(request('kind') === 'resource')>Resource</option>
                        </select>
                    </div>
                    <div>
                        <label for="practice-type" class="mb-2 block text-sm font-semibold text-sign-primary">Type</label>
                        <select id="practice-type" name="type" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All types</option>
                            @foreach ($resourceTypes as $value => $label)
                                <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="practice-subject" class="mb-2 block text-sm font-semibold text-sign-primary">Subject</label>
                        <select id="practice-subject" name="subject" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All subjects</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected((string) request('subject') === (string) $subject->id)>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="practice-status" class="mb-2 block text-sm font-semibold text-sign-primary">Status</label>
                        <select id="practice-status" name="status" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All</option>
                            <option value="published" @selected(request('status') === 'published')>Published</option>
                            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Filter</button>
                        @if (request()->hasAny(['q', 'kind', 'type', 'subject', 'course', 'lesson', 'status']))
                            <a href="{{ route('admin.practice.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Clear</a>
                        @endif
                    </div>
                </div>
            </form>

            <div class="mt-7 overflow-hidden rounded-2xl border border-sign-border bg-white sm:rounded-3xl">
                @if ($items->count())
                    <div class="hidden overflow-x-auto lg:block">
                        <table class="min-w-full divide-y divide-sign-border text-left text-sm">
                            <thead class="bg-sign-soft text-xs font-semibold uppercase tracking-wider text-sign-muted">
                                <tr>
                                    <th class="px-5 py-4">Order</th>
                                    <th class="px-5 py-4">Item</th>
                                    <th class="px-5 py-4">Lesson</th>
                                    <th class="px-5 py-4">Kind / Type</th>
                                    <th class="px-5 py-4">Status</th>
                                    <th class="px-5 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-sign-border">
                                @foreach ($items as $item)
                                    <tr class="align-top">
                                        <td class="px-5 py-4 font-semibold text-sign-muted">{{ $item->sort_order }}</td>
                                        <td class="px-5 py-4">
                                            <p class="font-semibold text-sign-primary">{{ $item->title }}</p>
                                            <p class="mt-1 max-w-xl text-xs leading-5 text-sign-muted">{{ $item->short_description ?: 'No short description yet.' }}</p>
                                            @if ($item->resource_url)
                                                <span class="mt-2 inline-flex rounded-lg bg-sign-soft px-2 py-1 text-[11px] font-semibold text-sign-cyan-dark">Linked resource</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="font-medium text-sign-text">{{ $item->lesson?->title ?? 'Missing lesson' }}</p>
                                            <p class="mt-1 text-xs text-sign-muted">{{ $item->lesson?->unit?->course?->title ?? 'Missing course' }}</p>
                                            <p class="mt-1 text-xs font-semibold text-sign-cyan-dark">{{ $item->lesson?->unit?->course?->subject?->name ?? 'Missing subject' }}</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex rounded-full bg-sign-soft px-2.5 py-1 text-xs font-semibold text-sign-primary">{{ ucfirst($item->kind) }}</span>
                                            <p class="mt-2 text-xs font-semibold text-sign-muted">{{ $resourceTypes[$item->resource_type] ?? $item->resource_type }}</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span @class([
                                                'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                                'bg-sign-light text-sign-primary' => $item->is_published,
                                                'bg-gray-100 text-sign-muted' => ! $item->is_published,
                                            ])>{{ $item->is_published ? 'Published' : 'Draft' }}</span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('admin.practice.edit', $item) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary transition hover:bg-sign-soft">Edit</a>
                                                <form method="POST" action="{{ route('admin.practice.destroy', $item) }}" onsubmit="return confirm('Delete this practice or resource item?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-50">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="divide-y divide-sign-border lg:hidden">
                        @foreach ($items as $item)
                            <article class="p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="font-heading text-xl font-semibold text-sign-primary">{{ $item->title }}</h3>
                                        <p class="mt-1 text-xs font-semibold text-sign-cyan-dark">{{ ucfirst($item->kind) }} · {{ $resourceTypes[$item->resource_type] ?? $item->resource_type }}</p>
                                        <p class="mt-1 text-xs text-sign-muted">{{ $item->lesson?->title ?? 'Missing lesson' }}</p>
                                    </div>
                                    <span @class([
                                        'shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold',
                                        'bg-sign-light text-sign-primary' => $item->is_published,
                                        'bg-gray-100 text-sign-muted' => ! $item->is_published,
                                    ])>{{ $item->is_published ? 'Published' : 'Draft' }}</span>
                                </div>
                                <p class="mt-3 text-sm leading-6 text-sign-muted">{{ $item->short_description ?: 'No short description yet.' }}</p>
                                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-sign-border pt-4">
                                    <span class="text-xs font-semibold text-sign-muted">Order {{ $item->sort_order }}</span>
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.practice.edit', $item) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary">Edit</a>
                                        <form method="POST" action="{{ route('admin.practice.destroy', $item) }}" onsubmit="return confirm('Delete this item?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center sm:p-12">
                        <h3 class="font-heading text-2xl font-semibold text-sign-primary">No practice or resources found</h3>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">Create the first item or clear the current filters.</p>
                        <a href="{{ route('admin.practice.create') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white">Add Item</a>
                    </div>
                @endif
            </div>

            @if ($items->hasPages())
                <div class="mt-6">{{ $items->links() }}</div>
            @endif
        </div>
    </section>
@endsection
