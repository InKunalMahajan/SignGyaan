@php
    $assessmentAttempts = $assessmentAttempts ?? collect();
    $assessmentSummary = $assessmentSummary ?? [
        'total_attempts' => 0,
        'submitted' => 0,
        'passed' => 0,
        'in_progress' => 0,
        'best_score' => null,
        'average_score' => null,
    ];
    $assessmentLimit = $assessmentLimit ?? null;
    $visibleAttempts = $assessmentLimit ? $assessmentAttempts->take($assessmentLimit) : $assessmentAttempts;
@endphp

<section aria-labelledby="assessment-progress-heading">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Practice & assessment</p>
            <h2 id="assessment-progress-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Assessment progress</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-sign-muted">Continue open attempts and review scores from completed quizzes and exercises.</p>
        </div>
        @if (($assessmentSummary['submitted'] ?? 0) > 0)
            <div class="text-sm text-sign-muted">
                Best score:
                <strong class="text-sign-primary">{{ number_format((float) $assessmentSummary['best_score'], 2) }}%</strong>
            </div>
        @endif
    </div>

    <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4" aria-label="Assessment summary">
        <div class="rounded-2xl border border-sign-border bg-white p-4">
            <p class="text-xs font-semibold text-sign-muted">Attempts</p>
            <p class="mt-2 font-heading text-2xl font-semibold text-sign-primary">{{ $assessmentSummary['total_attempts'] ?? 0 }}</p>
        </div>
        <div class="rounded-2xl border border-sign-border bg-white p-4">
            <p class="text-xs font-semibold text-sign-muted">In progress</p>
            <p class="mt-2 font-heading text-2xl font-semibold text-sign-primary">{{ $assessmentSummary['in_progress'] ?? 0 }}</p>
        </div>
        <div class="rounded-2xl border border-sign-border bg-white p-4">
            <p class="text-xs font-semibold text-sign-muted">Submitted</p>
            <p class="mt-2 font-heading text-2xl font-semibold text-sign-primary">{{ $assessmentSummary['submitted'] ?? 0 }}</p>
        </div>
        <div class="rounded-2xl border border-sign-border bg-white p-4">
            <p class="text-xs font-semibold text-sign-muted">Passed</p>
            <p class="mt-2 font-heading text-2xl font-semibold text-sign-primary">{{ $assessmentSummary['passed'] ?? 0 }}</p>
        </div>
    </div>

    @if ($visibleAttempts->isNotEmpty())
        <div class="mt-5 space-y-4">
            @foreach ($visibleAttempts as $attempt)
                @php
                    $assessment = $attempt->assessment;
                    $practice = $assessment->practiceResource;
                    $lesson = $practice->lesson;
                    $course = $lesson->unit->course;
                    $subject = $course->subject;
                    $isSubmitted = $attempt->status === 'submitted';
                    $isActive = $attempt->status === 'in-progress';
                    $statusLabel = match ($attempt->status) {
                        'submitted' => $attempt->passed ? 'Passed' : 'Submitted',
                        'in-progress' => 'In progress',
                        'expired' => 'Expired',
                        default => ucfirst(str_replace('-', ' ', $attempt->status)),
                    };
                @endphp

                <article class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-6">
                    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                                <span class="rounded-full bg-sign-soft px-3 py-1 text-sign-primary">{{ $subject->name }}</span>
                                <span class="rounded-full bg-sign-light px-3 py-1 text-sign-cyan-dark">{{ $statusLabel }}</span>
                                <span class="text-sign-muted">Attempt {{ $attempt->attempt_number }}</span>
                            </div>
                            <h3 class="mt-3 font-heading text-xl font-semibold text-sign-primary">{{ $practice->title }}</h3>
                            <p class="mt-1 text-sm text-sign-muted">{{ $course->title }} · {{ $lesson->title }}</p>

                            @if ($isSubmitted)
                                <div class="mt-4 flex flex-wrap items-center gap-4 text-sm">
                                    <span class="font-semibold text-sign-primary">{{ number_format((float) $attempt->percentage, 2) }}%</span>
                                    <span class="text-sign-muted">{{ number_format((float) $attempt->score_points, 2) }} / {{ number_format((float) $attempt->max_points, 2) }} points</span>
                                    @if ($attempt->submitted_at)
                                        <span class="text-sign-muted">{{ $attempt->submitted_at->format('d M Y') }}</span>
                                    @endif
                                </div>
                            @elseif ($isActive)
                                <p class="mt-3 text-sm leading-6 text-sign-muted">Your saved answers are waiting. Continue this attempt before starting another one.</p>
                            @else
                                <p class="mt-3 text-sm leading-6 text-sign-muted">This attempt is closed and was not submitted for a score.</p>
                            @endif
                        </div>

                        <div class="shrink-0">
                            @if ($isActive)
                                <a href="{{ route('assessment-attempts.show', [$assessment, $attempt]) }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark md:w-auto">Continue Attempt</a>
                            @elseif ($isSubmitted)
                                <a href="{{ route('assessment-attempts.result', [$assessment, $attempt]) }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-sign-primary bg-white px-5 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft md:w-auto">View Result</a>
                            @else
                                <a href="{{ route('assessments.show', $assessment) }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-sign-border bg-white px-5 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft md:w-auto">Assessment Overview</a>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="mt-5 rounded-2xl border border-dashed border-sign-border bg-sign-soft p-6 sm:rounded-3xl sm:p-8">
            <h3 class="font-heading text-xl font-semibold text-sign-primary">No assessment attempts yet</h3>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-sign-muted">Open a published lesson with a quiz or exercise and start an assessment. Your attempts and scores will appear here automatically.</p>
        </div>
    @endif
</section>
