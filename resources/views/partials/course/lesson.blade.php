@php
    $requestedLessonKey = request('lesson', 'unit-1-lesson-1');

    $unitTitlesBySubject = [
        'english' => ['Getting Started', 'Vocabulary & Meaning', 'Grammar & Sentences', 'Reading & Communication', 'Review & Practice'],
        'mathematics' => ['Number Foundations', 'Everyday Calculations', 'Money & Measurement', 'Problem Solving', 'Review & Practice'],
        'science' => ['Scientific Thinking', 'Matter & Materials', 'Energy & Change', 'Living World', 'Review & Practice'],
        'digital-skills' => ['Computer Foundations', 'Files, Folders & Software', 'Internet & Digital Safety', 'Practical Digital Tools', 'Review & Practice'],
        'general-knowledge' => ['India Basics', 'World Awareness', 'Society & Everyday Services', 'People, Places & Events', 'Review & Practice'],
        'life-skills' => ['Daily Routines', 'Communication Skills', 'Time & Organisation', 'Confidence & Independence', 'Review & Practice'],
    ];

    $lessonNames = ['Introduction', 'Key Ideas', 'Visual Explanation', 'Examples', 'Guided Practice', 'Quick Check', 'Review'];
    $unitTitles = $unitTitlesBySubject[$subjectSlug] ?? ['Getting Started', 'Core Concepts', 'Practical Learning', 'Review & Practice', 'Next Steps'];

    $unitCount = max(1, (int) $course['units']);
    $lessonCount = max($unitCount, (int) $course['lessons']);
    $baseLessons = intdiv($lessonCount, $unitCount);
    $extraLessons = $lessonCount % $unitCount;

    $lessonMap = [];

    for ($unit = 1; $unit <= $unitCount; $unit++) {
        $lessonsInUnit = $baseLessons + ($unit <= $extraLessons ? 1 : 0);
        $unitTitleForMap = $unitTitles[$unit - 1] ?? 'Learning Unit ' . $unit;

        for ($lesson = 1; $lesson <= $lessonsInUnit; $lesson++) {
            $lessonNameForMap = $lessonNames[($lesson - 1) % count($lessonNames)];

            $lessonMap[] = [
                'key' => 'unit-' . $unit . '-lesson-' . $lesson,
                'unit' => $unit,
                'lesson' => $lesson,
                'unitTitle' => $unitTitleForMap,
                'lessonName' => $lessonNameForMap,
                'title' => $lessonNameForMap . ': ' . $unitTitleForMap,
            ];
        }
    }

    $lessonKeys = array_column($lessonMap, 'key');
    $currentIndex = array_search($requestedLessonKey, $lessonKeys, true);

    if ($currentIndex === false) {
        $currentIndex = 0;
    }

    $currentLesson = $lessonMap[$currentIndex];
    $previousLesson = $currentIndex > 0 ? $lessonMap[$currentIndex - 1] : null;
    $nextLesson = $currentIndex < count($lessonMap) - 1 ? $lessonMap[$currentIndex + 1] : null;

    $unitNumber = $currentLesson['unit'];
    $lessonNumber = $currentLesson['lesson'];
    $unitTitle = $currentLesson['unitTitle'];
    $lessonTitle = $currentLesson['title'];
    $currentPosition = $currentIndex + 1;
    $totalLessons = count($lessonMap);
    $progressPercent = (int) round(($currentPosition / max(1, $totalLessons)) * 100);

    $courseRouteParameters = ['subject' => $subjectSlug, 'course' => $courseSlug];
    $previousUrl = $previousLesson
        ? route('courses.show', $courseRouteParameters + ['lesson' => $previousLesson['key']])
        : null;
    $nextUrl = $nextLesson
        ? route('courses.show', $courseRouteParameters + ['lesson' => $nextLesson['key']])
        : null;
    $courseUrl = route('courses.show', $courseRouteParameters);
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
            <span class="font-semibold text-sign-primary">Lesson {{ $currentPosition }}</span>
        </nav>

        <div class="mt-5 grid gap-5 sm:mt-6 xl:grid-cols-[minmax(0,1fr)_18rem] xl:items-end xl:gap-6">
            <div class="max-w-3xl">
                <div class="flex flex-wrap items-center gap-2 text-[11px] font-semibold sm:text-xs">
                    <span class="rounded-full bg-white px-3 py-1.5 text-sign-primary ring-1 ring-sign-border">Unit {{ $unitNumber }}</span>
                    <span class="rounded-full bg-sign-light px-3 py-1.5 text-sign-primary">Lesson {{ $lessonNumber }}</span>
                    <span class="rounded-full bg-white px-3 py-1.5 text-sign-cyan-dark ring-1 ring-sign-border">ISL + Notes</span>
                </div>

                <h1 class="mt-4 font-heading text-2xl font-semibold leading-tight tracking-tight text-sign-primary sm:text-4xl lg:text-5xl">
                    {{ $lessonTitle }}
                </h1>

                <p class="mt-4 max-w-2xl text-sm leading-7 text-sign-muted sm:text-base">
                    Learn one concept at a time through visual explanation, simple notes, examples and short practice.
                </p>
            </div>

            <div class="w-full rounded-2xl border border-sign-border bg-white p-4 shadow-sm xl:max-w-none">
                <div class="flex items-center justify-between gap-4 text-sm">
                    <span class="font-semibold text-sign-primary">Course progress</span>
                    <span class="font-semibold text-sign-cyan-dark">{{ $progressPercent }}%</span>
                </div>
                <div class="mt-3 h-2 overflow-hidden rounded-full bg-sign-light" role="progressbar" aria-label="Course progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $progressPercent }}">
                    <div class="h-full rounded-full bg-sign-primary transition-all" style="width: {{ $progressPercent }}%"></div>
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
                {{-- ISL Video --}}
                <section id="lesson-video" class="scroll-mt-24 overflow-hidden rounded-2xl border border-sign-border bg-white shadow-sm sm:rounded-3xl">
                    <div class="flex aspect-video min-h-52 items-center justify-center bg-sign-primary p-5 text-white sm:min-h-0 sm:p-8">
                        <div class="text-center">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white text-sign-primary shadow-lg sm:h-20 sm:w-20">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="ml-1 h-7 w-7 sm:h-8 sm:w-8" aria-hidden="true">
                                    <path d="M8.25 5.433c0-1.178 1.296-1.896 2.295-1.272l9.067 5.666c.94.588.94 1.958 0 2.546l-9.067 5.666c-.999.624-2.295-.094-2.295-1.272V5.433Z" />
                                </svg>
                            </div>
                            <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-white/75 sm:mt-5 sm:text-sm">ISL Lesson Video</p>
                            <p class="mt-2 font-heading text-lg font-semibold sm:text-2xl">{{ $lessonTitle }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                        <div>
                            <p class="text-sm font-semibold text-sign-primary">Visual explanation</p>
                            <p class="mt-1 text-sm leading-6 text-sign-muted">Video area ready for this lesson's Indian Sign Language content.</p>
                        </div>
                        <span class="inline-flex w-fit rounded-full bg-sign-soft px-3 py-1.5 text-xs font-semibold text-sign-primary">ISL supported</span>
                    </div>
                </section>

                {{-- Learning goals --}}
                <section id="lesson-goals" class="scroll-mt-24 rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl sm:p-8">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark sm:text-sm">Lesson goals</p>
                    <h2 class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-3xl">What you will understand</h2>

                    <div class="mt-5 grid gap-3 md:grid-cols-3 md:gap-4 sm:mt-6">
                        <div class="rounded-2xl bg-white p-4 sm:p-5">
                            <span class="text-xs font-semibold text-sign-cyan-dark">01</span>
                            <p class="mt-2 text-sm font-semibold leading-6 text-sign-primary">Understand the main idea clearly.</p>
                        </div>
                        <div class="rounded-2xl bg-white p-4 sm:p-5">
                            <span class="text-xs font-semibold text-sign-cyan-dark">02</span>
                            <p class="mt-2 text-sm font-semibold leading-6 text-sign-primary">Connect the idea with a simple example.</p>
                        </div>
                        <div class="rounded-2xl bg-white p-4 sm:p-5">
                            <span class="text-xs font-semibold text-sign-cyan-dark">03</span>
                            <p class="mt-2 text-sm font-semibold leading-6 text-sign-primary">Check your understanding with practice.</p>
                        </div>
                    </div>
                </section>

                {{-- Key Points --}}
                <section id="key-points" class="scroll-mt-24 rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-8">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark sm:text-sm">Key points</p>
                    <h2 class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-3xl">Remember these ideas</h2>

                    <div class="mt-5 space-y-3 sm:mt-6 sm:space-y-4">
                        <div class="flex gap-3 rounded-2xl bg-sign-soft p-4 sm:gap-4 sm:p-5">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white text-sm font-semibold text-sign-primary sm:h-9 sm:w-9">1</div>
                            <p class="text-sm leading-6 text-sign-muted sm:pt-1">Start with the meaning of the concept before trying to remember details.</p>
                        </div>
                        <div class="flex gap-3 rounded-2xl bg-sign-soft p-4 sm:gap-4 sm:p-5">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white text-sm font-semibold text-sign-primary sm:h-9 sm:w-9">2</div>
                            <p class="text-sm leading-6 text-sign-muted sm:pt-1">Use the visual example to connect the lesson with a real situation.</p>
                        </div>
                        <div class="flex gap-3 rounded-2xl bg-sign-soft p-4 sm:gap-4 sm:p-5">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white text-sm font-semibold text-sign-primary sm:h-9 sm:w-9">3</div>
                            <p class="text-sm leading-6 text-sign-muted sm:pt-1">Review the notes again if any part is unclear before moving to practice.</p>
                        </div>
                    </div>
                </section>

                {{-- Example --}}
                <section id="lesson-example" class="scroll-mt-24 rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-8">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark sm:text-sm">Visual example</p>
                    <h2 class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-3xl">See the idea in a simple situation</h2>

                    <div class="mt-5 grid gap-4 md:grid-cols-2 sm:mt-6 sm:gap-5">
                        <div class="rounded-2xl bg-sign-primary p-5 text-white sm:p-6">
                            <p class="text-xs font-semibold uppercase tracking-wider text-sign-light">Example</p>
                            <p class="mt-3 font-heading text-lg font-semibold sm:text-xl">Step 1: Observe</p>
                            <p class="mt-3 text-sm leading-6 text-white/80">Look at the visual information and identify the important parts first.</p>
                        </div>
                        <div class="rounded-2xl bg-sign-soft p-5 sm:p-6">
                            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Then</p>
                            <p class="mt-3 font-heading text-lg font-semibold text-sign-primary sm:text-xl">Step 2: Connect</p>
                            <p class="mt-3 text-sm leading-6 text-sign-muted">Connect what you observed with the key point explained in the lesson.</p>
                        </div>
                    </div>
                </section>

                {{-- Notes --}}
                <section id="lesson-notes" class="scroll-mt-24 rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-8">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark sm:text-sm">Simple notes</p>
                    <h2 class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-3xl">Lesson summary</h2>
                    <p class="mt-4 text-sm leading-7 text-sign-muted sm:mt-5 sm:text-base sm:leading-8">
                        This lesson introduces the core idea of <strong class="font-semibold text-sign-primary">{{ $unitTitle }}</strong>. Focus on understanding the meaning, viewing the example, and then applying the idea in the short practice below.
                    </p>
                    <div class="mt-5 rounded-2xl border-l-4 border-sign-cyan bg-sign-soft p-4 sm:p-5">
                        <p class="text-sm font-semibold text-sign-primary">Quick reminder</p>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">You can replay the ISL video and reread these notes as many times as you need.</p>
                    </div>
                </section>

                {{-- Practice --}}
                <section id="lesson-practice" class="scroll-mt-24 rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl sm:p-8">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark sm:text-sm">Quick practice</p>
                            <h2 class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-3xl">Check your understanding</h2>
                        </div>
                        <span class="w-fit rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-sign-primary">Practice preview</span>
                    </div>

                    <div class="mt-5 rounded-2xl bg-white p-4 sm:mt-6 sm:p-6">
                        <p class="text-sm font-semibold leading-7 text-sign-primary sm:text-base">What should you do first when learning a new concept?</p>
                        <div class="mt-4 grid gap-3">
                            <button type="button" class="min-h-11 rounded-xl border border-sign-border px-4 py-3 text-left text-sm leading-6 text-sign-muted transition hover:border-sign-cyan hover:bg-sign-soft">A. Memorise everything immediately</button>
                            <button type="button" class="min-h-11 rounded-xl border border-sign-border px-4 py-3 text-left text-sm leading-6 text-sign-muted transition hover:border-sign-cyan hover:bg-sign-soft">B. Understand the main idea and meaning</button>
                            <button type="button" class="min-h-11 rounded-xl border border-sign-border px-4 py-3 text-left text-sm leading-6 text-sign-muted transition hover:border-sign-cyan hover:bg-sign-soft">C. Skip the examples</button>
                        </div>
                    </div>
                </section>

                {{-- Previous / Next Navigation --}}
                <nav class="grid gap-3 border-t border-sign-border pt-6 md:grid-cols-2 sm:gap-4 sm:pt-8" aria-label="Lesson sequence">
                    @if ($previousLesson)
                        <a href="{{ $previousUrl }}" class="group min-h-28 rounded-2xl border border-sign-border bg-white p-4 transition hover:border-sign-cyan hover:shadow-sm sm:p-5">
                            <p class="text-xs font-semibold uppercase tracking-wider text-sign-muted">← Previous lesson</p>
                            <p class="mt-2 font-heading text-base font-semibold text-sign-primary group-hover:text-sign-cyan-dark sm:text-lg">{{ $previousLesson['title'] }}</p>
                            <p class="mt-1 text-sm text-sign-muted">Unit {{ $previousLesson['unit'] }} · Lesson {{ $previousLesson['lesson'] }}</p>
                        </a>
                    @else
                        <div class="min-h-28 rounded-2xl border border-dashed border-sign-border bg-sign-soft p-4 sm:p-5">
                            <p class="text-xs font-semibold uppercase tracking-wider text-sign-muted">Start of course</p>
                            <p class="mt-2 font-heading text-base font-semibold text-sign-primary sm:text-lg">You are on the first lesson.</p>
                        </div>
                    @endif

                    @if ($nextLesson)
                        <a href="{{ $nextUrl }}" class="group min-h-28 rounded-2xl border border-sign-primary bg-sign-primary p-4 text-white transition hover:bg-sign-dark sm:p-5">
                            <p class="text-xs font-semibold uppercase tracking-wider text-white/70">Next lesson →</p>
                            <p class="mt-2 font-heading text-base font-semibold sm:text-lg">{{ $nextLesson['title'] }}</p>
                            <p class="mt-1 text-sm text-white/70">Unit {{ $nextLesson['unit'] }} · Lesson {{ $nextLesson['lesson'] }}</p>
                        </a>
                    @else
                        <a href="{{ $courseUrl }}#course-curriculum" class="min-h-28 rounded-2xl border border-sign-primary bg-sign-primary p-4 text-white transition hover:bg-sign-dark sm:p-5">
                            <p class="text-xs font-semibold uppercase tracking-wider text-white/70">Course complete</p>
                            <p class="mt-2 font-heading text-base font-semibold sm:text-lg">Return to course curriculum</p>
                            <p class="mt-1 text-sm text-white/70">Review any unit or lesson again.</p>
                        </a>
                    @endif
                </nav>
            </div>

            <aside class="grid gap-4 md:grid-cols-2 xl:sticky xl:top-24 xl:block xl:space-y-5" aria-label="Lesson navigation">
                <div class="rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Your position</p>
                        <span class="text-xs font-semibold text-sign-primary">{{ $progressPercent }}%</span>
                    </div>
                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-white">
                        <div class="h-full rounded-full bg-sign-primary" style="width: {{ $progressPercent }}%"></div>
                    </div>
                    <p class="mt-3 text-sm font-semibold text-sign-primary">Lesson {{ $currentPosition }} of {{ $totalLessons }}</p>
                    <p class="mt-1 text-xs leading-5 text-sign-muted">Unit {{ $unitNumber }} · {{ $unitTitle }}</p>
                </div>

                <div class="rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Lesson contents</p>
                    <nav class="mt-4 grid grid-cols-2 gap-1 text-sm sm:grid-cols-3 md:grid-cols-2 xl:block xl:space-y-1">
                        <a href="#lesson-video" class="block rounded-xl px-3 py-2.5 font-semibold text-sign-primary transition hover:bg-sign-soft">Watch lesson</a>
                        <a href="#lesson-goals" class="block rounded-xl px-3 py-2.5 text-sign-muted transition hover:bg-sign-soft hover:text-sign-primary">Learning goals</a>
                        <a href="#key-points" class="block rounded-xl px-3 py-2.5 text-sign-muted transition hover:bg-sign-soft hover:text-sign-primary">Key points</a>
                        <a href="#lesson-example" class="block rounded-xl px-3 py-2.5 text-sign-muted transition hover:bg-sign-soft hover:text-sign-primary">Visual example</a>
                        <a href="#lesson-notes" class="block rounded-xl px-3 py-2.5 text-sign-muted transition hover:bg-sign-soft hover:text-sign-primary">Simple notes</a>
                        <a href="#lesson-practice" class="block rounded-xl px-3 py-2.5 text-sign-muted transition hover:bg-sign-soft hover:text-sign-primary">Quick practice</a>
                    </nav>
                </div>

                <div class="rounded-2xl bg-sign-dark p-5 text-white md:col-span-2 sm:rounded-3xl xl:col-auto">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan">Course</p>
                    <h2 class="mt-2 font-heading text-xl font-semibold">{{ $course['title'] }}</h2>
                    <p class="mt-2 text-sm leading-6 text-white/70">{{ $course['units'] }} units · {{ $course['lessons'] }} lessons</p>
                    <a href="{{ $courseUrl }}#course-curriculum" class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">
                        All lessons
                    </a>
                </div>
            </aside>
        </div>
    </x-container>
</section>