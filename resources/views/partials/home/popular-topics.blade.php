<section class="bg-white py-16 sm:py-20 lg:py-24">
    <x-container>
        <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
            <x-section-heading title="Popular topics" description="Jump into recently published lessons from the live SignGyaan catalogue." />
            <a href="{{ route('explore', ['type' => 'lesson']) }}" class="inline-flex min-h-11 items-center gap-2 text-sm font-semibold text-sign-primary transition hover:text-sign-cyan-dark">
                Explore all lessons <span aria-hidden="true">→</span>
            </a>
        </div>

        @if ($popularLessons->isNotEmpty())
            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($popularLessons as $lesson)
                    @php
                        $course = $lesson->unit->course;
                        $subject = $course->subject;
                    @endphp
                    <a href="{{ route('courses.show', ['subject' => $subject->slug, 'course' => $course->slug, 'lesson' => 'lesson-'.$lesson->id]) }}" class="group flex items-center gap-4 rounded-2xl border border-sign-border bg-white p-5 transition hover:-translate-y-0.5 hover:border-sign-cyan hover:shadow-md">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sign-light text-xs font-bold text-sign-primary" aria-hidden="true">
                            {{ strtoupper(substr($subject->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">{{ $subject->name }}</p>
                            <h3 class="mt-1 font-heading text-lg font-semibold leading-snug text-sign-primary">{{ $lesson->title }}</h3>
                            <p class="mt-1 text-sm leading-5 text-sign-muted">{{ $lesson->short_description ?: $course->title.' · '.$lesson->unit->title }}</p>
                        </div>
                        <span class="shrink-0 text-sign-primary transition group-hover:translate-x-1" aria-hidden="true">→</span>
                    </a>
                @endforeach
            </div>
        @else
            <div class="mt-10 rounded-3xl border border-dashed border-sign-border bg-sign-soft px-6 py-10 text-center">
                <h3 class="font-heading text-xl font-semibold text-sign-primary">Lessons are being prepared</h3>
                <p class="mt-2 text-sm text-sign-muted">Published lessons will appear here automatically.</p>
            </div>
        @endif

        <div class="mt-10 rounded-3xl bg-sign-primary px-6 py-8 text-white sm:flex sm:items-center sm:justify-between sm:gap-8 lg:px-10">
            <div>
                <p class="text-sm font-semibold text-sign-light">Quick learning</p>
                <h3 class="mt-2 font-heading text-2xl font-semibold sm:text-3xl">Learn one useful topic today.</h3>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-white/80">Open a published lesson directly, or explore complete courses when you want a longer learning path.</p>
            </div>
            <a href="{{ route('explore') }}" class="mt-6 inline-flex min-h-11 shrink-0 items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-light sm:mt-0">Browse Learning</a>
        </div>
    </x-container>
</section>
