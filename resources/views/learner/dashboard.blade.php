@extends('learner.layout')
@section('title', 'Dashboard')
@section('header_label', 'Dashboard')
@section('content')
@php($d = $learnerDashboard)
<div class="space-y-8">
    <section class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8" aria-labelledby="learner-dashboard-heading">
        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">My Learning</p>
                <h1 id="learner-dashboard-heading" class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">Welcome, {{ auth()->user()->name }}.</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">Continue your lessons, check assessments, and see your progress in one place.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('learner.progress.index') }}" class="sg-btn-secondary">View Progress</a>
                <a href="{{ $d['continue']['url'] }}" class="sg-btn-primary">{{ $d['continue']['label'] }}</a>
            </div>
        </div>
    </section>

    <section aria-labelledby="overview-heading">
        <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Overview</p>
                <h2 id="overview-heading" class="mt-1 text-xl font-black">Your learning today</h2>
            </div>
            <p class="text-xs font-semibold text-slate-500">Live data</p>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-slate-200 bg-white p-5">
                <p class="text-sm font-bold text-slate-500">Active courses</p>
                <p class="mt-3 text-3xl font-black">{{ $d['courses_count'] }}</p>
                <p class="mt-2 text-xs text-slate-500">Across {{ $d['classes_count'] }} active {{ $d['classes_count'] === 1 ? 'class' : 'classes' }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5">
                <p class="text-sm font-bold text-slate-500">Lessons completed</p>
                <p class="mt-3 text-3xl font-black">{{ $d['lessons_completed'] }}</p>
                <p class="mt-2 text-xs text-slate-500">{{ $d['lessons_in_progress'] }} currently in progress</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5">
                <p class="text-sm font-bold text-slate-500">Overall mastery</p>
                <p class="mt-3 text-3xl font-black">{{ $d['overall_mastery'] !== null ? number_format($d['overall_mastery'], 0).'%' : '—' }}</p>
                <p class="mt-2 text-xs text-slate-500">{{ $d['overall_mastery_label'] }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5">
                <p class="text-sm font-bold text-slate-500">Assessment average</p>
                <p class="mt-3 text-3xl font-black">{{ $d['average_assessment'] !== null ? number_format($d['average_assessment'], 0).'%' : '—' }}</p>
                <p class="mt-2 text-xs text-slate-500">{{ $d['pending_review'] }} pending teacher review</p>
            </article>
        </div>
    </section>

    <section class="grid gap-5 lg:grid-cols-[minmax(0,1.2fr)_minmax(300px,.8fr)]" aria-labelledby="continue-heading">
        <article class="rounded-2xl border border-slate-200 bg-white p-6">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Continue Learning</p>
            <h2 id="continue-heading" class="mt-2 text-2xl font-black">{{ $d['continue']['title'] }}</h2>
            @if ($d['continue']['available'])
                <p class="mt-2 text-sm font-bold text-slate-700">{{ $d['continue']['course'] }} · {{ $d['continue']['class'] }}</p>
            @endif
            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $d['continue']['meta'] }}</p>
            <a href="{{ $d['continue']['url'] }}" class="sg-btn-primary mt-5">{{ $d['continue']['label'] }}</a>
        </article>

        <aside class="rounded-2xl border border-slate-200 bg-slate-50 p-6" aria-labelledby="next-steps-heading">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Recommended</p>
            <h2 id="next-steps-heading" class="mt-2 text-xl font-black">Next steps</h2>
            <div class="mt-4 space-y-3">
                @forelse ($d['recommendations'] as $item)
                    <a href="{{ $item['url'] }}" class="block rounded-xl border border-slate-200 bg-white p-4 focus:outline-none focus:ring-2 focus:ring-slate-900">
                        <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">{{ $item['course'] }}</p>
                        <p class="mt-1 font-black">{{ $item['title'] }}</p>
                        <p class="mt-1 text-sm leading-5 text-slate-600">{{ $item['message'] }}</p>
                    </a>
                @empty
                    <p class="text-sm leading-6 text-slate-600">Complete more learning activity to receive a next-step recommendation.</p>
                @endforelse
            </div>
        </aside>
    </section>

    <section aria-labelledby="courses-heading">
        <div class="mb-4 flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Courses</p>
                <h2 id="courses-heading" class="mt-1 text-xl font-black">My course progress</h2>
            </div>
            <a href="{{ route('learner.progress.index') }}" class="text-sm font-black underline">Full progress</a>
        </div>
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($d['courses'] as $item)
                @php($m = $item['mastery'])
                <article class="rounded-2xl border border-slate-200 bg-white p-5">
                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">{{ $item['class']?->name ?? 'Assigned course' }}</p>
                    <h3 class="mt-2 text-lg font-black">{{ $item['course']->title }}</h3>
                    <div class="mt-5 h-2 overflow-hidden rounded-full bg-slate-200" role="progressbar" aria-label="{{ $item['course']->title }} lesson completion" aria-valuenow="{{ (float) $m->lesson_completion_percentage }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="h-full bg-slate-950" style="width: {{ min(100, max(0, (float) $m->lesson_completion_percentage)) }}%"></div>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div><p class="text-xs text-slate-500">Lessons</p><p class="mt-1 font-black">{{ $m->lessons_completed }}/{{ $m->lessons_total }}</p></div>
                        <div><p class="text-xs text-slate-500">Mastery</p><p class="mt-1 font-black">{{ number_format((float) $m->mastery_score, 0) }}% · {{ $m->levelLabel() }}</p></div>
                    </div>
                    <a href="{{ $item['url'] }}" class="sg-btn-secondary mt-5">Open Course</a>
                </article>
            @empty
                <div class="rounded-2xl border border-slate-200 bg-white p-6 md:col-span-2 xl:col-span-3">
                    <p class="font-black">No active courses yet.</p>
                    <p class="mt-2 text-sm text-slate-600">Assigned active courses will appear here.</p>
                </div>
            @endforelse
        </div>
    </section>

    <section class="grid gap-5 xl:grid-cols-[minmax(0,1.1fr)_minmax(0,.9fr)]">
        <article class="rounded-2xl border border-slate-200 bg-white p-6" aria-labelledby="assessments-heading">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Assessments</p>
                    <h2 id="assessments-heading" class="mt-1 text-xl font-black">Assessment status</h2>
                </div>
                <a href="{{ route('learner.assessments.index') }}" class="text-sm font-black underline">All assessments</a>
            </div>
            <div class="mt-5 divide-y divide-slate-200">
                @forelse ($d['assessments'] as $item)
                    <div class="flex flex-wrap items-center justify-between gap-4 py-4 first:pt-0 last:pb-0">
                        <div>
                            <p class="font-black">{{ $item['assessment']->title }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $item['assessment']->course->title }} · {{ $item['status'] }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            @if ($item['attempt']?->percentage !== null)
                                <span class="text-sm font-black">{{ number_format((float) $item['attempt']->percentage, 0) }}%</span>
                            @endif
                            <a href="{{ $item['url'] }}" class="text-sm font-black underline">Open</a>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-600">No published assessments are available yet.</p>
                @endforelse
            </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-6" aria-labelledby="activity-heading">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Recent</p>
            <h2 id="activity-heading" class="mt-1 text-xl font-black">Learning activity</h2>
            <div class="mt-5 divide-y divide-slate-200">
                @foreach ($d['activity'] as $activity)
                    <div class="py-4 first:pt-0 last:pb-0">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-black">{{ $activity['title'] }}</p>
                                <p class="mt-1 text-sm text-slate-600">{{ $activity['meta'] }}</p>
                            </div>
                            @if ($activity['when'])<span class="shrink-0 text-xs text-slate-500">{{ $activity['when'] }}</span>@endif
                        </div>
                    </div>
                @endforeach
            </div>
        </article>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5" aria-label="Quick learner links">
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('learner.classes.index') }}" class="sg-btn-secondary">My Classes</a>
            <a href="{{ route('learner.progress.index') }}" class="sg-btn-secondary">My Progress</a>
            <a href="{{ route('learner.assessments.history') }}" class="sg-btn-secondary">Assessment History</a>
            <a href="{{ route('learner.profile.show') }}" class="sg-btn-secondary">My Profile</a>
            <a href="{{ route('learner.parent-links.index') }}" class="sg-btn-secondary">Parent Access</a>
        </div>
    </section>
</div>
@endsection
