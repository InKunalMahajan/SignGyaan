@extends('layouts.app')

@section('title', 'ISL Vocabulary - SignGyaan')
@section('description', 'Browse published Indian Sign Language vocabulary by keyword, subject and course.')

@section('content')
    <section class="border-b border-sign-border bg-sign-soft py-10 sm:py-14 lg:py-20">
        <x-container>
            <div class="max-w-3xl">
                <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark sm:text-sm">Indian Sign Language</p>
                <h1 class="mt-3 font-heading text-3xl font-semibold leading-tight tracking-tight text-sign-primary sm:text-5xl">ISL Vocabulary Library</h1>
                <p class="mt-4 max-w-2xl text-sm leading-7 text-sign-muted sm:mt-5 sm:text-lg sm:leading-8">Find reusable signs, meanings and examples connected to SignGyaan subjects and courses.</p>
            </div>
        </x-container>
    </section>

    <section class="bg-white py-8 sm:py-12 lg:py-16">
        <x-container>
            <form method="GET" action="{{ route('vocabulary.index') }}" role="search" aria-label="Search ISL vocabulary" class="rounded-2xl border border-sign-border bg-sign-soft p-4 sm:rounded-3xl sm:p-6">
                <div class="grid gap-4 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,.8fr)_minmax(0,.9fr)_auto] lg:items-end">
                    <div>
                        <label for="vocabulary-search" class="mb-2 block text-sm font-semibold text-sign-primary">Keyword</label>
                        <input id="vocabulary-search" type="search" name="q" value="{{ $search }}" placeholder="Search term, meaning or example" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                    </div>

                    <div>
                        <label for="vocabulary-subject" class="mb-2 block text-sm font-semibold text-sign-primary">Subject</label>
                        <select id="vocabulary-subject" name="subject" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All subjects</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->slug }}" @selected($subjectSlug === $subject->slug)>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="vocabulary-course" class="mb-2 block text-sm font-semibold text-sign-primary">Course</label>
                        <select id="vocabulary-course" name="course" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All courses</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->slug }}" @selected($courseSlug === $course->slug)>{{ $course->subject?->name }} — {{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Search</button>
                        @if ($search !== '' || $subjectSlug !== '' || $courseSlug !== '')
                            <a href="{{ route('vocabulary.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-white/70">Clear</a>
                        @endif
                    </div>
                </div>
            </form>

            <div class="mt-8 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Vocabulary</p>
                    <h2 class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">{{ $terms->total() }} {{ $terms->total() === 1 ? 'sign' : 'signs' }} found</h2>
                </div>
                <p class="text-sm text-sign-muted">Only published vocabulary and published learning categories are shown.</p>
            </div>

            @if ($terms->count())
                <div class="mt-7 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($terms as $term)
                        @php
                            $linkedMedia = $term->mediaAsset;
                            $videoUrl = $linkedMedia?->publicUrl() ?: $term->isl_video_url;
                        @endphp
                        <article class="flex h-full flex-col rounded-2xl border border-sign-border bg-white p-5 shadow-sm transition hover:border-sign-cyan hover:shadow-md sm:rounded-3xl sm:p-6">
                            <div class="flex flex-wrap items-center gap-2 text-xs">
                                @if ($term->subject)
                                    <span class="rounded-full bg-sign-light px-3 py-1.5 font-semibold text-sign-primary">{{ $term->subject->name }}</span>
                                @endif
                                @if ($term->course)
                                    <span class="rounded-full bg-sign-soft px-3 py-1.5 font-semibold text-sign-muted">{{ $term->course->title }}</span>
                                @endif
                                @if ($videoUrl)
                                    <span class="rounded-full border border-sign-border bg-white px-3 py-1.5 font-semibold text-sign-cyan-dark">ISL video</span>
                                @endif
                            </div>

                            <h3 class="mt-4 font-heading text-2xl font-semibold text-sign-primary">{{ $term->term }}</h3>
                            <p class="mt-3 flex-1 text-sm leading-6 text-sign-muted">{{ $term->meaning ?: 'Meaning will be added soon.' }}</p>

                            @if ($term->example)
                                <div class="mt-4 rounded-xl bg-sign-soft p-4">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Example</p>
                                    <p class="mt-2 text-sm leading-6 text-sign-muted">{{ $term->example }}</p>
                                </div>
                            @endif

                            <a href="{{ route('vocabulary.show', $term->slug) }}" class="mt-5 inline-flex min-h-11 items-center gap-2 text-sm font-semibold text-sign-primary transition hover:text-sign-cyan-dark">View sign <span aria-hidden="true">→</span></a>
                        </article>
                    @endforeach
                </div>

                @if ($terms->hasPages())
                    <div class="mt-8">{{ $terms->links() }}</div>
                @endif
            @else
                <div class="mt-8 rounded-2xl border border-dashed border-sign-border bg-sign-soft p-8 text-center sm:rounded-3xl sm:p-12">
                    <h2 class="font-heading text-2xl font-semibold text-sign-primary">No vocabulary found</h2>
                    <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-sign-muted">Try another keyword, subject or course, or clear the filters to browse all published signs.</p>
                    <a href="{{ route('vocabulary.index') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white">Browse all vocabulary</a>
                </div>
            @endif
        </x-container>
    </section>
@endsection
