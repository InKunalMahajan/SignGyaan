@extends('layouts.admin')

@section('title', 'Units - SignGyaan Admin')
@section('page-title', 'Units')
@section('description', 'Manage SignGyaan course units, order, publishing and course assignment.')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Content management</p>
                    <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Units</h2>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-sign-muted">Organise each course into clear, ordered learning units before adding lessons.</p>
                </div>
                <a href="{{ route('admin.units.create') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">
                    + Add Unit
                </a>
            </div>

            <div class="mt-7 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">Total units</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $totalUnits }}</p>
                </div>
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">Published</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $publishedUnits }}</p>
                </div>
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">Drafts</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $draftUnits }}</p>
                </div>
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">Courses with units</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $coursesWithUnits }}</p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.units.index') }}" class="mt-7 rounded-2xl border border-sign-border bg-white p-4 sm:rounded-3xl sm:p-5" role="search">
                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-[minmax(0,1fr)_13rem_16rem_11rem_auto] xl:items-end">
                    <div>
                        <label for="unit-search" class="mb-2 block text-sm font-semibold text-sign-primary">Search units</label>
                        <input id="unit-search" type="search" name="q" value="{{ request('q') }}" placeholder="Title, slug or description" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                    </div>

                    <div>
                        <label for="unit-subject" class="mb-2 block text-sm font-semibold text-sign-primary">Subject</label>
                        <select id="unit-subject" name="subject" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All subjects</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected((string) request('subject') === (string) $subject->id)>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="unit-course" class="mb-2 block text-sm font-semibold text-sign-primary">Course</label>
                        <select id="unit-course" name="course" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All courses</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}" @selected((string) request('course') === (string) $course->id)>{{ $course->subject?->name ?? 'No subject' }} — {{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="unit-status" class="mb-2 block text-sm font-semibold text-sign-primary">Status</label>
                        <select id="unit-status" name="status" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All</option>
                            <option value="published" @selected(request('status') === 'published')>Published</option>
                            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Filter</button>
                        @if (request()->hasAny(['q', 'subject', 'course', 'status']))
                            <a href="{{ route('admin.units.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Clear</a>
                        @endif
                    </div>
                </div>
            </form>

            <div class="mt-7 overflow-hidden rounded-2xl border border-sign-border bg-white sm:rounded-3xl">
                @if ($units->count())
                    <div class="hidden overflow-x-auto lg:block">
                        <table class="min-w-full divide-y divide-sign-border text-left text-sm">
                            <thead class="bg-sign-soft text-xs font-semibold uppercase tracking-wider text-sign-muted">
                                <tr>
                                    <th class="px-5 py-4">Order</th>
                                    <th class="px-5 py-4">Unit</th>
                                    <th class="px-5 py-4">Course</th>
                                    <th class="px-5 py-4">Status</th>
                                    <th class="px-5 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-sign-border">
                                @foreach ($units as $unit)
                                    <tr class="align-top">
                                        <td class="px-5 py-4 font-semibold text-sign-muted">{{ $unit->sort_order }}</td>
                                        <td class="px-5 py-4">
                                            <p class="font-semibold text-sign-primary">{{ $unit->title }}</p>
                                            <p class="mt-1 max-w-xl text-xs leading-5 text-sign-muted">{{ $unit->short_description ?: 'No short description yet.' }}</p>
                                            <code class="mt-2 inline-flex rounded-lg bg-sign-soft px-2 py-1 text-xs text-sign-primary">/{{ $unit->slug }}</code>
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="font-medium text-sign-text">{{ $unit->course?->title ?? 'Missing course' }}</p>
                                            <p class="mt-1 text-xs font-semibold text-sign-cyan-dark">{{ $unit->course?->subject?->name ?? 'Missing subject' }}</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span @class([
                                                'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                                'bg-sign-light text-sign-primary' => $unit->is_published,
                                                'bg-gray-100 text-sign-muted' => ! $unit->is_published,
                                            ])>{{ $unit->is_published ? 'Published' : 'Draft' }}</span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('admin.units.edit', $unit) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary transition hover:bg-sign-soft">Edit</a>
                                                <form method="POST" action="{{ route('admin.units.destroy', $unit) }}" onsubmit="return confirm('Delete this unit? Future lessons inside it would also be removed.');">
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
                        @foreach ($units as $unit)
                            <article class="p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="font-heading text-xl font-semibold text-sign-primary">{{ $unit->title }}</h3>
                                        <p class="mt-1 text-xs font-semibold text-sign-cyan-dark">{{ $unit->course?->subject?->name ?? 'Missing subject' }} · {{ $unit->course?->title ?? 'Missing course' }}</p>
                                        <p class="mt-1 break-all text-xs text-sign-muted">/{{ $unit->slug }}</p>
                                    </div>
                                    <span @class([
                                        'shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold',
                                        'bg-sign-light text-sign-primary' => $unit->is_published,
                                        'bg-gray-100 text-sign-muted' => ! $unit->is_published,
                                    ])>{{ $unit->is_published ? 'Published' : 'Draft' }}</span>
                                </div>
                                <p class="mt-3 text-sm leading-6 text-sign-muted">{{ $unit->short_description ?: 'No short description yet.' }}</p>
                                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-sign-border pt-4">
                                    <span class="text-xs font-semibold text-sign-muted">Unit order {{ $unit->sort_order }}</span>
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.units.edit', $unit) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary">Edit</a>
                                        <form method="POST" action="{{ route('admin.units.destroy', $unit) }}" onsubmit="return confirm('Delete this unit?');">
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
                        <h3 class="font-heading text-2xl font-semibold text-sign-primary">No units found</h3>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">Create your first unit or clear the current filters.</p>
                        <a href="{{ route('admin.units.create') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white">Add Unit</a>
                    </div>
                @endif
            </div>

            @if ($units->hasPages())
                <div class="mt-6">{{ $units->links() }}</div>
            @endif
        </div>
    </section>
@endsection
