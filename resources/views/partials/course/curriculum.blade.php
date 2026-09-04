@php
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
@endphp

<section id="course-curriculum" class="scroll-mt-24 bg-white py-10 sm:py-14 lg:py-20">
    <x-container>
        <div class="grid gap-6 md:grid-cols-[1fr_auto] md:items-end lg:gap-8">
            <x-section-heading
                title="Units & lessons"
                description="Follow the course in order, open each unit, and select any lesson to start learning."
            />

            <div class="flex flex-wrap gap-2 text-xs sm:gap-3 sm:text-sm md:justify-end">
                <span class="rounded-full bg-sign-soft px-3 py-2 font-semibold text-sign-primary sm:px-4">{{ $course['units'] }} Units</span>
                <span class="rounded-full bg-sign-soft px-3 py-2 font-semibold text-sign-primary sm:px-4">{{ $course['lessons'] }} Lessons</span>
            </div>
        </div>

        <div class="mt-8 grid gap-6 sm:mt-10 xl:grid-cols-[minmax(0,1fr)_18rem] xl:items-start xl:gap-8">
            <div class="min-w-0 space-y-3 sm:space-y-4">
                @for ($unit = 1; $unit <= $unitCount; $unit++)
                    @php
                        $lessonsInUnit = $baseLessons + ($unit <= $extraLessons ? 1 : 0);
                        $unitTitle = $unitTitles[$unit - 1] ?? 'Learning Unit ' . $unit;
                    @endphp

                    <div x-data="{ open: {{ $unit === 1 ? 'true' : 'false' }} }" class="overflow-hidden rounded-2xl border border-sign-border bg-white sm:rounded-3xl">
                        <button
                            type="button"
                            @click="open = ! open"
                            class="flex min-h-16 w-full items-center gap-3 px-4 py-4 text-left transition hover:bg-sign-soft sm:gap-4 sm:px-6 sm:py-5"
                            :aria-expanded="open"
                        >
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sign-light text-xs font-bold text-sign-primary sm:h-11 sm:w-11 sm:rounded-2xl sm:text-sm">
                                {{ str_pad((string) $unit, 2, '0', STR_PAD_LEFT) }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] font-semibold uppercase tracking-wider text-sign-cyan-dark sm:text-xs">Unit {{ $unit }}</p>
                                <h3 class="mt-1 font-heading text-base font-semibold leading-snug text-sign-primary sm:text-xl">{{ $unitTitle }}</h3>
                                <p class="mt-1 text-xs text-sign-muted sm:text-sm">{{ $lessonsInUnit }} {{ $lessonsInUnit === 1 ? 'lesson' : 'lessons' }}</p>
                            </div>

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                                class="h-5 w-5 shrink-0 text-sign-primary transition-transform duration-200"
                                :class="open ? 'rotate-180' : ''"
                                aria-hidden="true"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            class="border-t border-sign-border"
                        >
                            <div class="divide-y divide-sign-border">
                                @for ($lesson = 1; $lesson <= $lessonsInUnit; $lesson++)
                                    @php
                                        $lessonName = $lessonNames[($lesson - 1) % count($lessonNames)];
                                        $lessonKey = 'unit-' . $unit . '-lesson-' . $lesson;
                                        $lessonUrl = route('courses.show', [
                                            'subject' => $subjectSlug,
                                            'course' => $courseSlug,
                                            'lesson' => $lessonKey,
                                        ]);
                                    @endphp

                                    <a href="{{ $lessonUrl }}" class="group flex min-h-14 gap-3 px-4 py-4 transition hover:bg-sign-soft sm:gap-4 sm:px-6">
                                        <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sign-soft text-sign-primary transition group-hover:bg-white sm:h-9 sm:w-9 sm:rounded-xl">
                                            @if ($lesson === $lessonsInUnit)
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h8.25A3.75 3.75 0 0 0 16.5 15V9a3.75 3.75 0 0 0-3.75-3.75H4.5A2.25 2.25 0 0 0 2.25 7.5v9A2.25 2.25 0 0 0 4.5 18.75Z" />
                                                </svg>
                                            @endif
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between sm:gap-3">
                                                <div class="min-w-0">
                                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-sign-muted sm:text-xs">Lesson {{ $lesson }}</p>
                                                    <h4 class="mt-1 text-sm font-semibold leading-6 text-sign-primary transition group-hover:text-sign-cyan-dark sm:text-base">
                                                        {{ $lessonName }}: {{ $unitTitle }}
                                                    </h4>
                                                </div>

                                                <div class="flex shrink-0 flex-wrap items-center gap-1.5 sm:gap-2">
                                                    @if ($lesson % 2 === 1)
                                                        <span class="rounded-full bg-sign-light px-2.5 py-1 text-[10px] font-semibold text-sign-primary sm:text-[11px]">ISL</span>
                                                    @endif
                                                    <span class="rounded-full bg-sign-soft px-2.5 py-1 text-[10px] font-semibold text-sign-muted sm:text-[11px]">Notes</span>
                                                    @if ($lesson === $lessonsInUnit)
                                                        <span class="rounded-full bg-sign-soft px-2.5 py-1 text-[10px] font-semibold text-sign-muted sm:text-[11px]">Practice</span>
                                                    @endif
                                                    <span class="ml-1 text-sign-primary transition group-hover:translate-x-1" aria-hidden="true">→</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endfor
                            </div>
                        </div>
                    </div>
                @endfor
            </div>

            <aside class="rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl xl:sticky xl:top-28" aria-label="Course roadmap">
                <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Course roadmap</p>
                <h3 class="mt-2 font-heading text-xl font-semibold text-sign-primary">Learn in sequence</h3>
                <p class="mt-3 text-sm leading-6 text-sign-muted">Choose any lesson or begin with Unit 1. Previous and Next controls will guide you through the whole course.</p>

                <div class="mt-5 grid gap-3 sm:grid-cols-3 xl:grid-cols-1">
                    <div class="flex items-center gap-3 rounded-2xl bg-white p-3">
                        <div class="h-2.5 w-2.5 rounded-full bg-sign-cyan"></div>
                        <span class="text-sm font-semibold text-sign-primary">Watch the concept</span>
                    </div>
                    <div class="flex items-center gap-3 rounded-2xl bg-white p-3">
                        <div class="h-2.5 w-2.5 rounded-full bg-sign-cyan"></div>
                        <span class="text-sm font-semibold text-sign-primary">Read the key notes</span>
                    </div>
                    <div class="flex items-center gap-3 rounded-2xl bg-white p-3">
                        <div class="h-2.5 w-2.5 rounded-full bg-sign-cyan"></div>
                        <span class="text-sm font-semibold text-sign-primary">Complete practice</span>
                    </div>
                </div>

                <a href="{{ route('courses.show', ['subject' => $subjectSlug, 'course' => $courseSlug, 'lesson' => 'unit-1-lesson-1']) }}" class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-sign-primary px-4 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">
                    Start first lesson
                </a>
            </aside>
        </div>
    </x-container>
</section>