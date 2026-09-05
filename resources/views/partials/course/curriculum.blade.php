<section id="course-curriculum" class="scroll-mt-24 bg-white py-10 sm:py-14 lg:py-20">
    <x-container>
        <div class="grid gap-6 md:grid-cols-[1fr_auto] md:items-end lg:gap-8">
            <x-section-heading
                title="Units & lessons"
                description="Follow the published course structure created in SignGyaan Admin and move through each lesson in order."
            />

            <div class="flex flex-wrap gap-2 text-xs sm:gap-3 sm:text-sm md:justify-end" aria-label="Course curriculum totals">
                <span class="rounded-full bg-sign-soft px-3 py-2 font-semibold text-sign-primary sm:px-4">
                    {{ $course['units'] }} {{ $course['units'] === 1 ? 'Unit' : 'Units' }}
                </span>
                <span class="rounded-full bg-sign-soft px-3 py-2 font-semibold text-sign-primary sm:px-4">
                    {{ $course['lessons'] }} {{ $course['lessons'] === 1 ? 'Lesson' : 'Lessons' }}
                </span>
            </div>
        </div>

        @if ($publishedUnits->isNotEmpty())
            <div class="mt-8 grid gap-6 sm:mt-10 xl:grid-cols-[minmax(0,1fr)_18rem] xl:items-start xl:gap-8">
                <div class="min-w-0 space-y-3 sm:space-y-4">
                    @foreach ($publishedUnits as $unitIndex => $unit)
                        @php
                            $unitNumber = $unitIndex + 1;
                            $publishedLessons = $unit->lessons;
                            $unitPanelId = 'course-unit-panel-' . $unit->id;
                        @endphp

                        <section
                            x-data="{ open: {{ $unitIndex === 0 ? 'true' : 'false' }} }"
                            class="overflow-hidden rounded-2xl border border-sign-border bg-white sm:rounded-3xl"
                            aria-labelledby="course-unit-heading-{{ $unit->id }}"
                        >
                            <button
                                type="button"
                                @click="open = ! open"
                                class="flex min-h-16 w-full items-center gap-3 px-4 py-4 text-left transition hover:bg-sign-soft sm:gap-4 sm:px-6 sm:py-5"
                                :aria-expanded="open"
                                aria-controls="{{ $unitPanelId }}"
                            >
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sign-light text-xs font-bold text-sign-primary sm:h-11 sm:w-11 sm:rounded-2xl sm:text-sm" aria-hidden="true">
                                    {{ str_pad((string) $unitNumber, 2, '0', STR_PAD_LEFT) }}
                                </span>

                                <span class="min-w-0 flex-1">
                                    <span class="block text-[11px] font-semibold uppercase tracking-wider text-sign-cyan-dark sm:text-xs">Unit {{ $unitNumber }}</span>
                                    <span id="course-unit-heading-{{ $unit->id }}" class="mt-1 block font-heading text-base font-semibold leading-snug text-sign-primary sm:text-xl">
                                        {{ $unit->title }}
                                    </span>
                                    <span class="mt-1 block text-xs leading-5 text-sign-muted sm:text-sm">
                                        {{ $publishedLessons->count() }} {{ $publishedLessons->count() === 1 ? 'lesson' : 'lessons' }}
                                        @if ($unit->estimated_duration_minutes)
                                            · {{ $unit->estimated_duration_minutes }} min
                                        @endif
                                    </span>
                                </span>

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
                                id="{{ $unitPanelId }}"
                                x-show="open"
                                x-cloak
                                class="border-t border-sign-border"
                            >
                                @if ($unit->short_description || $unit->description)
                                    <div class="bg-sign-soft/60 px-4 py-4 sm:px-6">
                                        <p class="max-w-3xl text-sm leading-6 text-sign-muted">
                                            {{ $unit->short_description ?: $unit->description }}
                                        </p>
                                    </div>
                                @endif

                                @if ($publishedLessons->isNotEmpty())
                                    <div class="divide-y divide-sign-border">
                                        @foreach ($publishedLessons as $lessonIndex => $lesson)
                                            @php
                                                $lessonNumber = $lessonIndex + 1;
                                                $lessonKey = 'unit-' . $unitNumber . '-lesson-' . $lessonNumber;
                                                $lessonUrl = route('courses.show', [
                                                    'subject' => $subjectSlug,
                                                    'course' => $courseSlug,
                                                    'lesson' => $lessonKey,
                                                ]);
                                                $hasNotes = filled($lesson->content) || filled($lesson->key_points) || filled($lesson->learning_objectives);
                                                $hasIslVideo = filled($lesson->isl_video_url)
                                                    || ($lesson->relationLoaded('mediaAsset')
                                                        && $lesson->mediaAsset
                                                        && $lesson->mediaAsset->is_published
                                                        && $lesson->mediaAsset->media_type === 'video');
                                            @endphp

                                            <a
                                                href="{{ $lessonUrl }}"
                                                class="group flex min-h-14 gap-3 px-4 py-4 transition hover:bg-sign-soft focus-visible:bg-sign-soft sm:gap-4 sm:px-6"
                                            >
                                                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sign-soft text-sign-primary transition group-hover:bg-white sm:h-9 sm:w-9 sm:rounded-xl" aria-hidden="true">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.399 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                                                    </svg>
                                                </span>

                                                <span class="min-w-0 flex-1">
                                                    <span class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between sm:gap-3">
                                                        <span class="min-w-0">
                                                            <span class="block text-[11px] font-semibold uppercase tracking-wide text-sign-muted sm:text-xs">Lesson {{ $lessonNumber }}</span>
                                                            <span class="mt-1 block text-sm font-semibold leading-6 text-sign-primary transition group-hover:text-sign-cyan-dark sm:text-base">
                                                                {{ $lesson->title }}
                                                            </span>
                                                            @if ($lesson->short_description)
                                                                <span class="mt-1 block text-xs leading-5 text-sign-muted sm:text-sm">
                                                                    {{ $lesson->short_description }}
                                                                </span>
                                                            @endif
                                                        </span>

                                                        <span class="flex shrink-0 flex-wrap items-center gap-1.5 sm:gap-2">
                                                            @if ($hasIslVideo)
                                                                <span class="rounded-full bg-sign-light px-2.5 py-1 text-[10px] font-semibold text-sign-primary sm:text-[11px]">ISL</span>
                                                            @endif
                                                            @if ($hasNotes)
                                                                <span class="rounded-full bg-sign-soft px-2.5 py-1 text-[10px] font-semibold text-sign-muted sm:text-[11px]">Notes</span>
                                                            @endif
                                                            @if ($lesson->estimated_duration_minutes)
                                                                <span class="rounded-full bg-sign-soft px-2.5 py-1 text-[10px] font-semibold text-sign-muted sm:text-[11px]">{{ $lesson->estimated_duration_minutes }} min</span>
                                                            @endif
                                                            <span class="ml-1 text-sign-primary transition group-hover:translate-x-1" aria-hidden="true">→</span>
                                                        </span>
                                                    </span>
                                                </span>
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="px-4 py-5 sm:px-6">
                                        <p class="text-sm leading-6 text-sign-muted">No published lessons are available in this unit yet.</p>
                                    </div>
                                @endif
                            </div>
                        </section>
                    @endforeach
                </div>

                <aside class="rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl xl:sticky xl:top-28" aria-label="Course roadmap">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Course roadmap</p>
                    <h3 class="mt-2 font-heading text-xl font-semibold text-sign-primary">Learn in sequence</h3>
                    <p class="mt-3 text-sm leading-6 text-sign-muted">The curriculum shown here comes directly from published units and lessons in the SignGyaan Admin Console.</p>

                    <div class="mt-5 grid gap-3 sm:grid-cols-3 xl:grid-cols-1">
                        <div class="flex items-center gap-3 rounded-2xl bg-white p-3">
                            <div class="h-2.5 w-2.5 rounded-full bg-sign-cyan" aria-hidden="true"></div>
                            <span class="text-sm font-semibold text-sign-primary">Follow units in order</span>
                        </div>
                        <div class="flex items-center gap-3 rounded-2xl bg-white p-3">
                            <div class="h-2.5 w-2.5 rounded-full bg-sign-cyan" aria-hidden="true"></div>
                            <span class="text-sm font-semibold text-sign-primary">Open each lesson</span>
                        </div>
                        <div class="flex items-center gap-3 rounded-2xl bg-white p-3">
                            <div class="h-2.5 w-2.5 rounded-full bg-sign-cyan" aria-hidden="true"></div>
                            <span class="text-sm font-semibold text-sign-primary">Review before moving on</span>
                        </div>
                    </div>

                    @if ($firstLessonKey)
                        <a href="{{ route('courses.show', ['subject' => $subjectSlug, 'course' => $courseSlug, 'lesson' => $firstLessonKey]) }}" class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-sign-primary px-4 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">
                            Start first lesson
                        </a>
                    @endif
                </aside>
            </div>
        @else
            <div class="mt-8 rounded-2xl border border-dashed border-sign-border bg-sign-soft px-5 py-10 text-center sm:mt-10 sm:rounded-3xl sm:px-8 sm:py-14">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-sign-primary shadow-sm" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25A8.966 8.966 0 0 1 18 3.75c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <h3 class="mt-4 font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Curriculum coming soon</h3>
                <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-sign-muted">This course is published, but it does not have any published units yet. Check back after the curriculum is added.</p>
            </div>
        @endif
    </x-container>
</section>