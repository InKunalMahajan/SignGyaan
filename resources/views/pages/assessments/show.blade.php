@extends('layouts.app')

@php
    $practice = $assessment->practiceResource;
    $lesson = $practice->lesson;
    $unit = $lesson->unit;
    $course = $unit->course;
    $subject = $course->subject;
    $lessonUrl = route('courses.show', [
        'subject' => $subject->slug,
        'course' => $course->slug,
        'lesson' => 'lesson-'.$lesson->id,
    ]);
    $canStartAnotherAttempt = $assessment->max_attempts === null || $attemptsUsed < $assessment->max_attempts;
@endphp

@section('title', $practice->title . ' - SignGyaan')
@section('description', $practice->short_description ?: 'Start this SignGyaan learner assessment.')

@section('content')
    <section class="border-b border-sign-border bg-sign-soft py-8 sm:py-12 lg:py-16">
        <x-container>
            <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-sign-muted sm:text-sm" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="transition hover:text-sign-primary">Home</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('subjects.show', $subject->slug) }}" class="transition hover:text-sign-primary">{{ $subject->name }}</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('courses.show', ['subject' => $subject->slug, 'course' => $course->slug]) }}" class="transition hover:text-sign-primary">{{ $course->title }}</a>
                <span aria-hidden="true">/</span>
                <a href="{{ $lessonUrl }}" class="transition hover:text-sign-primary">{{ $lesson->title }}</a>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-sign-primary">{{ $practice->title }}</span>
            </nav>

            <div class="mt-7 grid gap-7 xl:grid-cols-[minmax(0,1fr)_21rem] xl:items-start xl:gap-10">
                <div class="max-w-3xl">
                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                        <span class="rounded-full bg-sign-light px-3 py-1.5 text-sign-primary">{{ ucfirst($practice->resource_type) }}</span>
                        <span class="rounded-full bg-white px-3 py-1.5 text-sign-primary ring-1 ring-sign-border">Pass {{ $assessment->passing_percentage }}%</span>
                        @if ($assessment->time_limit_minutes)
                            <span class="rounded-full bg-white px-3 py-1.5 text-sign-muted ring-1 ring-sign-border">{{ $assessment->time_limit_minutes }} min</span>
                        @endif
                    </div>

                    <p class="mt-5 text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Learner assessment</p>
                    <h1 class="mt-2 font-heading text-3xl font-semibold leading-tight tracking-tight text-sign-primary sm:text-5xl">{{ $practice->title }}</h1>

                    @if ($practice->short_description)
                        <p class="mt-4 max-w-2xl text-sm leading-7 text-sign-muted sm:text-base sm:leading-8">{{ $practice->short_description }}</p>
                    @endif

                    @if ($practice->instructions)
                        <div class="mt-6 rounded-2xl border border-sign-border bg-white p-5 sm:p-6">
                            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Instructions</p>
                            <div class="mt-3 whitespace-pre-line text-sm leading-7 text-sign-muted">{{ $practice->instructions }}</div>
                        </div>
                    @endif
                </div>

                <aside class="rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl sm:p-6" aria-label="Assessment summary">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Assessment summary</p>
                    <div class="mt-5 space-y-3 text-sm">
                        <div class="flex items-center justify-between gap-4 border-b border-sign-border pb-3">
                            <span class="text-sign-muted">Questions</span>
                            <span class="font-semibold text-sign-primary">{{ $publishedQuestionsCount }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 border-b border-sign-border pb-3">
                            <span class="text-sign-muted">Pass mark</span>
                            <span class="font-semibold text-sign-primary">{{ $assessment->passing_percentage }}%</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 border-b border-sign-border pb-3">
                            <span class="text-sign-muted">Time limit</span>
                            <span class="font-semibold text-sign-primary">{{ $assessment->time_limit_minutes ? $assessment->time_limit_minutes.' min' : 'No limit' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-sign-muted">Attempts</span>
                            <span class="font-semibold text-sign-primary">{{ $assessment->max_attempts ?: 'Unlimited' }}</span>
                        </div>
                    </div>

                    @auth
                        <div class="mt-5 rounded-xl bg-sign-soft p-4 text-xs leading-5 text-sign-muted">
                            You have used <strong class="text-sign-primary">{{ $attemptsUsed }}</strong>
                            {{ $attemptsUsed === 1 ? 'attempt' : 'attempts' }}.
                            @if ($attemptsRemaining !== null)
                                {{ $attemptsRemaining }} remaining.
                            @endif
                        </div>
                    @endauth
                </aside>
            </div>
        </x-container>
    </section>

    <section class="bg-white py-10 sm:py-14 lg:py-18">
        <x-container>
            <div class="mx-auto max-w-4xl">
                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4" role="alert" aria-live="polite" data-error-summary>
                        <p class="text-sm font-semibold text-red-800">This assessment cannot be started right now.</p>
                        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="rounded-3xl border border-sign-border bg-sign-soft p-6 sm:p-8">
                    <div class="grid gap-6 md:grid-cols-3">
                        <div class="rounded-2xl bg-white p-5">
                            <span class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">1. Read</span>
                            <h2 class="mt-2 font-heading text-lg font-semibold text-sign-primary">Understand each question</h2>
                            <p class="mt-2 text-sm leading-6 text-sign-muted">Move through the assessment at your own pace unless a time limit is shown.</p>
                        </div>
                        <div class="rounded-2xl bg-white p-5">
                            <span class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">2. Answer</span>
                            <h2 class="mt-2 font-heading text-lg font-semibold text-sign-primary">Choose or type answers</h2>
                            <p class="mt-2 text-sm leading-6 text-sign-muted">Questions may use single choice, multiple choice, true/false or typed answers.</p>
                        </div>
                        <div class="rounded-2xl bg-white p-5">
                            <span class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">3. Review</span>
                            <h2 class="mt-2 font-heading text-lg font-semibold text-sign-primary">Check before finishing</h2>
                            <p class="mt-2 text-sm leading-6 text-sign-muted">Your in-progress answers can be saved while you work through the assessment.</p>
                        </div>
                    </div>

                    <div class="mt-7 flex flex-col gap-3 sm:flex-row sm:items-center">
                        @guest
                            <a href="{{ route('login') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl bg-sign-primary px-6 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Sign in to start</a>
                            <a href="{{ $lessonUrl }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-sign-border bg-white px-6 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Back to lesson</a>
                        @else
                            @if ($activeAttempt)
                                <a href="{{ route('assessment-attempts.show', [$assessment, $activeAttempt]) }}" class="inline-flex min-h-12 items-center justify-center rounded-xl bg-sign-primary px-6 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Continue Attempt {{ $activeAttempt->attempt_number }}</a>
                            @elseif ($publishedQuestionsCount > 0 && $canStartAnotherAttempt)
                                <form method="POST" action="{{ route('assessments.start', $assessment) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-sign-primary px-6 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark sm:w-auto">Start Assessment</button>
                                </form>
                            @elseif ($publishedQuestionsCount === 0)
                                <span class="inline-flex min-h-12 items-center justify-center rounded-xl border border-sign-border bg-gray-100 px-6 py-3 text-sm font-semibold text-sign-muted">Questions are being prepared</span>
                            @else
                                <span class="inline-flex min-h-12 items-center justify-center rounded-xl border border-sign-border bg-gray-100 px-6 py-3 text-sm font-semibold text-sign-muted">Attempt limit reached</span>
                            @endif

                            <a href="{{ $lessonUrl }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-sign-border bg-white px-6 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Back to lesson</a>
                        @endguest
                    </div>
                </div>
            </div>
        </x-container>
    </section>
@endsection
