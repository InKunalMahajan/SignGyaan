<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="View approved Learner progress in SignGyaan">
    <title>{{ $learner->name }} | Parent View | SignGyaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <a href="#main-content" class="sr-only z-50 rounded-lg bg-slate-950 px-4 py-3 text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Skip to main content</a>

    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-5 py-4 sm:px-8">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl focus:outline-none focus:ring-4 focus:ring-cyan-200">
                <span class="grid size-10 place-items-center rounded-2xl bg-gradient-to-br from-blue-700 to-cyan-500 text-sm font-black text-white">SG</span>
                <span>
                    <span class="block font-black">SignGyaan</span>
                    <span class="block text-xs font-semibold text-slate-500">Family Learning</span>
                </span>
            </a>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-black text-slate-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Parent Dashboard</a>
                <a href="{{ route('parents.profile.show') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-black text-slate-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Parent Profile</a>
            </div>
        </div>
    </header>

    <main id="main-content" class="mx-auto max-w-6xl space-y-7 px-5 py-8 sm:px-8 lg:py-10">
        <section class="rounded-3xl bg-gradient-to-br from-blue-800 via-blue-700 to-cyan-600 p-6 text-white shadow-lg sm:p-8">
            <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-black uppercase tracking-[0.14em]">Approved family access</span>
            <h1 class="mt-4 text-3xl font-black tracking-tight sm:text-4xl">{{ $learner->name }}</h1>
            <p class="mt-2 text-blue-50">Relationship: {{ ucfirst($link->relationship) }}</p>
            <div class="mt-6 max-w-3xl">
                <div class="flex items-center justify-between gap-4 text-sm font-black">
                    <span>Overall Lesson Progress</span>
                    <span>{{ $progress['overall_percent'] }}%</span>
                </div>
                <div class="mt-2 h-3 overflow-hidden rounded-full bg-white/20" role="progressbar" aria-label="Overall Lesson Progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $progress['overall_percent'] }}">
                    <div class="h-full rounded-full bg-white" style="width: {{ $progress['overall_percent'] }}%"></div>
                </div>
                <p class="mt-2 text-sm text-blue-50">{{ $progress['completed_lesson_count'] }} of {{ $progress['total_lesson_count'] }} published Lessons completed</p>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" aria-label="Learner progress summary">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-400">Active Classes</p>
                <p class="mt-2 text-3xl font-black">{{ $progress['active_class_count'] }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-400">Courses</p>
                <p class="mt-2 text-3xl font-black">{{ $progress['course_count'] }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-400">Lessons Completed</p>
                <p class="mt-2 text-3xl font-black">{{ $progress['completed_lesson_count'] }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-400">In-progress Courses</p>
                <p class="mt-2 text-3xl font-black">{{ $progress['in_progress_course_count'] }}</p>
            </article>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3" aria-label="Learner profile summary">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-400">Education level</p>
                <p class="mt-2 text-lg font-black text-slate-950">{{ $profile?->education_level ?: 'Not added yet' }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-400">Class / Grade</p>
                <p class="mt-2 text-lg font-black text-slate-950">{{ $profile?->class_grade ?: 'Not added yet' }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-400">Institution</p>
                <p class="mt-2 text-lg font-black text-slate-950">{{ $profile?->institution ?: 'Not added yet' }}</p>
            </article>
        </section>

        <section aria-labelledby="parent-course-progress-heading">
            <div class="mb-4">
                <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Course progress</p>
                <h2 id="parent-course-progress-heading" class="mt-1 text-2xl font-black">Courses & Completion</h2>
            </div>

            @if ($progress['course_cards']->isEmpty())
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-8 text-center">
                    <p class="font-black">No active assigned Courses yet.</p>
                    <p class="mt-2 text-sm text-slate-500">Course progress will appear after a Teacher assigns active curriculum to this Learner's classes.</p>
                </div>
            @else
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($progress['course_cards'] as $courseCard)
                        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-[0.14em] text-cyan-700">{{ $courseCard['course']->subject?->name ?: 'Course' }}</p>
                                    <h3 class="mt-1 text-xl font-black">{{ $courseCard['course']->title }}</h3>
                                    <p class="mt-1 text-sm text-slate-500">{{ $courseCard['class']->name }}</p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-black {{ $courseCard['state'] === 'Completed' ? 'bg-emerald-50 text-emerald-700' : ($courseCard['state'] === 'In progress' ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-600') }}">{{ $courseCard['state'] }}</span>
                            </div>
                            <div class="mt-5 flex items-center justify-between gap-4 text-sm">
                                <span class="font-bold text-slate-500">{{ $courseCard['completed'] }} of {{ $courseCard['total'] }} Lessons</span>
                                <span class="font-black text-blue-700">{{ $courseCard['percent'] }}%</span>
                            </div>
                            <div class="mt-2 h-2.5 overflow-hidden rounded-full bg-slate-100" role="progressbar" aria-label="{{ $courseCard['course']->title }} progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $courseCard['percent'] }}">
                                <div class="h-full rounded-full bg-gradient-to-r from-blue-700 to-cyan-500" style="width: {{ $courseCard['percent'] }}%"></div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <section aria-labelledby="parent-class-heading">
            <div class="mb-4">
                <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Classes</p>
                <h2 id="parent-class-heading" class="mt-1 text-2xl font-black">Enrolled Classes</h2>
            </div>
            @if ($progress['classes']->isEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white p-6 text-sm font-bold text-slate-500">No class enrollments yet.</div>
            @else
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($progress['classes'] as $class)
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-wide text-cyan-700">{{ $class->code }}</p>
                                    <h3 class="mt-1 font-black">{{ $class->name }}</h3>
                                </div>
                                <span class="rounded-full px-2.5 py-1 text-xs font-black {{ $class->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $class->is_active ? 'Active' : 'Archived' }}</span>
                            </div>
                            <p class="mt-3 text-sm text-slate-500">Teacher: {{ $class->teacher?->name ?: 'Teacher' }}</p>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <section aria-labelledby="parent-recent-heading">
            <div class="mb-4">
                <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Recent activity</p>
                <h2 id="parent-recent-heading" class="mt-1 text-2xl font-black">Latest Lesson Activity</h2>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                @if ($progress['recent_activity']->isEmpty())
                    <div class="p-8 text-center text-sm font-bold text-slate-500">No Lesson activity yet.</div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach ($progress['recent_activity'] as $activity)
                            <div class="flex flex-wrap items-center justify-between gap-4 p-5 sm:p-6">
                                <div>
                                    <h3 class="font-black">{{ $activity['lesson']->title }}</h3>
                                    <p class="mt-1 text-sm text-slate-500">{{ $activity['course']->title }} · {{ $activity['unit']->title }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="rounded-full px-3 py-1 text-xs font-black {{ $activity['status'] === 'completed' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">{{ $activity['status'] === 'completed' ? 'Completed' : 'In progress' }}</span>
                                    @if ($activity['last_viewed_at'])
                                        <p class="mt-2 text-xs font-semibold text-slate-400">{{ $activity['last_viewed_at']->diffForHumans() }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Accessibility & communication</p>
            <h2 class="mt-1 text-xl font-black">Learner Preferences</h2>
            <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl bg-slate-50 p-4">
                    <dt class="text-xs font-black uppercase tracking-wide text-slate-400">Preferred language</dt>
                    <dd class="mt-1 font-black text-slate-900">{{ $profile?->preferred_language ? ucfirst($profile->preferred_language) : 'Not added yet' }}</dd>
                </div>
                <div class="rounded-xl bg-slate-50 p-4">
                    <dt class="text-xs font-black uppercase tracking-wide text-slate-400">Communication mode</dt>
                    <dd class="mt-1 font-black text-slate-900">{{ $profile?->communication_mode ? strtoupper($profile->communication_mode) : 'Not added yet' }}</dd>
                </div>
            </dl>
        </section>

        <section class="rounded-2xl border border-rose-100 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="font-black text-slate-950">Remove this link</h2>
            <p class="mt-1 text-sm text-slate-500">Removing the link immediately removes Parent access to this Learner's profile and learning progress.</p>
            <form method="POST" action="{{ route('parents.learners.destroy', $link) }}" class="mt-4" onsubmit="return confirm('Remove this Learner link?')">
                @csrf
                @method('DELETE')
                <button class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-black text-rose-700 focus:outline-none focus:ring-4 focus:ring-rose-100">Remove learner link</button>
            </form>
        </section>
    </main>
</body>
</html>
