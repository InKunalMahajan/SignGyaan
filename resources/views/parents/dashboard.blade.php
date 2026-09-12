<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SignGyaan parent dashboard">
    <title>Parent Dashboard | SignGyaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <a href="#main-content" class="sr-only z-50 rounded-lg bg-slate-950 px-4 py-3 text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Skip to main content</a>

    <div class="min-h-screen lg:grid lg:grid-cols-[280px_minmax(0,1fr)]">
        <aside class="border-b border-slate-200 bg-white lg:min-h-screen lg:border-b-0 lg:border-r">
            <div class="px-5 py-5 lg:px-6">
                <a href="{{ route('dashboard.role', 'parents') }}" class="flex items-center gap-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-900">
                    <span class="grid size-11 place-items-center rounded-xl bg-slate-950 text-sm font-black text-white">SG</span>
                    <span><span class="block text-lg font-black">SignGyaan</span><span class="block text-xs font-medium text-slate-500">Family learning</span></span>
                </a>
            </div>
            <nav class="px-4 pb-5 lg:px-5" aria-label="Parent navigation">
                <div class="flex gap-2 overflow-x-auto pb-1 lg:flex-col lg:overflow-visible">
                    <a href="{{ route('dashboard.role', 'parents') }}" class="rounded-xl bg-slate-950 px-3 py-3 text-sm font-bold text-white">Dashboard</a>
                    <a href="{{ route('parents.profile.show') }}" class="rounded-xl px-3 py-3 text-sm font-bold text-slate-700 hover:bg-slate-100">Parent Profile</a>
                    <a href="{{ route('dashboard.guest') }}" class="rounded-xl px-3 py-3 text-sm font-bold text-slate-700 hover:bg-slate-100">Explore Public</a>
                </div>
            </nav>
        </aside>

        <main id="main-content" class="min-w-0">
            <header class="border-b border-slate-200 bg-white px-5 py-4 sm:px-8 lg:px-10">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-4">
                    <div><p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Family Learning</p><p class="mt-1 text-sm font-semibold text-slate-500">Parent Dashboard</p></div>
                    <x-app-account-menu role="parents" />
                </div>
            </header>

            <div class="mx-auto max-w-7xl space-y-8 px-5 py-8 sm:px-8 lg:px-10 lg:py-10">
                <section>
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-500">Parent dashboard</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight">Support learning with clear progress.</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">See approved linked learners, mastery snapshots, assessment results, and areas where support may help.</p>
                </section>

                <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5" aria-label="Family learning summary">
                    @php($stats = $parentDashboard['stats'])
                    @foreach ([
                        ['Linked learners', $stats['linked_learners'], 'Approved family access'],
                        ['Active classes', $stats['active_classes'], 'Across linked learners'],
                        ['Courses tracked', $stats['courses_with_mastery'], 'Mastery snapshots'],
                        ['Assessment average', $stats['assessment_average'] === null ? '—' : $stats['assessment_average'].'%', 'Completed results only'],
                        ['Needs attention', $stats['support_items'], $stats['pending_reviews'].' result(s) pending review'],
                    ] as $card)
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p class="text-xs font-black uppercase tracking-[0.12em] text-slate-500">{{ $card[0] }}</p>
                            <p class="mt-2 text-3xl font-black">{{ $card[1] }}</p>
                            <p class="mt-1 text-xs font-semibold text-slate-500">{{ $card[2] }}</p>
                        </article>
                    @endforeach
                </section>

                @if ($parentDashboard['learners']->isEmpty())
                    <section class="rounded-2xl border border-slate-200 bg-white p-7 shadow-sm">
                        <h2 class="text-xl font-black">No approved learner link yet</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Link a learner from Parent Profile. Progress becomes visible only after the learner approves the request.</p>
                        <a href="{{ route('parents.profile.show') }}" class="mt-5 inline-flex rounded-xl bg-slate-950 px-4 py-3 text-sm font-black text-white">Open Parent Profile</a>
                        @if ($parentDashboard['links']['pending'] > 0)
                            <p class="mt-4 text-sm font-bold text-slate-700">{{ $parentDashboard['links']['pending'] }} link request(s) waiting for learner approval.</p>
                        @endif
                    </section>
                @else
                    <section>
                        <div class="mb-4 flex items-end justify-between gap-4">
                            <div><p class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">9B</p><h2 class="mt-1 text-2xl font-black">Linked Learners</h2></div>
                            <a href="{{ route('parents.profile.show') }}" class="text-sm font-black text-slate-700 underline underline-offset-4">Manage links</a>
                        </div>
                        <div class="grid gap-4 lg:grid-cols-2">
                            @foreach ($parentDashboard['learners'] as $learner)
                                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div><p class="text-xs font-black uppercase tracking-[0.12em] text-slate-500">{{ $learner['relationship'] }}</p><h3 class="mt-1 text-xl font-black">{{ $learner['name'] }}</h3><p class="mt-1 text-sm text-slate-500">{{ implode(', ', $learner['class_names']) ?: 'No active class' }}</p></div>
                                        <a href="{{ route('parents.learners.show', $learner['link_id']) }}" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-black">View learner</a>
                                    </div>
                                    <dl class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
                                        <div class="rounded-xl bg-slate-50 p-3"><dt class="text-xs font-bold text-slate-500">Classes</dt><dd class="mt-1 text-lg font-black">{{ $learner['active_classes'] }}</dd></div>
                                        <div class="rounded-xl bg-slate-50 p-3"><dt class="text-xs font-bold text-slate-500">Mastery</dt><dd class="mt-1 text-lg font-black">{{ $learner['mastery_average'] === null ? '—' : $learner['mastery_average'].'%' }}</dd></div>
                                        <div class="rounded-xl bg-slate-50 p-3"><dt class="text-xs font-bold text-slate-500">Assessments</dt><dd class="mt-1 text-lg font-black">{{ $learner['assessment_average'] === null ? '—' : $learner['assessment_average'].'%' }}</dd></div>
                                        <div class="rounded-xl bg-slate-50 p-3"><dt class="text-xs font-bold text-slate-500">Support</dt><dd class="mt-1 text-lg font-black">{{ $learner['support_count'] }}</dd></div>
                                    </dl>
                                    @if ($learner['pending_reviews'] > 0)
                                        <p class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-bold">{{ $learner['pending_reviews'] }} assessment result(s) waiting for teacher review.</p>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif

                <section class="grid gap-6 xl:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">9C · Mastery</p>
                        <h2 class="mt-1 text-xl font-black">Support Needed</h2>
                        <div class="mt-4 space-y-3">
                            @forelse ($parentDashboard['support_items'] as $item)
                                <a href="{{ route('parents.learners.show', $item['link_id']) }}" class="block rounded-xl border border-slate-200 p-4 hover:bg-slate-50">
                                    <div class="flex items-start justify-between gap-3"><div><p class="font-black">{{ $item['learner_name'] }} · {{ $item['course_title'] }}</p><p class="mt-1 text-sm text-slate-600">{{ $item['message'] }}</p></div><span class="text-sm font-black">{{ $item['score'] }}%</span></div>
                                    <p class="mt-2 text-xs font-bold uppercase tracking-wide text-slate-500">{{ $item['level_label'] }}</p>
                                </a>
                            @empty
                                <p class="rounded-xl bg-slate-50 p-4 text-sm text-slate-600">No current Needs Support or Developing mastery snapshot.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">9E · Activity</p>
                        <h2 class="mt-1 text-xl font-black">Recent Learning Activity</h2>
                        <div class="mt-4 space-y-3">
                            @forelse ($parentDashboard['recent_activity'] as $activity)
                                <a href="{{ route('parents.learners.show', $activity['link_id']) }}" class="block rounded-xl border border-slate-200 p-4 hover:bg-slate-50">
                                    <p class="font-black">{{ $activity['learner_name'] }} · {{ $activity['title'] }}</p>
                                    <p class="mt-1 text-sm text-slate-600">{{ $activity['meta'] }}</p>
                                    <p class="mt-2 text-xs font-semibold text-slate-400">{{ $activity['timestamp']->diffForHumans() }}</p>
                                </a>
                            @empty
                                <p class="rounded-xl bg-slate-50 p-4 text-sm text-slate-600">No recent progress or assessment activity yet.</p>
                            @endforelse
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
