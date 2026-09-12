@extends('teacher.layout')
@section('title', 'Dashboard')
@section('content')
@php($d = $teacherDashboard)
<div class="space-y-8">
    <section class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8" aria-labelledby="teacher-dashboard-heading">
        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Teaching Workspace</p>
                <h1 id="teacher-dashboard-heading" class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">Welcome, {{ auth()->user()->name }}.</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">Manage classes, review assessments, and support learner progress from one place.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('teacher.assessments.create') }}" class="sg-btn-secondary">Create Assessment</a>
                <a href="{{ route('teacher.classes.index') }}" class="sg-btn-primary">Open My Classes</a>
            </div>
        </div>
    </section>

    <section aria-labelledby="teacher-overview-heading">
        <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Overview</p>
                <h2 id="teacher-overview-heading" class="mt-1 text-xl font-black">Teaching at a glance</h2>
            </div>
            <p class="text-xs font-semibold text-slate-500">Live operational data</p>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-slate-200 bg-white p-5">
                <p class="text-sm font-bold text-slate-500">Active classes</p>
                <p class="mt-3 text-3xl font-black">{{ $d['active_classes'] }}</p>
                <p class="mt-2 text-xs text-slate-500">{{ $d['active_learners'] }} unique active learners</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5">
                <p class="text-sm font-bold text-slate-500">Assigned courses</p>
                <p class="mt-3 text-3xl font-black">{{ $d['assigned_courses'] }}</p>
                <p class="mt-2 text-xs text-slate-500">Across your active classes</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5">
                <p class="text-sm font-bold text-slate-500">Pending reviews</p>
                <p class="mt-3 text-3xl font-black">{{ $d['pending_reviews'] }}</p>
                <p class="mt-2 text-xs text-slate-500">Short-answer attempts waiting for review</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5">
                <p class="text-sm font-bold text-slate-500">Assessment average</p>
                <p class="mt-3 text-3xl font-black">{{ $d['assessment_average'] !== null ? number_format($d['assessment_average'], 0).'%' : '—' }}</p>
                <p class="mt-2 text-xs text-slate-500">Completed attempts on your assessments</p>
            </article>
        </div>
    </section>

    <section aria-labelledby="classes-heading">
        <div class="mb-4 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Classes</p>
                <h2 id="classes-heading" class="mt-1 text-xl font-black">My active classes</h2>
            </div>
            <a href="{{ route('teacher.classes.index') }}" class="text-sm font-black underline">All classes</a>
        </div>
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($d['classes'] as $item)
                <article class="rounded-2xl border border-slate-200 bg-white p-5">
                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">{{ $item['class']->code }}</p>
                    <h3 class="mt-2 text-lg font-black">{{ $item['class']->name }}</h3>
                    <p class="mt-1 text-sm text-slate-600">{{ $item['class']->subject }} · {{ $item['class']->academic_year }}</p>
                    <div class="mt-5 grid grid-cols-3 gap-3 text-sm">
                        <div><p class="text-xs text-slate-500">Learners</p><p class="mt-1 font-black">{{ $item['learner_count'] }}</p></div>
                        <div><p class="text-xs text-slate-500">Courses</p><p class="mt-1 font-black">{{ $item['course_count'] }}</p></div>
                        <div><p class="text-xs text-slate-500">Mastery</p><p class="mt-1 font-black">{{ $item['mastery_average'] !== null ? number_format($item['mastery_average'], 0).'%' : '—' }}</p></div>
                    </div>
                    <p class="mt-3 text-xs text-slate-500">Mastery uses the latest stored learner/course snapshots.</p>
                    <div class="mt-5 flex flex-wrap gap-2">
                        <a href="{{ $item['url'] }}" class="sg-btn-secondary">Open Class</a>
                        <a href="{{ $item['progress_url'] }}" class="sg-btn-secondary">Class Progress</a>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-slate-200 bg-white p-6 md:col-span-2 xl:col-span-3">
                    <p class="font-black">No active classes yet.</p>
                    <p class="mt-2 text-sm text-slate-600">Create or activate a class to begin your teaching workspace.</p>
                    <a href="{{ route('teacher.classes.create') }}" class="sg-btn-primary mt-5">Create Class</a>
                </div>
            @endforelse
        </div>
    </section>

    <section class="grid gap-5 xl:grid-cols-[minmax(0,1.05fr)_minmax(0,.95fr)]">
        <article class="rounded-2xl border border-slate-200 bg-white p-6" aria-labelledby="reviews-heading">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Assessment Workload</p>
                    <h2 id="reviews-heading" class="mt-1 text-xl font-black">Pending reviews</h2>
                </div>
                <a href="{{ route('teacher.assessments.index') }}" class="text-sm font-black underline">All assessments</a>
            </div>
            <div class="mt-5 divide-y divide-slate-200">
                @forelse ($d['pending_attempts'] as $attempt)
                    <div class="flex flex-wrap items-center justify-between gap-4 py-4 first:pt-0 last:pb-0">
                        <div>
                            <p class="font-black">{{ $attempt->assessment?->title ?? 'Assessment' }}</p>
                            <p class="mt-1 text-sm text-slate-600">{{ $attempt->learner?->name ?? 'Learner' }} · Pending review</p>
                        </div>
                        <a href="{{ route('teacher.assessments.attempts.review', $attempt) }}" class="sg-btn-secondary">Review</a>
                    </div>
                @empty
                    <p class="text-sm leading-6 text-slate-600">No assessment attempts are waiting for manual review.</p>
                @endforelse
            </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-6" aria-labelledby="support-heading">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Learner Progress</p>
                    <h2 id="support-heading" class="mt-1 text-xl font-black">Learners needing support</h2>
                </div>
                <a href="{{ route('teacher.progress.index') }}" class="text-sm font-black underline">Full progress</a>
            </div>
            <div class="mt-5 divide-y divide-slate-200">
                @forelse ($d['support_learners'] as $item)
                    <div class="flex flex-wrap items-center justify-between gap-4 py-4 first:pt-0 last:pb-0">
                        <div>
                            <p class="font-black">{{ $item['learner']?->name ?? 'Learner' }}</p>
                            <p class="mt-1 text-sm text-slate-600">{{ $item['course']?->title ?? 'Course' }} · {{ $item['level'] }}</p>
                            @if ($item['updated_at'])
                                <p class="mt-1 text-xs text-slate-500">Snapshot updated {{ $item['updated_at']->diffForHumans() }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-lg font-black">{{ number_format($item['score'], 0) }}%</span>
                            @if ($item['learner'])
                                <a href="{{ route('teacher.progress.learners.show', $item['learner']) }}" class="text-sm font-black underline">Open</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm leading-6 text-slate-600">No current mastery snapshots are marked Needs Support or Developing.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section aria-labelledby="assessment-summary-heading">
        <div class="mb-4 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Assessments</p>
                <h2 id="assessment-summary-heading" class="mt-1 text-xl font-black">Assessment management</h2>
            </div>
            <div class="text-right text-xs text-slate-500">
                <p>{{ $d['published_assessments'] }} published</p>
                <p>{{ $d['draft_assessments'] }} drafts</p>
            </div>
        </div>
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($d['assessments'] as $item)
                @php($a = $item['assessment'])
                <article class="rounded-2xl border border-slate-200 bg-white p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">{{ ucfirst($a->status) }}</p>
                            <h3 class="mt-2 text-lg font-black">{{ $a->title }}</h3>
                            <p class="mt-1 text-sm text-slate-600">{{ $a->course?->title ?? 'Course' }}</p>
                        </div>
                        @if ($a->pending_review_count > 0)
                            <span class="rounded-full border border-slate-300 px-2.5 py-1 text-xs font-black">{{ $a->pending_review_count }} review</span>
                        @endif
                    </div>
                    <p class="mt-4 text-sm text-slate-600">{{ $a->completed_attempts_count }} completed attempts</p>
                    <div class="mt-5 flex flex-wrap gap-2">
                        <a href="{{ $item['url'] }}" class="sg-btn-secondary">Edit</a>
                        <a href="{{ $item['attempts_url'] }}" class="sg-btn-secondary">Attempts</a>
                        <a href="{{ $item['analytics_url'] }}" class="sg-btn-secondary">Analytics</a>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-slate-200 bg-white p-6 md:col-span-2 xl:col-span-3">
                    <p class="font-black">No assessments yet.</p>
                    <p class="mt-2 text-sm text-slate-600">Create your first assessment for a course you manage.</p>
                    <a href="{{ route('teacher.assessments.create') }}" class="sg-btn-primary mt-5">Create Assessment</a>
                </div>
            @endforelse
        </div>
    </section>

    <section class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_minmax(320px,.7fr)]">
        <article class="rounded-2xl border border-slate-200 bg-white p-6" aria-labelledby="activity-heading">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Recent</p>
            <h2 id="activity-heading" class="mt-1 text-xl font-black">Teaching activity</h2>
            <div class="mt-5 divide-y divide-slate-200">
                @foreach ($d['activity'] as $activity)
                    <div class="py-4 first:pt-0 last:pb-0">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-black">{{ $activity['title'] }}</p>
                                <p class="mt-1 text-sm text-slate-600">{{ $activity['meta'] }}</p>
                            </div>
                            @if ($activity['when'])
                                <span class="shrink-0 text-xs text-slate-500">{{ $activity['when'] }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </article>

        <aside class="rounded-2xl border border-slate-200 bg-slate-50 p-6" aria-labelledby="quick-actions-heading">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Quick Actions</p>
            <h2 id="quick-actions-heading" class="mt-1 text-xl font-black">Teaching tools</h2>
            <div class="mt-5 grid gap-3">
                <a href="{{ route('teacher.classes.create') }}" class="sg-btn-secondary justify-center">Create Class</a>
                <a href="{{ route('teacher.courses.index') }}" class="sg-btn-secondary justify-center">Manage Course Content</a>
                <a href="{{ route('teacher.assessments.create') }}" class="sg-btn-secondary justify-center">Create Assessment</a>
                <a href="{{ route('teacher.progress.index') }}" class="sg-btn-secondary justify-center">View Learner Progress</a>
            </div>
            <div class="mt-5 rounded-xl border border-slate-200 bg-white p-4">
                <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Mastery snapshot</p>
                <p class="mt-2 text-2xl font-black">{{ $d['mastery_snapshot_average'] !== null ? number_format($d['mastery_snapshot_average'], 0).'%' : '—' }}</p>
                <p class="mt-1 text-xs leading-5 text-slate-500">Average of the latest available course mastery snapshots for learners in your active classes.</p>
            </div>
        </aside>
    </section>
</div>
@endsection
