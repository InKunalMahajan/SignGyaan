@extends('layouts.admin')

@section('title', 'ISL Vocabulary - SignGyaan Admin')
@section('page-title', 'ISL Vocabulary')
@section('description', 'Manage reusable vocabulary terms, meanings, ISL sign videos and learning context.')

@section('content')
<section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
    <div class="mx-auto max-w-7xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">ISL learning</p>
                <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">ISL Vocabulary</h2>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-sign-muted">Create reusable signs and vocabulary that can later be connected to lessons and the public vocabulary library.</p>
            </div>
            <a href="{{ route('admin.vocabulary.create') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">+ Add Term</a>
        </div>

        <div class="mt-7 grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-sign-border bg-white p-5"><p class="text-sm font-semibold text-sign-muted">Total terms</p><p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $totalTerms }}</p></div>
            <div class="rounded-2xl border border-sign-border bg-white p-5"><p class="text-sm font-semibold text-sign-muted">Published</p><p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $publishedTerms }}</p></div>
            <div class="rounded-2xl border border-sign-border bg-white p-5"><p class="text-sm font-semibold text-sign-muted">With ISL video</p><p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $termsWithVideo }}</p></div>
        </div>

        <form method="GET" action="{{ route('admin.vocabulary.index') }}" class="mt-7 rounded-2xl border border-sign-border bg-white p-4 sm:p-5" role="search" aria-label="Filter vocabulary terms">
            <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_12rem_14rem_10rem_10rem_auto] lg:items-end">
                <div>
                    <label for="vocabulary-search" class="mb-2 block text-sm font-semibold text-sign-primary">Search</label>
                    <input id="vocabulary-search" type="search" name="q" value="{{ request('q') }}" placeholder="Term, meaning or example" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                </div>
                <div>
                    <label for="vocabulary-subject" class="mb-2 block text-sm font-semibold text-sign-primary">Subject</label>
                    <select id="vocabulary-subject" name="subject" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-3 py-3 text-base text-sign-text outline-none focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                        <option value="">All</option>
                        @foreach ($subjects as $subject)<option value="{{ $subject->id }}" @selected((string) request('subject') === (string) $subject->id)>{{ $subject->name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label for="vocabulary-course" class="mb-2 block text-sm font-semibold text-sign-primary">Course</label>
                    <select id="vocabulary-course" name="course" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-3 py-3 text-base text-sign-text outline-none focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                        <option value="">All</option>
                        @foreach ($courses as $course)<option value="{{ $course->id }}" @selected((string) request('course') === (string) $course->id)>{{ $course->title }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label for="vocabulary-status" class="mb-2 block text-sm font-semibold text-sign-primary">Status</label>
                    <select id="vocabulary-status" name="status" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-3 py-3 text-base text-sign-text outline-none focus:border-sign-cyan focus:ring-4 focus:ring-sign-light"><option value="">All</option><option value="published" @selected(request('status') === 'published')>Published</option><option value="draft" @selected(request('status') === 'draft')>Draft</option></select>
                </div>
                <div>
                    <label for="vocabulary-video" class="mb-2 block text-sm font-semibold text-sign-primary">ISL video</label>
                    <select id="vocabulary-video" name="video" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-3 py-3 text-base text-sign-text outline-none focus:border-sign-cyan focus:ring-4 focus:ring-sign-light"><option value="">All</option><option value="with" @selected(request('video') === 'with')>With video</option><option value="without" @selected(request('video') === 'without')>Without</option></select>
                </div>
                <div class="flex gap-2"><button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white">Filter</button><a href="{{ route('admin.vocabulary.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-sign-border px-4 py-3 text-sm font-semibold text-sign-primary">Clear</a></div>
            </div>
        </form>

        <div class="mt-7 overflow-hidden rounded-2xl border border-sign-border bg-white sm:rounded-3xl">
            @if ($terms->count())
                <div class="overflow-x-auto" tabindex="0" aria-label="Scrollable vocabulary terms table">
                    <table class="min-w-full divide-y divide-sign-border text-left text-sm">
                        <caption class="sr-only">ISL vocabulary terms</caption>
                        <thead class="bg-sign-soft text-xs font-semibold uppercase tracking-wider text-sign-muted"><tr><th scope="col" class="px-5 py-4">Order</th><th scope="col" class="px-5 py-4">Term</th><th scope="col" class="px-5 py-4">Context</th><th scope="col" class="px-5 py-4">Video</th><th scope="col" class="px-5 py-4">Status</th><th scope="col" class="px-5 py-4 text-right">Actions</th></tr></thead>
                        <tbody class="divide-y divide-sign-border">
                        @foreach ($terms as $term)
                            <tr class="align-top">
                                <td class="px-5 py-4 font-semibold text-sign-muted">{{ $term->sort_order }}</td>
                                <td class="px-5 py-4"><p class="font-semibold text-sign-primary">{{ $term->term }}</p><p class="mt-1 max-w-md text-xs leading-5 text-sign-muted">{{ $term->meaning ?: 'No meaning added yet.' }}</p></td>
                                <td class="px-5 py-4 text-xs text-sign-muted"><p>{{ $term->subject?->name ?: 'General' }}</p>@if ($term->course)<p class="mt-1 font-semibold text-sign-primary">{{ $term->course->title }}</p>@endif</td>
                                <td class="px-5 py-4"><span class="rounded-full bg-sign-soft px-2.5 py-1 text-xs font-semibold text-sign-primary">{{ ($term->mediaAsset || $term->isl_video_url) ? 'Available' : 'Not added' }}</span></td>
                                <td class="px-5 py-4"><span @class(['rounded-full px-2.5 py-1 text-xs font-semibold','bg-sign-light text-sign-primary' => $term->is_published,'bg-gray-100 text-sign-muted' => ! $term->is_published])>{{ $term->is_published ? 'Published' : 'Draft' }}</span></td>
                                <td class="px-5 py-4"><div class="flex justify-end gap-2"><a href="{{ route('admin.vocabulary.edit', $term) }}" class="inline-flex min-h-10 items-center rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary">Edit</a><form method="POST" action="{{ route('admin.vocabulary.destroy', $term) }}" onsubmit="return confirm('Delete this vocabulary term?');">@csrf @method('DELETE')<button type="submit" class="inline-flex min-h-10 items-center rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700">Delete</button></form></div></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-10 text-center"><h3 class="font-heading text-2xl font-semibold text-sign-primary">No vocabulary terms found</h3><p class="mt-2 text-sm text-sign-muted">Create a term or clear the current filters.</p><a href="{{ route('admin.vocabulary.create') }}" class="mt-5 inline-flex min-h-11 items-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white">Add Term</a></div>
            @endif
        </div>
        @if ($terms->hasPages())<div class="mt-6">{{ $terms->links() }}</div>@endif
    </div>
</section>
@endsection
