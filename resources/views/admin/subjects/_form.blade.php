@php
    $editing = isset($subject);
@endphp

@if ($errors->any())
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3" role="alert" aria-live="polite">
        <p class="text-sm font-semibold text-red-800">Please check the subject details.</p>
        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_18rem] xl:items-start">
    <div class="space-y-6">
        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="subject-main-details">
            <h2 id="subject-main-details" class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Subject details</h2>
            <p class="mt-2 text-sm leading-6 text-sign-muted">Set the subject name, URL slug and learner-facing descriptions.</p>

            <div class="mt-6 grid gap-5">
                <div>
                    <label for="name" class="mb-2 block text-sm font-semibold text-sign-primary">Subject name</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $subject->name ?? '') }}"
                        maxlength="120"
                        required
                        autofocus
                        @class([
                            'min-h-12 w-full rounded-xl border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light',
                            'border-red-300' => $errors->has('name'),
                            'border-sign-border' => ! $errors->has('name'),
                        ])
                    >
                    @error('name')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="slug" class="mb-2 block text-sm font-semibold text-sign-primary">URL slug</label>
                    <input
                        id="slug"
                        name="slug"
                        type="text"
                        value="{{ old('slug', $subject->slug ?? '') }}"
                        maxlength="140"
                        spellcheck="false"
                        placeholder="Leave blank to create from the name"
                        @class([
                            'min-h-12 w-full rounded-xl border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light',
                            'border-red-300' => $errors->has('slug'),
                            'border-sign-border' => ! $errors->has('slug'),
                        ])
                    >
                    <p class="mt-2 text-xs leading-5 text-sign-muted">Example: <span class="font-semibold text-sign-primary">digital-skills</span>. If blank, SignGyaan creates it automatically.</p>
                    @error('slug')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="short_description" class="mb-2 block text-sm font-semibold text-sign-primary">Short description</label>
                    <textarea
                        id="short_description"
                        name="short_description"
                        rows="3"
                        maxlength="255"
                        class="w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light"
                        placeholder="Short summary shown on subject cards."
                    >{{ old('short_description', $subject->short_description ?? '') }}</textarea>
                    @error('short_description')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="description" class="mb-2 block text-sm font-semibold text-sign-primary">Full description</label>
                    <textarea
                        id="description"
                        name="description"
                        rows="6"
                        maxlength="5000"
                        class="w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light"
                        placeholder="Explain what learners can expect from this subject."
                    >{{ old('description', $subject->description ?? '') }}</textarea>
                    @error('description')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>
    </div>

    <aside class="space-y-5 xl:sticky xl:top-24" aria-label="Subject publishing settings">
        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Publishing</p>

            <input type="hidden" name="is_published" value="0">
            <label class="mt-4 flex cursor-pointer items-start gap-3 rounded-xl bg-sign-soft p-4">
                <input
                    type="checkbox"
                    name="is_published"
                    value="1"
                    @checked(old('is_published', $subject->is_published ?? true))
                    class="mt-0.5 h-5 w-5 shrink-0 rounded border-sign-border accent-sign-primary"
                >
                <span>
                    <span class="block text-sm font-semibold text-sign-primary">Published</span>
                    <span class="mt-1 block text-xs leading-5 text-sign-muted">Turn off to keep this subject as a draft.</span>
                </span>
            </label>

            <div class="mt-5">
                <label for="sort_order" class="mb-2 block text-sm font-semibold text-sign-primary">Display order</label>
                <input
                    id="sort_order"
                    name="sort_order"
                    type="number"
                    min="0"
                    max="9999"
                    value="{{ old('sort_order', $subject->sort_order ?? 0) }}"
                    required
                    class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light"
                >
                <p class="mt-2 text-xs leading-5 text-sign-muted">Lower numbers appear first.</p>
                @error('sort_order')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
            </div>
        </section>

        <div class="grid gap-2">
            <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">
                {{ $editing ? 'Save Changes' : 'Create Subject' }}
            </button>
            <a href="{{ route('admin.subjects.index') }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">
                Cancel
            </a>
        </div>
    </aside>
</div>
