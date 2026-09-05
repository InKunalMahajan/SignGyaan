@php
    $recommendedNextLessons = $recommendedNextLessons ?? collect();
@endphp

@if ($recommendedNextLessons->isNotEmpty())
<section aria-labelledby="recommended-next-lessons-heading">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Recommended for you</p>
            <h2 id="recommended-next-lessons-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Recommended next lessons</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-sign-muted">These suggestions come from your saved course progress and only include published lessons you have not completed yet.</p>
        </div>
        <a href="{{ route('my-courses') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-sign-primary hover:text-sign-cyan-dark">View My Courses →</a>
    </div>

    <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($recommendedNextLessons as $lesson)
            <article class="flex h-full flex-col rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-6">
                <div class="flex flex-wrap gap-2 text-xs font-semibold">
                    <span class="rounded-full bg-sign-soft px-3 py-1 text-sign-primary">{{ $lesson['subject'] }}</span>
                    <span class="rounded-full bg-sign-light px-3 py-1 text-sign-cyan-dark">{{ $lesson['course_level'] }}</span>
                </div>

                <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-sign-muted">{{ $lesson['course_title'] }}</p>
                <h3 class="mt-2 font-heading text-xl font-semibold text-sign-primary">{{ $lesson['lesson_title'] }}</h3>
                <p class="mt-1 text-sm text-sign-muted">{{ $lesson['unit_title'] }}</p>

                <div class="mt-4 flex flex-wrap gap-x-4 gap-y-2 text-xs text-sign-muted">
                    @if ($lesson['lesson_duration'])
                        <span>{{ $lesson['lesson_duration'] }} min lesson</span>
                    @endif
                    <span>{{ $lesson['course_progress_percent'] }}% course complete</span>
                </div>

                <p class="mt-4 flex-1 text-sm leading-6 text-sign-muted">{{ $lesson['reason'] }}</p>

                <a href="{{ $lesson['url'] }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-4 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark focus:outline-none focus:ring-4 focus:ring-sign-light">Open Recommended Lesson</a>
            </article>
        @endforeach
    </div>
</section>
@endif
