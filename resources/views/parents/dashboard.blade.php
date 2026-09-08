<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Live SignGyaan Parent dashboard with approved Learner progress">
    <title>Parent Dashboard | SignGyaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <a href="#main-content" class="sr-only z-50 rounded-lg bg-slate-950 px-4 py-3 text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Skip to main content</a>

    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-5 py-4 sm:px-8">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl focus:outline-none focus:ring-4 focus:ring-cyan-200">
                <span class="grid size-11 place-items-center rounded-2xl bg-gradient-to-br from-blue-700 to-cyan-500 text-sm font-black text-white">SG</span>
                <span>
                    <span class="block text-lg font-black">SignGyaan</span>
                    <span class="block text-xs font-semibold text-slate-500">Family Learning</span>
                </span>
            </a>

            <nav class="flex flex-wrap items-center gap-2" aria-label="Parent navigation">
                <a href="{{ route('dashboard') }}" class="rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Dashboard</a>
                <a href="{{ route('parents.profile.show') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-black text-slate-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Parent Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-black text-slate-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Sign out</button>
                </form>
            </nav>
        </div>
    </header>

    <main id="main-content" class="mx-auto max-w-7xl space-y-8 px-5 py-8 sm:px-8 lg:py-10">
        <section class="overflow-hidden rounded-3xl bg-gradient-to-br from-blue-800 via-blue-700 to-cyan-600 p-6 text-white shadow-lg sm:p-8 lg:p-10">
            <div class="max-w-3xl">
                <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-black uppercase tracking-[0.16em] text-cyan-50">Live family progress</span>
                <h1 class="mt-5 text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl">Support learning with a clear view of progress.</h1>
                <p class="mt-4 max-w-2xl text-base leading-7 text-blue-50">Only Learners who approved your Parent link appear here. Review classes, course progress, completed Lessons, and recent activity.</p>
                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="{{ route('parents.profile.show') }}" class="rounded-xl bg-white px-5 py-3 text-sm font-black text-blue-800 focus:outline-none focus:ring-4 focus:ring-white/40">Manage linked Learners</a>
                    @if ($learnerCards->isNotEmpty())
                        <a href="#learners" class="rounded-xl border border-white/25 bg-white/10 px-5 py-3 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-white/30">View Learner progress</a>
                    @endif
                </div>
            </div>
        </section>

        <section aria-labelledby="parent-overview-heading">
            <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Overview</p>
                    <h2 id="parent-overview-heading" class="mt-1 text-2xl font-black">Family learning at a glance</h2>
                </div>
                @if ($pendingCount > 0)
                    <a href="{{ route('parents.profile.show') }}" class="rounded-full bg-amber-50 px-3 py-1 text-xs font-black text-amber-800">{{ $pendingCount }} pending request{{ $pendingCount === 1 ? '' : 's' }}</a>
                @endif
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-bold text-slate-500">Approved Learners</p>
                    <p class="mt-3 text-3xl font-black">{{ $approvedLearnerCount }}</p>
                    <p class="mt-2 text-xs font-semibold text-cyan-700">Visible to this Parent account</p>
                </article>
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-bold text-slate-500">Active Classes</p>
                    <p class="mt-3 text-3xl font-black">{{ $activeClassCount }}</p>
                    <p class="mt-2 text-xs font-semibold text-cyan-700">Across approved Learners</p>
                </article>
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-bold text-slate-500">Lessons Completed</p>
                    <p class="mt-3 text-3xl font-black">{{ $completedLessonCount }}</p>
                    <p class="mt-2 text-xs font-semibold text-cyan-700">Published accessible Lessons</p>
                </article>
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-bold text-slate-500">Average Progress</p>
                    <p class="mt-3 text-3xl font-black">{{ $averageProgress }}%</p>
                    <p class="mt-2 text-xs font-semibold text-cyan-700">Across approved Learners</p>
                </article>
            </div>
        </section>

        <section id="learners" aria-labelledby="linked-learners-heading">
            <div class="mb-4">
                <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Approved access only</p>
                <h2 id="linked-learners-heading" class="mt-1 text-2xl font-black">Linked Learner Progress</h2>
            </div>

            @if ($learnerCards->isEmpty())
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">
                    <h3 class="text-xl font-black">No approved Learner links yet</h3>
                    <p class="mx-auto mt-2 max-w-2xl text-sm leading-6 text-slate-500">Send a link request from your Parent Profile. The Learner must approve it before any profile or learning progress is shown here.</p>
                    <a href="{{ route('parents.profile.show') }}" class="mt-5 inline-flex rounded-xl bg-blue-700 px-4 py-3 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Manage linked Learners</a>
                </div>
            @else
                <div class="grid gap-5 lg:grid-cols-2">
                    @foreach ($learnerCards as $card)
                        @php($progress = $card['progress'])
                        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-[0.14em] text-cyan-700">{{ ucfirst($card['link']->relationship) }}</p>
                                    <h3 class="mt-1 text-2xl font-black">{{ $card['learner']->name }}</h3>
                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $card['profile']?->education_level ?: 'Education level not set' }}
                                        @if ($card['profile']?->class_grade)
                                            · {{ $card['profile']->class_grade }}
                                        @endif
                                    </p>
                                </div>
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-black text-emerald-700">Approved</span>
                            </div>

                            <div class="mt-6 grid grid-cols-3 gap-3 text-center">
                                <div class="rounded-2xl bg-slate-50 p-3">
                                    <p class="text-2xl font-black">{{ $progress['active_class_count'] }}</p>
                                    <p class="mt-1 text-xs font-bold text-slate-500">Classes</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-3">
                                    <p class="text-2xl font-black">{{ $progress['course_count'] }}</p>
                                    <p class="mt-1 text-xs font-bold text-slate-500">Courses</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-3">
                                    <p class="text-2xl font-black">{{ $progress['completed_lesson_count'] }}</p>
                                    <p class="mt-1 text-xs font-bold text-slate-500">Completed</p>
                                </div>
                            </div>

                            <div class="mt-6">
                                <div class="flex items-center justify-between gap-4 text-sm">
                                    <span class="font-black">Overall Lesson Progress</span>
                                    <span class="font-black text-blue-700">{{ $progress['overall_percent'] }}%</span>
                                </div>
                                <div class="mt-2 h-3 overflow-hidden rounded-full bg-slate-100" role="progressbar" aria-label="{{ $card['learner']->name }} progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $progress['overall_percent'] }}">
                                    <div class="h-full rounded-full bg-gradient-to-r from-blue-700 to-cyan-500" style="width: {{ $progress['overall_percent'] }}%"></div>
                                </div>
                                <p class="mt-2 text-xs font-semibold text-slate-500">{{ $progress['completed_lesson_count'] }} of {{ $progress['total_lesson_count'] }} published Lessons completed</p>
                            </div>

                            <a href="{{ route('parents.learners.show', $card['link']) }}" class="mt-6 inline-flex rounded-xl bg-blue-700 px-4 py-3 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">View full progress →</a>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <section aria-labelledby="family-activity-heading">
            <div class="mb-4">
                <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Recent learning</p>
                <h2 id="family-activity-heading" class="mt-1 text-2xl font-black">Latest Learner Activity</h2>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                @if ($recentActivity->isEmpty())
                    <div class="p-8 text-center">
                        <p class="font-black">No Lesson activity yet.</p>
                        <p class="mt-2 text-sm text-slate-500">Recent Lesson starts and completions from approved Learners will appear here.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach ($recentActivity as $activity)
                            <div class="flex flex-wrap items-center justify-between gap-4 p-5 sm:p-6">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-[0.14em] text-cyan-700">{{ $activity['learner']->name }}</p>
                                    <h3 class="mt-1 font-black">{{ $activity['lesson']->title }}</h3>
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
    </main>
</body>
</html>
