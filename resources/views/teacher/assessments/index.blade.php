@extends('teacher.layout')

@section('title', 'Assessments')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Assessment System</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight">Assessments</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Create, review, publish, score, and analyse assessments for courses you own.</p>
        </div>
        <a href="{{ route('teacher.assessments.create') }}" class="sg-btn-primary">Create Assessment</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        @forelse ($assessments as $assessment)
            <article class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200 p-5 last:border-b-0">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="font-black text-slate-950">{{ $assessment->title }}</h2>
                        <span class="rounded-full border border-slate-300 px-2.5 py-1 text-xs font-bold capitalize">{{ $assessment->status }}</span>
                    </div>
                    <p class="mt-1 text-sm text-slate-600">{{ $assessment->course->title }} · {{ $assessment->questions_count }} questions · {{ $assessment->total_marks }} marks</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $assessment->attempts_count }} learner attempt(s)</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if ($assessment->attempts_count > 0)
                        <a href="{{ route('teacher.assessments.analytics', $assessment) }}" class="sg-btn-secondary">Analytics</a>
                        <a href="{{ route('teacher.assessments.attempts.index', $assessment) }}" class="sg-btn-secondary">Review Attempts</a>
                    @endif
                    <a href="{{ route('teacher.assessments.preview', $assessment) }}" class="sg-btn-secondary">Preview</a>
                    <a href="{{ route('teacher.assessments.edit', $assessment) }}" class="sg-btn-primary">Manage</a>
                </div>
            </article>
        @empty
            <div class="p-10 text-center">
                <p class="font-black">No assessments yet.</p>
                <p class="mt-2 text-sm text-slate-500">Create your first draft assessment for one of your courses.</p>
            </div>
        @endforelse
    </div>

    {{ $assessments->links() }}
</div>
@endsection
