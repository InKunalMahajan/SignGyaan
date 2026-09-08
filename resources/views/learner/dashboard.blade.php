<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Live SignGyaan Learner learning progress dashboard">
    <title>My Learning Dashboard | SignGyaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <a href="#main-content" class="sr-only z-50 rounded-lg bg-slate-950 px-4 py-3 text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Skip to main content</a>

    <div class="min-h-screen lg:grid lg:grid-cols-[280px_minmax(0,1fr)]">
        <aside class="border-b border-slate-200 bg-white lg:min-h-screen lg:border-b-0 lg:border-r">
            <div class="flex items-center justify-between gap-4 px-5 py-5 lg:px-6">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl focus:outline-none focus:ring-4 focus:ring-cyan-200" aria-label="SignGyaan Learner dashboard">
                    <span class="grid size-11 place-items-center rounded-2xl bg-gradient-to-br from-blue-700 to-cyan-500 text-sm font-black text-white">SG</span>
                    <span>
                        <span class="block text-lg font-black">SignGyaan</span>
                        <span class="block text-xs font-medium text-slate-500">My Learning</span>
                    </span>
                </a>
                <span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-bold text-cyan-800 lg:hidden">Learner</span>
            </div>

            <nav class="px-4 pb-5 lg:px-5" aria-label="Learner navigation">
                <p class="mb-2 hidden px-3 text-xs font-bold uppercase tracking-[0.18em] text-slate-400 lg:block">Navigation</p>
                <div class="flex gap-2 overflow-x-auto pb-1 lg:flex-col lg:overflow-visible">
                    <a href="{{ route('dashboard') }}" class="flex min-w-fit items-center gap-3 rounded-xl bg-blue-700 px-3 py-3 text-sm font-bold text-white shadow-sm focus:outline-none focus:ring-4 focus:ring-cyan-200" aria-current="page">My Dashboard</a>
                    <a href="{{ route('learner.classes.index') }}" class="flex min-w-fit items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-cyan-200">My Classes</a>
                    <a href="{{ route('learner.profile.show') }}" class="flex min-w-fit items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-cyan-200">My Profile</a>
                    <a href="{{ route('learner.parent-links.index') }}" class="flex min-w-fit items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-cyan-200">Parent Access</a>
                    <a href="{{ route('dashboard.guest') }}" class="flex min-w-fit items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-cyan-200">Explore Public</a>
                </div>
            </nav>

            <div class="mx-5 mb-5 hidden rounded-2xl border border-slate-200 bg-slate-50 p-4 lg:block">
                <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-400">Signed in as</p>
                <p class="mt-2 truncate text-sm font-black">{{ $learner->name }}</p>
                <p class="mt-1 truncate text-xs font-semibold text-slate-500">{{ $learner->email }}</p>
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-black text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-cyan-200">Sign out</button>
                </form>
            </div>
        </aside>

        <main id="main-content" class="min-w-0">
            <header class="border-b border-slate-200 bg-white px-5 py-4 sm:px-8 lg:px-10">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-700">Live learning progress</p>
                        <p class="mt-1 text-sm font-semibold text-slate-500">Learner Dashboard</p>
                    </div>
                    <span class="hidden text-right sm:block">
                        <span class="block max-w-56 truncate text-sm font-black">{{ $learner->name }}</span>
                        <span class="block text-xs font-semibold text-slate-500">{{ $activeClassCount }} active {{ Str::plural('class', $activeClassCount) }}</span>
                    </span>
                </div>
            </header>

            <div class="mx-auto max-w-7xl space-y-8 px-5 py-7 sm:px-8 lg:px-10 lg:py-10">
                <section class="overflow-hidden rounded-3xl bg-gradient-to-br from-blue-800 via-blue-700 to-cyan-600 p-6 text-white shadow-lg sm:p-8 lg:p-10">
                    <div class="grid gap-7 lg:grid-cols-[minmax(0,1.35fr)_minmax(300px,.65fr)] lg:items-end">
                        <div>
                            <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-[0.14em] text-cyan-50">My learning</span>
                            <h1 class="mt-5 text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl">Keep learning, {{ Str::before($learner->name, ' ') }}.</h1>
                            <p class="mt-4 max-w-2xl text-base leading-7 text-blue-50 sm:text-lg">Your dashboard now uses your real classes, published Lessons, and completion records.</p>

                            <div class="mt-7 flex flex-wrap gap-3">
                                @if ($continueLearning)
                                    <a href="{{ $continueLearning['url'] }}" class="rounded-xl bg-white px-5 py-3 text-sm font-black text-blue-800 focus:outline-none focus:ring-4 focus:ring-white/40">
                                        {{ $continueLearning['is_resume'] ? 'Resume Lesson' : 'Start Learning' }}
                                    </a>
                                @else
                                    <a href="{{ route('learner.classes.index') }}" class="rounded-xl bg-white px-5 py-3 text-sm font-black text-blue-800 focus:outline-none focus:ring-4 focus:ring-white/40">Open My Classes</a>
                                @endif
                                <a href="#courses" class="rounded-xl border border-white/25 bg-white/10 px-5 py-3 text-sm font-bold text-white focus:outline-none focus:ring-4 focus:ring-white/30">View Course Progress</a>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-white/20 bg-white/10 p-5 backdrop-blur-sm">
                            @if ($continueLearning)
                                <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-100">{{ $continueLearning['is_resume'] ? 'Continue learning' : 'Ready to start' }}</p>
                                <p class="mt-3 text-xl font-black">{{ $continueLearning['lesson']->title }}</p>
                                <p class="mt-2 text-sm leading-6 text-blue-50">{{ $continueLearning['course']->title }} · {{ $continueLearning['unit']->title }}</p>
                                <p class="mt-2 text-xs font-bold text-cyan-100">{{ $continueLearning['class']->name }}{{ $continueLearning['lesson']->estimated_minutes ? ' · '.$continueLearning['lesson']->estimated_minutes.' min' : '' }}</p>
                            @elseif ($totalLessonCount > 0 && $completedLessonCount === $totalLessonCount)
                                <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-100">Learning status</p>
                                <p class="mt-3 text-xl font-black">All published Lessons complete ✓</p>
                                <p class="mt-2 text-sm leading-6 text-blue-50">You have completed every currently available Lesson.</p>
                            @else
                                <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-100">Next step</p>
                                <p class="mt-3 text-xl font-black">Your learning path is being prepared.</p>
                                <p class="mt-2 text-sm leading-6 text-blue-50">Published Lessons will appear here automatically when your Teacher adds them.</p>
                            @endif
                        </div>
                    </div>
                </section>

                <section aria-labelledby="overview-heading">
                    <div class="mb-4 flex items-end justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Overview</p>
                            <h2 id="overview-heading" class="mt-1 text-xl font-black">Live progress at a glance</h2>
                        </div>
                        <span class="text-xs font-semibold text-emerald-700">Live data</span>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p class="text-sm font-bold text-slate-500">Active Classes</p>
                            <p class="mt-3 text-3xl font-black">{{ $activeClassCount }}</p>
                            <p class="mt-2 text-xs font-semibold text-cyan-700">{{ $totalClassCount }} enrolled total</p>
                        </article>
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p class="text-sm font-bold text-slate-500">Courses in Progress</p>
                            <p class="mt-3 text-3xl font-black">{{ $inProgressCourseCount }}</p>
                            <p class="mt-2 text-xs font-semibold text-cyan-700">{{ $completedCourseCount }} completed · {{ $courseCount }} assigned</p>
                        </article>
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p class="text-sm font-bold text-slate-500">Lessons Completed</p>
                            <p class="mt-3 text-3xl font-black">{{ $completedLessonCount }}</p>
                            <p class="mt-2 text-xs font-semibold text-cyan-700">{{ $completedLessonCount }} of {{ $totalLessonCount }} published</p>
                        </article>
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p class="text-sm font-bold text-slate-500">Overall Progress</p>
                            <p class="mt-3 text-3xl font-black">{{ $overallPercent }}%</p>
                            <p class="mt-2 text-xs font-semibold text-cyan-700">{{ $startedLessonCount }} {{ Str::plural('Lesson', $startedLessonCount) }} started</p>
                        </article>
                    </div>
                </section>

                <section id="courses" aria-labelledby="courses-heading">
                    <div class="mb-4 flex flex-wrap items-end justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">My Courses</p>
                            <h2 id="courses-heading" class="mt-1 text-xl font-black">Course progress</h2>
                        </div>
                        <a href="{{ route('learner.classes.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-black text-blue-700 shadow-sm focus:outline-none focus:ring-4 focus:ring-cyan-200">View all Classes</a>
                    </div>

                    @if ($courseCards->isEmpty())
                        <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">
                            <h3 class="text-lg font-black">No assigned Courses yet.</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-500">Once a Teacher assigns a Course to one of your Classes, it will appear here automatically.</p>
                            <a href="{{ route('learner.classes.index') }}" class="mt-5 inline-flex rounded-xl bg-blue-700 px-4 py-3 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Open My Classes</a>
                        </div>
                    @else
                        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            @foreach ($courseCards->take(6) as $item)
                                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                                    <div class="flex items-start justify-between gap-3">
                                        <span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-black text-cyan-800">{{ $item['course']->subject?->name ?: 'Course' }}</span>
                                        <span class="rounded-full px-2.5 py-1 text-[11px] font-black {{ $item['state'] === 'Completed' ? 'bg-emerald-50 text-emerald-700' : ($item['state'] === 'In progress' ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-600') }}">{{ $item['state'] }}</span>
                                    </div>
                                    <h3 class="mt-4 text-lg font-black">{{ $item['course']->title }}</h3>
                                    <p class="mt-2 text-sm font-semibold text-slate-500">{{ $item['class']->name }}</p>

                                    <div class="mt-5" aria-label="{{ $item['course']->title }} progress {{ $item['percent'] }} percent">
                                        <div class="flex items-center justify-between gap-4 text-xs font-bold">
                                            <span class="text-slate-500">{{ $item['completed'] }} of {{ $item['total'] }} Lessons</span>
                                            <span class="text-blue-700">{{ $item['percent'] }}%</span>
                                        </div>
                                        <div class="mt-2 h-2.5 overflow-hidden rounded-full bg-slate-100">
                                            <div class="h-full rounded-full bg-gradient-to-r from-blue-700 to-cyan-500" style="width: {{ $item['percent'] }}%"></div>
                                        </div>
                                    </div>

                                    <a href="{{ $item['url'] }}" class="mt-5 inline-flex rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">{{ $item['state'] === 'Completed' ? 'Review Course' : 'Open Course' }}</a>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>

                <section class="grid gap-5 lg:grid-cols-[minmax(0,1.25fr)_minmax(280px,.75fr)]" aria-labelledby="activity-heading">
                    <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Recent</p>
                        <h2 id="activity-heading" class="mt-1 text-xl font-black">Learning activity</h2>

                        @if ($recentActivity->isEmpty())
                            <div class="mt-5 rounded-2xl bg-slate-50 p-6 text-center">
                                <p class="font-black">No Lesson activity yet.</p>
                                <p class="mt-2 text-sm text-slate-500">Open your first published Lesson and your recent learning will appear here.</p>
                            </div>
                        @else
                            <div class="mt-5 divide-y divide-slate-100">
                                @foreach ($recentActivity as $activity)
                                    <a href="{{ $activity['url'] }}" class="flex gap-4 py-4 first:pt-0 last:pb-0 focus:outline-none focus:ring-4 focus:ring-cyan-200">
                                        <span class="mt-1.5 size-2.5 shrink-0 rounded-full {{ $activity['status'] === 'completed' ? 'bg-emerald-500' : 'bg-cyan-500' }}" aria-hidden="true"></span>
                                        <span class="min-w-0">
                                            <span class="block text-sm font-black">{{ $activity['lesson']->title }}</span>
                                            <span class="mt-1 block text-sm text-slate-500">{{ $activity['course']->title }} · {{ $activity['unit']->title }}</span>
                                            <span class="mt-1 block text-xs font-bold text-cyan-700">{{ $activity['status'] === 'completed' ? 'Completed' : 'In progress' }}{{ $activity['last_viewed_at'] ? ' · '.$activity['last_viewed_at']->diffForHumans() : '' }}</span>
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </article>

                    <aside class="space-y-5">
                        <section class="rounded-3xl border border-blue-100 bg-blue-50 p-5 sm:p-6" aria-labelledby="learning-spaces-heading">
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-blue-700">Learning spaces</p>
                            <h2 id="learning-spaces-heading" class="mt-1 text-xl font-black">My Classes</h2>
                            @if ($classes->isEmpty())
                                <p class="mt-4 text-sm leading-6 text-blue-900/80">You are not enrolled in a Class yet.</p>
                            @else
                                <div class="mt-4 space-y-3">
                                    @foreach ($classes->take(4) as $class)
                                        <a href="{{ route('learner.classes.show', $class) }}" class="block rounded-2xl bg-white p-4 shadow-sm focus:outline-none focus:ring-4 focus:ring-cyan-200">
                                            <span class="flex items-start justify-between gap-3">
                                                <span>
                                                    <span class="block font-black text-slate-950">{{ $class->name }}</span>
                                                    <span class="mt-1 block text-xs font-semibold text-slate-500">{{ $class->teacher?->name ?: 'Teacher' }} · {{ $class->code }}</span>
                                                </span>
                                                <span class="rounded-full px-2 py-1 text-[10px] font-black {{ $class->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $class->is_active ? 'Active' : 'Archived' }}</span>
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </section>

                        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Account</p>
                            <h2 class="mt-1 text-lg font-black">Learning preferences</h2>
                            <p class="mt-3 text-sm leading-6 text-slate-500">Update accessibility, communication, language, avatar, and account settings from your Learner Profile.</p>
                            <a href="{{ route('learner.profile.show') }}" class="mt-4 inline-flex rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-black text-blue-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Open My Profile</a>
                        </section>
                    </aside>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
