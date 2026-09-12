<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Track your SignGyaan course progress and continue published Lessons">
    <title>{{ $course->title }} | SignGyaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <a href="#main-content" class="sr-only z-50 rounded-lg bg-slate-950 px-4 py-3 text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Skip to main content</a>

    <form method="POST" action="{{ route('logout') }}" class="hidden" aria-hidden="true">
        @csrf
        <button type="submit">Sign out</button>
    </form>

    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-5 py-4 sm:px-8">
            <a href="{{ route('learner.classes.show', $class) }}" class="flex items-center gap-3 rounded-xl focus:outline-none focus:ring-4 focus:ring-cyan-200">
                <span class="grid size-11 place-items-center rounded-2xl bg-gradient-to-br from-blue-700 to-cyan-500 text-sm font-black text-white">SG</span>
                <span>
                    <span class="block text-lg font-black">SignGyaan</span>
                    <span class="block text-xs font-semibold text-slate-500">{{ $class->name }}</span>
                </span>
            </a>
            <a href="{{ route('dashboard.role', 'learner') }}" class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Dashboard</a>
        </div>
    </header>

    <main id="main-content" class="mx-auto max-w-6xl space-y-7 px-5 py-8 sm:px-8 lg:py-10">
        <section class="rounded-3xl bg-gradient-to-br from-blue-800 via-blue-700 to-cyan-600 p-6 text-white shadow-lg sm:p-8">
            <a href="{{ route('learner.classes.show', $class) }}" class="text-sm font-black text-cyan-100 focus:outline-none focus:ring-4 focus:ring-white/30">← Back to {{ $class->name }}</a>
            <div class="mt-5 flex flex-wrap items-start justify-between gap-5">
                <div class="max-w-3xl">
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-cyan-100">{{ $course->subject?->name ?: 'Course' }}</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">{{ $course->title }}</h1>
                    <p class="mt-3 text-sm leading-6 text-blue-50 sm:text-base">{{ $course->description ?: 'Your Teacher has organised this course into Units and Lessons.' }}</p>
                </div>
                @if ($course->level)
                    <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-black">{{ $course->level }}</span>
                @endif
            </div>

            <div class="mt-7 max-w-3xl rounded-2xl border border-white/20 bg-white/10 p-5">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-cyan-100">Course progress</p>
                        <p class="mt-1 text-3xl font-black">{{ $progressPercent }}%</p>
                    </div>
                    <p class="text-sm font-bold text-blue-50">{{ $completedLessonCount }} of {{ $publishedLessonCount }} Lessons completed</p>
                </div>
                <div class="mt-4 h-3 overflow-hidden rounded-full bg-white/20" aria-label="{{ $progressPercent }} percent complete">
                    <div class="h-full rounded-full bg-white" style="width: {{ $progressPercent }}%"></div>
                </div>

                @if ($continueLesson)
                    <a href="{{ route('learner.classes.courses.lessons.show', [$class, $course, $continueLesson]) }}" class="mt-5 inline-flex rounded-xl bg-white px-5 py-3 text-sm font-black text-blue-800 focus:outline-none focus:ring-4 focus:ring-white/40">{{ $progressByLesson->has($continueLesson->id) ? 'Resume Learning' : 'Start Learning' }} →</a>
                @elseif ($publishedLessonCount > 0)
                    <span class="mt-5 inline-flex rounded-xl bg-emerald-100 px-5 py-3 text-sm font-black text-emerald-900">Course complete ✓</span>
                @endif
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-3" aria-label="Course summary">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-bold text-slate-500">Visible Units</p>
                <p class="mt-2 text-3xl font-black">{{ $course->units->count() }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-bold text-slate-500">Published Lessons</p>
                <p class="mt-2 text-3xl font-black">{{ $publishedLessonCount }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-bold text-slate-500">Completed</p>
                <p class="mt-2 text-3xl font-black">{{ $completedLessonCount }}</p>
            </article>
        </section>

        <section aria-labelledby="learning-path-heading">
            <div class="mb-4">
                <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Learning path</p>
                <h2 id="learning-path-heading" class="mt-1 text-2xl font-black">Units & Lessons</h2>
            </div>

            @if ($course->units->isEmpty())
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">
                    <p class="font-black">Course content is being prepared.</p>
                    <p class="mt-2 text-sm text-slate-500">Your Teacher has not published any visible Units yet.</p>
                </div>
            @else
                <div class="space-y-5">
                    @foreach ($course->units as $unit)
                        @php
                            $unitCompleted = $unit->lessons->filter(fn ($item) => optional($progressByLesson->get($item->id))->status === 'completed')->count();
                            $unitTotal = $unit->lessons->count();
                        @endphp
                        <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-100 bg-slate-50 p-5 sm:p-6">
                                <div class="flex flex-wrap items-start justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-black uppercase tracking-[0.14em] text-cyan-700">Unit {{ $unit->position }}</p>
                                        <h3 class="mt-1 text-xl font-black">{{ $unit->title }}</h3>
                                        @if ($unit->description)
                                            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">{{ $unit->description }}</p>
                                        @endif
                                    </div>
                                    <span class="rounded-full bg-white px-3 py-1 text-xs font-black text-slate-600 shadow-sm">{{ $unitCompleted }}/{{ $unitTotal }} complete</span>
                                </div>
                            </div>

                            @if ($unit->lessons->isEmpty())
                                <div class="p-6 text-sm font-bold text-slate-500">No published Lessons are available in this Unit yet.</div>
                            @else
                                <div class="divide-y divide-slate-100">
                                    @foreach ($unit->lessons as $lesson)
                                        @php
                                            $lessonProgress = $progressByLesson->get($lesson->id);
                                            $lessonStatus = $lessonProgress?->status ?? 'not_started';
                                        @endphp
                                        <article class="flex flex-wrap items-center justify-between gap-4 p-5 sm:p-6">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <p class="text-xs font-black uppercase tracking-wide text-cyan-700">Lesson {{ $lesson->position }}</p>
                                                    @if ($lessonStatus === 'completed')
                                                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-black text-emerald-700">Completed</span>
                                                    @elseif ($lessonStatus === 'in_progress')
                                                        <span class="rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-black text-amber-700">In progress</span>
                                                    @else
                                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-black text-slate-600">Not started</span>
                                                    @endif
                                                </div>
                                                <h4 class="mt-2 text-lg font-black">{{ $lesson->title }}</h4>
                                                @if ($lesson->summary)
                                                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">{{ $lesson->summary }}</p>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-3">
                                                @if ($lesson->estimated_minutes)
                                                    <span class="text-xs font-bold text-slate-400">{{ $lesson->estimated_minutes }} min</span>
                                                @endif
                                                <a href="{{ route('learner.classes.courses.lessons.show', [$class, $course, $lesson]) }}" class="rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">{{ $lessonStatus === 'not_started' ? 'Start' : 'Open' }}</a>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </main>
</body>
</html>
