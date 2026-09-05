@extends('layouts.app')

@php
    $practice = $assessment->practiceResource;
    $lesson = $practice->lesson;
    $course = $lesson->unit->course;
    $subject = $course->subject;
    $percentage = (float) ($attempt->percentage ?? 0);
    $scorePoints = (float) ($attempt->score_points ?? 0);
    $maxPoints = (float) ($attempt->max_points ?? 0);
    $passed = (bool) $attempt->passed;
@endphp

@section('title', $practice->title . ' Result - SignGyaan')
@section('description', 'Review your SignGyaan assessment result and learner feedback.')

@section('content')
    <section class="border-b border-sign-border bg-sign-soft py-8 sm:py-12 lg:py-16">
        <x-container>
            <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-sign-muted sm:text-sm" aria-label="Breadcrumb">
                <a href="{{ $lessonUrl }}" class="transition hover:text-sign-primary">{{ $lesson->title }}</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('assessments.show', $assessment) }}" class="transition hover:text-sign-primary">{{ $practice->title }}</a>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-sign-primary">Attempt {{ $attempt->attempt_number }} result</span>
            </nav>

            <div class="mt-7 grid gap-7 xl:grid-cols-[minmax(0,1fr)_21rem] xl:items-start xl:gap-10">
                <div class="max-w-3xl">
                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                        <span @class([
                            'rounded-full px-3 py-1.5',
                            'bg-sign-light text-sign-primary' => $passed,
                            'bg-white text-sign-muted ring-1 ring-sign-border' => ! $passed,
                        ])>{{ $passed ? 'Passed' : 'Not passed yet' }}</span>
                        <span class="rounded-full bg-white px-3 py-1.5 text-sign-primary ring-1 ring-sign-border">Attempt {{ $attempt->attempt_number }}</span>
                        <span class="rounded-full bg-white px-3 py-1.5 text-sign-muted ring-1 ring-sign-border">Pass mark {{ $assessment->passing_percentage }}%</span>
                    </div>

                    <p class="mt-5 text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Assessment result</p>
                    <h1 class="mt-2 font-heading text-3xl font-semibold leading-tight tracking-tight text-sign-primary sm:text-5xl">{{ $practice->title }}</h1>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-sign-muted sm:text-base sm:leading-8">
                        {{ $passed
                            ? 'You reached the passing score for this assessment.'
                            : 'Your attempt has been scored. Review the result and try again when another attempt is available.' }}
                    </p>
                </div>

                <aside class="rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl sm:p-6" aria-label="Result summary">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Your score</p>
                    <p class="mt-3 font-heading text-5xl font-semibold text-sign-primary">{{ number_format($percentage, 2) }}%</p>
                    <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-sign-light" role="progressbar" aria-label="Assessment score" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ min(100, max(0, $percentage)) }}">
                        <div class="h-full rounded-full bg-sign-primary" style="width: {{ min(100, max(0, $percentage)) }}%"></div>
                    </div>

                    <dl class="mt-5 space-y-3 text-sm">
                        <div class="flex items-center justify-between gap-4 border-b border-sign-border pb-3">
                            <dt class="text-sign-muted">Points</dt>
                            <dd class="font-semibold text-sign-primary">{{ number_format($scorePoints, 2) }} / {{ number_format($maxPoints, 2) }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 border-b border-sign-border pb-3">
                            <dt class="text-sign-muted">Correct</dt>
                            <dd class="font-semibold text-sign-primary">{{ $correctCount }} / {{ $questionCount }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 border-b border-sign-border pb-3">
                            <dt class="text-sign-muted">Answered</dt>
                            <dd class="font-semibold text-sign-primary">{{ $answeredCount }} / {{ $questionCount }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 border-b border-sign-border pb-3">
                            <dt class="text-sign-muted">Submitted</dt>
                            <dd class="text-right font-semibold text-sign-primary">{{ $attempt->submitted_at?->format('d M Y, H:i') }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-sign-muted">Outcome</dt>
                            <dd class="font-semibold text-sign-primary">{{ $passed ? 'Passed' : 'Try again' }}</dd>
                        </div>
                    </dl>
                </aside>
            </div>
        </x-container>
    </section>

    <section class="bg-white py-10 sm:py-14 lg:py-16">
        <x-container>
            <div class="mx-auto max-w-5xl">
                @if (session('status'))
                    <div class="mb-6 rounded-2xl border border-sign-cyan bg-sign-light px-5 py-4 text-sm font-semibold text-sign-primary" role="status" aria-live="polite">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($assessment->show_feedback)
                    <div class="mb-6">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Question review</p>
                        <h2 class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Review your answers</h2>
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-sign-muted">See what you answered, the correct answer and teacher feedback for this submitted attempt.</p>
                    </div>

                    <div class="space-y-5">
                        @foreach ($questions as $questionIndex => $question)
                            @php
                                $answer = $answers->get($question->id);
                                $response = $answer?->response ?? [];
                                $selectedIds = collect($response['option_ids'] ?? [])->map(fn ($id) => (int) $id);
                                $selectedOptions = $question->options->filter(fn ($option) => $selectedIds->contains((int) $option->id));
                                $correctOptions = $question->options->where('is_correct', true);
                                $typedAnswer = (string) ($response['text'] ?? '');
                                $acceptedAnswers = collect(data_get($question->answer_key, 'accepted_answers', []))->filter()->values();
                            @endphp

                            <article class="rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl sm:p-7" aria-labelledby="result-question-{{ $question->id }}">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Question {{ $questionIndex + 1 }}</p>
                                        <h3 id="result-question-{{ $question->id }}" class="mt-2 whitespace-pre-line font-heading text-xl font-semibold leading-8 text-sign-primary">{{ $answer?->question_snapshot ?: $question->prompt }}</h3>
                                    </div>
                                    <span @class([
                                        'inline-flex w-fit shrink-0 rounded-full px-3 py-1.5 text-xs font-semibold',
                                        'bg-sign-light text-sign-primary' => $answer?->is_correct,
                                        'bg-red-50 text-red-700' => ! $answer?->is_correct,
                                    ])>{{ $answer?->is_correct ? 'Correct' : 'Needs review' }}</span>
                                </div>

                                <div class="mt-5 grid gap-4 md:grid-cols-2">
                                    <div class="rounded-2xl bg-sign-soft p-4 sm:p-5">
                                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-muted">Your answer</p>
                                        @if ($question->question_type === 'fill-blank')
                                            <p class="mt-2 text-sm font-semibold leading-6 text-sign-text">{{ $typedAnswer !== '' ? $typedAnswer : 'No answer' }}</p>
                                        @elseif ($selectedOptions->isNotEmpty())
                                            <ul class="mt-2 space-y-2 text-sm font-semibold leading-6 text-sign-text">
                                                @foreach ($selectedOptions as $option)
                                                    <li>{{ $option->option_text }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="mt-2 text-sm font-semibold text-sign-muted">No answer</p>
                                        @endif
                                    </div>

                                    <div class="rounded-2xl bg-sign-light p-4 sm:p-5">
                                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Correct answer</p>
                                        @if ($question->question_type === 'fill-blank')
                                            @if ($acceptedAnswers->isNotEmpty())
                                                <p class="mt-2 text-sm font-semibold leading-6 text-sign-primary">{{ $acceptedAnswers->join(' / ') }}</p>
                                            @else
                                                <p class="mt-2 text-sm text-sign-muted">No accepted answer is available.</p>
                                            @endif
                                        @elseif ($correctOptions->isNotEmpty())
                                            <ul class="mt-2 space-y-2 text-sm font-semibold leading-6 text-sign-primary">
                                                @foreach ($correctOptions as $option)
                                                    <li>{{ $option->option_text }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>

                                @if ($selectedOptions->whereNotNull('feedback')->isNotEmpty())
                                    <div class="mt-4 rounded-2xl border border-sign-border bg-sign-soft p-4 sm:p-5">
                                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Answer feedback</p>
                                        <div class="mt-3 space-y-3">
                                            @foreach ($selectedOptions as $option)
                                                @if ($option->feedback)
                                                    <div>
                                                        <p class="text-sm font-semibold text-sign-primary">{{ $option->option_text }}</p>
                                                        <p class="mt-1 whitespace-pre-line text-sm leading-6 text-sign-muted">{{ $option->feedback }}</p>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if ($question->explanation)
                                    <div class="mt-4 rounded-2xl border border-sign-border bg-white p-4 sm:p-5">
                                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Explanation</p>
                                        <div class="mt-2 whitespace-pre-line text-sm leading-7 text-sign-muted">{{ $question->explanation }}</div>
                                    </div>
                                @endif

                                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-sign-border pt-4 text-xs">
                                    <span class="font-semibold text-sign-muted">Points earned</span>
                                    <span class="font-semibold text-sign-primary">{{ number_format((float) ($answer?->points_awarded ?? 0), 2) }} / {{ number_format((float) $question->points, 2) }}</span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-3xl border border-sign-border bg-sign-soft p-6 text-center sm:p-10">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Result recorded</p>
                        <h2 class="mt-2 font-heading text-2xl font-semibold text-sign-primary">Detailed feedback is not available for this assessment</h2>
                        <p class="mx-auto mt-3 max-w-2xl text-sm leading-7 text-sign-muted">Your score and pass result are shown above. The teacher has disabled question-by-question answer feedback for this assessment.</p>
                    </div>
                @endif

                <div class="mt-8 flex flex-col gap-3 border-t border-sign-border pt-6 sm:flex-row sm:flex-wrap">
                    <a href="{{ route('assessments.show', $assessment) }}" class="inline-flex min-h-12 items-center justify-center rounded-xl bg-sign-primary px-6 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Assessment overview</a>
                    <a href="{{ $lessonUrl }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-sign-border bg-white px-6 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Back to lesson</a>
                    <a href="{{ route('my-learning') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-sign-border bg-white px-6 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">My Learning</a>
                </div>
            </div>
        </x-container>
    </section>
@endsection
