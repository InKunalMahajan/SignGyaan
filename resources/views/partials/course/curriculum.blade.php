@php
    $unitTitlesBySubject = [
        'english' => [
            'Getting Started',
            'Vocabulary & Meaning',
            'Grammar & Sentences',
            'Reading & Communication',
            'Review & Practice',
        ],
        'mathematics' => [
            'Number Foundations',
            'Everyday Calculations',
            'Money & Measurement',
            'Problem Solving',
            'Review & Practice',
        ],
        'science' => [
            'Scientific Thinking',
            'Matter & Materials',
            'Energy & Change',
            'Living World',
            'Review & Practice',
        ],
        'digital-skills' => [
            'Computer Foundations',
            'Files, Folders & Software',
            'Internet & Digital Safety',
            'Practical Digital Tools',
            'Review & Practice',
        ],
        'general-knowledge' => [
            'India Basics',
            'World Awareness',
            'Society & Everyday Services',
            'People, Places & Events',
            'Review & Practice',
        ],
        'life-skills' => [
            'Daily Routines',
            'Communication Skills',
            'Time & Organisation',
            'Confidence & Independence',
            'Review & Practice',
        ],
    ];

    $lessonNames = [
        'Introduction',
        'Key Ideas',
        'Visual Explanation',
        'Examples',
        'Guided Practice',
        'Quick Check',
        'Review',
    ];

    $unitTitles = $unitTitlesBySubject[$subjectSlug] ?? [
        'Getting Started',
        'Core Concepts',
        'Practical Learning',
        'Review & Practice',
        'Next Steps',
    ];

    $unitCount = max(1, (int) $course['units']);
    $lessonCount = max($unitCount, (int) $course['lessons']);
    $baseLessons = intdiv($lessonCount, $unitCount);
    $extraLessons = $lessonCount % $unitCount;
@endphp

<section id="course-curriculum" class="scroll-mt-24 bg-white py-14 sm:py-16 lg:py-20">
    <x-container>
        <div class="grid gap-8 lg:grid-cols-[1fr_auto] lg:items-end">
            <x-section-heading
                title="Units & lessons"
                description="Follow the course in order, open each unit, and see the lessons you will complete inside it."
            />

            <div class="flex flex-wrap gap-3 text-sm">
                <span class="rounded-full bg-sign-soft px-4 py-2 font-semibold text-sign-primary">
                    {{ $course['units'] }} Units
                </span>
                <span class="rounded-full bg-sign-soft px-4 py-2 font-semibold text-sign-primary">
                    {{ $course['lessons'] }} Lessons
                </span>
            </div>
        </div>

        <div class="mt-10 grid gap-8 lg:grid-cols-[1fr_18rem] lg:items-start">
            <div class="space-y-4">
                @for ($unit = 1; $unit <= $unitCount; $unit++)
                    @php
                        $lessonsInUnit = $baseLessons + ($unit <= $extraLessons ? 1 : 0);
                        $unitTitle = $unitTitles[$unit - 1] ?? 'Learning Unit ' . $unit;
                    @endphp

                    <div
                        x-data="{ open: {{ $unit === 1 ? 'true' : 'false' }} }"
                        class="overflow-hidden rounded-3xl border border-sign-border bg-white"
                    >
                        <button
                            type="button"
                            @click="open = ! open"
                            class="flex w-full items-center gap-4 px-5 py-5 text-left transition hover:bg-sign-soft sm:px-6"
                            :aria-expanded="open"
                        >
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-sign-light text-sm font-bold text-sign-primary">
                                {{ str_pad((string) $unit, 2, '0', STR_PAD_LEFT) }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">
                                    Unit {{ $unit }}
                                </p>
                                <h3 class="mt-1 font-heading text-lg font-semibold text-sign-primary sm:text-xl">
                                    {{ $unitTitle }}
                                </h3>
                                <p class="mt-1 text-sm text-sign-muted">
                                    {{ $lessonsInUnit }} {{ Str::plural('lesson', $lessonsInUnit) }}
                                </p>
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

                        <div x-show="open" x-collapse class="border-t border-sign-border">
                            <div class="divide-y divide-sign-border">
                                @for ($lesson = 1; $lesson <= $lessonsInUnit; $lesson++)
                                    @php
                                        $lessonName = $lessonNames[($lesson - 1) % count($lessonNames)];
                                    @endphp

                                    <div class="flex gap-4 px-5 py-4 sm:px-6">
                                        <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-sign-soft text-sign-primary">
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
                                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                                <div>
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-sign-muted">
                                                        Lesson {{ $lesson }}
                                                    </p>
                                                    <h4 class="mt-1 font-semibold text-sign-primary">
                                                        {{ $lessonName }}: {{ $unitTitle }}
                                                    </h4>
                                                </div>

                                                <div class="flex flex-wrap gap-2">
                                                    @if ($lesson % 2 === 1)
                                                        <span class="rounded-full bg-sign-light px-2.5 py-1 text-[11px] font-semibold text-sign-primary">ISL</span>
                                                    @endif
                                                    <span class="rounded-full bg-sign-soft px-2.5 py-1 text-[11px] font-semibold text-sign-muted">Notes</span>
                                                    @if ($lesson === $lessonsInUnit)
                                                        <span class="rounded-full bg-sign-soft px-2.5 py-1 text-[11px] font-semibold text-sign-muted">Practice</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                @endfor
            </div>

            <aside class="rounded-3xl border border-sign-border bg-sign-soft p-5 lg:sticky lg:top-28" aria-label="Course roadmap">
                <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Course roadmap</p>
                <h3 class="mt-2 font-heading text-xl font-semibold text-sign-primary">Learn in sequence</h3>
                <p class="mt-3 text-sm leading-6 text-sign-muted">
                    Complete one lesson at a time. You can reopen earlier units whenever you want to revise.
                </p>

                <div class="mt-5 space-y-3">
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

                <a
                    href="#course-overview"
                    class="mt-5 inline-flex w-full items-center justify-center rounded-xl border border-sign-primary px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-white"
                >
                    Back to overview
                </a>
            </aside>
        </div>
    </x-container>
</section>
