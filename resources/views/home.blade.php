@extends('layouts.app')

@section('title', 'SignGyaan - Learn Visually')
@section('description', 'Accessible visual learning through Indian Sign Language with published subjects, courses and lessons from SignGyaan.')

@section('content')
    <section class="overflow-hidden bg-white py-14 sm:py-18 lg:py-24">
        <x-container>
            <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center rounded-full bg-sign-soft px-4 py-2 text-sm font-semibold text-sign-primary">
                        Visual learning through Indian Sign Language
                    </div>

                    <h1 class="mt-6 font-heading text-4xl font-semibold leading-tight tracking-tight text-sign-primary sm:text-5xl lg:text-6xl">
                        Learn visually. <span class="text-sign-cyan-dark">Learn clearly.</span>
                    </h1>

                    <p class="mt-6 max-w-xl text-base leading-7 text-sign-muted sm:text-lg sm:leading-8">
                        SignGyaan combines structured courses, clear lesson notes, practice and ISL support in one learner-friendly experience.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <x-button href="{{ route('learn') }}">Start Learning</x-button>
                        <x-button href="{{ route('subjects') }}" class="border border-sign-primary bg-white text-sign-primary hover:bg-sign-soft">Explore Subjects</x-button>
                    </div>

                    <div class="mt-8 flex flex-wrap gap-x-6 gap-y-3 text-sm text-sign-muted">
                        <span class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-sign-cyan" aria-hidden="true"></span>Published learning paths</span>
                        <span class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-sign-cyan" aria-hidden="true"></span>ISL-supported lessons</span>
                        <span class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-sign-cyan" aria-hidden="true"></span>Practice resources</span>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -left-6 -top-6 h-28 w-28 rounded-full bg-sign-light blur-2xl" aria-hidden="true"></div>
                    <div class="absolute -bottom-6 -right-6 h-32 w-32 rounded-full bg-sign-light blur-2xl" aria-hidden="true"></div>

                    <div class="relative rounded-3xl border border-sign-border bg-sign-soft p-5 shadow-sm sm:p-7">
                        <div class="rounded-2xl bg-white p-5 shadow-sm">
                            <div class="flex aspect-video items-center justify-center rounded-2xl bg-sign-primary text-white">
                                <div class="text-center">
                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white text-sign-primary shadow-sm" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="ml-1 h-7 w-7"><path d="M8.25 5.433c0-1.178 1.296-1.896 2.295-1.272l9.067 5.666c.94.588.94 1.958 0 2.546l-9.067 5.666c-.999.624-2.295-.094-2.295-1.272V5.433Z" /></svg>
                                    </div>
                                    <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-white/75">Watch · Understand · Practise</p>
                                </div>
                            </div>

                            <div class="mt-5 grid grid-cols-3 gap-3 text-center">
                                <div class="rounded-xl bg-sign-soft p-3"><p class="font-heading text-xl font-semibold text-sign-primary">{{ $subjects->count() }}</p><p class="mt-1 text-xs text-sign-muted">Subjects</p></div>
                                <div class="rounded-xl bg-sign-soft p-3"><p class="font-heading text-xl font-semibold text-sign-primary">{{ $featuredCourses->count() }}</p><p class="mt-1 text-xs text-sign-muted">Featured</p></div>
                                <div class="rounded-xl bg-sign-soft p-3"><p class="font-heading text-xl font-semibold text-sign-primary">{{ $popularLessons->count() }}</p><p class="mt-1 text-xs text-sign-muted">Recent lessons</p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-container>
    </section>

    <section class="border-y border-sign-border bg-sign-soft py-14 sm:py-18 lg:py-20">
        <x-container>
            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <x-section-heading title="What do you want to learn?" description="These subjects come directly from the published SignGyaan catalogue." />
                <a href="{{ route('subjects') }}" class="inline-flex min-h-11 items-center gap-2 text-sm font-semibold text-sign-primary transition hover:text-sign-cyan-dark">View all subjects <span aria-hidden="true">→</span></a>
            </div>

            @if ($subjects->isNotEmpty())
                <div class="mt-9 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($subjects as $subject)
                        <a href="{{ route('subjects.show', $subject->slug) }}" class="group flex h-full flex-col rounded-2xl border border-sign-border bg-white p-5 transition duration-200 hover:-translate-y-1 hover:border-sign-cyan hover:shadow-md sm:rounded-3xl sm:p-6">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sign-light text-sm font-bold text-sign-primary" aria-hidden="true">
                                {{ strtoupper(substr($subject->name, 0, 2)) }}
                            </div>
                            <h2 class="mt-5 font-heading text-xl font-semibold text-sign-primary sm:text-2xl">{{ $subject->name }}</h2>
                            <p class="mt-2 flex-1 text-sm leading-6 text-sign-muted">{{ $subject->short_description ?: ($subject->description ?: 'Explore structured courses and lessons in this subject.') }}</p>
                            <div class="mt-5 flex items-center justify-between gap-3 border-t border-sign-border pt-4">
                                <span class="text-xs font-semibold text-sign-muted">{{ $subject->courses_count }} {{ $subject->courses_count === 1 ? 'course' : 'courses' }}</span>
                                <span class="text-sm font-semibold text-sign-primary">Explore <span class="inline-block transition group-hover:translate-x-1" aria-hidden="true">→</span></span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="mt-9 rounded-3xl border border-dashed border-sign-border bg-white px-6 py-10 text-center">
                    <h2 class="font-heading text-xl font-semibold text-sign-primary">Published subjects are coming soon</h2>
                    <p class="mt-2 text-sm text-sign-muted">Once subjects are published in Admin, they will appear here automatically.</p>
                </div>
            @endif
        </x-container>
    </section>

    @auth
        @if ($continueLearning->isNotEmpty())
            <section class="bg-white py-14 sm:py-18 lg:py-20">
                <x-container>
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                        <x-section-heading title="Continue learning" description="Return to your recently saved SignGyaan courses." />
                        <a href="{{ route('my-learning') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-sign-primary hover:text-sign-cyan-dark">View My Learning →</a>
                    </div>

                    <div class="mt-9 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($continueLearning as $entry)
                            <a href="{{ $entry['url'] }}" class="group rounded-2xl border border-sign-border bg-white p-5 transition hover:border-sign-cyan hover:shadow-md sm:rounded-3xl sm:p-6">
                                <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">{{ $entry['course']->subject->name }}</p>
                                <h2 class="mt-2 font-heading text-xl font-semibold text-sign-primary">{{ $entry['course']->title }}</h2>
                                <div class="mt-5 flex items-center justify-between gap-4 text-sm">
                                    <span class="text-sign-muted">Saved progress</span>
                                    <span class="font-semibold text-sign-primary">{{ $entry['progress']->progressPercent() }}%</span>
                                </div>
                                <div class="mt-2 h-2 overflow-hidden rounded-full bg-sign-light" role="progressbar" aria-label="Saved course progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $entry['progress']->progressPercent() }}">
                                    <div class="h-full rounded-full bg-sign-primary" style="width: {{ $entry['progress']->progressPercent() }}%"></div>
                                </div>
                                <span class="mt-5 inline-flex text-sm font-semibold text-sign-primary">Continue course <span class="ml-2 transition group-hover:translate-x-1" aria-hidden="true">→</span></span>
                            </a>
                        @endforeach
                    </div>
                </x-container>
            </section>
        @endif
    @endauth

    <section class="{{ auth()->check() && $continueLearning->isNotEmpty() ? 'border-t' : '' }} border-sign-border bg-white py-14 sm:py-18 lg:py-20">
        <x-container>
            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <x-section-heading title="Featured courses" description="Start with published courses selected by the SignGyaan Admin team." />
                <a href="{{ route('learn') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-sign-primary hover:text-sign-cyan-dark">View learning paths →</a>
            </div>

            @if ($featuredCourses->isNotEmpty())
                <div class="mt-9 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($featuredCourses as $course)
                        <article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-sign-border bg-white transition duration-200 hover:-translate-y-1 hover:border-sign-cyan hover:shadow-lg sm:rounded-3xl">
                            <div class="bg-sign-soft p-5 sm:p-6">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-sign-primary">{{ $course->level }}</span>
                                    @if ($course->is_featured)<span class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Featured</span>@endif
                                </div>
                                <p class="mt-5 text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">{{ $course->subject->name }}</p>
                                <h2 class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-2xl">{{ $course->title }}</h2>
                            </div>
                            <div class="flex flex-1 flex-col p-5 sm:p-6">
                                <p class="flex-1 text-sm leading-6 text-sign-muted">{{ $course->short_description ?: ($course->description ?: 'Follow this course through clear units, lessons and practice.') }}</p>
                                <div class="mt-5 flex flex-wrap gap-3 text-xs font-semibold text-sign-muted">
                                    <span>{{ $course->units_count }} {{ $course->units_count === 1 ? 'unit' : 'units' }}</span>
                                    <span aria-hidden="true">•</span>
                                    <span>{{ $course->lessons_count }} {{ $course->lessons_count === 1 ? 'lesson' : 'lessons' }}</span>
                                </div>
                                <a href="{{ route('courses.show', ['subject' => $course->subject->slug, 'course' => $course->slug]) }}" class="mt-5 inline-flex min-h-11 items-center gap-2 text-sm font-semibold text-sign-primary">View course <span class="transition group-hover:translate-x-1" aria-hidden="true">→</span></a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="mt-9 rounded-3xl border border-dashed border-sign-border bg-sign-soft px-6 py-10 text-center">
                    <h2 class="font-heading text-xl font-semibold text-sign-primary">Courses are being prepared</h2>
                    <p class="mt-2 text-sm text-sign-muted">Published courses will appear here automatically.</p>
                </div>
            @endif
        </x-container>
    </section>
@endsection
