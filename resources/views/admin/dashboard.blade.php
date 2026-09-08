<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SignGyaan live platform analytics for administrators">
    <title>Admin Dashboard | SignGyaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <a href="#main-content" class="sr-only z-50 rounded-lg bg-slate-950 px-4 py-3 text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Skip to main content</a>

    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-5 py-4 sm:px-8">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl focus:outline-none focus:ring-4 focus:ring-cyan-200">
                <span class="grid size-10 place-items-center rounded-xl bg-gradient-to-br from-blue-700 to-cyan-500 text-sm font-black text-white">SG</span>
                <span><span class="block font-black">SignGyaan</span><span class="block text-xs font-semibold text-slate-500">Platform Management</span></span>
            </a>
            <nav class="flex flex-wrap items-center gap-2" aria-label="Admin navigation">
                <a href="{{ route('admin.users.index') }}" class="rounded-xl px-3 py-2 text-sm font-black text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-cyan-200">Users & Roles</a>
                <a href="{{ route('dashboard.guest') }}" class="rounded-xl px-3 py-2 text-sm font-black text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-cyan-200">Public View</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-black text-slate-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Sign out</button></form>
            </nav>
        </div>
    </header>

    <main id="main-content" class="mx-auto max-w-7xl space-y-8 px-5 py-8 sm:px-8 lg:py-10">
        <section class="overflow-hidden rounded-3xl bg-gradient-to-br from-blue-800 via-blue-700 to-cyan-600 p-7 text-white shadow-lg sm:p-9">
            <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-100">Live platform analytics</p>
            <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">See SignGyaan activity from one control centre.</h1>
            <p class="mt-4 max-w-3xl text-blue-50">Users, curriculum, active classes and learner completion are calculated from current platform data.</p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('admin.users.index') }}" class="rounded-xl bg-white px-5 py-3 text-sm font-black text-blue-800 focus:outline-none focus:ring-4 focus:ring-white/40">Manage Users & Roles</a>
                <a href="#class-insights" class="rounded-xl border border-white/25 bg-white/10 px-5 py-3 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-white/30">View class insights</a>
            </div>
        </section>

        <section aria-labelledby="overview-heading">
            <div class="mb-4"><p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Platform overview</p><h2 id="overview-heading" class="mt-1 text-xl font-black">Live totals</h2></div>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm font-bold text-slate-500">Total Users</p><p class="mt-3 text-3xl font-black">{{ $totalUsers }}</p><p class="mt-2 text-xs font-semibold text-cyan-700">{{ $activeUsers }} active · {{ $inactiveUsers }} inactive</p></article>
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm font-bold text-slate-500">Active Classes</p><p class="mt-3 text-3xl font-black">{{ $activeClassCount }}</p><p class="mt-2 text-xs font-semibold text-cyan-700">{{ $totalClasses }} total classes</p></article>
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm font-bold text-slate-500">Active Learners</p><p class="mt-3 text-3xl font-black">{{ $uniqueLearnerCount }}</p><p class="mt-2 text-xs font-semibold text-cyan-700">Enrolled in active classes</p></article>
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm font-bold text-slate-500">Average Completion</p><p class="mt-3 text-3xl font-black">{{ $averageCompletion }}%</p><p class="mt-2 text-xs font-semibold text-cyan-700">Across active classes with learning data</p></article>
            </div>
        </section>

        <section class="grid gap-5 lg:grid-cols-2">
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Accounts</p><h2 class="mt-1 text-xl font-black">Role distribution</h2>
                <div class="mt-5 grid grid-cols-2 gap-3">
                    @foreach ($roleCounts as $role => $count)
                        <div class="rounded-xl bg-slate-50 p-4"><p class="text-xs font-black uppercase tracking-wide text-slate-400">{{ ucfirst($role) }}</p><p class="mt-2 text-2xl font-black">{{ $count }}</p></div>
                    @endforeach
                </div>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Curriculum</p><h2 class="mt-1 text-xl font-black">Content inventory</h2>
                <dl class="mt-5 grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-xl bg-slate-50 p-4"><dt class="font-bold text-slate-500">Subjects</dt><dd class="mt-1 text-xl font-black">{{ $activeSubjects }} / {{ $totalSubjects }}</dd></div>
                    <div class="rounded-xl bg-slate-50 p-4"><dt class="font-bold text-slate-500">Courses</dt><dd class="mt-1 text-xl font-black">{{ $activeCourses }} / {{ $totalCourses }}</dd></div>
                    <div class="rounded-xl bg-slate-50 p-4"><dt class="font-bold text-slate-500">Published Lessons</dt><dd class="mt-1 text-xl font-black">{{ $publishedLessons }}</dd></div>
                    <div class="rounded-xl bg-slate-50 p-4"><dt class="font-bold text-slate-500">Draft Lessons</dt><dd class="mt-1 text-xl font-black">{{ $draftLessons }}</dd></div>
                    <div class="rounded-xl bg-blue-50 p-4"><dt class="font-bold text-blue-700">Assigned active Courses</dt><dd class="mt-1 text-xl font-black text-blue-950">{{ $activeAssignedCourseCount }}</dd></div>
                    <div class="rounded-xl bg-blue-50 p-4"><dt class="font-bold text-blue-700">Live curriculum Lessons</dt><dd class="mt-1 text-xl font-black text-blue-950">{{ $activeCurriculumLessonCount }}</dd></div>
                </dl>
            </article>
        </section>

        <section id="class-insights" aria-labelledby="classes-heading">
            <div class="mb-4 flex flex-wrap items-end justify-between gap-3"><div><p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Learning operations</p><h2 id="classes-heading" class="mt-1 text-xl font-black">Class insights</h2></div><span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-black text-amber-800">{{ $needsAttentionLearnerCount }} learners need attention</span></div>
            @if ($classCards->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center"><p class="font-black">No classes yet</p><p class="mt-2 text-sm text-slate-500">Teacher-created classes will appear here.</p></div>
            @else
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($classCards as $summary)
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="flex items-start justify-between gap-3"><div><p class="text-xs font-black uppercase tracking-wide text-cyan-700">{{ $summary['class']->code }}</p><h3 class="mt-1 text-lg font-black">{{ $summary['class']->name }}</h3></div><span class="rounded-full px-2.5 py-1 text-xs font-black {{ $summary['class']->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $summary['class']->is_active ? 'Active' : 'Archived' }}</span></div>
                            <p class="mt-2 text-sm text-slate-500">Teacher: {{ $summary['class']->teacher?->name ?: 'Unknown' }}</p>
                            <dl class="mt-4 grid grid-cols-2 gap-2 text-sm"><div><dt class="text-slate-500">Learners</dt><dd class="font-black">{{ $summary['learner_count'] }}</dd></div><div><dt class="text-slate-500">Courses</dt><dd class="font-black">{{ $summary['course_count'] }}</dd></div><div><dt class="text-slate-500">Lessons</dt><dd class="font-black">{{ $summary['published_lesson_count'] }}</dd></div><div><dt class="text-slate-500">Average</dt><dd class="font-black">{{ $summary['average_percent'] }}%</dd></div></dl>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="grid gap-5 lg:grid-cols-2">
            <article class="rounded-2xl border border-amber-200 bg-amber-50 p-6">
                <p class="text-xs font-black uppercase tracking-[0.16em] text-amber-700">Support signal</p><h2 class="mt-1 text-xl font-black">Learners below 50%</h2>
                @forelse ($supportSignals as $row)
                    <div class="mt-4 rounded-xl bg-white/80 p-4"><p class="font-black">{{ $row['learner']->name }}</p><p class="mt-1 text-sm text-slate-600">{{ $row['class']->name }} · {{ $row['percent'] }}% complete</p></div>
                @empty
                    <p class="mt-4 text-sm text-amber-900/80">No active-class learners are currently below the support threshold.</p>
                @endforelse
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Recent learning</p><h2 class="mt-1 text-xl font-black">Platform activity</h2>
                @forelse ($recentActivity as $activity)
                    <div class="mt-4 border-t border-slate-100 pt-4 first:border-0 first:pt-0"><p class="font-black">{{ $activity['learner']->name }} · {{ $activity['lesson']->title }}</p><p class="mt-1 text-sm text-slate-500">{{ $activity['class']->name }} · {{ $activity['status'] === 'completed' ? 'Completed' : 'In progress' }} · {{ $activity['last_viewed_at']?->diffForHumans() }}</p></div>
                @empty
                    <p class="mt-4 text-sm text-slate-500">No recent Lesson activity yet.</p>
                @endforelse
            </article>
        </section>
    </main>
</body>
</html>
