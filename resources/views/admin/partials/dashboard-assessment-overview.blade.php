<section class="px-4 pb-8 sm:px-6 lg:px-8" aria-labelledby="assessment-overview-heading" data-assessment-overview-dashboard>
    <div class="mx-auto max-w-7xl rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl sm:p-7">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Assessment overview</p>
                <h2 id="assessment-overview-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary">Assessment Overview Dashboard</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-sign-muted">Monitor learner attempts, pass performance, participation and recent assessment activity.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.assessments.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Manage Assessments</a>
                <a href="{{ route('admin.assessment-results.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-4 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">View Results</a>
            </div>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Total attempts', 'value' => $assessmentStats['total_attempts'], 'text' => number_format($assessmentStats['attempts_7_days']).' started in the last 7 days'],
                ['label' => 'Submitted', 'value' => $assessmentStats['submitted_attempts'], 'text' => number_format($assessmentStats['in_progress_attempts']).' currently in progress'],
                ['label' => 'Pass rate', 'value' => $assessmentStats['pass_rate'].'%', 'text' => number_format($assessmentStats['passed_attempts']).' passed · '.number_format($assessmentStats['failed_attempts']).' not passed'],
                ['label' => 'Average score', 'value' => $assessmentStats['average_percentage'].'%', 'text' => number_format($assessmentStats['participating_learners']).' participating learners'],
            ] as $stat)
                <article class="rounded-2xl bg-sign-soft p-5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-muted">{{ $stat['label'] }}</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $stat['value'] }}</p>
                    <p class="mt-2 text-xs leading-5 text-sign-muted">{{ $stat['text'] }}</p>
                </article>
            @endforeach
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <section class="rounded-2xl border border-sign-border p-5" aria-labelledby="assessment-health-heading">
                <h3 id="assessment-health-heading" class="font-heading text-xl font-semibold text-sign-primary">Assessment Health</h3>
                <dl class="mt-4 space-y-3 text-sm">
                    @foreach ([
                        ['label' => 'Total assessments', 'value' => $assessmentStats['assessments']],
                        ['label' => 'Published assessments', 'value' => $assessmentStats['published_assessments']],
                        ['label' => 'Attempts in last 30 days', 'value' => $assessmentStats['attempts_30_days']],
                        ['label' => 'Expired attempts', 'value' => $assessmentStats['expired_attempts']],
                    ] as $row)
                        <div class="flex items-center justify-between gap-4 rounded-xl bg-sign-soft px-4 py-3">
                            <dt class="font-medium text-sign-muted">{{ $row['label'] }}</dt>
                            <dd class="font-semibold text-sign-primary">{{ number_format($row['value']) }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>

            <section class="rounded-2xl border border-sign-border p-5" aria-labelledby="assessment-performance-heading">
                <h3 id="assessment-performance-heading" class="font-heading text-xl font-semibold text-sign-primary">Performance Snapshot</h3>
                <div class="mt-4 space-y-4">
                    <div>
                        <div class="flex items-center justify-between gap-3 text-sm"><span class="font-medium text-sign-muted">Pass rate</span><span class="font-semibold text-sign-primary">{{ $assessmentStats['pass_rate'] }}%</span></div>
                        <div class="mt-2 h-2 overflow-hidden rounded-full bg-sign-soft" role="progressbar" aria-label="Assessment pass rate" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $assessmentStats['pass_rate'] }}"><div class="h-full rounded-full bg-sign-cyan" style="width: {{ min(100, $assessmentStats['pass_rate']) }}%"></div></div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between gap-3 text-sm"><span class="font-medium text-sign-muted">Average score</span><span class="font-semibold text-sign-primary">{{ $assessmentStats['average_percentage'] }}%</span></div>
                        <div class="mt-2 h-2 overflow-hidden rounded-full bg-sign-soft" role="progressbar" aria-label="Average assessment score" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $assessmentStats['average_percentage'] }}"><div class="h-full rounded-full bg-sign-cyan" style="width: {{ min(100, $assessmentStats['average_percentage']) }}%"></div></div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <div class="rounded-xl bg-sign-soft p-4"><p class="text-xs font-semibold text-sign-muted">Passed</p><p class="mt-2 font-heading text-2xl font-semibold text-sign-primary">{{ number_format($assessmentStats['passed_attempts']) }}</p></div>
                        <div class="rounded-xl bg-sign-soft p-4"><p class="text-xs font-semibold text-sign-muted">Not passed</p><p class="mt-2 font-heading text-2xl font-semibold text-sign-primary">{{ number_format($assessmentStats['failed_attempts']) }}</p></div>
                    </div>
                </div>
            </section>
        </div>

        <section class="mt-6 overflow-hidden rounded-2xl border border-sign-border" aria-labelledby="recent-assessment-attempts-heading">
            <div class="flex flex-col gap-2 border-b border-sign-border bg-sign-soft px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 id="recent-assessment-attempts-heading" class="font-heading text-xl font-semibold text-sign-primary">Recent Assessment Attempts</h3>
                    <p class="mt-1 text-xs text-sign-muted">Latest learner assessment activity</p>
                </div>
                <a href="{{ route('admin.assessment-results.index') }}" class="text-sm font-semibold text-sign-primary hover:text-sign-cyan-dark">View all results →</a>
            </div>

            @forelse ($recentAssessmentAttempts as $attempt)
                <div class="flex flex-col gap-3 border-b border-sign-border px-5 py-4 last:border-b-0 lg:flex-row lg:items-center lg:justify-between">
                    <div class="min-w-0">
                        <p class="font-semibold text-sign-primary">{{ $attempt->user?->name ?? 'Unknown learner' }}</p>
                        <p class="mt-1 text-sm text-sign-text">{{ $attempt->assessment?->practiceResource?->title ?? 'Assessment #'.$attempt->assessment_id }}</p>
                        <p class="mt-1 text-xs text-sign-muted">Attempt {{ $attempt->attempt_number }} · Started {{ $attempt->started_at?->diffForHumans() ?? 'time not recorded' }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="rounded-full bg-gray-100 px-2.5 py-1 font-semibold text-sign-muted">{{ ucfirst(str_replace('-', ' ', $attempt->status)) }}</span>
                        @if ($attempt->isSubmitted())
                            <span class="rounded-full px-2.5 py-1 font-semibold {{ $attempt->passed ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ $attempt->passed ? 'Passed' : 'Not passed' }}</span>
                            <span class="font-semibold text-sign-primary">{{ number_format((float) $attempt->percentage, 1) }}%</span>
                            <a href="{{ route('admin.assessment-results.show', $attempt) }}" class="inline-flex min-h-9 items-center rounded-lg border border-sign-border px-3 py-2 font-semibold text-sign-primary hover:bg-sign-soft">Open result</a>
                        @endif
                    </div>
                </div>
            @empty
                <p class="px-5 py-8 text-center text-sm text-sign-muted">No assessment attempts have been recorded yet.</p>
            @endforelse
        </section>
    </div>
</section>
