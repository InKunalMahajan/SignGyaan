@php
    $lesson = $currentLessonModel;
    $unit = $currentUnitModel;
    $unitNumber = $currentLessonEntry['unit_number'];
    $lessonNumber = $currentLessonEntry['lesson_number'];

    $cleanLines = function (?string $value) {
        return collect(preg_split('/\R/u', (string) $value) ?: [])
            ->map(function ($line) {
                $line = trim((string) $line);

                return trim((string) preg_replace('/^(?:[-*•]|\d+[.)])\s*/u', '', $line));
            })
            ->filter()
            ->values();
    };

    $objectives = $cleanLines($lesson->learning_objectives);
    $keyPoints = $cleanLines($lesson->key_points);
    $hasLessonText = filled($lesson->content) || filled($lesson->example_content) || $objectives->isNotEmpty() || $keyPoints->isNotEmpty();

    $videoUrl = $lesson->isl_video_url;
    $isDirectVideo = false;

    if ($videoUrl) {
        $videoPath = (string) parse_url($videoUrl, PHP_URL_PATH);
        $videoExtension = strtolower(pathinfo($videoPath, PATHINFO_EXTENSION));
        $isDirectVideo = in_array($videoExtension, ['mp4', 'webm', 'mov', 'ogg'], true);
    }

    $courseRouteParameters = [
        'subject' => $subjectSlug,
        'course' => $courseSlug,
    ];

    $courseUrl = route('courses.show', $courseRouteParameters);
    $previousUrl = $previousLessonEntry
        ? route('courses.show', $courseRouteParameters + ['lesson' => $previousLessonEntry['key']])
        : null;
    $nextUrl = $nextLessonEntry
        ? route('courses.show', $courseRouteParameters + ['lesson' => $nextLessonEntry['key']])
        : null;
@endphp

<section class="border-b border-sign-border bg-sign-soft py-7 sm:py-10">
    <x-container>
        <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-sign-muted sm:text-sm" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="transition hover:text-sign-primary">Home</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('subjects.show', $subjectSlug) }}" class="transition hover:text-sign-primary">{{ $subject['name'] }}</a>
            <span aria-hidden="true">/</span>
            <a href="{{ $courseUrl }}" class="transition hover:text-sign-primary">{{ $course['title'] }}</a>
            <span aria-hidden="true">/</span>
            <span class="font-semibold text-sign-primary">{{ $lesson->title }}</span>
        </nav>

        <div class="mt-5 grid gap-5 sm:mt-6 xl:grid-cols-[minmax(0,1fr)_18rem] xl:items-end xl:gap-6">
            <div class="max-w-3xl">
                <div class="flex flex-wrap items-center gap-2 text-[11px] font-semibold sm:text-xs">
                    <span class="rounded-full bg-white px-3 py-1.5 text-sign-primary ring-1 ring-sign-border">Unit {{ $unitNumber }}</span>
                    <span class="rounded-full bg-sign-light px-3 py-1.5 text-sign-primary">Lesson {{ $lessonNumber }}</span>
                    @if ($videoUrl)
                        <span class="rounded-full bg-white px-3 py-1.5 text-sign-cyan-dark ring-1 ring-sign-border">ISL video</span>
                    @endif
                    @if ($lesson->estimated_duration_minutes)
                        <span class="rounded-full bg-white px-3 py-1.5 text-sign-muted ring-1 ring-sign-border">{{ $lesson->estimated_duration_minutes }} min</span>
                    @endif
                </div>

                <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">{{ $unit->title }}</p>
                <h1 class="mt-2 font-heading text-2xl font-semibold leading-tight tracking-tight text-sign-primary sm:text-4xl lg:text-5xl">
                    {{ $lesson->title }}
                </h1>

                @if ($lesson->short_description)
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-sign-muted sm:text-base">
                        {{ $lesson->short_description }}
                    </p>
                @endif
            </div>

            <div class="w-full rounded-2xl border border-sign-border bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-4 text-sm">
                    <span class="font-semibold text-sign-primary">Course position</span>
                    <span class="font-semibold text-sign-cyan-dark">{{ $positionProgressPercent }}%</span>
                </div>
                <div class="mt-3 h-2 overflow-hidden rounded-full bg-sign-light" role="progressbar" aria-label="Course position" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $positionProgressPercent }}">
                    <div class="h-full rounded-full bg-sign-primary" style="width: {{ $positionProgressPercent }}%"></div>
                </div>
                <p class="mt-2 text-xs text-sign-muted">Lesson {{ $currentPosition }} of {{ $totalLessons }}</p>
            </div>
        </div>
    </x-container>
</section>

<section class="bg-white py-8 sm:py-12 lg:py-16">
    <x-container>
        <div class="grid gap-7 xl:grid-cols-[minmax(0,1fr)_19rem] xl:items-start xl:gap-8">
            <div class="min-w-0 space-y-6 sm:space-y-8">
                <section id="lesson-video" class="scroll-mt-24 overflow-hidden rounded-2xl border border-sign-border bg-white shadow-sm sm:rounded-3xl" aria-labelledby="lesson-video-heading">
                    @if ($videoUrl && $isDirectVideo)
                        <video controls preload="metadata" class="aspect-video w-full bg-black" aria-label="ISL video for {{ $lesson->title }}">
                            <source src="{{ $videoUrl }}">
                            Your browser does not support this video.
                        </video>
                    @elseif ($videoUrl)
                        <div class="flex aspect-video min-h-52 items-center justify-center bg-sign-primary p-6 text-white sm:min-h-0 sm:p-8">
                            <div class="max-w-xl text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white text-sign-primary shadow-lg sm:h-20 sm:w-20" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="ml-1 h-7 w-7 sm:h-8 sm:w-8">
                                        <path d="M8.25 5.433c0-1.178 1.296-1.896 2.295-1.272l9.067 5.666c.94.588.94 1.958 0 2.546l-9.067 5.666c-.999.624-2.295-.094-2.295-1.272V5.433Z" />
                                    </svg>
                                </div>
                                <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-white/75 sm:mt-5 sm:text-sm">ISL Lesson Video</p>
                                <p id="lesson-video-heading" class="mt-2 font-heading text-lg font-semibold sm:text-2xl">{{ $lesson->title }}</p>
                                <a href="{{ $videoUrl }}" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">
                                    Open ISL Video
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="flex min-h-52 items-center justify-center bg-sign-soft p-6 sm:min-h-64">
                            <div class="max-w-lg text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-sign-primary shadow-sm" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-7 w-7">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h8.25A3.75 3.75 0 0 0 16.5 15V9a3.75 3.75 0 0 0-3.75-3.75H4.5A2.25 2.25 0 0 0 2.25 7.5v9A2.25 2.25 0 0 0 4.5 18.75Z" />
                                    </svg>
                                </div>
                                <p id="lesson-video-heading" class="mt-4 font-heading text-xl font-semibold text-sign-primary">ISL video not added yet</p>
                                <p class="mt-2 text-sm leading-6 text-sign-muted">You can continue with the lesson notes and examples below.</p>
                            </div>
                        </div>
                    @endif

                    <div class="flex flex-col gap-2 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                        <div>
                            <p class="text-sm font-semibold text-sign-primary">Indian Sign Language support</p>
                            <p class="mt-1 text-sm leading-6 text-sign-muted">{{ $videoUrl ? 'This lesson includes an ISL video link from the Admin lesson content.' : 'No ISL video has been published for this lesson yet.' }}</p>
                        </div>
                        <span class="inline-flex w-fit rounded-full bg-sign-soft px-3 py-1.5 text-xs font-semibold text-sign-primary">{{ $videoUrl ? 'Available' : 'Notes available' }}</span>
                    </div>
                </section>

                @if ($objectives->isNotEmpty())
                    <section id="lesson-goals" class="scroll-mt-24 rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl sm:p-8">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark sm:text-sm">Lesson goals</p>
                        <h2 class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-3xl">What you will understand</h2>

                        <div class="mt-5 grid gap-3 md:grid-cols-2 sm:mt-6 sm:gap-4">
                            @foreach ($objectives as $objectiveIndex => $objective)
                                <div class="rounded-2xl bg-white p-4 sm:p-5">
                                    <span class="text-xs font-semibold text-sign-cyan-dark">{{ str_pad((string) ($objectiveIndex + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                    <p class="mt-2 text-sm font-semibold leading-6 text-sign-primary">{{ $objective }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($lesson->content)
                    <section id="lesson-notes" class="scroll-mt-24 rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-8">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark sm:text-sm">Lesson notes</p>
                        <h2 class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-3xl">Learn the concept</h2>
                        <div class="mt-4 whitespace-pre-line text-sm leading-7 text-sign-muted sm:mt-5 sm:text-base sm:leading-8">{{ $lesson->content }}</div>
                    </section>
                @endif

                @if ($keyPoints->isNotEmpty())
                    <section id="key-points" class="scroll-mt-24 rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-8">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark sm:text-sm">Key points</p>
                        <h2 class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-3xl">Remember these ideas</h2>

                        <div class="mt-5 space-y-3 sm:mt-6 sm:space-y-4">
                            @foreach ($keyPoints as $pointIndex => $point)
                                <div class="flex gap-3 rounded-2xl bg-sign-soft p-4 sm:gap-4 sm:p-5">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white text-sm font-semibold text-sign-primary sm:h-9 sm:w-9">{{ $pointIndex + 1 }}</div>
                                    <p class="text-sm leading-6 text-sign-muted sm:pt-1">{{ $point }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($lesson->example_content)
                    <section id="lesson-example" class="scroll-mt-24 rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl sm:p-8">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark sm:text-sm">Example</p>
                        <h2 class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-3xl">See the idea in context</h2>
                        <div class="mt-5 whitespace-pre-line rounded-2xl bg-white p-5 text-sm leading-7 text-sign-muted sm:mt-6 sm:p-6 sm:text-base">{{ $lesson->example_content }}</div>
                    </section>
                @endif

                @if (! $hasLessonText)
                    <section class="rounded-2xl border border-dashed border-sign-border bg-sign-soft p-6 text-center sm:rounded-3xl sm:p-10">
                        <h2 class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Lesson notes are being prepared</h2>
                        <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-sign-muted">This lesson is published, but its learning objectives, notes, key points and examples have not been added yet.</p>
                    </section>
                @endif

                <nav class="grid gap-3 border-t border-sign-border pt-6 sm:grid-cols-2 sm:pt-8" aria-label="Lesson navigation">
                    @if ($previousLessonEntry)
                        <a href="{{ $previousUrl }}" class="group rounded-2xl border border-sign-border bg-white p-4 transition hover:border-sign-cyan hover:bg-sign-soft sm:p-5">
                            <span class="text-xs font-semibold uppercase tracking-wider text-sign-muted">← Previous lesson</span>
                            <span class="mt-2 block font-heading text-lg font-semibold text-sign-primary">{{ $previousLessonEntry['lesson']->title }}</span>
                            <span class="mt-1 block text-xs text-sign-muted">Unit {{ $previousLessonEntry['unit_number'] }} · Lesson {{ $previousLessonEntry['lesson_number'] }}</span>
                        </a>
                    @else
                        <a href="{{ $courseUrl }}#course-curriculum" class="rounded-2xl border border-sign-border bg-white p-4 transition hover:border-sign-cyan hover:bg-sign-soft sm:p-5">
                            <span class="text-xs font-semibold uppercase tracking-wider text-sign-muted">← Course curriculum</span>
                            <span class="mt-2 block font-heading text-lg font-semibold text-sign-primary">{{ $course['title'] }}</span>
                        </a>
                    @endif

                    @if ($nextLessonEntry)
                        <a href="{{ $nextUrl }}" class="group rounded-2xl border border-sign-primary bg-sign-primary p-4 text-white transition hover:bg-sign-dark sm:p-5">
                            <span class="text-xs font-semibold uppercase tracking-wider text-white/70">Next lesson →</span>
                            <span class="mt-2 block font-heading text-lg font-semibold">{{ $nextLessonEntry['lesson']->title }}</span>
                            <span class="mt-1 block text-xs text-white/70">Unit {{ $nextLessonEntry['unit_number'] }} · Lesson {{ $nextLessonEntry['lesson_number'] }}</span>
                        </a>
                    @else
                        <a href="{{ $courseUrl }}#course-curriculum" class="rounded-2xl border border-sign-primary bg-sign-primary p-4 text-white transition hover:bg-sign-dark sm:p-5">
                            <span class="text-xs font-semibold uppercase tracking-wider text-white/70">Course complete</span>
                            <span class="mt-2 block font-heading text-lg font-semibold">Return to curriculum</span>
                        </a>
                    @endif
                </nav>
            </div>

            <aside class="space-y-5 xl:sticky xl:top-28" aria-label="Lesson overview">
                <section class="rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Current unit</p>
                    <h2 class="mt-2 font-heading text-xl font-semibold text-sign-primary">{{ $unit->title }}</h2>
                    @if ($unit->short_description || $unit->description)
                        <p class="mt-3 text-sm leading-6 text-sign-muted">{{ $unit->short_description ?: $unit->description }}</p>
                    @endif
                    <a href="{{ $courseUrl }}#course-curriculum" class="mt-4 inline-flex min-h-11 items-center text-sm font-semibold text-sign-primary hover:text-sign-cyan-dark">View full curriculum →</a>
                </section>

                <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">In this lesson</p>
                    <nav class="mt-3 grid gap-1 text-sm font-semibold" aria-label="Lesson sections">
                        <a href="#lesson-video" class="rounded-xl px-3 py-2.5 text-sign-primary transition hover:bg-sign-soft">ISL video</a>
                        @if ($objectives->isNotEmpty())
                            <a href="#lesson-goals" class="rounded-xl px-3 py-2.5 text-sign-primary transition hover:bg-sign-soft">Learning goals</a>
                        @endif
                        @if ($lesson->content)
                            <a href="#lesson-notes" class="rounded-xl px-3 py-2.5 text-sign-primary transition hover:bg-sign-soft">Lesson notes</a>
                        @endif
                        @if ($keyPoints->isNotEmpty())
                            <a href="#key-points" class="rounded-xl px-3 py-2.5 text-sign-primary transition hover:bg-sign-soft">Key points</a>
                        @endif
                        @if ($lesson->example_content)
                            <a href="#lesson-example" class="rounded-xl px-3 py-2.5 text-sign-primary transition hover:bg-sign-soft">Example</a>
                        @endif
                    </nav>
                </section>
            </aside>
        </div>
    </x-container>
</section>
