@extends('layouts.admin')

@section('title', 'Lessons - SignGyaan Admin')
@section('page-title', 'Lessons')
@section('description', 'Manage SignGyaan lessons, content, ISL video support, order and publishing.')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Content management</p>
                    <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Lessons</h2>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-sign-muted">Build the learner-facing lesson sequence with notes, objectives, key points, examples and ISL video support.</p>
                </div>
                <a href="{{ route('admin.lessons.create') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">+ Add Lesson</a>
            </div>

            <div class="mt-7 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">Total lessons</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $totalLessons }}</p>
                </div>
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">Published</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $publishedLessons }}</p>
                </div>
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">Drafts</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $draftLessons }}</p>
                </div>
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">With ISL video</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $lessonsWithVideo }}</p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.lessons.index') }}" class="mt-7 rounded-2xl border border-sign-border bg-white p-4 sm:rounded-3xl sm:p-5" role="search">
                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-[minmax(0,1fr)_12rem_14rem_14rem_10rem_11rem_auto] 2xl:items-end">
                    <div>
                        <label for="lesson-search" class="mb-2 block text-sm font-semibold text-sign-primary">Search lessons</label>
                        <input id="lesson-search" type="search" name="q" value="{{ request('q') }}" placeholder="Title, slug or description" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                    </div>

                    <div>
                        <label for="lesson-subject" class="mb-2 block text-sm font-semibold text-sign-primary">Subject</label>
                        <select id="lesson-subject" name="subject" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All subjects</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected((string) request('subject') === (string) $subject->id)>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="lesson-course" class="mb-2 block text-sm font-semibold text-sign-primary">Course</label>
                        <select id="lesson-course" name="course" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All courses</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}" @selected((string) request('course') === (string) $course->id)>{{ $course->subject?->name ?? 'No subject' }} — {{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="lesson-unit" class="mb-2 block text-sm font-semibold text-sign-primary">Unit</label>
                        <select id="lesson-unit" name="unit" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All units</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" @selected((string) request('unit') === (string) $unit->id)>{{ $unit->course?->title ?? 'No course' }} — {{ $unit->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="lesson-status" class="mb-2 block text-sm font-semibold text-sign-primary">Status</label>
                        <select id="lesson-status" name="status" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All</option>
                            <option value="published" @selected(request('status') === 'published')>Published</option>
                            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                        </select>
                    </div>

                    <div>
                        <label for="lesson-video" class="mb-2 block text-sm font-semibold text-sign-primary">ISL video</label>
                        <select id="lesson-video" name="video" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All</option>
                            <option value="with" @selected(request('video') === 'with')>With video</option>
                            <option value="without" @selected(request('video') === 'without')>Without video</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Filter</button>
                        @if (request()->hasAny(['q', 'subject', 'course', 'unit', 'status', 'video']))
                            <a href="{{ route('admin.lessons.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Clear</a>
                        @endif
                    </div>
                </div>
            </form>

            <div class="mt-7 overflow-hidden rounded-2xl border border-sign-border bg-white sm:rounded-3xl">
                @if ($lessons->count())
                    <div class="hidden overflow-x-auto lg:block">
                        <table class="min-w-full divide-y divide-sign-border text-left text-sm">
                            <thead class="bg-sign-soft text-xs font-semibold uppercase tracking-wider text-sign-muted">
                                <tr>
                                    <th class="px-5 py-4">Order</th>
                                    <th class="px-5 py-4">Lesson</th>
                                    <th class="px-5 py-4">Unit / Course</th>
                                    <th class="px-5 py-4">ISL</th>
                                    <th class="px-5 py-4">Status</th>
                                    <th class="px-5 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-sign-border">
                                @foreach ($lessons as $lesson)
                                    @php
                                        $hasIslVideo = filled($lesson->isl_video_url) || $lesson->isl_media_asset_id;
                                    @endphp
                                    <tr class="align-top">
                                        <td class="px-5 py-4 font-semibold text-sign-muted">{{ $lesson->sort_order }}</td>
                                        <td class="px-5 py-4">
                                            <p class="font-semibold text-sign-primary">{{ $lesson->title }}</p>
                                            <p class="mt-1 max-w-xl text-xs leading-5 text-sign-muted">{{ $lesson->short_description ?: 'No short description yet.' }}</p>
                                            <code class="mt-2 inline-flex rounded-lg bg-sign-soft px-2 py-1 text-xs text-sign-primary">/{{ $lesson->slug }}</code>
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="font-medium text-sign-text">{{ $lesson->unit?->title ?? 'Missing unit' }}</p>
                                            <p class="mt-1 text-xs text-sign-muted">{{ $lesson->unit?->course?->title ?? 'Missing course' }}</p>
                                            <p class="mt-1 text-xs font-semibold text-sign-cyan-dark">{{ $lesson->unit?->course?->subject?->name ?? 'Missing subject' }}</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            @if ($lesson->isl_media_asset_id)
                                                <span class="inline-flex rounded-full bg-sign-light px-2.5 py-1 text-xs font-semibold text-sign-primary">Media Library</span>
                                                @if ($lesson->mediaAsset)
                                                    <p class="mt-1 max-w-40 truncate text-xs text-sign-muted">{{ $lesson->mediaAsset->title }}</p>
                                                @endif
                                            @elseif ($lesson->isl_video_url)
                                                <span class="inline-flex rounded-full bg-sign-light px-2.5 py-1 text-xs font-semibold text-sign-primary">URL linked</span>
                                            @else
                                                <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-sign-muted">No video</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4">
                                            <span @class([
                                                'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                                'bg-sign-light text-sign-primary' => $lesson->is_published,
                                                'bg-gray-100 text-sign-muted' => ! $lesson->is_published,
                                            ])>{{ $lesson->is_published ? 'Published' : 'Draft' }}</span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('admin.lessons.edit', $lesson) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary transition hover:bg-sign-soft">Edit</a>
                                                <form method="POST" action="{{ route('admin.lessons.destroy', $lesson) }}" onsubmit="return confirm('Delete this lesson? This action cannot be undone.');">
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
                        @foreach ($lessons as $lesson)
                            @php
                                $hasIslVideo = filled($lesson->isl_video_url) || $lesson->isl_media_asset_id;
                            @endphp
                            <article class="p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="font-heading text-xl font-semibold text-sign-primary">{{ $lesson->title }}</h3>
                                        <p class="mt-1 text-xs font-semibold text-sign-cyan-dark">{{ $lesson->unit?->course?->subject?->name ?? 'Missing subject' }}</p>
                                        <p class="mt-1 text-xs text-sign-muted">{{ $lesson->unit?->course?->title ?? 'Missing course' }} → {{ $lesson->unit?->title ?? 'Missing unit' }}</p>
                                    </div>
                                    <span @class([
                                        'shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold',
                                        'bg-sign-light text-sign-primary' => $lesson->is_published,
                                        'bg-gray-100 text-sign-muted' => ! $lesson->is_published,
                                    ])>{{ $lesson->is_published ? 'Published' : 'Draft' }}</span>
                                </div>
                                <p class="mt-3 text-sm leading-6 text-sign-muted">{{ $lesson->short_description ?: 'No short description yet.' }}</p>
                                <div class="mt-3 flex flex-wrap gap-2 text-xs font-semibold">
                                    <span class="rounded-full bg-sign-soft px-2.5 py-1 text-sign-primary">Order {{ $lesson->sort_order }}</span>
                                    <span class="rounded-full bg-sign-soft px-2.5 py-1 text-sign-primary">
                                        {{ $hasIslVideo ? ($lesson->isl_media_asset_id ? 'Media Library video' : 'ISL video URL') : 'No ISL video' }}
                                    </span>
                                </div>
                                <div class="mt-4 flex justify-end gap-2 border-t border-sign-border pt-4">
                                    <a href="{{ route('admin.lessons.edit', $lesson) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary">Edit</a>
                                    <form method="POST" action="{{ route('admin.lessons.destroy', $lesson) }}" onsubmit="return confirm('Delete this lesson?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700">Delete</button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center sm:p-12">
                        <h3 class="font-heading text-2xl font-semibold text-sign-primary">No lessons found</h3>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">Create your first lesson or clear the current filters.</p>
                        <a href="{{ route('admin.lessons.create') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white">Add Lesson</a>
                    </div>
                @endif
            </div>

            @if ($lessons->hasPages())
                <div class="mt-6">{{ $lessons->links() }}</div>
            @endif
        </div>
    </section>
@endsection