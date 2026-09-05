@extends('layouts.app')

@php
    $practice = $assessment->practiceResource;
    $lesson = $practice->lesson;
    $course = $lesson->unit->course;
    $subject = $course->subject;
    $expiresAtMs = $attempt->expires_at?->getTimestampMs();
@endphp

@section('title', $practice->title . ' - Attempt ' . $attempt->attempt_number . ' - SignGyaan')
@section('description', 'Complete this SignGyaan learner assessment.')

@section('content')
    <div
        x-data="{
            current: 0,
            total: {{ $questions->count() }},
            remainingSeconds: {{ $expiresAtMs ? 'Math.max(0, Math.floor(('.$expiresAtMs.' - Date.now()) / 1000))' : 'null' }},
            timer: null,
            init() {
                if (this.remainingSeconds !== null) {
                    this.timer = setInterval(() => {
                        this.remainingSeconds = Math.max(0, this.remainingSeconds - 1);
                        if (this.remainingSeconds === 0) {
                            clearInterval(this.timer);
                            window.location.reload();
                        }
                    }, 1000);
                }
            },
            goTo(index) {
                this.current = Math.max(0, Math.min(index, this.total - 1));
                this.$nextTick(() => document.getElementById('assessment-question-' + this.current)?.focus());
            },
            timeLabel() {
                if (this.remainingSeconds === null) return '';
                const hours = Math.floor(this.remainingSeconds / 3600);
                const minutes = Math.floor((this.remainingSeconds % 3600) / 60);
                const seconds = this.remainingSeconds % 60;
                return hours > 0
                    ? `${hours}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`
                    : `${minutes}:${String(seconds).padStart(2, '0')}`;
            }
        }"
        data-assessment-player
        class="bg-sign-soft"
    >
        <section class="border-b border-sign-border bg-white py-5 sm:py-7">
            <x-container>
                <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-sign-muted sm:text-sm" aria-label="Breadcrumb">
                    <a href="{{ $lessonUrl }}" class="transition hover:text-sign-primary">{{ $lesson->title }}</a>
                    <span aria-hidden="true">/</span>
                    <a href="{{ route('assessments.show', $assessment) }}" class="transition hover:text-sign-primary">{{ $practice->title }}</a>
                    <span aria-hidden="true">/</span>
                    <span class="font-semibold text-sign-primary">Attempt {{ $attempt->attempt_number }}</span>
                </nav>

                <div class="mt-5 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">{{ $subject->name }} · {{ $course->title }}</p>
                        <h1 class="mt-2 font-heading text-2xl font-semibold leading-tight text-sign-primary sm:text-3xl">{{ $practice->title }}</h1>
                        <p class="mt-2 text-sm text-sign-muted">Attempt {{ $attempt->attempt_number }} · {{ $questions->count() }} {{ $questions->count() === 1 ? 'question' : 'questions' }} · Pass {{ $assessment->passing_percentage }}%</p>
                    </div>

                    @if ($attempt->expires_at)
                        <div class="w-fit rounded-2xl border border-sign-border bg-sign-soft px-4 py-3" role="timer" aria-live="off">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-sign-muted">Time remaining</p>
                            <p class="mt-1 font-heading text-xl font-semibold text-sign-primary" x-text="timeLabel()"></p>
                        </div>
                    @endif
                </div>

                <div class="mt-5 h-2 overflow-hidden rounded-full bg-sign-light" role="progressbar" aria-label="Assessment question progress" aria-valuemin="1" aria-valuemax="{{ $questions->count() }}" :aria-valuenow="current + 1">
                    <div class="h-full rounded-full bg-sign-primary transition-all" :style="`width: ${((current + 1) / total) * 100}%`"></div>
                </div>
                <p class="mt-2 text-xs font-semibold text-sign-muted">Question <span x-text="current + 1"></span> of {{ $questions->count() }}</p>
            </x-container>
        </section>

        <section class="py-7 sm:py-10 lg:py-12">
            <x-container>
                <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_18rem] xl:items-start xl:gap-8">
                    <main class="min-w-0">
                        @if (session('status'))
                            <div class="mb-5 rounded-2xl border border-sign-cyan bg-sign-light px-4 py-3 text-sm font-semibold text-sign-primary" role="status" aria-live="polite">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3" role="alert" aria-live="polite" data-error-summary>
                                <p class="text-sm font-semibold text-red-800">Please check your answers before continuing.</p>
                                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form id="assessment-answer-form" method="POST" action="{{ route('assessment-attempts.save', [$assessment, $attempt]) }}">
                            @csrf

                            @foreach ($questions as $questionIndex => $question)
                                @php
                                    $savedResponse = $savedAnswers->get($question->id)?->response ?? [];
                                    $currentResponse = old('answers.'.$question->id, $savedResponse);
                                    $currentResponse = is_array($currentResponse) ? $currentResponse : [];
                                    $savedOptionIds = collect($currentResponse['option_ids'] ?? [])->map(fn ($id) => (int) $id);
                                    $savedText = (string) ($currentResponse['text'] ?? '');
                                    $typeLabel = match ($question->question_type) {
                                        'single-choice' => 'Single Choice',
                                        'multiple-choice' => 'Multiple Choice',
                                        'true-false' => 'True / False',
                                        'fill-blank' => 'Fill in the Blank',
                                        default => 'Question',
                                    };
                                @endphp

                                <article
                                    x-show="current === {{ $questionIndex }}"
                                    x-cloak
                                    id="assessment-question-{{ $questionIndex }}"
                                    tabindex="-1"
                                    class="rounded-2xl border border-sign-border bg-white p-5 shadow-sm outline-none sm:rounded-3xl sm:p-8"
                                    aria-labelledby="assessment-question-heading-{{ $question->id }}"
                                >
                                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                                        <span class="rounded-full bg-sign-light px-3 py-1.5 text-sign-primary">{{ $typeLabel }}</span>
                                        <span class="rounded-full bg-sign-soft px-3 py-1.5 text-sign-muted">{{ number_format((float) $question->points, 2) }} {{ (float) $question->points === 1.0 ? 'point' : 'points' }}</span>
                                        @if ($question->is_required)
                                            <span class="rounded-full bg-sign-soft px-3 py-1.5 text-sign-cyan-dark">Required</span>
                                        @else
                                            <span class="rounded-full bg-sign-soft px-3 py-1.5 text-sign-muted">Optional</span>
                                        @endif
                                    </div>

                                    <p class="mt-5 text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Question {{ $questionIndex + 1 }}</p>
                                    <h2 id="assessment-question-heading-{{ $question->id }}" class="mt-2 whitespace-pre-line font-heading text-xl font-semibold leading-8 text-sign-primary sm:text-2xl">{{ $question->prompt }}</h2>

                                    @if (in_array($question->question_type, ['single-choice', 'true-false'], true))
                                        <fieldset class="mt-6 space-y-3">
                                            <legend class="sr-only">Choose one answer</legend>
                                            @foreach ($question->options as $option)
                                                <label class="flex min-h-14 cursor-pointer items-start gap-3 rounded-2xl border border-sign-border bg-sign-soft p-4 transition hover:border-sign-cyan hover:bg-sign-light/50 has-[:checked]:border-sign-primary has-[:checked]:bg-sign-light">
                                                    <input
                                                        type="radio"
                                                        name="answers[{{ $question->id }}][option_ids][]"
                                                        value="{{ $option->id }}"
                                                        @checked($savedOptionIds->contains((int) $option->id))
                                                        class="mt-0.5 h-5 w-5 shrink-0 border-sign-border accent-sign-primary"
                                                    >
                                                    <span class="text-sm font-semibold leading-6 text-sign-text">{{ $option->option_text }}</span>
                                                </label>
                                            @endforeach
                                        </fieldset>
                                    @elseif ($question->question_type === 'multiple-choice')
                                        <fieldset class="mt-6 space-y-3">
                                            <legend class="mb-3 text-sm font-medium text-sign-muted">Select all answers that apply.</legend>
                                            @foreach ($question->options as $option)
                                                <label class="flex min-h-14 cursor-pointer items-start gap-3 rounded-2xl border border-sign-border bg-sign-soft p-4 transition hover:border-sign-cyan hover:bg-sign-light/50 has-[:checked]:border-sign-primary has-[:checked]:bg-sign-light">
                                                    <input
                                                        type="checkbox"
                                                        name="answers[{{ $question->id }}][option_ids][]"
                                                        value="{{ $option->id }}"
                                                        @checked($savedOptionIds->contains((int) $option->id))
                                                        class="mt-0.5 h-5 w-5 shrink-0 rounded border-sign-border accent-sign-primary"
                                                    >
                                                    <span class="text-sm font-semibold leading-6 text-sign-text">{{ $option->option_text }}</span>
                                                </label>
                                            @endforeach
                                        </fieldset>
                                    @elseif ($question->question_type === 'fill-blank')
                                        <div class="mt-6">
                                            <label for="assessment-answer-{{ $question->id }}" class="mb-2 block text-sm font-semibold text-sign-primary">Your answer</label>
                                            <input
                                                id="assessment-answer-{{ $question->id }}"
                                                type="text"
                                                name="answers[{{ $question->id }}][text]"
                                                value="{{ $savedText }}"
                                                maxlength="5000"
                                                autocomplete="off"
                                                class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light"
                                            >
                                        </div>
                                    @endif

                                    <div class="mt-8 flex flex-col-reverse gap-3 border-t border-sign-border pt-5 sm:flex-row sm:items-center sm:justify-between">
                                        <button
                                            type="button"
                                            @click="goTo(current - 1)"
                                            x-show="current > 0"
                                            class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-5 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft"
                                        >
                                            ← Previous
                                        </button>
                                        <span x-show="current === 0" class="hidden sm:block"></span>

                                        <button
                                            type="button"
                                            @click="goTo(current + 1)"
                                            x-show="current < total - 1"
                                            class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark"
                                        >
                                            Next question →
                                        </button>

                                        <div x-show="current === total - 1" class="flex flex-col gap-2 sm:flex-row">
                                            <button
                                                type="submit"
                                                class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-5 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft"
                                            >
                                                Save Progress
                                            </button>
                                            <button
                                                type="submit"
                                                formaction="{{ route('assessment-attempts.submit', [$assessment, $attempt]) }}"
                                                onclick="return confirm('Submit this assessment now? You will not be able to change answers in this attempt after submission.');"
                                                class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark"
                                            >
                                                Submit Assessment
                                            </button>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </form>
                    </main>

                    <aside class="space-y-4 xl:sticky xl:top-28" aria-label="Assessment navigation">
                        <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Questions</p>
                            <div class="mt-4 grid grid-cols-5 gap-2 sm:grid-cols-8 xl:grid-cols-4">
                                @foreach ($questions as $questionIndex => $question)
                                    @php
                                        $hasStoredOrOldAnswer = $savedAnswers->has($question->id) || old('answers.'.$question->id) !== null;
                                    @endphp
                                    <button
                                        type="button"
                                        @click="goTo({{ $questionIndex }})"
                                        @class([
                                            'flex h-10 w-full items-center justify-center rounded-xl border text-xs font-semibold transition',
                                            'border-sign-cyan bg-sign-light text-sign-primary' => $hasStoredOrOldAnswer,
                                            'border-sign-border bg-sign-soft text-sign-muted' => ! $hasStoredOrOldAnswer,
                                        ])
                                        :class="current === {{ $questionIndex }} ? 'ring-2 ring-sign-primary ring-offset-2' : ''"
                                        aria-label="Go to question {{ $questionIndex + 1 }}"
                                    >
                                        {{ $questionIndex + 1 }}
                                    </button>
                                @endforeach
                            </div>
                            <div class="mt-4 flex flex-wrap gap-3 text-[11px] text-sign-muted">
                                <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-sign-light ring-1 ring-sign-cyan" aria-hidden="true"></span> Saved / entered</span>
                                <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-sign-soft ring-1 ring-sign-border" aria-hidden="true"></span> No answer</span>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Attempt</p>
                            <dl class="mt-4 space-y-3 text-sm">
                                <div class="flex justify-between gap-3"><dt class="text-sign-muted">Number</dt><dd class="font-semibold text-sign-primary">{{ $attempt->attempt_number }}</dd></div>
                                <div class="flex justify-between gap-3"><dt class="text-sign-muted">Started</dt><dd class="text-right font-semibold text-sign-primary">{{ $attempt->started_at?->format('d M, H:i') }}</dd></div>
                                <div class="flex justify-between gap-3"><dt class="text-sign-muted">Status</dt><dd class="font-semibold text-sign-primary">In progress</dd></div>
                            </dl>
                            <div class="mt-4 space-y-2 rounded-xl bg-sign-soft p-3 text-xs leading-5 text-sign-muted">
                                <p><strong class="text-sign-primary">Save Progress</strong> stores answers without grading them.</p>
                                <p><strong class="text-sign-primary">Submit Assessment</strong> validates required questions, grades the attempt and closes it.</p>
                            </div>
                        </div>

                        <a href="{{ $lessonUrl }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Back to lesson</a>
                    </aside>
                </div>
            </x-container>
        </section>
    </div>
@endsection
