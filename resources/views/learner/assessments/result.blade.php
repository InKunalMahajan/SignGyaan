@extends('learner.layout')
@section('title', $assessment->title.' Result')
@section('header_label', 'Assessment Result')
@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    @if (session('status'))
        <div class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-bold" role="status">{{ session('status') }}</div>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">{{ $assessment->course->title }}</p>
        <h1 class="mt-2 text-3xl font-black">{{ $assessment->title }}</h1>
        <div class="mt-5 grid gap-3 sm:grid-cols-4">
            <div class="rounded-xl bg-slate-100 p-4"><p class="text-xs font-bold text-slate-500">Status</p><p class="mt-1 font-black">{{ str($attempt->status)->replace('_',' ')->title() }}</p></div>
            <div class="rounded-xl bg-slate-100 p-4"><p class="text-xs font-bold text-slate-500">Score</p><p class="mt-1 font-black">{{ $attempt->earned_marks ?? 0 }} / {{ $attempt->total_marks_snapshot }}</p></div>
            <div class="rounded-xl bg-slate-100 p-4"><p class="text-xs font-bold text-slate-500">Percentage</p><p class="mt-1 font-black">{{ $attempt->percentage ?? 0 }}%</p></div>
            <div class="rounded-xl bg-slate-100 p-4"><p class="text-xs font-bold text-slate-500">Result</p><p class="mt-1 font-black">@if($attempt->status === 'pending_review') Pending Review @elseif($attempt->passing_marks_snapshot === null) Completed @else {{ $attempt->passed ? 'Pass' : 'Needs Improvement' }} @endif</p></div>
        </div>
        @if ($attempt->status === 'pending_review')
            <p class="mt-4 text-sm font-semibold text-slate-600">Objective questions have been scored. Your final result will update after the teacher reviews the short-answer questions.</p>
        @endif
    </section>

    <section class="space-y-4">
        @foreach ($answers as $answer)
            @php $snapshot = $answer->question_snapshot ?? []; @endphp
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Question {{ $loop->iteration }} · {{ str($snapshot['type'] ?? 'question')->replace('_',' ')->title() }}</p>
                        <h2 class="mt-2 font-black">{{ $snapshot['prompt'] ?? $answer->question?->prompt }}</h2>
                    </div>
                    <span class="rounded-full border border-slate-300 px-3 py-1 text-xs font-bold">
                        @if ($answer->requires_review) Pending review @else {{ $answer->awarded_marks ?? 0 }} / {{ $snapshot['marks'] ?? $answer->question?->marks ?? 0 }} @endif
                    </span>
                </div>
                @if ($answer->feedback)
                    <div class="mt-4 rounded-xl bg-slate-100 p-4 text-sm"><span class="font-black">Teacher feedback:</span> {{ $answer->feedback }}</div>
                @endif
            </article>
        @endforeach
    </section>

    <a href="{{ route('learner.assessments.index') }}" class="inline-flex rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-black">Back to Assessments</a>
</div>
@endsection
