@extends('layouts.app')

@section('title', $query !== '' ? 'Search: ' . $query . ' - SignGyaan' : 'Search - SignGyaan')
@section('description', 'Search published SignGyaan subjects, courses, lessons and learning topics.')

@section('content')
    @php
        $typeLabels = [
            'all' => 'All Results',
            'subject' => 'Subjects',
            'course' => 'Courses',
            'lesson' => 'Lessons',
            'topic' => 'Topics',
        ];
    @endphp

    <section class="border-b border-sign-border bg-sign-soft py-10 sm:py-14 lg:py-16">
        <x-container>
            <div class="mx-auto max-w-4xl">
                <nav class="flex flex-wrap items-center gap-2 text-sm text-sign-muted" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="transition hover:text-sign-primary">Home</a>
                    <span aria-hidden="true">/</span>
                    <span class="font-semibold text-sign-primary">Search</span>
                </nav>

                <div class="mt-7 text-center">
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Search SignGyaan</p>
                    <h1 class="mt-3 font-heading text-3xl font-semibold tracking-tight text-sign-primary sm:text-5xl">
                        Find your next learning step
                    </h1>
                    <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-sign-muted sm:text-base">
                        Search the published learning catalogue across subjects, courses, lesson content, practice material and curriculum topics.
                    </p>
                </div>

                <form method="GET" action="{{ route('search') }}" class="mt-8" role="search">
                    @if ($activeType !== 'all')
                        <input type="hidden" name="type" value="{{ $activeType }}">
                    @endif
                    @if ($activeSubject !== 'all')
                        <input type="hidden" name="subject" value="{{ $activeSubject }}">
                    @endif

                    <div class="flex flex-col gap-2 rounded-2xl border border-sign-border bg-white p-2 shadow-sm sm:flex-row sm:items-center">
                        <div class="flex min-w-0 flex-1 items-center gap-3 px-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 shrink-0 text-sign-muted" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.4a6.75 6.75 0 1 1-13.5 0 6.75 6.75 0 0 1 13.5 0Z" />
                            </svg>
                            <label for="catalog-search" class="sr-only">Search SignGyaan</label>
                            <input
                                id="catalog-search"
                                type="search"
                                name="q"
                                value="{{ $query }}"
                                maxlength="120"
                                placeholder="Try computer, English, fractions, internet safety..."
                                autocomplete="off"
                                class="min-h-12 w-full min-w-0 border-0 bg-transparent text-base text-sign-text outline-none placeholder:text-sign-muted/70"
                            >
                        </div>
                        <button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-xl bg-sign-primary px-6 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">
                            Search
                        </button>
                    </div>
                </form>

                <div class="mt-4 flex flex-wrap justify-center gap-x-4 gap-y-2 text-xs text-sign-muted sm:text-sm">
                    <span>Try:</span>
                    <a href="{{ route('search', ['q' => 'computer']) }}" class="font-semibold text-sign-primary hover:text-sign-cyan-dark">Computer</a>
                    <a href="{{ route('search', ['q' => 'english']) }}" class="font-semibold text-sign-primary hover:text-sign-cyan-dark">English</a>
                    <a href="{{ route('search', ['q' => 'ISL']) }}" class="font-semibold text-sign-primary hover:text-sign-cyan-dark">ISL</a>
                    <a href="{{ route('search', ['q' => 'practice']) }}" class="font-semibold text-sign-primary hover:text-sign-cyan-dark">Practice</a>
                </div>
            </div>
        </x-container>
    </section>

    <section class="bg-white py-10 sm:py-14 lg:py-20">
        <x-container>
            <div class="grid gap-5 lg:grid-cols-[14rem_minmax(0,1fr)] lg:gap-8">
                <aside class="lg:sticky lg:top-28 lg:self-start" aria-label="Search filters">
                    <form method="GET" action="{{ route('search') }}" class="rounded-2xl border border-sign-border bg-sign-soft p-4 sm:p-5">
                        <input type="hidden" name="q" value="{{ $query }}">
                        @if ($activeType !== 'all')
                            <input type="hidden" name="type" value="{{ $activeType }}">
                        @endif

                        <label for="search-subject-filter" class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Subject</label>
                        <select id="search-subject-filter" name="subject" onchange="this.form.submit()" class="mt-2 min-h-11 w-full rounded-xl border border-sign-border bg-white px-3 py-2.5 text-sm text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="all">All Subjects</option>
                            @foreach ($subjects as $subjectOption)
                                <option value="{{ $subjectOption->slug }}" @selected($activeSubject === $subjectOption->slug)>{{ $subjectOption->name }}</option>
                            @endforeach
                        </select>

                        @if ($query !== '' || $activeSubject !== 'all' || $activeType !== 'all')
                            <a href="{{ route('search') }}" class="mt-4 inline-flex min-h-10 items-center text-sm font-semibold text-sign-primary hover:text-sign-cyan-dark">Clear filters</a>
                        @endif
                    </form>
                </aside>

                <div class="min-w-0">
                    <div class="flex flex-col gap-4 border-b border-sign-border pb-6 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Database search</p>
                            <h2 class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">
                                @if ($query !== '')
                                    Results for “{{ $query }}”
                                @else
                                    Search the learning catalogue
                                @endif
                            </h2>
                            @if ($query !== '')
                                <p class="mt-2 text-sm text-sign-muted">{{ $totalResults }} {{ $totalResults === 1 ? 'result' : 'results' }} found in {{ $typeLabels[$activeType] ?? 'All Results' }}.</p>
                            @endif
                        </div>
                    </div>

                    <div class="-mx-4 mt-5 overflow-x-auto px-4 pb-2 sm:mx-0 sm:px-0" aria-label="Search result types">
                        <div class="flex min-w-max gap-2 sm:flex-wrap">
                            @foreach ($typeLabels as $typeKey => $typeLabel)
                                @php
                                    $params = ['q' => $query];
                                    if ($typeKey !== 'all') $params['type'] = $typeKey;
                                    if ($activeSubject !== 'all') $params['subject'] = $activeSubject;
                                @endphp
                                <a
                                    href="{{ route('search', $params) }}"
                                    @class([
                                        'inline-flex min-h-10 items-center justify-center rounded-full border px-4 py-2 text-sm font-semibold transition',
                                        'border-sign-primary bg-sign-primary text-white' => $activeType === $typeKey,
                                        'border-sign-border bg-white text-sign-primary hover:border-sign-cyan hover:bg-sign-soft' => $activeType !== $typeKey,
                                    ])
                                    @if ($activeType === $typeKey) aria-current="page" @endif
                                >
                                    {{ $typeLabel }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    @if ($query === '')
                        <div class="mt-8 rounded-3xl border border-sign-border bg-sign-soft p-6 sm:p-8">
                            <h3 class="font-heading text-2xl font-semibold text-sign-primary">Start with a keyword</h3>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-sign-muted">Search by a subject, course name, lesson idea, unit topic, lesson notes or published practice content.</p>

                            @if ($suggestedCourses->isNotEmpty())
                                <div class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                    @foreach ($suggestedCourses as $courseSuggestion)
                                        <a href="{{ route('courses.show', ['subject' => $courseSuggestion->subject->slug, 'course' => $courseSuggestion->slug]) }}" class="rounded-2xl border border-sign-border bg-white p-4 transition hover:border-sign-cyan hover:shadow-sm">
                                            <p class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">{{ $courseSuggestion->subject->name }}</p>
                                            <h4 class="mt-2 font-heading text-lg font-semibold text-sign-primary">{{ $courseSuggestion->title }}</h4>
                                            <p class="mt-2 text-xs text-sign-muted">{{ $courseSuggestion->level }}{{ $courseSuggestion->is_featured ? ' · Featured' : '' }}</p>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @elseif ($totalResults === 0)
                        <div class="mt-8 rounded-3xl border border-dashed border-sign-border bg-sign-soft px-6 py-12 text-center sm:px-10">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-sign-primary shadow-sm" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.4a6.75 6.75 0 1 1-13.5 0 6.75 6.75 0 0 1 13.5 0Z" /></svg>
                            </div>
                            <h3 class="mt-5 font-heading text-2xl font-semibold text-sign-primary">No matching learning found</h3>
                            <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-sign-muted">Try fewer words, another subject, or a broader term such as computer, English, science or communication.</p>
                            <a href="{{ route('search') }}" class="mt-6 inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Start a new search</a>
                        </div>
                    @else
                        <div class="mt-8 space-y-10">
                            @if (in_array($activeType, ['all', 'subject'], true) && $subjectResults->isNotEmpty())
                                <section aria-labelledby="search-subjects-heading">
                                    <div class="flex items-center justify-between gap-4">
                                        <h3 id="search-subjects-heading" class="font-heading text-2xl font-semibold text-sign-primary">Subjects</h3>
                                        <span class="text-sm font-semibold text-sign-muted">{{ $subjectResults->count() }}</span>
                                    </div>
                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        @foreach ($subjectResults as $result)
                                            <a href="{{ route('subjects.show', $result->slug) }}" class="group rounded-2xl border border-sign-border bg-white p-5 transition hover:border-sign-cyan hover:shadow-sm">
                                                <div class="flex items-start justify-between gap-4">
                                                    <div>
                                                        <p class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">Subject</p>
                                                        <h4 class="mt-2 font-heading text-xl font-semibold text-sign-primary">{{ $result->name }}</h4>
                                                    </div>
                                                    <span class="rounded-full bg-sign-soft px-3 py-1 text-xs font-semibold text-sign-primary">{{ $result->courses_count }} {{ $result->courses_count === 1 ? 'course' : 'courses' }}</span>
                                                </div>
                                                <p class="mt-3 text-sm leading-6 text-sign-muted">{{ $result->short_description ?: ($result->description ?: 'Explore published courses in this subject.') }}</p>
                                                <span class="mt-4 inline-flex text-sm font-semibold text-sign-primary">Explore subject <span class="ml-2 transition group-hover:translate-x-1">→</span></span>
                                            </a>
                                        @endforeach
                                    </div>
                                </section>
                            @endif

                            @if (in_array($activeType, ['all', 'course'], true) && $courseResults->isNotEmpty())
                                <section aria-labelledby="search-courses-heading">
                                    <div class="flex items-center justify-between gap-4">
                                        <h3 id="search-courses-heading" class="font-heading text-2xl font-semibold text-sign-primary">Courses</h3>
                                        <span class="text-sm font-semibold text-sign-muted">{{ $courseResults->count() }}</span>
                                    </div>
                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        @foreach ($courseResults as $result)
                                            <a href="{{ route('courses.show', ['subject' => $result->subject->slug, 'course' => $result->slug]) }}" class="group flex h-full flex-col rounded-2xl border border-sign-border bg-white p-5 transition hover:border-sign-cyan hover:shadow-sm">
                                                <div class="flex flex-wrap items-center justify-between gap-2">
                                                    <span class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">{{ $result->subject->name }}</span>
                                                    <div class="flex gap-2">
                                                        @if ($result->is_featured)<span class="rounded-full bg-sign-light px-2.5 py-1 text-[11px] font-semibold text-sign-primary">Featured</span>@endif
                                                        <span class="rounded-full bg-sign-soft px-2.5 py-1 text-[11px] font-semibold text-sign-muted">{{ $result->level }}</span>
                                                    </div>
                                                </div>
                                                <h4 class="mt-3 font-heading text-xl font-semibold text-sign-primary">{{ $result->title }}</h4>
                                                <p class="mt-2 flex-1 text-sm leading-6 text-sign-muted">{{ $result->short_description ?: ($result->description ?: 'Structured visual learning course.') }}</p>
                                                <div class="mt-4 flex items-center justify-between gap-3 border-t border-sign-border pt-4 text-xs text-sign-muted">
                                                    <span>{{ $result->units_count }} {{ $result->units_count === 1 ? 'unit' : 'units' }} · {{ $result->lessons_count }} {{ $result->lessons_count === 1 ? 'lesson' : 'lessons' }}</span>
                                                    <span class="font-semibold text-sign-primary transition group-hover:translate-x-1">Open →</span>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </section>
                            @endif

                            @if (in_array($activeType, ['all', 'lesson'], true) && $lessonResults->isNotEmpty())
                                <section aria-labelledby="search-lessons-heading">
                                    <div class="flex items-center justify-between gap-4">
                                        <h3 id="search-lessons-heading" class="font-heading text-2xl font-semibold text-sign-primary">Lessons</h3>
                                        <span class="text-sm font-semibold text-sign-muted">{{ $lessonResults->count() }}</span>
                                    </div>
                                    <div class="mt-4 space-y-3">
                                        @foreach ($lessonResults as $result)
                                            @php
                                                $lessonCourse = $result->unit->course;
                                                $lessonSubject = $lessonCourse->subject;
                                                $hasIsl = filled($result->isl_video_url) || ($result->mediaAsset?->is_published && $result->mediaAsset?->publicUrl());
                                            @endphp
                                            <a href="{{ route('courses.show', ['subject' => $lessonSubject->slug, 'course' => $lessonCourse->slug, 'lesson' => 'lesson-' . $result->id]) }}" class="group block rounded-2xl border border-sign-border bg-white p-5 transition hover:border-sign-cyan hover:shadow-sm">
                                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                    <div class="min-w-0">
                                                        <p class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">{{ $lessonSubject->name }} · {{ $lessonCourse->title }}</p>
                                                        <h4 class="mt-2 font-heading text-xl font-semibold text-sign-primary">{{ $result->title }}</h4>
                                                        <p class="mt-1 text-xs font-semibold text-sign-muted">{{ $result->unit->title }}</p>
                                                        <p class="mt-3 text-sm leading-6 text-sign-muted">{{ $result->short_description ?: 'Open this lesson for notes, examples, practice and visual learning support.' }}</p>
                                                    </div>
                                                    <div class="flex shrink-0 flex-wrap gap-2">
                                                        @if ($hasIsl)<span class="rounded-full bg-sign-light px-2.5 py-1 text-xs font-semibold text-sign-primary">ISL</span>@endif
                                                        @if ($result->estimated_duration_minutes)<span class="rounded-full bg-sign-soft px-2.5 py-1 text-xs font-semibold text-sign-muted">{{ $result->estimated_duration_minutes }} min</span>@endif
                                                    </div>
                                                </div>
                                                <span class="mt-4 inline-flex text-sm font-semibold text-sign-primary">Open lesson <span class="ml-2 transition group-hover:translate-x-1">→</span></span>
                                            </a>
                                        @endforeach
                                    </div>
                                </section>
                            @endif

                            @if (in_array($activeType, ['all', 'topic'], true) && $topicResults->isNotEmpty())
                                <section aria-labelledby="search-topics-heading">
                                    <div class="flex items-center justify-between gap-4">
                                        <h3 id="search-topics-heading" class="font-heading text-2xl font-semibold text-sign-primary">Topics</h3>
                                        <span class="text-sm font-semibold text-sign-muted">{{ $topicResults->count() }}</span>
                                    </div>
                                    <p class="mt-1 text-sm text-sign-muted">Curriculum units that group related lessons and concepts.</p>
                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        @foreach ($topicResults as $result)
                                            <a href="{{ route('courses.show', ['subject' => $result->course->subject->slug, 'course' => $result->course->slug]) }}#course-unit-heading-{{ $result->id }}" class="group rounded-2xl border border-sign-border bg-sign-soft p-5 transition hover:border-sign-cyan hover:shadow-sm">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">{{ $result->course->subject->name }} · {{ $result->course->title }}</p>
                                                <h4 class="mt-2 font-heading text-xl font-semibold text-sign-primary">{{ $result->title }}</h4>
                                                <p class="mt-2 text-sm leading-6 text-sign-muted">{{ $result->short_description ?: ($result->description ?: 'Open this curriculum topic and its published lessons.') }}</p>
                                                <div class="mt-4 flex items-center justify-between text-xs text-sign-muted">
                                                    <span>{{ $result->lessons_count }} {{ $result->lessons_count === 1 ? 'lesson' : 'lessons' }}</span>
                                                    <span class="font-semibold text-sign-primary transition group-hover:translate-x-1">View topic →</span>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </section>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </x-container>
    </section>
@endsection
