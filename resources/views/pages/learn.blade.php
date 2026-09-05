@extends('layouts.app')

@section('title', 'Learn - SignGyaan')
@section('description', 'Explore published SignGyaan subjects, courses and structured visual lessons with Indian Sign Language support.')

@section('content')
    <section class="border-b border-sign-border bg-sign-soft py-14 sm:py-16 lg:py-20">
        <x-container>
            <div class="grid items-center gap-10 lg:grid-cols-[1.15fr_0.85fr] lg:gap-16">
                <div class="max-w-3xl">
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Learn on SignGyaan</p>
                    <h1 class="mt-3 font-heading text-4xl font-semibold tracking-tight text-sign-primary sm:text-5xl lg:text-6xl">Choose a path and learn step by step.</h1>
                    <p class="mt-5 max-w-2xl text-base leading-7 text-sign-muted sm:text-lg sm:leading-8">Everything on this page comes from the published SignGyaan catalogue. Choose a subject, open a course, then move through its units and lessons.</p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <x-button href="{{ route('subjects') }}">Browse Subjects</x-button>
                        <a href="#learning-paths" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-primary px-5 py-3 text-sm font-semibold text-sign-primary transition hover:bg-white">View Learning Paths</a>
                    </div>
                </div>

                <aside class="rounded-3xl border border-sign-border bg-white p-6 shadow-sm sm:p-7" aria-label="Published learning catalogue summary">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Published catalogue</p>
                    <div class="mt-5 grid grid-cols-3 gap-3 text-center">
                        <div class="rounded-2xl bg-sign-soft p-4"><p class="font-heading text-2xl font-semibold text-sign-primary">{{ $catalogCounts['subjects'] }}</p><p class="mt-1 text-xs text-sign-muted">Subjects</p></div>
                        <div class="rounded-2xl bg-sign-soft p-4"><p class="font-heading text-2xl font-semibold text-sign-primary">{{ $catalogCounts['courses'] }}</p><p class="mt-1 text-xs text-sign-muted">Courses</p></div>
                        <div class="rounded-2xl bg-sign-soft p-4"><p class="font-heading text-2xl font-semibold text-sign-primary">{{ $catalogCounts['lessons'] }}</p><p class="mt-1 text-xs text-sign-muted">Lessons</p></div>
                    </div>
                    <div class="mt-5 space-y-3 text-sm text-sign-muted">
                        <div class="flex items-center gap-3 rounded-xl bg-sign-soft p-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-sign-primary font-semibold text-white">1</span><span>Choose a subject</span></div>
                        <div class="flex items-center gap-3 rounded-xl bg-sign-soft p-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-sign-primary font-semibold text-white">2</span><span>Follow a course</span></div>
                        <div class="flex items-center gap-3 rounded-xl bg-sign-soft p-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-sign-primary font-semibold text-white">3</span><span>Complete units and lessons</span></div>
                    </div>
                </aside>
            </div>
        </x-container>
    </section>

    <section id="learning-paths" class="scroll-mt-24 bg-white py-14 sm:py-16 lg:py-20">
        <x-container>
            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <x-section-heading title="Learning paths" description="Featured courses appear first, followed by other published courses from the catalogue." />
                <a href="{{ route('explore', ['type' => 'course']) }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-sign-primary hover:text-sign-cyan-dark">Explore all courses →</a>
            </div>

            @if ($learningPaths->isNotEmpty())
                <div class="mt-9 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($learningPaths as $course)
                        <article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-sign-border bg-white transition duration-200 hover:-translate-y-1 hover:border-sign-cyan hover:shadow-lg sm:rounded-3xl">
                            <div class="bg-sign-soft p-5 sm:p-6">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-sm font-bold text-sign-primary shadow-sm" aria-hidden="true">{{ strtoupper(substr($course->subject->name, 0, 2)) }}</div>
                                    <div class="flex flex-wrap justify-end gap-2">
                                        @if ($course->is_featured)<span class="rounded-full bg-sign-light px-3 py-1 text-xs font-semibold text-sign-primary">Featured</span>@endif
                                        <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-sign-primary">{{ $course->level }}</span>
                                    </div>
                                </div>
                                <p class="mt-6 text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">{{ $course->subject->name }}</p>
                                <h2 class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-2xl">{{ $course->title }}</h2>
                            </div>

                            <div class="flex flex-1 flex-col p-5 sm:p-6">
                                <p class="flex-1 text-sm leading-6 text-sign-muted">{{ $course->short_description ?: ($course->description ?: 'A structured course with visual lessons and practice.') }}</p>
                                <div class="mt-5 flex flex-wrap gap-3 text-xs font-semibold text-sign-muted">
                                    <span>{{ $course->units_count }} {{ $course->units_count === 1 ? 'unit' : 'units' }}</span><span aria-hidden="true">•</span><span>{{ $course->lessons_count }} {{ $course->lessons_count === 1 ? 'lesson' : 'lessons' }}</span>
                                </div>
                                <a href="{{ route('courses.show', ['subject' => $course->subject->slug, 'course' => $course->slug]) }}" class="mt-5 inline-flex min-h-11 items-center gap-2 text-sm font-semibold text-sign-primary">View learning path <span class="transition group-hover:translate-x-1" aria-hidden="true">→</span></a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="mt-9 rounded-3xl border border-dashed border-sign-border bg-sign-soft px-6 py-10 text-center">
                    <h2 class="font-heading text-xl font-semibold text-sign-primary">Learning paths are being prepared</h2>
                    <p class="mt-2 text-sm text-sign-muted">Published courses will appear here automatically.</p>
                </div>
            @endif
        </x-container>
    </section>

    <section class="border-y border-sign-border bg-sign-soft py-14 sm:py-16 lg:py-20">
        <x-container>
            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <x-section-heading title="Browse by subject" description="See how many published courses are available in each learning area." />
                <a href="{{ route('subjects') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-sign-primary hover:text-sign-cyan-dark">All subjects →</a>
            </div>

            @if ($subjects->isNotEmpty())
                <div class="mt-9 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($subjects as $subject)
                        <a href="{{ route('subjects.show', $subject->slug) }}" class="group rounded-2xl border border-sign-border bg-white p-5 transition hover:-translate-y-1 hover:border-sign-cyan hover:shadow-md sm:rounded-3xl sm:p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-sign-light text-sm font-bold text-sign-primary" aria-hidden="true">{{ strtoupper(substr($subject->name, 0, 2)) }}</div>
                                <span class="rounded-full bg-sign-soft px-3 py-1 text-xs font-semibold text-sign-muted">{{ $subject->courses_count }} {{ $subject->courses_count === 1 ? 'course' : 'courses' }}</span>
                            </div>
                            <h2 class="mt-5 font-heading text-xl font-semibold text-sign-primary">{{ $subject->name }}</h2>
                            <p class="mt-2 text-sm leading-6 text-sign-muted">{{ $subject->short_description ?: ($subject->description ?: 'Explore this subject and its published courses.') }}</p>
                            <span class="mt-5 inline-flex text-sm font-semibold text-sign-primary">Explore subject <span class="ml-2 transition group-hover:translate-x-1" aria-hidden="true">→</span></span>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-container>
    </section>

    <section class="bg-white py-14 sm:py-16 lg:py-20">
        <x-container>
            <div class="rounded-3xl bg-sign-dark px-6 py-9 text-center text-white sm:px-10 sm:py-12">
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan">Visual learning</p>
                <h2 class="mx-auto mt-3 max-w-3xl font-heading text-3xl font-semibold sm:text-4xl">Watch, understand, practise and continue at your own pace.</h2>
                <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-white/75 sm:text-base">Published lessons can include ISL video, notes, examples, practice activities and supporting resources.</p>
                <a href="{{ route('explore') }}" class="mt-6 inline-flex min-h-11 items-center justify-center rounded-xl bg-white px-6 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Explore Learning</a>
            </div>
        </x-container>
    </section>
@endsection
