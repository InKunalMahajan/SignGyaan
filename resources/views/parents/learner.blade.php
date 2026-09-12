<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="View an approved Learner summary in SignGyaan">
    <title>{{ $learner->name }} | Parent View | SignGyaan</title>
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
            <a href="{{ route('dashboard.role', 'parents') }}" class="flex items-center gap-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-900">
                <span class="grid size-10 place-items-center rounded-xl bg-slate-950 text-sm font-black text-white">SG</span>
                <span><span class="block font-black">SignGyaan</span><span class="block text-xs font-semibold text-slate-500">Family learning</span></span>
            </a>
            <div class="flex gap-2">
                <a href="{{ route('dashboard.role', 'parents') }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-black text-slate-700">Dashboard</a>
                <a href="{{ route('parents.profile.show') }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-black text-slate-700">Parent Profile</a>
            </div>
        </div>
    </header>

    <main id="main-content" class="mx-auto max-w-6xl space-y-6 px-5 py-7 sm:px-8 lg:py-10">
        <section class="rounded-3xl bg-slate-950 p-6 text-white shadow-lg sm:p-8">
            <span class="inline-flex rounded-full border border-white/20 px-3 py-1 text-xs font-black uppercase tracking-[0.14em]">Approved family access</span>
            <h1 class="mt-4 text-3xl font-black tracking-tight sm:text-4xl">{{ $learner->name }}</h1>
            <p class="mt-2 text-slate-300">Relationship: {{ ucfirst($link->relationship) }}</p>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" aria-label="Learner progress summary">
            @foreach ([
                ['Active classes', $summary['active_classes']],
                ['Mastery average', $summary['mastery_average'] === null ? '—' : $summary['mastery_average'].'%'],
                ['Assessment average', $summary['assessment_average'] === null ? '—' : $summary['assessment_average'].'%'],
                ['Needs support', $summary['support_count']],
            ] as $item)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">{{ $item[0] }}</p>
                    <p class="mt-2 text-2xl font-black">{{ $item[1] }}</p>
                </article>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">Course progress</p>
                <h2 class="mt-1 text-xl font-black">Mastery by Course</h2>
                <div class="mt-4 space-y-4">
                    @forelse ($summary['masteries'] as $mastery)
                        <article class="rounded-xl border border-slate-200 p-4">
                            <div class="flex items-start justify-between gap-3"><div><h3 class="font-black">{{ $mastery['course_title'] }}</h3><p class="mt-1 text-sm text-slate-600">{{ $mastery['level_label'] }}</p></div><span class="text-lg font-black">{{ $mastery['score'] }}%</span></div>
                            <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-200" role="progressbar" aria-valuenow="{{ $mastery['score'] }}" aria-valuemin="0" aria-valuemax="100"><div class="h-full bg-slate-950" style="width: {{ min(100, max(0, $mastery['score'])) }}%"></div></div>
                            <div class="mt-3 grid grid-cols-2 gap-3 text-xs font-semibold text-slate-500">
                                <span>Lessons: {{ $mastery['lesson_completion'] }}%</span>
                                <span>Assessments: {{ $mastery['assessment_percentage'] === null ? 'No completed result' : $mastery['assessment_percentage'].'%' }}</span>
                            </div>
                        </article>
                    @empty
                        <p class="rounded-xl bg-slate-50 p-4 text-sm text-slate-600">No mastery snapshot is available yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">Assessment results</p>
                <h2 class="mt-1 text-xl font-black">Recent Assessments</h2>
                <div class="mt-4 space-y-4">
                    @forelse ($summary['assessments'] as $assessment)
                        <article class="rounded-xl border border-slate-200 p-4">
                            <div class="flex items-start justify-between gap-3"><div><h3 class="font-black">{{ $assessment['title'] }}</h3><p class="mt-1 text-sm text-slate-600">{{ $assessment['course_title'] }}</p></div>
                                @if ($assessment['status'] === 'pending_review')
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black">Pending review</span>
                                @else
                                    <span class="text-lg font-black">{{ $assessment['percentage'] === null ? '—' : $assessment['percentage'].'%' }}</span>
                                @endif
                            </div>
                            @if ($assessment['status'] === 'completed')
                                <p class="mt-3 text-sm font-bold text-slate-700">{{ $assessment['passed'] ? 'Passed' : 'Needs improvement' }}</p>
                            @else
                                <p class="mt-3 text-sm text-slate-600">The teacher has not completed the final review yet.</p>
                            @endif
                        </article>
                    @empty
                        <p class="rounded-xl bg-slate-50 p-4 text-sm text-slate-600">No submitted assessment results yet.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-black">Learner Profile</h2>
            <dl class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-xl bg-slate-50 p-4"><dt class="text-xs font-black uppercase tracking-wide text-slate-500">Education level</dt><dd class="mt-1 font-black">{{ $profile?->education_level ?: 'Not added yet' }}</dd></div>
                <div class="rounded-xl bg-slate-50 p-4"><dt class="text-xs font-black uppercase tracking-wide text-slate-500">Class / Grade</dt><dd class="mt-1 font-black">{{ $profile?->class_grade ?: 'Not added yet' }}</dd></div>
                <div class="rounded-xl bg-slate-50 p-4"><dt class="text-xs font-black uppercase tracking-wide text-slate-500">Institution</dt><dd class="mt-1 font-black">{{ $profile?->institution ?: 'Not added yet' }}</dd></div>
            </dl>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="font-black">Remove this link</h2>
            <p class="mt-1 text-sm text-slate-500">Removing the link immediately removes Parent access to this learner.</p>
            <form method="POST" action="{{ route('parents.learners.destroy', $link) }}" class="mt-4" onsubmit="return confirm('Remove this Learner link?')">
                @csrf
                @method('DELETE')
                <button class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-black text-slate-800">Remove learner link</button>
            </form>
        </section>
    </main>
</body>
</html>
