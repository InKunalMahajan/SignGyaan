<section aria-labelledby="dashboard-continue-heading">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Your learning</p>
            <h2 id="dashboard-continue-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Continue learning</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-sign-muted">Resume exactly where you stopped. Your saved lesson and video position stay connected to each course.</p>
        </div>
        <a href="{{ route('my-courses') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-sign-primary hover:text-sign-cyan-dark">View My Courses →</a>
    </div>

    @if ($continueLearning->isNotEmpty())
        <div class="mt-5 grid gap-4 lg:grid-cols-2">
            @foreach ($continueLearning->take(4) as $item)
                <article class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-full bg-sign-soft px-3 py-1 text-xs font-semibold text-sign-primary">{{ $item['subject'] }}</span>
                            <span class="rounded-full bg-sign-light px-3 py-1 text-xs font-semibold text-sign-cyan-dark">{{ $item['course_level'] }}</span>
                        </div>
                        <span class="text-xs font-semibold text-sign-cyan-dark">{{ $item['progress_percent'] }}%</span>
                    </div>

                    <h3 class="mt-4 font-heading text-xl font-semibold text-sign-primary">{{ $item['course_title'] }}</h3>
                    <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-sign-muted">Resume lesson</p>
                    <p class="mt-1 text-sm font-semibold text-sign-primary">{{ $item['lesson_title'] ?: 'Current lesson' }}</p>
                    @if ($item['unit_title'])
                        <p class="mt-1 text-xs text-sign-muted">{{ $item['unit_title'] }}</p>
                    @endif

                    <div class="mt-4 flex flex-wrap gap-x-4 gap-y-2 text-xs text-sign-muted">
                        <span>{{ $item['completed_lessons'] }} of {{ $item['total_lessons'] }} lessons completed</span>
                        @if ($item['lesson_duration'])
                            <span>{{ $item['lesson_duration'] }} min lesson</span>
                        @endif
                        @if ($item['last_accessed_at'])
                            <span>Last opened {{ $item['last_accessed_at']->diffForHumans() }}</span>
                        @endif
                    </div>

                    <div class="mt-4 h-2 overflow-hidden rounded-full bg-sign-soft" role="progressbar" aria-label="{{ $item['course_title'] }} progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $item['progress_percent'] }}">
                        <div class="h-full rounded-full bg-sign-primary" style="width: {{ $item['progress_percent'] }}%"></div>
                    </div>

                    @if ($item['video_watched_percent'] !== null)
                        <p class="mt-3 text-xs font-semibold text-sign-muted">Current lesson video: {{ $item['video_watched_percent'] }}% watched</p>
                    @endif

                    <a href="{{ $item['resume_url'] }}" class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-sign-primary px-4 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark focus:outline-none focus:ring-4 focus:ring-sign-light">Continue Lesson</a>
                </article>
            @endforeach
        </div>
    @else
        <div class="mt-5 rounded-2xl border border-dashed border-sign-border bg-sign-soft p-6 sm:rounded-3xl sm:p-8">
            <h3 class="font-heading text-xl font-semibold text-sign-primary">No saved course progress yet</h3>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-sign-muted">Open a published lesson and use Save My Place or Complete & Continue. Your next visit will return you to that lesson.</p>
            <a href="{{ route('subjects') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white">Browse Subjects</a>
        </div>
    @endif
</section>
