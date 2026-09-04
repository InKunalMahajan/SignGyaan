@php
    $editing = isset($course);
@endphp

@if ($errors->any())
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3" role="alert" aria-live="polite">
        <p class="text-sm font-semibold text-red-800">Please check the course details.</p>
        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_19rem] xl:items-start">
    <div class="space-y-6">
        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="course-main-details">
            <h2 id="course-main-details" class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Course details</h2>
            <p class="mt-2 text-sm leading-6 text-sign-muted">Choose the subject, title, URL slug and learner-facing course information.</p>

            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="subject_id" class="mb-2 block text-sm font-semibold text-sign-primary">Subject</label>
                    <select
                        id="subject_id"
                        name="subject_id"
                        required
                        class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light"
                    >
                        <option value="">Select a subject</option>
                        @foreach ($subjects as $subjectOption)
                            <option value="{{ $subjectOption->id }}" @selected((string) old('subject_id', $course->subject_id ?? '') === (string) $subjectOption->id)>
                                {{ $subjectOption->name }}{{ $subjectOption->is_published ? '' : ' — Draft subject' }}
                            </option>
                        @endforeach
                    </select>
                    @error('subject_id')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="title" class="mb-2 block text-sm font-semibold text-sign-primary">Course title</label>
                    <input
                        id="title"
                        name="title"
                        type="text"
                        value="{{ old('title', $course->title ?? '') }}"
                        maxlength="160"
                        required
                        autofocus
                        @class([
                            'min-h-12 w-full rounded-xl border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light',
                            'border-red-300' => $errors->has('title'),
                            'border-sign-border' => ! $errors->has('title'),
                        ])
                    >
                    @error('title')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="slug" class="mb-2 block text-sm font-semibold text-sign-primary">URL slug</label>
                    <input
                        id="slug"
                        name="slug"
                        type="text"
                        value="{{ old('slug', $course->slug ?? '') }}"
                        maxlength="180"
                        spellcheck="false"
                        placeholder="Leave blank to create from title"
                        @class([
                            'min-h-12 w-full rounded-xl border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light',
                            'border-red-300' => $errors->has('slug'),
                            'border-sign-border' => ! $errors->has('slug'),
                        ])
                    >
                    <p class="mt-2 text-xs leading-5 text-sign-muted">Example: <span class="font-semibold text-sign-primary">computer-basics</span>.</p>
                    @error('slug')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="level" class="mb-2 block text-sm font-semibold text-sign-primary">Learning level</label>
                    <select id="level" name="level" required class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                        @foreach (['Beginner', 'Intermediate', 'Advanced'] as $level)
                            <option value="{{ $level }}" @selected(old('level', $course->level ?? 'Beginner') === $level)>{{ $level }}</option>
                        @endforeach
                    </select>
                    @error('level')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="short_description" class="mb-2 block text-sm font-semibold text-sign-primary">Short description</label>
                    <textarea
                        id="short_description"
                        name="short_description"
                        rows="3"
                        maxlength="255"
                        class="w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light"
                        placeholder="Short summary shown on course cards."
                    >{{ old('short_description', $course->short_description ?? '') }}</textarea>
                    @error('short_description')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="mb-2 block text-sm font-semibold text-sign-primary">Full description</label>
                    <textarea
                        id="description"
                        name="description"
                        rows="7"
                        maxlength="5000"
                        class="w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light"
                        placeholder="Explain the course goals, approach and what learners will study."
                    >{{ old('description', $course->description ?? '') }}</textarea>
                    @error('description')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>
    </div>

    <aside class="space-y-5 xl:sticky xl:top-24" aria-label="Course publishing settings">
        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Publishing</p>

            <label class="mt-4 flex cursor-pointer items-start gap-3 rounded-xl bg-sign-soft p-4">
                <input
                    type="checkbox"
                    name="is_published"
                    value="1"
                    @checked(old('is_published', $course->is_published ?? true))
                    class="mt-0.5 h-5 w-5 shrink-0 rounded border-sign-border accent-sign-primary"
                >
                <span>
                    <span class="block text-sm font-semibold text-sign-primary">Published</span>
                    <span class="mt-1 block text-xs leading-5 text-sign-muted">Turn off to keep this course as a draft.</span>
                </span>
            </label>

            <label class="mt-3 flex cursor-pointer items-start gap-3 rounded-xl bg-sign-soft p-4">
                <input
                    type="checkbox"
                    name="is_featured"
                    value="1"
                    @checked(old('is_featured', $course->is_featured ?? false))
                    class="mt-0.5 h-5 w-5 shrink-0 rounded border-sign-border accent-sign-primary"
                >
                <span>
                    <span class="block text-sm font-semibold text-sign-primary">Featured course</span>
                    <span class="mt-1 block text-xs leading-5 text-sign-muted">Mark this course for featured learning sections.</span>
                </span>
            </label>
        </section>

        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Organisation</p>

            <div class="mt-4">
                <label for="sort_order" class="mb-2 block text-sm font-semibold text-sign-primary">Display order</label>
                <input
                    id="sort_order"
                    name="sort_order"
                    type="number"
                    min="0"
                    max="9999"
                    value="{{ old('sort_order', $course->sort_order ?? 0) }}"
                    required
                    class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light"
                >
                <p class="mt-2 text-xs leading-5 text-sign-muted">Lower numbers appear first within the subject.</p>
                @error('sort_order')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
            </div>

            <div class="mt-5">
                <label for="estimated_duration_minutes" class="mb-2 block text-sm font-semibold text-sign-primary">Estimated duration</label>
                <div class="relative">
                    <input
                        id="estimated_duration_minutes"
                        name="estimated_duration_minutes"
                        type="number"
                        min="1"
                        max="100000"
                        value="{{ old('estimated_duration_minutes', $course->estimated_duration_minutes ?? '') }}"
                        placeholder="Optional"
                        class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 pr-20 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light"
                    >
                    <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-xs font-semibold text-sign-muted">minutes</span>
                </div>
                @error('estimated_duration_minutes')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
            </div>
        </section>

        <div class="grid gap-2">
            <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">
                {{ $editing ? 'Save Changes' : 'Create Course' }}
            </button>
            <a href="{{ route('admin.courses.index') }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">
                Cancel
            </a>
        </div>
    </aside>
</div>
