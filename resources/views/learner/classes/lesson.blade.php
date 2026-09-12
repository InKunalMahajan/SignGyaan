<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Learn a SignGyaan Lesson and track completion">
    <title>{{ $lesson->title }} | SignGyaan</title>
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
            <a href="{{ route('learner.classes.courses.show', [$class, $course]) }}" class="flex items-center gap-3 rounded-xl focus:outline-none focus:ring-4 focus:ring-cyan-200">
                <span class="grid size-11 place-items-center rounded-2xl bg-gradient-to-br from-blue-700 to-cyan-500 text-sm font-black text-white">SG</span>
                <span>
                    <span class="block text-lg font-black">SignGyaan</span>
                    <span class="block text-xs font-semibold text-slate-500">{{ $course->title }}</span>
                </span>
            </a>
            <a href="{{ route('dashboard.role', 'learner') }}" class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Dashboard</a>
        </div>
    </header>

    <main id="main-content" class="mx-auto max-w-6xl space-y-7 px-5 py-8 sm:px-8 lg:py-10">
        @if (session('status'))
            <div role="status" class="rounded-2xl border border-slate-300 bg-white px-5 py-4 text-sm font-bold text-slate-950">{{ session('status') }}</div>
        @endif

        <section class="rounded-3xl bg-gradient-to-br from-blue-800 via-blue-700 to-cyan-600 p-6 text-white shadow-lg sm:p-8">
            <a href="{{ route('learner.classes.courses.show', [$class, $course]) }}" class="text-sm font-black text-cyan-100 focus:outline-none focus:ring-4 focus:ring-white/30">← Back to {{ $course->title }}</a>
            <div class="mt-5 flex flex-wrap items-start justify-between gap-5">
                <div class="max-w-3xl">
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-cyan-100">{{ $lesson->unit->title }} · Lesson {{ $lesson->position }}</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">{{ $lesson->title }}</h1>
                    @if ($lesson->summary)
                        <p class="mt-3 text-sm leading-6 text-blue-50 sm:text-base">{{ $lesson->summary }}</p>
                    @endif
                </div>
                <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-black">{{ $progress->isCompleted() ? 'Completed' : 'In progress' }}</span>
            </div>

            <div class="mt-7 max-w-3xl rounded-2xl border border-white/20 bg-white/10 p-5">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-cyan-100">Course progress</p>
                        <p class="mt-1 text-3xl font-black">{{ $progressPercent }}%</p>
                    </div>
                    <p class="text-sm font-bold text-blue-50">{{ $completedCount }} of {{ $totalLessons }} Lessons completed</p>
                </div>
                <div class="mt-4 h-3 overflow-hidden rounded-full bg-white/20">
                    <div class="h-full rounded-full bg-white" style="width: {{ $progressPercent }}%"></div>
                </div>
            </div>
        </section>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
            <section class="space-y-6">
                @if ($lesson->isl_video_url)
                    <article class="rounded-3xl border border-cyan-200 bg-cyan-50 p-6 shadow-sm sm:p-7">
                        <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Indian Sign Language</p>
                        <h2 class="mt-2 text-2xl font-black text-cyan-950">ISL Video Lesson</h2>
                        <p class="mt-2 text-sm leading-6 text-cyan-900/80">Open the Teacher-provided ISL video in a new tab, then return here to continue.</p>
                        <a href="{{ $lesson->isl_video_url }}" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex rounded-xl bg-cyan-700 px-5 py-3 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Open ISL video</a>
                    </article>
                @endif

                @if ($lesson->notes)
                    <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
                        <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Lesson material</p>
                        <h2 class="mt-2 text-2xl font-black">Lesson Notes</h2>
                        <div class="mt-5 whitespace-pre-line text-sm leading-8 text-slate-700">{{ $lesson->notes }}</div>
                    </article>
                @endif

                @if (! $lesson->isl_video_url && ! $lesson->notes)
                    <article class="rounded-3xl border border-dashed border-slate-300 bg-white p-8 text-center">
                        <p class="font-black">Learning material is not available yet.</p>
                        <p class="mt-2 text-sm text-slate-500">This Lesson is published, but your Teacher has not added ISL video or notes.</p>
                    </article>
                @endif
            </section>

            <aside class="space-y-5">
                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-black uppercase tracking-[0.14em] text-cyan-700">Lesson progress</p>
                    <h2 class="mt-2 text-xl font-black">{{ $progress->isCompleted() ? 'Completed ✓' : 'In progress' }}</h2>
                    @if ($lesson->estimated_minutes)
                        <p class="mt-2 text-sm font-bold text-slate-500">Estimated time: {{ $lesson->estimated_minutes }} minutes</p>
                    @endif
                    @if ($isFinalLesson && ! $progress->isCompleted())
                        <p class="mt-3 text-sm leading-6 text-slate-600">This is the final published lesson. Completing it may finish the course.</p>
                    @endif
                    <p class="mt-3 text-xs leading-5 text-slate-400">Last viewed: {{ $progress->last_viewed_at?->format('d M Y, h:i A') }}</p>

                    <form method="POST" action="{{ route('learner.classes.courses.lessons.progress.update', [$class, $course, $lesson]) }}" class="mt-5">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $progress->isCompleted() ? 'in_progress' : 'completed' }}">
                        <button type="submit" class="w-full rounded-xl px-4 py-3 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200 {{ $progress->isCompleted() ? 'bg-slate-700' : 'bg-emerald-600' }}">{{ $progress->isCompleted() ? 'Mark as in progress' : ($isFinalLesson ? 'Complete Course' : 'Mark Lesson complete') }}</button>
                    </form>
                </section>

                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-black uppercase tracking-[0.14em] text-cyan-700">Navigate</p>
                    <div class="mt-4 space-y-3">
                        @if ($previousLesson)
                            <a href="{{ route('learner.classes.courses.lessons.show', [$class, $course, $previousLesson]) }}" class="block rounded-xl border border-slate-200 px-4 py-3 text-sm font-black text-slate-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">← Previous Lesson</a>
                        @endif
                        @if ($nextLesson)
                            <a href="{{ route('learner.classes.courses.lessons.show', [$class, $course, $nextLesson]) }}" class="block rounded-xl bg-blue-700 px-4 py-3 text-center text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Next Lesson →</a>
                        @else
                            <a href="{{ route('learner.classes.courses.show', [$class, $course]) }}" class="block rounded-xl border border-slate-300 bg-white px-4 py-3 text-center text-sm font-black text-slate-950 focus:outline-none focus:ring-4 focus:ring-slate-200">Back to Course</a>
                        @endif
                    </div>
                </section>
            </aside>
        </div>
    </main>
</body>
</html>
