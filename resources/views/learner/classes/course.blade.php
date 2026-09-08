<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="View your SignGyaan course Units and published Lessons">
    <title>{{ $course->title }} | SignGyaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <a href="#main-content" class="sr-only z-50 rounded-lg bg-slate-950 px-4 py-3 text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Skip to main content</a>

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
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-cyan-100">{{ $course->subject?->name ?: 'Course' }}</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">{{ $course->title }}</h1>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-blue-50 sm:text-base">{{ $course->description ?: 'Your Teacher has organised this course into Units and Lessons.' }}</p>
                </div>
                @if ($course->level)
                    <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-black">{{ $course->level }}</span>
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
                <p class="text-sm font-bold text-slate-500">Teacher</p>
                <p class="mt-2 font-black">{{ $class->teacher?->name ?: 'Teacher' }}</p>
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
                        <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-100 bg-slate-50 p-5 sm:p-6">
                                <p class="text-xs font-black uppercase tracking-[0.14em] text-cyan-700">Unit {{ $unit->position }}</p>
                                <h3 class="mt-1 text-xl font-black">{{ $unit->title }}</h3>
                                @if ($unit->description)
                                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">{{ $unit->description }}</p>
                                @endif
                            </div>

                            @if ($unit->lessons->isEmpty())
                                <div class="p-6 text-sm font-bold text-slate-500">No published Lessons are available in this Unit yet.</div>
                            @else
                                <div class="divide-y divide-slate-100">
                                    @foreach ($unit->lessons as $lesson)
                                        <details class="group p-5 sm:p-6">
                                            <summary class="cursor-pointer list-none focus:outline-none focus:ring-4 focus:ring-cyan-200">
                                                <div class="flex flex-wrap items-center justify-between gap-4">
                                                    <div>
                                                        <p class="text-xs font-black uppercase tracking-wide text-cyan-700">Lesson {{ $lesson->position }}</p>
                                                        <h4 class="mt-1 text-lg font-black">{{ $lesson->title }}</h4>
                                                        @if ($lesson->summary)
                                                            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">{{ $lesson->summary }}</p>
                                                        @endif
                                                    </div>
                                                    @if ($lesson->estimated_minutes)
                                                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">{{ $lesson->estimated_minutes }} min</span>
                                                    @endif
                                                </div>
                                            </summary>

                                            <div class="mt-5 space-y-5 border-t border-slate-100 pt-5">
                                                @if ($lesson->isl_video_url)
                                                    <section class="rounded-2xl bg-cyan-50 p-5" aria-labelledby="isl-video-{{ $lesson->id }}">
                                                        <p id="isl-video-{{ $lesson->id }}" class="text-xs font-black uppercase tracking-[0.14em] text-cyan-700">Indian Sign Language</p>
                                                        <p class="mt-2 text-sm leading-6 text-cyan-950">This Lesson includes an ISL video resource.</p>
                                                        <a href="{{ $lesson->isl_video_url }}" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex rounded-xl bg-cyan-700 px-4 py-2.5 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Open ISL video</a>
                                                    </section>
                                                @endif

                                                @if ($lesson->notes)
                                                    <section aria-labelledby="lesson-notes-{{ $lesson->id }}">
                                                        <h5 id="lesson-notes-{{ $lesson->id }}" class="text-base font-black">Lesson Notes</h5>
                                                        <div class="mt-3 whitespace-pre-line rounded-2xl border border-slate-200 bg-slate-50 p-5 text-sm leading-7 text-slate-700">{{ $lesson->notes }}</div>
                                                    </section>
                                                @endif

                                                @if (! $lesson->isl_video_url && ! $lesson->notes)
                                                    <p class="text-sm font-bold text-slate-500">This Lesson is published, but learning materials have not been added yet.</p>
                                                @endif
                                            </div>
                                        </details>
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
