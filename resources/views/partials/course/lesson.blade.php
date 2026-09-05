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
    $hasLessonText = filled($lesson->content)
        || filled($lesson->example_content)
        || $objectives->isNotEmpty()
        || $keyPoints->isNotEmpty();

    $publishedItems = $lesson->relationLoaded('practiceResources')
        ? $lesson->practiceResources
        : collect();
    $practiceItems = $publishedItems->where('kind', 'practice')->values();
    $resourceItems = $publishedItems->where('kind', 'resource')->values();

    $resourceTypeLabels = [
        'exercise' => 'Exercise',
        'quiz' => 'Quiz',
        'reflection' => 'Reflection',
        'worksheet' => 'Worksheet',
        'notes' => 'Notes / Handout',
        'download' => 'Download',
        'external-link' => 'External Link',
        'reference' => 'Reference',
    ];

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
                    @if ($practiceItems->isNotEmpty())
                        <span class="rounded-full bg-white px-3 py-1.5 text-sign-cyan-dark ring-1 ring-sign-border">{{ $practiceItems->count() }} Practice</span>
                    @endif
                    @if ($resourceItems->isNotEmpty())
                        <span class="rounded-full bg-white px-3 py-1.5 text-sign-muted ring-1 ring-sign-border">{{ $resourceItems->count() }} Resources</span>
                    @endif
                    @if ($lesson->estimated_duration_minutes)
                        <span class="rounded-full bg-white px-3 py-1.5 text-sign-muted ring-1 ring-sign-border">{{ $lesson->estimated_duration_minutes }} min</span>
                    @endif
                </div>

                <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">{{ $unit->title }}</p>
                <h1 class="mt-2 font-heading text-2xl font-semibold leading-tight tracking-tight text-sign-primary sm:text-4xl lg:text-5xl">{{ $lesson->title }}</h1>

                @if ($lesson->short_description)
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-sign-muted sm:text-base">{{ $lesson->short_description }}</p>
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
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white text-sign-primary shadow-lg" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="ml-1 h-7 w-7"><path d="M8.25 5.433c0-1.178 1.296-1.896 2.295-1.272l9.067 5.666c.94.588.94 1.958 0 2.546l-9.067 5.666c-.999.624-2.295-.094-2.295-1.272V5.433Z" /></svg>
                                </div>
                                <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-white/75">ISL Lesson Video</p>
                                <p id="lesson-video-heading" class="mt-2 font-heading text-lg font-semibold sm:text-2xl">{{ $lesson->title }}</p>
                                <a href="{{ $videoUrl }}" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Open ISL Video</a>
                            </div>
                        </div>
                    @else
                        <div class="flex min-h-52 items-center justify-center bg-sign-soft p-6 sm:min-h-64">
                            <div class="max-w-lg text-center">
                                <p id="lesson-video-heading" class="font-heading text-xl font-semibold text-sign-primary">ISL video not added yet</p>
                                <p class="mt-2 text-sm leading-6 text-sign-muted">Continue with the lesson notes, examples and practice below.</p>
                            </div>
                        </div>
                    @endif
                </section>

                @if ($objectives->isNotEmpty())
                    <section id="lesson-goals" class="scroll-mt-24 rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl sm:p-8">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Lesson goals</p>
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
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Lesson notes</p>
                        <h2 class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-3xl">Learn the concept</h2>
                        <div class="mt-4 whitespace-pre-line text-sm leading-7 text-sign-muted sm:mt-5 sm:text-base sm:leading-8">{{ $lesson->content }}</div>
                    </section>
                @endif

                @if ($keyPoints->isNotEmpty())
                    <section id="key-points" class="scroll-mt-24 rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-8">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Key points</p>
                        <h2 class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-3xl">Remember these ideas</h2>
                        <div class="mt-5 space-y-3 sm:mt-6 sm:space-y-4">
                            @foreach ($keyPoints as $pointIndex => $point)
                                <div class="flex gap-3 rounded-2xl bg-sign-soft p-4 sm:gap-4 sm:p-5">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white text-sm font-semibold text-sign-primary">{{ $pointIndex + 1 }}</div>
                                    <p class="text-sm leading-6 text-sign-muted sm:pt-1">{{ $point }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($lesson->example_content)
                    <section id="lesson-example" class="scroll-mt-24 rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl sm:p-8">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Example</p>
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

                <section id="lesson-practice" class="scroll-mt-24 rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl sm:p-8" aria-labelledby="lesson-practice-heading">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Practice</p>
                            <h2 id="lesson-practice-heading" class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-3xl">Check your understanding</h2>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-sign-muted">Published practice activities from this lesson appear here in the order set by the teacher.</p>
                        </div>
                        @if ($practiceItems->isNotEmpty())
                            <span class="w-fit rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-sign-primary ring-1 ring-sign-border">{{ $practiceItems->count() }} {{ $practiceItems->count() === 1 ? 'activity' : 'activities' }}</span>
                        @endif
                    </div>

                    @if ($practiceItems->isNotEmpty())
                        <div class="mt-6 space-y-4">
                            @foreach ($practiceItems as $practiceIndex => $item)
                                <article class="rounded-2xl border border-sign-border bg-white p-5 sm:p-6" aria-labelledby="practice-item-{{ $item->id }}">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-full bg-sign-light px-3 py-1 text-[11px] font-semibold text-sign-primary">{{ $resourceTypeLabels[$item->resource_type] ?? ucfirst(str_replace('-', ' ', $item->resource_type)) }}</span>
                                        <span class="text-xs font-semibold text-sign-muted">Activity {{ $practiceIndex + 1 }}</span>
                                        @if ($item->estimated_duration_minutes)
                                            <span class="text-xs text-sign-muted">· {{ $item->estimated_duration_minutes }} min</span>
                                        @endif
                                    </div>
                                    <h3 id="practice-item-{{ $item->id }}" class="mt-3 font-heading text-lg font-semibold text-sign-primary sm:text-xl">{{ $item->title }}</h3>
                                    @if ($item->short_description)
                                        <p class="mt-2 text-sm leading-6 text-sign-muted">{{ $item->short_description }}</p>
                                    @endif
                                    @if ($item->instructions)
                                        <div class="mt-4 rounded-xl bg-sign-soft p-4">
                                            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Instructions</p>
                                            <div class="mt-2 whitespace-pre-line text-sm leading-6 text-sign-muted">{{ $item->instructions }}</div>
                                        </div>
                                    @endif
                                    @if ($item->content)
                                        <div class="mt-4 whitespace-pre-line text-sm leading-7 text-sign-text">{{ $item->content }}</div>
                                    @endif
                                    @if ($item->resource_url)
                                        <a href="{{ $item->resource_url }}" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Open activity</a>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-6 rounded-2xl border border-dashed border-sign-border bg-white px-5 py-7 text-center">
                            <p class="text-sm font-semibold text-sign-primary">No published practice activities yet.</p>
                            <p class="mt-1 text-sm text-sign-muted">Review the lesson notes and continue when you are ready.</p>
                        </div>
                    @endif
                </section>

                @if ($resourceItems->isNotEmpty())
                    <section id="lesson-resources" class="scroll-mt-24 rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-8" aria-labelledby="lesson-resources-heading">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Resources</p>
                        <h2 id="lesson-resources-heading" class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-3xl">Extra learning material</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-sign-muted">Use these published handouts, worksheets, downloads, references and links to support this lesson.</p>

                        <div class="mt-6 grid gap-4 md:grid-cols-2">
                            @foreach ($resourceItems as $item)
                                <article class="flex h-full flex-col rounded-2xl border border-sign-border bg-sign-soft p-5">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-full bg-white px-3 py-1 text-[11px] font-semibold text-sign-primary">{{ $resourceTypeLabels[$item->resource_type] ?? ucfirst(str_replace('-', ' ', $item->resource_type)) }}</span>
                                        @if ($item->estimated_duration_minutes)
                                            <span class="text-xs text-sign-muted">{{ $item->estimated_duration_minutes }} min</span>
                                        @endif
                                    </div>
                                    <h3 class="mt-3 font-heading text-lg font-semibold text-sign-primary">{{ $item->title }}</h3>
                                    @if ($item->short_description)
                                        <p class="mt-2 text-sm leading-6 text-sign-muted">{{ $item->short_description }}</p>
                                    @endif
                                    @if ($item->instructions)
                                        <div class="mt-3 whitespace-pre-line text-sm leading-6 text-sign-muted">{{ $item->instructions }}</div>
                                    @endif
                                    @if ($item->content)
                                        <div class="mt-3 whitespace-pre-line text-sm leading-6 text-sign-muted">{{ $item->content }}</div>
                                    @endif
                                    @if ($item->resource_url)
                                        <a href="{{ $item->resource_url }}" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex min-h-11 w-fit items-center justify-center rounded-xl border border-sign-primary bg-white px-4 py-2.5 text-sm font-semibold text-sign-primary transition hover:bg-white/70">Open resource →</a>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif

                <nav class="grid gap-3 border-t border-sign-border pt-6 sm:grid-cols-2 sm:pt-8" aria-label="Lesson navigation">
                    @if ($previousLessonEntry)
                        <a href="{{ $previousUrl }}" class="rounded-2xl border border-sign-border bg-white p-4 transition hover:border-sign-cyan hover:bg-sign-soft sm:p-5">
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
                        <a href="{{ $nextUrl }}" class="rounded-2xl border border-sign-primary bg-sign-primary p-4 text-white transition hover:bg-sign-dark sm:p-5">
                            <span class="text-xs font-semibold uppercase tracking-wider text-white/70">Next lesson →</span>
                            <span class="mt-2 block font-heading text-lg font-semibold">{{ $nextLessonEntry['lesson']->title }}</span>
                            <span class="mt-1 block text-xs text-white/70">Unit {{ $nextLessonEntry['unit_number'] }} · Lesson {{ $nextLessonEntry['lesson_number'] }}</span>
                        </a>
                    @else
                        <a href="{{ $courseUrl }}#course-curriculum" class="rounded-2xl border border-sign-primary bg-sign-primary p-4 text-white transition hover:bg-sign-dark sm:p-5">
                            <span class="text-xs font-semibold uppercase tracking-wider text-white/70">Course complete</span>
                            <span class="mt-2 block font-heading text-lg font-semibold">Back to curriculum →</span>
                        </a>
                    @endif
                </nav>
            </div>

            <aside class="space-y-4 xl:sticky xl:top-28" aria-label="Lesson contents">
                <div class="rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">In this lesson</p>
                    <nav class="mt-4 grid gap-1 text-sm" aria-label="Lesson section links">
                        <a href="#lesson-video" class="min-h-10 rounded-xl px-3 py-2.5 font-semibold text-sign-primary transition hover:bg-white">ISL video</a>
                        @if ($objectives->isNotEmpty())<a href="#lesson-goals" class="min-h-10 rounded-xl px-3 py-2.5 font-semibold text-sign-primary transition hover:bg-white">Learning goals</a>@endif
                        @if ($lesson->content)<a href="#lesson-notes" class="min-h-10 rounded-xl px-3 py-2.5 font-semibold text-sign-primary transition hover:bg-white">Lesson notes</a>@endif
                        @if ($keyPoints->isNotEmpty())<a href="#key-points" class="min-h-10 rounded-xl px-3 py-2.5 font-semibold text-sign-primary transition hover:bg-white">Key points</a>@endif
                        @if ($lesson->example_content)<a href="#lesson-example" class="min-h-10 rounded-xl px-3 py-2.5 font-semibold text-sign-primary transition hover:bg-white">Example</a>@endif
                        <a href="#lesson-practice" class="min-h-10 rounded-xl px-3 py-2.5 font-semibold text-sign-primary transition hover:bg-white">Practice</a>
                        @if ($resourceItems->isNotEmpty())<a href="#lesson-resources" class="min-h-10 rounded-xl px-3 py-2.5 font-semibold text-sign-primary transition hover:bg-white">Resources</a>@endif
                    </nav>
                </div>

                <a href="{{ $courseUrl }}#course-curriculum" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">View course curriculum</a>
            </aside>
        </div>
    </x-container>
</section>
