@php
    $editing = isset($lesson);
    $selectedUnit = old('unit_id', $lesson->unit_id ?? $selectedUnitId ?? null);
    $selectedMediaAsset = old('isl_media_asset_id', $lesson->isl_media_asset_id ?? null);
@endphp

@if ($errors->any())
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3" role="alert" aria-live="polite">
        <p class="text-sm font-semibold text-red-800">Please check the lesson details.</p>
        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_19rem] xl:items-start">
    <div class="space-y-6">
        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="lesson-basic-heading">
            <h2 id="lesson-basic-heading" class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Lesson details</h2>
            <p class="mt-2 text-sm leading-6 text-sign-muted">Choose the unit, title and learner-facing summary for this lesson.</p>

            <div class="mt-6 grid gap-5">
                <div>
                    <label for="unit_id" class="mb-2 block text-sm font-semibold text-sign-primary">Unit</label>
                    <select id="unit_id" name="unit_id" required class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                        <option value="">Select a unit</option>
                        @foreach ($units as $unitOption)
                            <option value="{{ $unitOption->id }}" @selected((string) $selectedUnit === (string) $unitOption->id)>
                                {{ $unitOption->course?->subject?->name ?? 'No subject' }} — {{ $unitOption->course?->title ?? 'No course' }} — {{ $unitOption->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('unit_id')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="title" class="mb-2 block text-sm font-semibold text-sign-primary">Lesson title</label>
                        <input id="title" name="title" type="text" value="{{ old('title', $lesson->title ?? '') }}" maxlength="180" required autofocus class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                        @error('title')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="slug" class="mb-2 block text-sm font-semibold text-sign-primary">URL slug</label>
                        <input id="slug" name="slug" type="text" value="{{ old('slug', $lesson->slug ?? '') }}" maxlength="200" spellcheck="false" placeholder="Leave blank to create from title" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                        <p class="mt-2 text-xs leading-5 text-sign-muted">Unique inside the selected unit.</p>
                        @error('slug')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="short_description" class="mb-2 block text-sm font-semibold text-sign-primary">Short description</label>
                    <textarea id="short_description" name="short_description" rows="3" maxlength="255" class="w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light" placeholder="A short summary of what this lesson teaches.">{{ old('short_description', $lesson->short_description ?? '') }}</textarea>
                    @error('short_description')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="lesson-content-heading">
            <h2 id="lesson-content-heading" class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Learning content</h2>
            <p class="mt-2 text-sm leading-6 text-sign-muted">Add the lesson objectives, explanation, key points and examples. Practice activities are managed separately.</p>

            <div class="mt-6 grid gap-5">
                <div>
                    <label for="learning_objectives" class="mb-2 block text-sm font-semibold text-sign-primary">Learning objectives</label>
                    <textarea id="learning_objectives" name="learning_objectives" rows="5" class="w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light" placeholder="One objective per line is recommended.">{{ old('learning_objectives', $lesson->learning_objectives ?? '') }}</textarea>
                    @error('learning_objectives')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="content" class="mb-2 block text-sm font-semibold text-sign-primary">Lesson notes / explanation</label>
                    <textarea id="content" name="content" rows="12" class="w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base leading-7 text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light" placeholder="Write the main lesson explanation in clear, learner-friendly language.">{{ old('content', $lesson->content ?? '') }}</textarea>
                    @error('content')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>

                <div class="grid gap-5 lg:grid-cols-2">
                    <div>
                        <label for="key_points" class="mb-2 block text-sm font-semibold text-sign-primary">Key points</label>
                        <textarea id="key_points" name="key_points" rows="7" class="w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light" placeholder="Important ideas learners should remember.">{{ old('key_points', $lesson->key_points ?? '') }}</textarea>
                        @error('key_points')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="example_content" class="mb-2 block text-sm font-semibold text-sign-primary">Example</label>
                        <textarea id="example_content" name="example_content" rows="7" class="w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light" placeholder="Add a simple worked example or real-life example.">{{ old('example_content', $lesson->example_content ?? '') }}</textarea>
                        @error('example_content')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="lesson-isl-heading">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 id="lesson-isl-heading" class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">ISL video support</h2>
                    <p class="mt-2 text-sm leading-6 text-sign-muted">Choose a reusable Media Library video first. A direct URL with its own title and caption can be kept as a fallback.</p>
                </div>
                <a href="{{ route('admin.media.index', ['type' => 'video', 'isl' => 'yes']) }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-xl border border-sign-border bg-sign-soft px-4 py-2.5 text-sm font-semibold text-sign-primary transition hover:border-sign-cyan hover:bg-sign-light">Open ISL Videos</a>
            </div>

            <div class="mt-6 grid gap-5">
                <div>
                    <label for="isl_media_asset_id" class="mb-2 block text-sm font-semibold text-sign-primary">Media Library video</label>
                    <select id="isl_media_asset_id" name="isl_media_asset_id" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                        <option value="">No Media Library video selected</option>
                        @foreach ($mediaAssets as $asset)
                            <option value="{{ $asset->id }}" @selected((string) $selectedMediaAsset === (string) $asset->id)>
                                {{ $asset->is_isl ? 'ISL — ' : '' }}{{ $asset->title }} — {{ ucfirst($asset->source) }} — {{ $asset->is_published ? 'Published' : 'Draft' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs leading-5 text-sign-muted">A published selected Media Library video takes priority on the learner lesson page. Draft media is never shown publicly.</p>
                    @error('isl_media_asset_id')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>

                <div class="rounded-2xl bg-sign-soft p-4 sm:p-5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Fallback video</p>
                    <div class="mt-4 grid gap-5">
                        <div>
                            <label for="isl_video_url" class="mb-2 block text-sm font-semibold text-sign-primary">External ISL video URL</label>
                            <input id="isl_video_url" name="isl_video_url" type="url" value="{{ old('isl_video_url', $lesson->isl_video_url ?? '') }}" maxlength="2048" inputmode="url" placeholder="https://..." class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            @error('isl_video_url')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="isl_video_title" class="mb-2 block text-sm font-semibold text-sign-primary">Fallback video title</label>
                            <input id="isl_video_title" name="isl_video_title" type="text" value="{{ old('isl_video_title', $lesson->isl_video_title ?? '') }}" maxlength="180" placeholder="ISL explanation: Input Devices" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            @error('isl_video_title')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="isl_video_caption" class="mb-2 block text-sm font-semibold text-sign-primary">Fallback video caption</label>
                            <textarea id="isl_video_caption" name="isl_video_caption" rows="4" maxlength="5000" class="w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light" placeholder="Short learner-facing note about what the ISL video explains.">{{ old('isl_video_caption', $lesson->isl_video_caption ?? '') }}</textarea>
                            <p class="mt-2 text-xs leading-5 text-sign-muted">Used only when the lesson falls back to the external URL instead of a published Media Library video.</p>
                            @error('isl_video_caption')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <aside class="space-y-5 xl:sticky xl:top-24" aria-label="Lesson publishing settings">
        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Publishing</p>

            <label class="mt-4 flex cursor-pointer items-start gap-3 rounded-xl bg-sign-soft p-4">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $lesson->is_published ?? true)) class="mt-0.5 h-5 w-5 shrink-0 rounded border-sign-border accent-sign-primary">
                <span>
                    <span class="block text-sm font-semibold text-sign-primary">Published</span>
                    <span class="mt-1 block text-xs leading-5 text-sign-muted">Turn off to keep this lesson as a draft.</span>
                </span>
            </label>

            <div class="mt-5">
                <label for="sort_order" class="mb-2 block text-sm font-semibold text-sign-primary">Lesson order</label>
                <input id="sort_order" name="sort_order" type="number" min="0" max="9999" value="{{ old('sort_order', $lesson->sort_order ?? 0) }}" required class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                <p class="mt-2 text-xs leading-5 text-sign-muted">Lower numbers appear first inside the unit.</p>
                @error('sort_order')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
            </div>

            <div class="mt-5">
                <label for="estimated_duration_minutes" class="mb-2 block text-sm font-semibold text-sign-primary">Estimated duration</label>
                <div class="relative">
                    <input id="estimated_duration_minutes" name="estimated_duration_minutes" type="number" min="1" max="100000" value="{{ old('estimated_duration_minutes', $lesson->estimated_duration_minutes ?? '') }}" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 pr-20 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                    <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-xs font-semibold text-sign-muted">minutes</span>
                </div>
                @error('estimated_duration_minutes')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
            </div>
        </section>

        <div class="grid gap-2">
            <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">
                {{ $editing ? 'Save Changes' : 'Create Lesson' }}
            </button>
            <a href="{{ route('admin.lessons.index') }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Cancel</a>
        </div>
    </aside>
</div>