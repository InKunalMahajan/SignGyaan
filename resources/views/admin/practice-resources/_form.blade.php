@php
    $editing = isset($item);
    $currentKind = old('kind', $item->kind ?? 'practice');
    $currentType = old('resource_type', $item->resource_type ?? 'exercise');
    $selectedMediaAsset = old('media_asset_id', $item->media_asset_id ?? null);
@endphp

@if ($errors->any())
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3" role="alert" aria-live="polite">
        <p class="text-sm font-semibold text-red-800">Please check the practice or resource details.</p>
        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_19rem] xl:items-start" x-data="{ kind: @js($currentKind) }">
    <div class="space-y-6">
        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="practice-main-details">
            <h2 id="practice-main-details" class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Item details</h2>
            <p class="mt-2 text-sm leading-6 text-sign-muted">Choose the lesson, item kind and learner-facing information.</p>

            <div class="mt-6 grid gap-5">
                <div>
                    <label for="lesson_id" class="mb-2 block text-sm font-semibold text-sign-primary">Lesson</label>
                    <select id="lesson_id" name="lesson_id" required class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                        <option value="">Select a lesson</option>
                        @foreach ($lessons as $lesson)
                            @php
                                $lessonSelected = old('lesson_id', $item->lesson_id ?? $selectedLessonId ?? null);
                            @endphp
                            <option value="{{ $lesson->id }}" @selected((string) $lessonSelected === (string) $lesson->id)>
                                {{ $lesson->unit?->course?->subject?->name ?? 'No subject' }} — {{ $lesson->unit?->course?->title ?? 'No course' }} — {{ $lesson->unit?->title ?? 'No unit' }} — {{ $lesson->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('lesson_id')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="kind" class="mb-2 block text-sm font-semibold text-sign-primary">Kind</label>
                        <select id="kind" name="kind" x-model="kind" required class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="practice">Practice</option>
                            <option value="resource">Resource</option>
                        </select>
                        <p class="mt-2 text-xs leading-5 text-sign-muted" x-text="kind === 'practice' ? 'Use for exercises, quizzes and reflection activities.' : 'Use for worksheets, handouts, references and useful links.'"></p>
                        @error('kind')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="resource_type" class="mb-2 block text-sm font-semibold text-sign-primary">Type</label>
                        <select id="resource_type" name="resource_type" required class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            @foreach ($resourceTypes as $value => $label)
                                <option value="{{ $value }}" @selected($currentType === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('resource_type')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="title" class="mb-2 block text-sm font-semibold text-sign-primary">Title</label>
                    <input id="title" name="title" type="text" value="{{ old('title', $item->title ?? '') }}" maxlength="180" required autofocus class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                    @error('title')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="slug" class="mb-2 block text-sm font-semibold text-sign-primary">URL slug</label>
                    <input id="slug" name="slug" type="text" value="{{ old('slug', $item->slug ?? '') }}" maxlength="200" spellcheck="false" placeholder="Leave blank to create from the title" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                    <p class="mt-2 text-xs leading-5 text-sign-muted">Unique within the selected lesson.</p>
                    @error('slug')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="short_description" class="mb-2 block text-sm font-semibold text-sign-primary">Short description</label>
                    <textarea id="short_description" name="short_description" rows="3" maxlength="255" class="w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light" placeholder="Short summary shown to learners.">{{ old('short_description', $item->short_description ?? '') }}</textarea>
                    @error('short_description')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="practice-content-heading">
            <h2 id="practice-content-heading" class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Learning content</h2>
            <p class="mt-2 text-sm leading-6 text-sign-muted">Add instructions, questions/content and an optional answer key.</p>

            <div class="mt-6 grid gap-5">
                <div>
                    <label for="instructions" class="mb-2 block text-sm font-semibold text-sign-primary">Instructions</label>
                    <textarea id="instructions" name="instructions" rows="4" maxlength="50000" class="w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light" placeholder="Explain what the learner should do.">{{ old('instructions', $item->instructions ?? '') }}</textarea>
                    @error('instructions')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="content" class="mb-2 block text-sm font-semibold text-sign-primary">Content / Questions</label>
                    <textarea id="content" name="content" rows="8" maxlength="100000" class="w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light" placeholder="Add exercise questions, quiz content, notes or resource text.">{{ old('content', $item->content ?? '') }}</textarea>
                    @error('content')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="answer_key" class="mb-2 block text-sm font-semibold text-sign-primary">Answer key / Teacher notes</label>
                    <textarea id="answer_key" name="answer_key" rows="6" maxlength="100000" class="w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light" placeholder="Optional answers or reference notes.">{{ old('answer_key', $item->answer_key ?? '') }}</textarea>
                    <p class="mt-2 text-xs leading-5 text-sign-muted">Stored separately and not shown on the public learner lesson page.</p>
                    @error('answer_key')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="practice-link-heading">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 id="practice-link-heading" class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Linked media or resource</h2>
                    <p class="mt-2 text-sm leading-6 text-sign-muted">Select a reusable Media Library item. A direct URL can remain as a fallback.</p>
                </div>
                <a href="{{ route('admin.media.index') }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-xl border border-sign-border bg-sign-soft px-4 py-2.5 text-sm font-semibold text-sign-primary transition hover:border-sign-cyan hover:bg-sign-light">Open Media Library</a>
            </div>

            <div class="mt-6 grid gap-5">
                <div>
                    <label for="media_asset_id" class="mb-2 block text-sm font-semibold text-sign-primary">Media Library item</label>
                    <select id="media_asset_id" name="media_asset_id" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                        <option value="">No Media Library item selected</option>
                        @foreach ($mediaAssets as $asset)
                            <option value="{{ $asset->id }}" @selected((string) $selectedMediaAsset === (string) $asset->id)>
                                {{ ucfirst($asset->media_type) }} — {{ $asset->title }} — {{ $asset->is_published ? 'Published' : 'Draft' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs leading-5 text-sign-muted">On the learner page, a published linked Media Library item takes priority over the fallback URL.</p>
                    @error('media_asset_id')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="resource_url" class="mb-2 block text-sm font-semibold text-sign-primary">Fallback / external resource URL</label>
                    <input id="resource_url" name="resource_url" type="url" value="{{ old('resource_url', $item->resource_url ?? '') }}" maxlength="2048" placeholder="https://..." class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                    <p class="mt-2 text-xs leading-5 text-sign-muted">Used when no published Media Library item is linked.</p>
                    @error('resource_url')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>
    </div>

    <aside class="space-y-5 xl:sticky xl:top-24" aria-label="Practice and resource settings">
        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Publishing</p>

            <label class="mt-4 flex cursor-pointer items-start gap-3 rounded-xl bg-sign-soft p-4">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $item->is_published ?? true)) class="mt-0.5 h-5 w-5 shrink-0 rounded border-sign-border accent-sign-primary">
                <span>
                    <span class="block text-sm font-semibold text-sign-primary">Published</span>
                    <span class="mt-1 block text-xs leading-5 text-sign-muted">Turn off to keep this item as a draft.</span>
                </span>
            </label>

            <div class="mt-5">
                <label for="sort_order" class="mb-2 block text-sm font-semibold text-sign-primary">Display order</label>
                <input id="sort_order" name="sort_order" type="number" min="0" max="9999" value="{{ old('sort_order', $item->sort_order ?? 0) }}" required class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                <p class="mt-2 text-xs leading-5 text-sign-muted">Lower numbers appear first inside the lesson.</p>
                @error('sort_order')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
            </div>

            <div class="mt-5">
                <label for="estimated_duration_minutes" class="mb-2 block text-sm font-semibold text-sign-primary">Estimated minutes</label>
                <input id="estimated_duration_minutes" name="estimated_duration_minutes" type="number" min="1" max="100000" value="{{ old('estimated_duration_minutes', $item->estimated_duration_minutes ?? '') }}" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                @error('estimated_duration_minutes')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
            </div>
        </section>

        <div class="grid gap-2">
            <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">{{ $editing ? 'Save Changes' : 'Create Item' }}</button>
            <a href="{{ route('admin.practice.index') }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Cancel</a>
        </div>
    </aside>
</div>