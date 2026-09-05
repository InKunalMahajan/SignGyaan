@php
    $editing = isset($assessment);
    $selectedPractice = old('practice_resource_id', $assessment->practice_resource_id ?? $selectedPracticeId ?? null);
@endphp

@if ($errors->any())
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3" role="alert" aria-live="polite">
        <p class="text-sm font-semibold text-red-800">Please check the assessment settings.</p>
        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_20rem] xl:items-start">
    <div class="space-y-6">
        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="assessment-source-heading">
            <h2 id="assessment-source-heading" class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Assessment source</h2>
            <p class="mt-2 text-sm leading-6 text-sign-muted">Each assessment belongs to one Practice item. Only Quiz and Exercise items can be used.</p>

            <div class="mt-6">
                <label for="practice_resource_id" class="mb-2 block text-sm font-semibold text-sign-primary">Quiz or Exercise practice item</label>
                <select id="practice_resource_id" name="practice_resource_id" required class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                    <option value="">Select a practice item</option>
                    @foreach ($practiceResources as $practice)
                        <option value="{{ $practice->id }}" @selected((string) $selectedPractice === (string) $practice->id)>
                            {{ $practice->lesson?->unit?->course?->subject?->name ?? 'No subject' }} — {{ $practice->lesson?->unit?->course?->title ?? 'No course' }} — {{ $practice->lesson?->title ?? 'No lesson' }} — {{ $practice->title }} ({{ ucfirst($practice->resource_type) }})
                        </option>
                    @endforeach
                </select>
                @if ($practiceResources->isEmpty())
                    <div class="mt-3 rounded-xl bg-sign-soft p-4 text-sm leading-6 text-sign-muted">
                        No eligible practice items are available. Create a Practice item with type <strong class="text-sign-primary">Quiz</strong> or <strong class="text-sign-primary">Exercise</strong> first.
                        <a href="{{ route('admin.practice.create') }}" class="ml-1 font-semibold text-sign-primary hover:text-sign-cyan-dark">Create practice item →</a>
                    </div>
                @else
                    <p class="mt-2 text-xs leading-5 text-sign-muted">Items already connected to another assessment are not listed.</p>
                @endif
                @error('practice_resource_id')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
            </div>
        </section>

        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="assessment-rules-heading">
            <h2 id="assessment-rules-heading" class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Scoring & attempt rules</h2>
            <p class="mt-2 text-sm leading-6 text-sign-muted">Set the pass mark and optional limits. Automatic scoring will use these settings when the learner quiz player is connected.</p>

            <div class="mt-6 grid gap-5 md:grid-cols-3">
                <div>
                    <label for="passing_percentage" class="mb-2 block text-sm font-semibold text-sign-primary">Passing percentage</label>
                    <div class="relative">
                        <input id="passing_percentage" name="passing_percentage" type="number" min="0" max="100" value="{{ old('passing_percentage', $assessment->passing_percentage ?? 70) }}" required class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 pr-10 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                        <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-sign-muted">%</span>
                    </div>
                    @error('passing_percentage')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="max_attempts" class="mb-2 block text-sm font-semibold text-sign-primary">Maximum attempts</label>
                    <input id="max_attempts" name="max_attempts" type="number" min="1" max="1000" value="{{ old('max_attempts', $assessment->max_attempts ?? '') }}" placeholder="Unlimited" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                    <p class="mt-2 text-xs leading-5 text-sign-muted">Leave blank for unlimited attempts.</p>
                    @error('max_attempts')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="time_limit_minutes" class="mb-2 block text-sm font-semibold text-sign-primary">Time limit (minutes)</label>
                    <input id="time_limit_minutes" name="time_limit_minutes" type="number" min="1" max="1440" value="{{ old('time_limit_minutes', $assessment->time_limit_minutes ?? '') }}" placeholder="No limit" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                    <p class="mt-2 text-xs leading-5 text-sign-muted">Leave blank when the assessment is untimed.</p>
                    @error('time_limit_minutes')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="assessment-behaviour-heading">
            <h2 id="assessment-behaviour-heading" class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Learner experience</h2>
            <p class="mt-2 text-sm leading-6 text-sign-muted">Choose how questions appear and whether learners can review feedback after submission.</p>

            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-sign-border bg-sign-soft p-4">
                    <input type="checkbox" name="shuffle_questions" value="1" @checked(old('shuffle_questions', $assessment->shuffle_questions ?? false)) class="mt-0.5 h-5 w-5 shrink-0 rounded border-sign-border accent-sign-primary">
                    <span>
                        <span class="block text-sm font-semibold text-sign-primary">Shuffle questions</span>
                        <span class="mt-1 block text-xs leading-5 text-sign-muted">Use a different question order for each attempt.</span>
                    </span>
                </label>

                <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-sign-border bg-sign-soft p-4">
                    <input type="checkbox" name="shuffle_options" value="1" @checked(old('shuffle_options', $assessment->shuffle_options ?? false)) class="mt-0.5 h-5 w-5 shrink-0 rounded border-sign-border accent-sign-primary">
                    <span>
                        <span class="block text-sm font-semibold text-sign-primary">Shuffle options</span>
                        <span class="mt-1 block text-xs leading-5 text-sign-muted">Randomise answer option order where applicable.</span>
                    </span>
                </label>

                <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-sign-border bg-sign-soft p-4">
                    <input type="checkbox" name="show_feedback" value="1" @checked(old('show_feedback', $assessment->show_feedback ?? true)) class="mt-0.5 h-5 w-5 shrink-0 rounded border-sign-border accent-sign-primary">
                    <span>
                        <span class="block text-sm font-semibold text-sign-primary">Show feedback</span>
                        <span class="mt-1 block text-xs leading-5 text-sign-muted">Allow learners to review explanations after submission.</span>
                    </span>
                </label>
            </div>
        </section>

        @if ($editing)
            <section class="rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl sm:p-7" aria-labelledby="assessment-questions-heading">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Next build step</p>
                        <h2 id="assessment-questions-heading" class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Questions & answer options</h2>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">This assessment currently has {{ $assessment->questions_count ?? 0 }} {{ ($assessment->questions_count ?? 0) === 1 ? 'question' : 'questions' }}. The visual question builder is added in Step 9C.</p>
                    </div>
                    <span class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-2.5 text-sm font-semibold text-sign-muted">Question Builder — Step 9C</span>
                </div>
            </section>
        @endif
    </div>

    <aside class="space-y-5 xl:sticky xl:top-24" aria-label="Assessment publishing settings">
        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Publishing</p>

            <label class="mt-4 flex cursor-pointer items-start gap-3 rounded-xl bg-sign-soft p-4">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $assessment->is_published ?? false)) class="mt-0.5 h-5 w-5 shrink-0 rounded border-sign-border accent-sign-primary">
                <span>
                    <span class="block text-sm font-semibold text-sign-primary">Published</span>
                    <span class="mt-1 block text-xs leading-5 text-sign-muted">Keep new assessments as drafts until their questions are ready.</span>
                </span>
            </label>

            @if ($editing)
                <div class="mt-5 space-y-3 text-sm">
                    <div class="flex items-center justify-between rounded-xl bg-sign-soft px-4 py-3">
                        <span class="text-sign-muted">Questions</span>
                        <span class="font-semibold text-sign-primary">{{ $assessment->questions_count ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl bg-sign-soft px-4 py-3">
                        <span class="text-sign-muted">Learner attempts</span>
                        <span class="font-semibold text-sign-primary">{{ $assessment->attempts_count ?? 0 }}</span>
                    </div>
                </div>
            @endif
        </section>

        <div class="grid gap-2">
            <button type="submit" @disabled($practiceResources->isEmpty() && ! $editing) class="inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark disabled:cursor-not-allowed disabled:opacity-50">{{ $editing ? 'Save Assessment' : 'Create Assessment' }}</button>
            <a href="{{ route('admin.assessments.index') }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Cancel</a>
        </div>
    </aside>
</div>
