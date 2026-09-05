@include('partials.course.linked-vocabulary')

@php
    $currentProgressLessonKey = 'lesson-'.$currentLessonModel->id;
    $existingProgress = auth()->check()
        ? auth()->user()->learningProgress()
            ->where('subject_slug', $subjectSlug)
            ->where('course_slug', $courseSlug)
            ->first()
        : null;

    $publishedProgressEntries = collect($lessonMap ?? [])->map(fn ($entry) => [
        'stable_key' => $entry['stable_key'] ?? ('lesson-'.$entry['lesson']->id),
        'legacy_key' => $entry['legacy_key'] ?? null,
    ]);

    $normalizedCompletedLessonKeys = collect($existingProgress?->completed_lessons ?? [])
        ->map(function ($savedKey) use ($publishedProgressEntries) {
            $entry = $publishedProgressEntries->first(
                fn ($entry) => $entry['stable_key'] === $savedKey
                    || ($entry['legacy_key'] && $entry['legacy_key'] === $savedKey)
            );

            return $entry['stable_key'] ?? null;
        })
        ->filter()
        ->unique()
        ->values();

    $isCurrentLessonCompleted = $normalizedCompletedLessonKeys->contains($currentProgressLessonKey);
    $publishedLessonCount = $publishedProgressEntries->count();
    $savedProgressPercent = $publishedLessonCount > 0
        ? (int) min(100, round(($normalizedCompletedLessonKeys->count() / $publishedLessonCount) * 100))
        : 0;
    $savedVideoProgress = data_get($existingProgress?->video_progress ?? [], $currentProgressLessonKey, []);
    $savedVideoPosition = (float) data_get($savedVideoProgress, 'position_seconds', 0);
    $savedVideoPercent = (int) data_get($savedVideoProgress, 'watched_percent', 0);
@endphp

<section class="border-t border-sign-border bg-sign-soft py-8 sm:py-10">
    <x-container>
        <div class="mx-auto max-w-5xl">
            @if (session('status'))
                <div class="mb-5 rounded-2xl border border-sign-cyan bg-sign-light px-4 py-3 text-sm font-semibold text-sign-primary" role="status" aria-live="polite">{{ session('status') }}</div>
            @endif

            @if ($errors->has('course_slug') || $errors->has('lesson_id'))
                <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800" role="alert" aria-live="assertive">{{ $errors->first('course_slug') ?: $errors->first('lesson_id') }}</div>
            @endif

            <div class="overflow-hidden rounded-2xl border border-sign-border bg-white shadow-sm sm:rounded-3xl">
                <div class="grid lg:grid-cols-[minmax(0,1fr)_18rem]">
                    <div class="p-5 sm:p-7 lg:p-8">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Saved learning progress</p>
                        <h2 class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Keep your place in this course</h2>

                        @auth
                            <p class="mt-3 max-w-2xl text-sm leading-6 text-sign-muted">Video position can be saved while you watch, but watching a video never marks the lesson complete automatically. You decide when the lesson is complete.</p>

                            <div class="mt-5 flex flex-wrap items-center gap-3 text-xs font-semibold">
                                <span class="rounded-full bg-sign-soft px-3 py-1.5 text-sign-primary">Saved progress: {{ $savedProgressPercent }}%</span>
                                @if ($isCurrentLessonCompleted)
                                    <span class="rounded-full bg-sign-light px-3 py-1.5 text-sign-primary">✓ Lesson completed</span>
                                @else
                                    <span class="rounded-full bg-white px-3 py-1.5 text-sign-muted ring-1 ring-sign-border">Not completed yet</span>
                                @endif
                                @if ($savedVideoPercent > 0)
                                    <span class="rounded-full bg-white px-3 py-1.5 text-sign-cyan-dark ring-1 ring-sign-border" data-video-progress-label>Video watched: {{ $savedVideoPercent }}%</span>
                                @endif
                            </div>

                            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                                <form method="POST" action="{{ route('learning-progress.store') }}">
                                    @csrf
                                    <input type="hidden" name="subject_slug" value="{{ $subjectSlug }}"><input type="hidden" name="course_slug" value="{{ $courseSlug }}"><input type="hidden" name="lesson_id" value="{{ $currentLessonModel->id }}"><input type="hidden" name="action" value="save">
                                    <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-sign-primary px-5 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Save My Place</button>
                                </form>

                                <form method="POST" action="{{ route('learning-progress.store') }}">
                                    @csrf
                                    <input type="hidden" name="subject_slug" value="{{ $subjectSlug }}"><input type="hidden" name="course_slug" value="{{ $courseSlug }}"><input type="hidden" name="lesson_id" value="{{ $currentLessonModel->id }}"><input type="hidden" name="action" value="complete">
                                    <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">{{ $nextLessonEntry ? 'Complete & Continue' : 'Complete Course' }}</button>
                                </form>
                            </div>

                            <div data-video-progress-config data-endpoint="{{ route('learning-progress.video.store') }}" data-subject="{{ $subjectSlug }}" data-course="{{ $courseSlug }}" data-lesson-id="{{ $currentLessonModel->id }}" data-position="{{ $savedVideoPosition }}" data-watched-percent="{{ $savedVideoPercent }}" class="mt-4 text-xs leading-5 text-sign-muted">
                                <span data-video-save-status aria-live="polite">{{ $savedVideoPercent > 0 ? 'Saved video position available.' : 'Video position will be saved while you watch.' }}</span>
                            </div>
                        @else
                            <p class="mt-3 max-w-2xl text-sm leading-6 text-sign-muted">Sign in to save your current lesson, video position and completed lessons across visits.</p>
                            <a href="{{ route('login') }}" class="mt-6 inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Sign In to Save Progress</a>
                        @endauth
                    </div>

                    <div class="flex items-center justify-center bg-sign-dark p-6 text-white sm:p-8">
                        <div class="w-full text-center">
                            <p class="font-heading text-4xl font-semibold">{{ $savedProgressPercent }}%</p>
                            <p class="mt-2 text-sm text-white/70">Saved course progress</p>
                            <div class="mx-auto mt-4 h-2 max-w-40 overflow-hidden rounded-full bg-white/20" role="progressbar" aria-label="Saved course progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $savedProgressPercent }}"><div class="h-full rounded-full bg-white" style="width: {{ $savedProgressPercent }}%"></div></div>
                            @auth<a href="{{ route('my-learning') }}" class="mt-5 inline-flex min-h-11 items-center justify-center text-sm font-semibold text-sign-cyan hover:text-white">View My Learning →</a>@endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-container>
</section>
