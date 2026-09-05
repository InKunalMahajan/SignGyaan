@extends('layouts.app')

@section('title', $subject->name . ' - SignGyaan')
@section('description', $subject->description ?: ($subject->short_description ?: 'Explore SignGyaan courses and lessons.'))

@section('content')
    @php
        $subjectCodes = [
            'english' => 'Aa',
            'mathematics' => '123',
            'science' => 'SCI',
            'digital-skills' => 'PC',
            'general-knowledge' => 'GK',
            'life-skills' => 'LS',
        ];
        $subjectCode = $subjectCodes[$subject->slug] ?? strtoupper(substr($subject->name, 0, 2));
        $totalUnits = $subject->courses->sum('units_count');
        $totalLessons = $subject->courses->sum('lessons_count');
    @endphp

    <section class="border-b border-sign-border bg-sign-soft py-8 sm:py-10 lg:py-16">
        <x-container>
            <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-sign-muted sm:text-sm" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="transition hover:text-sign-primary">Home</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('subjects') }}" class="transition hover:text-sign-primary">Subjects</a>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-sign-primary">{{ $subject->name }}</span>
            </nav>

            <div class="mt-6 grid items-start gap-7 sm:mt-8 lg:grid-cols-[minmax(0,1fr)_20rem] lg:gap-10 xl:grid-cols-[minmax(0,1fr)_22rem] xl:gap-12">
                <div class="max-w-3xl">
                    <div class="flex h-14 min-w-14 w-fit items-center justify-center rounded-2xl bg-white px-3 text-base font-bold text-sign-primary shadow-sm ring-1 ring-sign-border">{{ $subjectCode }}</div>
                    <p class="mt-5 text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark sm:mt-6 sm:text-sm">Structured learning</p>
                    <h1 class="mt-3 font-heading text-3xl font-semibold leading-tight tracking-tight text-sign-primary sm:text-5xl lg:text-6xl">{{ $subject->name }}</h1>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-sign-muted sm:mt-5 sm:text-lg sm:leading-8">{{ $subject->description ?: ($subject->short_description ?: 'Explore published courses, units and lessons in this subject.') }}</p>
                    <div class="mt-6 flex flex-wrap gap-2 text-xs text-sign-muted sm:mt-7 sm:gap-3 sm:text-sm">
                        <span class="rounded-full border border-sign-border bg-white px-3 py-2 sm:px-4">{{ $subject->courses->count() }} Courses</span>
                        <span class="rounded-full border border-sign-border bg-white px-3 py-2 sm:px-4">{{ $totalUnits }} Units</span>
                        <span class="rounded-full border border-sign-border bg-white px-3 py-2 sm:px-4">{{ $totalLessons }} Lessons</span>
                    </div>
                </div>

                <aside class="w-full rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl sm:p-6" aria-label="Subject starting point">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Start here</p>
                    @if ($featuredCourse)
                        <h2 class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-2xl">{{ $featuredCourse->title }}</h2>
                        <p class="mt-3 text-sm leading-6 text-sign-muted">{{ $featuredCourse->short_description ?: 'Begin with this structured course and move lesson by lesson.' }}</p>
                        <div class="mt-5 grid grid-cols-2 gap-3 text-center text-sm">
                            <div class="rounded-xl bg-sign-soft p-3">
                                <p class="font-semibold text-sign-primary">{{ $featuredCourse->units_count }}</p>
                                <p class="mt-1 text-xs text-sign-muted">Units</p>
                            </div>
                            <div class="rounded-xl bg-sign-soft p-3">
                                <p class="font-semibold text-sign-primary">{{ $featuredCourse->lessons_count }}</p>
                                <p class="mt-1 text-xs text-sign-muted">Lessons</p>
                            </div>
                        </div>
                        <a href="{{ route('courses.show', ['subject' => $subject->slug, 'course' => $featuredCourse->slug]) }}" class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Start featured course</a>
                    @else
                        <h2 class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Courses coming soon</h2>
                        <p class="mt-3 text-sm leading-6 text-sign-muted">Published courses added by an administrator will appear here automatically.</p>
                    @endif
                </aside>
            </div>
        </x-container>
    </section>

    <section class="bg-white py-10 sm:py-14 lg:py-20">
        <x-container>
            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <x-section-heading title="Courses in {{ $subject->name }}" description="Choose a published course and move through its units and lessons in sequence." />
                <a href="{{ route('learn') }}" class="inline-flex min-h-11 items-center gap-2 self-start text-sm font-semibold text-sign-primary transition hover:text-sign-cyan-dark sm:self-auto">View all learning <span aria-hidden="true">→</span></a>
            </div>

            @if ($subject->courses->isNotEmpty())
                <div class="mt-8 grid gap-4 sm:mt-10 sm:grid-cols-2 lg:grid-cols-3 lg:gap-6">
                    @foreach ($subject->courses as $course)
                        <article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-sign-border bg-white transition duration-200 hover:-translate-y-1 hover:border-sign-cyan hover:shadow-lg sm:rounded-3xl">
                            <div class="bg-sign-soft p-5 sm:p-6">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-sign-primary shadow-sm">{{ $course->level }}</span>
                                    @if ($course->is_featured)
                                        <span class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">Featured</span>
                                    @endif
                                </div>
                                <h2 class="mt-5 font-heading text-xl font-semibold text-sign-primary sm:mt-6 sm:text-2xl">{{ $course->title }}</h2>
                            </div>
                            <div class="flex flex-1 flex-col p-5 sm:p-6">
                                <p class="text-sm leading-6 text-sign-muted">{{ $course->short_description ?: ($course->description ?: 'Structured visual learning with clear lessons and practice.') }}</p>
                                <div class="mt-5 flex flex-wrap gap-x-3 gap-y-2 text-sm text-sign-muted sm:mt-6 sm:gap-4">
                                    <span>{{ $course->units_count }} Units</span>
                                    <span aria-hidden="true">•</span>
                                    <span>{{ $course->lessons_count }} Lessons</span>
                                </div>
                                <a href="{{ route('courses.show', ['subject' => $subject->slug, 'course' => $course->slug]) }}" class="mt-5 inline-flex min-h-11 items-center gap-2 text-sm font-semibold text-sign-primary sm:mt-6">View course <span class="transition group-hover:translate-x-1" aria-hidden="true">→</span></a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="mt-10 rounded-2xl border border-dashed border-sign-border bg-sign-soft p-8 text-center sm:rounded-3xl sm:p-12">
                    <h2 class="font-heading text-2xl font-semibold text-sign-primary">No published courses yet</h2>
                    <p class="mt-3 text-sm leading-6 text-sign-muted">Courses published from the Admin Console will appear here automatically.</p>
                </div>
            @endif
        </x-container>
    </section>

    <section class="border-y border-sign-border bg-sign-soft py-10 sm:py-14 lg:py-20">
        <x-container>
            <div class="grid gap-8 lg:grid-cols-[0.8fr_1.2fr] lg:items-start lg:gap-10">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark sm:text-sm">Learning approach</p>
                    <h2 class="mt-3 font-heading text-2xl font-semibold text-sign-primary sm:text-4xl">What you can expect</h2>
                    <p class="mt-4 max-w-xl text-sm leading-7 text-sign-muted sm:text-base">Published content is organised into smaller steps so learners can understand, practise and revise without feeling overloaded.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2 sm:gap-4">
                    @foreach (['Clear course structure', 'Visual explanations and examples', 'ISL support where available', 'Practice and revision activities'] as $item)
                        <div class="flex gap-3 rounded-2xl border border-sign-border bg-white p-4 sm:gap-4 sm:p-5">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-sign-light text-sign-primary sm:h-10 sm:w-10" aria-hidden="true">✓</div>
                            <p class="pt-1 text-sm font-semibold leading-6 text-sign-primary sm:pt-2">{{ $item }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-container>
    </section>

    <section class="bg-white py-10 sm:py-14 lg:py-20">
        <x-container>
            <div class="rounded-2xl bg-sign-dark px-5 py-8 text-white sm:rounded-3xl sm:px-10 sm:py-12 lg:flex lg:items-center lg:justify-between lg:gap-10">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan sm:text-sm">Ready to begin?</p>
                    <h2 class="mt-3 font-heading text-2xl font-semibold leading-tight sm:text-4xl">Start learning {{ $subject->name }} step by step.</h2>
                    <p class="mt-4 text-sm leading-7 text-white/75 sm:text-base">Choose a course, follow its units and continue at your own pace.</p>
                </div>
                <div class="mt-6 flex flex-col gap-3 sm:flex-row lg:mt-0">
                    @if ($featuredCourse)
                        <a href="{{ route('courses.show', ['subject' => $subject->slug, 'course' => $featuredCourse->slug]) }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-white px-6 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft sm:w-auto">Start Learning</a>
                    @endif
                    <a href="{{ route('subjects') }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-white/40 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10 sm:w-auto">All Subjects</a>
                </div>
            </div>
        </x-container>
    </section>
@endsection
