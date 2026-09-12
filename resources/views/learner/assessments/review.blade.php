@extends('learner.layout')
@section('title', 'Review '.$assessment->title)
@section('header_label', 'Review Assessment')
@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Before you submit</p>
        <h1 class="mt-2 text-3xl font-black">Review your answers</h1>
        <p class="mt-2 text-sm text-slate-600">After submission, you cannot change your answers.</p>
    </div>

    <div class="space-y-4">
        @foreach ($questions as $question)
            @php $response = $answersByQuestion->get($question->id)?->response; @endphp
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <h2 class="font-black">{{ $loop->iteration }}. {{ $question->prompt }}</h2>
                    <span class="text-xs font-bold text-slate-500">{{ $question->marks }} marks</span>
                </div>
                <div class="mt-3 rounded-xl bg-slate-100 p-3 text-sm text-slate-800">
                    @if (!$response)
                        <span class="font-bold text-slate-500">Not answered</span>
                    @elseif (isset($response['values']))
                        {{ implode(', ', $response['values']) }}
                    @elseif (isset($response['pairs']))
                        @foreach ($response['pairs'] as $left => $right)<div>{{ $left }} → {{ $right }}</div>@endforeach
                    @else
                        {{ $response['value'] ?? 'Not answered' }}
                    @endif
                </div>
            </section>
        @endforeach
    </div>

    <div class="flex flex-wrap justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-4">
        <a href="{{ route('learner.assessments.attempts.show', $attempt) }}" class="rounded-xl border border-slate-300 px-4 py-3 text-sm font-black">Back to Answers</a>
        <form method="POST" action="{{ route('learner.assessments.attempts.submit', $attempt) }}">
            @csrf
            <button class="rounded-xl bg-slate-950 px-5 py-3 text-sm font-black text-white" type="submit">Submit Assessment</button>
        </form>
    </div>
</div>
@endsection
