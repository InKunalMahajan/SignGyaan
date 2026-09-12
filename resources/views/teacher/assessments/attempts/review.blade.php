@extends('teacher.layout')
@section('title', 'Review Assessment Attempt')
@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Manual Review</p>
        <h1 class="mt-2 text-3xl font-black">{{ $assessment->title }}</h1>
        <p class="mt-2 text-sm text-slate-600">Learner: <span class="font-black">{{ $attempt->learner->name }}</span> · Attempt #{{ $attempt->attempt_number }}</p>
    </div>

    <section class="grid gap-3 sm:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4"><p class="text-xs font-bold text-slate-500">Status</p><p class="mt-1 font-black">{{ str($attempt->status)->replace('_',' ')->title() }}</p></div>
        <div class="rounded-xl border border-slate-200 bg-white p-4"><p class="text-xs font-bold text-slate-500">Score</p><p class="mt-1 font-black">{{ $attempt->earned_marks ?? 0 }} / {{ $attempt->total_marks_snapshot }}</p></div>
        <div class="rounded-xl border border-slate-200 bg-white p-4"><p class="text-xs font-bold text-slate-500">Percentage</p><p class="mt-1 font-black">{{ $attempt->percentage ?? 0 }}%</p></div>
        <div class="rounded-xl border border-slate-200 bg-white p-4"><p class="text-xs font-bold text-slate-500">Result</p><p class="mt-1 font-black">@if($attempt->status === 'pending_review') Pending Review @elseif($attempt->passing_marks_snapshot === null) Completed @else {{ $attempt->passed ? 'Pass' : 'Needs Improvement' }} @endif</p></div>
    </section>

    <form method="POST" action="{{ route('teacher.assessments.attempts.update', $attempt) }}" class="space-y-4">
        @csrf
        @method('PATCH')
        @foreach ($answers as $answer)
            @php
                $snapshot = $answer->question_snapshot ?? [];
                $type = $snapshot['type'] ?? $answer->question?->type;
                $maxMarks = $snapshot['marks'] ?? $answer->question?->marks ?? 0;
                $response = $answer->response ?? [];
            @endphp
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Question {{ $loop->iteration }} · {{ str($type)->replace('_',' ')->title() }}</p>
                        <h2 class="mt-2 text-lg font-black">{{ $snapshot['prompt'] ?? $answer->question?->prompt }}</h2>
                    </div>
                    <span class="rounded-full border border-slate-300 px-3 py-1 text-xs font-bold">{{ $answer->awarded_marks ?? 0 }} / {{ $maxMarks }}</span>
                </div>

                <div class="mt-4 rounded-xl bg-slate-100 p-4 text-sm">
                    <p class="font-black">Learner answer</p>
                    <pre class="mt-2 whitespace-pre-wrap font-sans text-slate-700">{{ is_array($response) ? json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $response }}</pre>
                </div>

                @if ($type === 'short_answer')
                    <div class="mt-4 grid gap-4 sm:grid-cols-[180px_1fr]">
                        <label class="text-sm font-black">Marks
                            <input type="number" step="0.01" min="0" max="{{ $maxMarks }}" name="scores[{{ $answer->id }}]" value="{{ old('scores.'.$answer->id, $answer->awarded_marks) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2" required>
                        </label>
                        <label class="text-sm font-black">Feedback
                            <textarea name="feedback[{{ $answer->id }}]" class="mt-2 min-h-24 w-full rounded-xl border border-slate-300 px-3 py-2" placeholder="Optional feedback">{{ old('feedback.'.$answer->id, $answer->feedback) }}</textarea>
                        </label>
                    </div>
                @else
                    <p class="mt-4 text-sm font-bold {{ $answer->is_correct ? 'text-slate-950' : 'text-slate-500' }}">Auto-scored: {{ $answer->is_correct ? 'Correct' : 'Incorrect' }}</p>
                @endif
            </article>
        @endforeach

        <div class="flex flex-wrap justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-4">
            <a href="{{ route('teacher.assessments.attempts.index', $assessment) }}" class="rounded-xl border border-slate-300 px-4 py-3 text-sm font-black">Back to Attempts</a>
            <button class="rounded-xl bg-slate-950 px-4 py-3 text-sm font-black text-white" type="submit">Save Review & Recalculate</button>
        </div>
    </form>
</div>
@endsection
