@extends('learner.layout')
@section('title', 'Assessment History')
@section('header_label', 'Assessment History')
@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Assessment System</p>
            <h1 class="mt-2 text-3xl font-black text-slate-950">Assessment History</h1>
            <p class="mt-2 text-sm text-slate-600">Review your submitted assessments, scores, results, and pending teacher reviews.</p>
        </div>
        <a href="{{ route('learner.assessments.index') }}" class="sg-btn-secondary">Back to Assessments</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        @forelse ($history as $attempt)
            <article class="grid gap-4 border-b border-slate-200 p-5 last:border-b-0 lg:grid-cols-[minmax(0,1fr)_repeat(4,minmax(90px,auto))_auto] lg:items-center">
                <div class="min-w-0">
                    <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">{{ $attempt->assessment->course->title }}</p>
                    <h2 class="mt-1 font-black text-slate-950">{{ $attempt->assessment->title }}</h2>
                    <p class="mt-1 text-xs text-slate-500">Attempt {{ $attempt->attempt_number }} · {{ optional($attempt->submitted_at)->format('d M Y, h:i A') }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Status</p>
                    <p class="mt-1 text-sm font-black">{{ str($attempt->status)->replace('_', ' ')->title() }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Score</p>
                    <p class="mt-1 text-sm font-black">{{ $attempt->earned_marks !== null ? $attempt->earned_marks.' / '.$attempt->total_marks_snapshot : '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Percentage</p>
                    <p class="mt-1 text-sm font-black">{{ $attempt->percentage !== null ? number_format((float) $attempt->percentage, 2).'%' : '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Result</p>
                    <p class="mt-1 text-sm font-black">
                        @if ($attempt->status === 'pending_review') Pending Review
                        @elseif ($attempt->passed === true) Passed
                        @elseif ($attempt->passed === false) Needs Improvement
                        @else —
                        @endif
                    </p>
                </div>
                <div>
                    <a href="{{ route('learner.assessments.attempts.result', $attempt) }}" class="sg-btn-primary">View Result</a>
                </div>
            </article>
        @empty
            <div class="p-10 text-center">
                <p class="font-black">No assessment history yet.</p>
                <p class="mt-2 text-sm text-slate-500">Submitted assessments will appear here.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
