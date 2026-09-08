@extends('teacher.layout')

@section('title', 'Teacher Dashboard')

@section('content')
    <section class="overflow-hidden rounded-3xl bg-gradient-to-br from-blue-800 via-blue-700 to-cyan-600 p-6 text-white shadow-lg sm:p-8 lg:p-10">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <div class="max-w-3xl">
                <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-black uppercase tracking-[0.14em] text-cyan-50">Live teaching overview</span>
                <h1 class="mt-5 text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl">Welcome, {{ $teacher->name }}</h1>
                <p class="mt-4 max-w-2xl text-base leading-7 text-blue-50 sm:text-lg">See real class activity, completion progress, published learning coverage, and Learners who may need extra support.</p>
                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="{{ route('teacher.classes.index') }}" class="rounded-xl bg-white px-5 py-3 text-sm font-black text-blue-800 focus:outline-none focus:ring-4 focus:ring-white/40">Open My Classes</a>
                    <a href="{{ route('teacher.classes.create') }}" class="rounded-xl border border-white/25 bg-white/10 px-5 py-3 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-white/30">Create Class</a>
                    <a href="{{ route('teacher.courses.index') }}" class="rounded-xl border border-white/25 bg-white/10 px-5 py-3 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-white/30">My Courses</a>
                </div>
            </div>

            <div class="min-w-48 rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur-sm">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-cyan-100">Learners needing attention</p>
                <p class="mt-2 text-4xl font-black">{{ $needsAttentionLearnerCount }}</p>
                <p class="mt-2 text-sm leading-6 text-blue-50">Below 50% completion in at least one active class with published Lessons.</p>
            </div>
        </div>
    </section>

    <section class="mt-8" aria-labelledby="teacher-overview-heading">
        <div class="mb-4 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Live overview</p>
                <h2 id="teacher-overview-heading" class="mt-1 text-2xl font-black">Teaching at a glance</h2>
            </div>
            <p class="text-xs font-bold text-slate-400">Active classes only for live learning totals</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-bold text-slate-500">Active Classes</p>
                <p class="mt-3 text-3xl font-black">{{ $activeClassCount }}</p>
                <p class="mt-2 text-xs font-semibold text-cyan-700">{{ $totalClassCount }} total including archived</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-bold text-slate-500">Unique Learners</p>
                <p class="mt-3 text-3xl font-black">{{ $uniqueLearnerCount }}</p>
                <p class="mt-2 text-xs font-semibold text-cyan-700">Across active classes</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-bold text-slate-500">Published Lessons</p>
                <p class="mt-3 text-3xl font-black">{{ $publishedLessonCount }}</p>
                <p class="mt-2 text-xs font-semibold text-cyan-700">{{ $activeCourseCount }} active Courses</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-bold text-slate-500">Average Completion</p>
                <p class="mt-3 text-3xl font-black">{{ $averageCompletion }}%</p>
                <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100" aria-label="Average completion {{ $averageCompletion }} percent">
                    <div class="h-full rounded-full bg-cyan-600" style="width: {{ $averageCompletion }}%"></div>
                </div>
            </article>
        </div>
    </section>

    <section class="mt-8" aria-labelledby="class-insights-heading">
        <div class="mb-4 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Class insights</p>
                <h2 id="class-insights-heading" class="mt-1 text-2xl font-black">Progress by Class</h2>
            </div>
            <a href="{{ route('teacher.classes.index') }}" class="rounded-lg text-sm font-black text-blue-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Manage all classes →</a>
        </div>

        @if ($classCards->isEmpty())
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">
                <h3 class="text-xl font-black">No classes yet</h3>
                <p class="mt-2 text-sm text-slate-500">Create your first class, enroll Learners, assign a Course, and publish Lessons to start seeing live insights.</p>
                <a href="{{ route('teacher.classes.create') }}" class="mt-5 inline-flex rounded-xl bg-blue-700 px-4 py-3 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Create first class</a>
            </div>
        @else
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($classCards as $summary)
                    <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-xs font-black uppercase tracking-[0.14em] text-cyan-700">{{ $summary['class']->code }}</p>
                                <h3 class="mt-2 truncate text-xl font-black">{{ $summary['class']->name }}</h3>
                                <p class="mt-1 text-sm text-slate-500">{{ $summary['class']->subject ?: 'Subject not set' }}{{ $summary['class']->level ? ' · '.$summary['class']->level : '' }}</p>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-xs font-black {{ $summary['class']->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $summary['class']->is_active ? 'Active' : 'Archived' }}</span>
                        </div>

                        <dl class="mt-5 grid grid-cols-3 gap-3 text-center">
                            <div class="rounded-xl bg-slate-50 p-3">
                                <dt class="text-[11px] font-black uppercase tracking-wide text-slate-400">Learners</dt>
                                <dd class="mt-1 text-lg font-black">{{ $summary['learner_count'] }}</dd>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-3">
                                <dt class="text-[11px] font-black uppercase tracking-wide text-slate-400">Courses</dt>
                                <dd class="mt-1 text-lg font-black">{{ $summary['course_count'] }}</dd>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-3">
                                <dt class="text-[11px] font-black uppercase tracking-wide text-slate-400">Lessons</dt>
                                <dd class="mt-1 text-lg font-black">{{ $summary['published_lesson_count'] }}</dd>
                            </div>
                        </dl>

                        <div class="mt-5">
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="font-bold text-slate-500">Average completion</span>
                                <span class="font-black">{{ $summary['average_percent'] }}%</span>
                            </div>
                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100" aria-label="{{ $summary['class']->name }} completion {{ $summary['average_percent'] }} percent">
                                <div class="h-full rounded-full bg-blue-700" style="width: {{ $summary['average_percent'] }}%"></div>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2 text-xs font-black">
                            @if ($summary['published_lesson_count'] === 0)
                                <span class="rounded-full bg-amber-50 px-3 py-1.5 text-amber-800">No published Lessons</span>
                            @else
                                <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-emerald-700">{{ $summary['completed_learner_count'] }} fully complete</span>
                                <span class="rounded-full bg-rose-50 px-3 py-1.5 text-rose-700">{{ $summary['needs_attention_count'] }} need attention</span>
                            @endif
                        </div>

                        <div class="mt-5 flex flex-wrap gap-2 border-t border-slate-100 pt-5">
                            <a href="{{ route('teacher.classes.progress', $summary['class']) }}" class="rounded-xl bg-blue-700 px-3 py-2 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">View Progress</a>
                            <a href="{{ route('teacher.classes.show', $summary['class']) }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-black text-slate-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Open Class</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <section class="mt-8 grid gap-6 xl:grid-cols-2">
        <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="support-heading">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-rose-600">Support signals</p>
                    <h2 id="support-heading" class="mt-1 text-xl font-black">Learners needing attention</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">Learners below 50% completion in an active class with published Lessons.</p>
                </div>
                <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-black text-rose-700">{{ $needsAttentionLearnerCount }} unique</span>
            </div>

            @if ($supportSignals->isEmpty())
                <div class="mt-5 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center">
                    <p class="font-black">No support signals right now.</p>
                    <p class="mt-1 text-sm text-slate-500">Learners with published content are currently at or above 50% completion.</p>
                </div>
            @else
                <div class="mt-5 divide-y divide-slate-100">
                    @foreach ($supportSignals as $signal)
                        <div class="flex flex-wrap items-center justify-between gap-4 py-4 first:pt-0 last:pb-0">
                            <div class="min-w-0">
                                <p class="font-black">{{ $signal['learner']->name }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $signal['class']->name }} · {{ $signal['completed'] }} of {{ $signal['total'] }} Lessons completed</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-black text-rose-700">{{ $signal['percent'] }}%</span>
                                <a href="{{ route('teacher.classes.progress', $signal['class']) }}" class="rounded-lg text-sm font-black text-blue-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Review →</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="recent-teacher-activity-heading">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Recent learning</p>
                <h2 id="recent-teacher-activity-heading" class="mt-1 text-xl font-black">Learner Activity</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">Latest Lesson activity from Learners in your active classes.</p>
            </div>

            @if ($recentActivity->isEmpty())
                <div class="mt-5 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center">
                    <p class="font-black">No Lesson activity yet.</p>
                    <p class="mt-1 text-sm text-slate-500">Activity will appear after Learners start or complete published Lessons.</p>
                </div>
            @else
                <div class="mt-5 divide-y divide-slate-100">
                    @foreach ($recentActivity as $activity)
                        <div class="py-4 first:pt-0 last:pb-0">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="font-black">{{ $activity['learner']->name }} · {{ $activity['lesson']->title }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $activity['class']->name }} · {{ $activity['course']->title }} · {{ $activity['unit']->title }}</p>
                                </div>
                                <span class="rounded-full px-2.5 py-1 text-xs font-black {{ $activity['status'] === 'completed' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">{{ $activity['status'] === 'completed' ? 'Completed' : 'In progress' }}</span>
                            </div>
                            @if ($activity['last_viewed_at'])
                                <p class="mt-2 text-xs font-semibold text-slate-400">Updated {{ $activity['last_viewed_at']->diffForHumans() }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </article>
    </section>
@endsection
