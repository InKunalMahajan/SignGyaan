@extends('learner.layout')
@section('title', $assessment->title)
@section('header_label', 'Assessment Attempt')
@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">{{ $assessment->course->title }}</p>
        <h1 class="mt-2 text-3xl font-black">{{ $assessment->title }}</h1>
        @if ($assessment->instructions)<p class="mt-3 whitespace-pre-line text-sm text-slate-600">{{ $assessment->instructions }}</p>@endif
        <div class="mt-4 flex flex-wrap gap-3 text-sm font-bold text-slate-600">
            <span>{{ $questions->count() }} questions</span><span>•</span><span>{{ $attempt->total_marks_snapshot }} marks</span>
            @if ($assessment->duration_minutes)<span>•</span><span>{{ $assessment->duration_minutes }} minutes</span>@endif
        </div>
    </section>

    <form method="POST" action="{{ route('learner.assessments.attempts.save', $attempt) }}" class="space-y-5">
        @csrf
        @method('PUT')
        @foreach ($questions as $question)
            @php $saved = $answersByQuestion->get($question->id)?->response; @endphp
            <fieldset class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <legend class="px-1 text-lg font-black">{{ $loop->iteration }}. {{ $question->prompt }}</legend>
                <p class="mt-2 text-xs font-bold uppercase tracking-[0.12em] text-slate-500">{{ str($question->type)->replace('_',' ')->title() }} · {{ $question->marks }} marks</p>

                <div class="mt-4 space-y-3">
                    @if ($question->type === 'single_choice')
                        @foreach (($question->config['options'] ?? []) as $option)
                            <label class="flex items-center gap-3 rounded-xl border border-slate-200 p-3"><input type="radio" name="responses[{{ $question->id }}]" value="{{ $option }}" @checked(($saved['value'] ?? null) === $option)> <span>{{ $option }}</span></label>
                        @endforeach
                    @elseif ($question->type === 'multi_select')
                        @foreach (($question->config['options'] ?? []) as $option)
                            <label class="flex items-center gap-3 rounded-xl border border-slate-200 p-3"><input type="checkbox" name="responses[{{ $question->id }}][]" value="{{ $option }}" @checked(in_array($option, $saved['values'] ?? [], true))> <span>{{ $option }}</span></label>
                        @endforeach
                    @elseif ($question->type === 'true_false')
                        @foreach (['True','False'] as $option)
                            <label class="flex items-center gap-3 rounded-xl border border-slate-200 p-3"><input type="radio" name="responses[{{ $question->id }}]" value="{{ $option }}" @checked(($saved['value'] ?? null) === $option)> <span>{{ $option }}</span></label>
                        @endforeach
                    @elseif ($question->type === 'matching')
                        @foreach (($question->config['pairs'] ?? []) as $left => $right)
                            <div class="grid gap-2 sm:grid-cols-2 sm:items-center">
                                <div class="rounded-xl bg-slate-100 px-3 py-2 font-semibold">{{ $left }}</div>
                                <input class="rounded-xl border border-slate-300 px-3 py-2" name="responses[{{ $question->id }}][{{ $left }}]" value="{{ $saved['pairs'][$left] ?? '' }}" aria-label="Match for {{ $left }}">
                            </div>
                        @endforeach
                    @elseif ($question->type === 'short_answer')
                        <textarea class="min-h-32 w-full rounded-xl border border-slate-300 px-3 py-3" name="responses[{{ $question->id }}]" placeholder="Type your answer">{{ $saved['value'] ?? '' }}</textarea>
                    @else
                        <input class="w-full rounded-xl border border-slate-300 px-3 py-3" name="responses[{{ $question->id }}]" value="{{ $saved['value'] ?? '' }}" placeholder="Type your answer">
                    @endif
                </div>
            </fieldset>
        @endforeach

        <div class="flex flex-wrap justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-4">
            <a href="{{ route('learner.assessments.index') }}" class="rounded-xl border border-slate-300 px-4 py-3 text-sm font-black">Save later / Exit</a>
            <div class="flex gap-3">
                <button class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-black" type="submit">Save Answers</button>
                <a href="{{ route('learner.assessments.attempts.review', $attempt) }}" class="rounded-xl bg-slate-950 px-4 py-3 text-sm font-black text-white">Review & Submit</a>
            </div>
        </div>
    </form>
</div>
@endsection
