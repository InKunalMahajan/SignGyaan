@extends('layouts.admin')

@php
    $practice = $assessment->practiceResource;
    $lesson = $practice?->lesson;
    $course = $lesson?->unit?->course;
    $subject = $course?->subject;
@endphp

@section('title', 'Attempt '.$attempt->attempt_number.' - Assessment Results - SignGyaan Admin')
@section('page-title', 'Attempt Details')
@section('description', 'Review an individual learner assessment attempt and question performance.')

@section('content')
<section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
    <div class="mx-auto max-w-6xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('admin.assessment-results.index') }}" class="text-sm font-semibold text-sign-primary hover:text-sign-cyan-dark">← Assessment Results</a>
                <p class="mt-4 text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Learner attempt</p>
                <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">{{ $practice?->title ?? 'Assessment' }}</h2>
                <p class="mt-2 text-sm text-sign-muted">{{ $attempt->user?->name ?? 'Deleted learner' }} · Attempt {{ $attempt->attempt_number }}</p>
            </div>
            <a href="{{ route('admin.assessments.edit', $assessment) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-5 py-3 text-sm font-semibold text-sign-primary hover:bg-sign-soft">Manage Assessment</a>
        </div>

        <div class="mt-7 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            @foreach ([
                ['label' => 'Status', 'value' => str($attempt->status)->headline()],
                ['label' => 'Score', 'value' => $attempt->status === 'submitted' ? number_format((float) $attempt->percentage, 2).'%' : '—'],
                ['label' => 'Points', 'value' => $attempt->status === 'submitted' ? number_format((float) $attempt->score_points, 2).' / '.number_format((float) $attempt->max_points, 2) : '—'],
                ['label' => 'Correct', 'value' => $correctCount.' / '.$questionCount],
                ['label' => 'Answered', 'value' => $answeredCount.' / '.$questionCount],
            ] as $stat)
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">{{ $stat['label'] }}</p>
                    <p class="mt-2 font-heading text-2xl font-semibold text-sign-primary">{{ $stat['value'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-7 grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
            <div class="space-y-5">
                @if ($questions->isNotEmpty())
                    @foreach ($questions as $index => $question)
                        @php
                            $answer = $answers->get($question->id);
                            $response = $answer?->response ?? [];
                            $selectedIds = collect($response['option_ids'] ?? [])->map(fn ($id) => (int) $id);
                            $selectedOptions = $question->options->filter(fn ($option) => $selectedIds->contains((int) $option->id));
                            $correctOptions = $question->options->where('is_correct', true);
                            $typedAnswer = (string) ($response['text'] ?? '');
                            $acceptedAnswers = collect(data_get($question->answer_key, 'accepted_answers', []))->filter()->values();
                        @endphp
                        <article class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Question {{ $index + 1 }}</p>
                                    <h3 class="mt-2 whitespace-pre-line font-heading text-xl font-semibold text-sign-primary">{{ $answer?->question_snapshot ?: $question->prompt }}</h3>
                                </div>
                                @if ($attempt->status === 'submitted')
                                    <span @class([
                                        'inline-flex w-fit rounded-full px-3 py-1.5 text-xs font-semibold',
                                        'bg-sign-light text-sign-primary' => $answer?->is_correct,
                                        'bg-red-50 text-red-700' => ! $answer?->is_correct,
                                    ])>{{ $answer?->is_correct ? 'Correct' : 'Incorrect' }}</span>
                                @endif
                            </div>

                            <div class="mt-5 grid gap-4 md:grid-cols-2">
                                <div class="rounded-2xl bg-sign-soft p-4">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-muted">Learner answer</p>
                                    @if ($question->question_type === 'fill-blank')
                                        <p class="mt-2 text-sm font-semibold text-sign-text">{{ $typedAnswer !== '' ? $typedAnswer : 'No answer' }}</p>
                                    @elseif ($selectedOptions->isNotEmpty())
                                        <ul class="mt-2 space-y-1 text-sm font-semibold text-sign-text">@foreach($selectedOptions as $option)<li>{{ $option->option_text }}</li>@endforeach</ul>
                                    @else
                                        <p class="mt-2 text-sm text-sign-muted">No answer</p>
                                    @endif
                                </div>
                                <div class="rounded-2xl bg-sign-light p-4">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Correct answer</p>
                                    @if ($question->question_type === 'fill-blank')
                                        <p class="mt-2 text-sm font-semibold text-sign-primary">{{ $acceptedAnswers->isNotEmpty() ? $acceptedAnswers->join(' / ') : 'Not configured' }}</p>
                                    @else
                                        <ul class="mt-2 space-y-1 text-sm font-semibold text-sign-primary">@forelse($correctOptions as $option)<li>{{ $option->option_text }}</li>@empty<li>Not configured</li>@endforelse</ul>
                                    @endif
                                </div>
                            </div>

                            @if ($question->explanation)
                                <div class="mt-4 rounded-xl border border-sign-border p-4"><p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Explanation</p><p class="mt-2 whitespace-pre-line text-sm leading-6 text-sign-muted">{{ $question->explanation }}</p></div>
                            @endif

                            <div class="mt-4 flex justify-between border-t border-sign-border pt-4 text-xs"><span class="font-semibold text-sign-muted">Points</span><span class="font-semibold text-sign-primary">{{ number_format((float) ($answer?->points_awarded ?? 0), 2) }} / {{ number_format((float) $question->points, 2) }}</span></div>
                        </article>
                    @endforeach
                @else
                    <div class="rounded-2xl border border-dashed border-sign-border bg-sign-soft p-8 text-center sm:rounded-3xl"><h3 class="font-heading text-xl font-semibold text-sign-primary">No saved answers yet</h3><p class="mt-2 text-sm text-sign-muted">This attempt has no saved question responses.</p></div>
                @endif
            </div>

            <aside class="space-y-5 lg:sticky lg:top-24 lg:self-start">
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Learner</p>
                    <p class="mt-3 font-semibold text-sign-primary">{{ $attempt->user?->name ?? 'Deleted learner' }}</p>
                    <p class="mt-1 break-all text-sm text-sign-muted">{{ $attempt->user?->email }}</p>
                </div>

                <div class="rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Assessment context</p>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div><dt class="text-sign-muted">Subject</dt><dd class="mt-1 font-semibold text-sign-primary">{{ $subject?->name ?? '—' }}</dd></div>
                        <div><dt class="text-sign-muted">Course</dt><dd class="mt-1 font-semibold text-sign-primary">{{ $course?->title ?? '—' }}</dd></div>
                        <div><dt class="text-sign-muted">Lesson</dt><dd class="mt-1 font-semibold text-sign-primary">{{ $lesson?->title ?? '—' }}</dd></div>
                        <div><dt class="text-sign-muted">Pass mark</dt><dd class="mt-1 font-semibold text-sign-primary">{{ $assessment->passing_percentage }}%</dd></div>
                    </dl>
                </div>

                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Timing</p>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div><dt class="text-sign-muted">Started</dt><dd class="mt-1 font-semibold text-sign-primary">{{ $attempt->started_at?->format('d M Y, H:i') ?? '—' }}</dd></div>
                        <div><dt class="text-sign-muted">Submitted</dt><dd class="mt-1 font-semibold text-sign-primary">{{ $attempt->submitted_at?->format('d M Y, H:i') ?? '—' }}</dd></div>
                        <div><dt class="text-sign-muted">Expires</dt><dd class="mt-1 font-semibold text-sign-primary">{{ $attempt->expires_at?->format('d M Y, H:i') ?? 'No limit' }}</dd></div>
                    </dl>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
