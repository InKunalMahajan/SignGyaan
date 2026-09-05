<section aria-labelledby="learning-activity-heading">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Learning activity</p>
            <h2 id="learning-activity-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Your learning streak</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-sign-muted">A learning day is counted when you save lesson progress, complete a lesson, watch lesson video content, or submit an assessment.</p>
        </div>
        <a href="{{ route('learning-history') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-sign-primary hover:text-sign-cyan-dark">View Learning History →</a>
    </div>

    <div class="mt-5 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl">
            <p class="text-sm font-semibold text-sign-muted">Current streak</p>
            <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $activitySummary['current_streak'] }} day{{ $activitySummary['current_streak'] === 1 ? '' : 's' }}</p>
            <p class="mt-1 text-xs text-sign-muted">{{ $activitySummary['active_today'] ? 'Active today' : 'Continue learning today to keep building your streak.' }}</p>
        </div>
        <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
            <p class="text-sm font-semibold text-sign-muted">Longest streak</p>
            <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $activitySummary['longest_streak'] }} day{{ $activitySummary['longest_streak'] === 1 ? '' : 's' }}</p>
            <p class="mt-1 text-xs text-sign-muted">Your best consecutive learning run.</p>
        </div>
        <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
            <p class="text-sm font-semibold text-sign-muted">Active days</p>
            <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $activitySummary['active_days'] }}</p>
            <p class="mt-1 text-xs text-sign-muted">Distinct days with saved learning activity.</p>
        </div>
    </div>

    <div class="mt-5 rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-6">
        <h3 class="font-heading text-xl font-semibold text-sign-primary">Recent activity</h3>
        @if ($activitySummary['recent_activity']->isNotEmpty())
            <ol class="mt-4 divide-y divide-sign-border">
                @foreach ($activitySummary['recent_activity'] as $activity)
                    <li class="py-4 first:pt-0 last:pb-0">
                        <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
                            <div>
                                <p class="text-sm font-semibold text-sign-primary">{{ $activity['title'] }}</p>
                                @if ($activity['lesson_title'])
                                    <p class="mt-1 text-sm text-sign-muted">{{ $activity['lesson_title'] }}</p>
                                @endif
                                @if ($activity['course_title'])
                                    <p class="mt-1 text-xs text-sign-muted">{{ $activity['course_title'] }}</p>
                                @endif
                            </div>
                            @if ($activity['occurred_at'])
                                <time class="shrink-0 text-xs font-semibold text-sign-cyan-dark" datetime="{{ $activity['occurred_at']->toIso8601String() }}">{{ $activity['occurred_at']->diffForHumans() }}</time>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        @else
            <p class="mt-3 text-sm leading-6 text-sign-muted">Your learning activity will appear here after you save progress, complete a lesson, watch a lesson video, or finish an assessment.</p>
        @endif
    </div>
</section>
