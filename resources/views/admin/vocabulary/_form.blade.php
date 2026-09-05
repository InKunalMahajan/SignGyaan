@php
    $editing = isset($vocabulary);
    $selectedSubject = old('subject_id', $vocabulary->subject_id ?? '');
    $selectedCourse = old('course_id', $vocabulary->course_id ?? '');
    $selectedMedia = old('isl_media_asset_id', $vocabulary->isl_media_asset_id ?? '');
@endphp

@if ($errors->any())
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3" role="alert" aria-live="polite">
        <p class="text-sm font-semibold text-red-800">Please check the vocabulary details.</p>
        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_19rem] xl:items-start">
    <div class="space-y-6">
        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="vocabulary-basic-heading">
            <h2 id="vocabulary-basic-heading" class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Vocabulary term</h2>
            <p class="mt-2 text-sm leading-6 text-sign-muted">Create one reusable word or concept for the SignGyaan ISL vocabulary library.</p>

            <div class="mt-6 grid gap-5">
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="term" class="mb-2 block text-sm font-semibold text-sign-primary">Term</label>
                        <input id="term" name="term" type="text" value="{{ old('term', $vocabulary->term ?? '') }}" maxlength="180" required autofocus class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                        @error('term')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="slug" class="mb-2 block text-sm font-semibold text-sign-primary">URL slug</label>
                        <input id="slug" name="slug" type="text" value="{{ old('slug', $vocabulary->slug ?? '') }}" maxlength="200" placeholder="Leave blank to create from term" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                        @error('slug')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="meaning" class="mb-2 block text-sm font-semibold text-sign-primary">Meaning</label>
                    <textarea id="meaning" name="meaning" rows="6" maxlength="10000" class="w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base leading-7 text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light" placeholder="Explain the term in clear learner-friendly language.">{{ old('meaning', $vocabulary->meaning ?? '') }}</textarea>
                    @error('meaning')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="example" class="mb-2 block text-sm font-semibold text-sign-primary">Example</label>
                    <textarea id="example" name="example" rows="5" maxlength="10000" class="w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base leading-7 text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light" placeholder="Add a simple sentence or real-life example.">{{ old('example', $vocabulary->example ?? '') }}</textarea>
                    @error('example')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="vocabulary-context-heading">
            <h2 id="vocabulary-context-heading" class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Learning context</h2>
            <p class="mt-2 text-sm leading-6 text-sign-muted">Optionally associate this term with a subject and course. Leave both blank for a general vocabulary term.</p>
            <div class="mt-6 grid gap-5 md:grid-cols-2">
                <div>
                    <label for="subject_id" class="mb-2 block text-sm font-semibold text-sign-primary">Subject</label>
                    <select id="subject_id" name="subject_id" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                        <option value="">General / all subjects</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected((string) $selectedSubject === (string) $subject->id)>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                    @error('subject_id')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="course_id" class="mb-2 block text-sm font-semibold text-sign-primary">Course</label>
                    <select id="course_id" name="course_id" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                        <option value="">No specific course</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}" @selected((string) $selectedCourse === (string) $course->id)>{{ $course->subject?->name ?? 'No subject' }} — {{ $course->title }}</option>
                        @endforeach
                    </select>
                    @error('course_id')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="vocabulary-video-heading">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 id="vocabulary-video-heading" class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">ISL sign video</h2>
                    <p class="mt-2 text-sm leading-6 text-sign-muted">Choose a reusable ISL video from Media Library. A direct URL can be kept as fallback.</p>
                </div>
                <a href="{{ route('admin.media.index', ['type' => 'video', 'isl' => 'yes']) }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-xl border border-sign-border bg-sign-soft px-4 py-2.5 text-sm font-semibold text-sign-primary transition hover:bg-sign-light">Open ISL Videos</a>
            </div>
            <div class="mt-6 grid gap-5">
                <div>
                    <label for="isl_media_asset_id" class="mb-2 block text-sm font-semibold text-sign-primary">Media Library ISL video</label>
                    <select id="isl_media_asset_id" name="isl_media_asset_id" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                        <option value="">No Media Library video selected</option>
                        @foreach ($mediaAssets as $asset)
                            <option value="{{ $asset->id }}" @selected((string) $selectedMedia === (string) $asset->id)>{{ $asset->title }} — {{ $asset->is_published ? 'Published' : 'Draft' }}</option>
                        @endforeach
                    </select>
                    @error('isl_media_asset_id')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="isl_video_url" class="mb-2 block text-sm font-semibold text-sign-primary">Fallback ISL video URL</label>
                    <input id="isl_video_url" name="isl_video_url" type="url" value="{{ old('isl_video_url', $vocabulary->isl_video_url ?? '') }}" maxlength="2048" placeholder="https://..." class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                    @error('isl_video_url')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>
    </div>

    <aside class="space-y-5 xl:sticky xl:top-24" aria-label="Vocabulary publishing settings">
        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Publishing</p>
            <label class="mt-4 flex cursor-pointer items-start gap-3 rounded-xl bg-sign-soft p-4">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $vocabulary->is_published ?? false)) class="mt-0.5 h-5 w-5 shrink-0 rounded border-sign-border accent-sign-primary">
                <span><span class="block text-sm font-semibold text-sign-primary">Published</span><span class="mt-1 block text-xs leading-5 text-sign-muted">Only published terms will appear in the future public vocabulary library.</span></span>
            </label>
            <div class="mt-5">
                <label for="sort_order" class="mb-2 block text-sm font-semibold text-sign-primary">Display order</label>
                <input id="sort_order" name="sort_order" type="number" min="0" max="9999" value="{{ old('sort_order', $vocabulary->sort_order ?? 0) }}" required class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                @error('sort_order')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
            </div>
        </section>
        <div class="grid gap-2">
            <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">{{ $editing ? 'Save Changes' : 'Create Term' }}</button>
            <a href="{{ route('admin.vocabulary.index') }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Cancel</a>
        </div>
    </aside>
</div>
