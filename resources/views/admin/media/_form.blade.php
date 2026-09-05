@php
    $editing = isset($mediaAsset);
    $currentSource = old('source', $mediaAsset->source ?? 'upload');
    $currentType = old('media_type', $mediaAsset->media_type ?? 'image');
@endphp

@if ($errors->any())
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3" role="alert" aria-live="polite">
        <p class="text-sm font-semibold text-red-800">Please check the media details.</p>
        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div x-data="{ source: @js($currentSource), mediaType: @js($currentType) }" class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_19rem] xl:items-start">
    <div class="space-y-6">
        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="media-details-heading">
            <h2 id="media-details-heading" class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Media details</h2>
            <p class="mt-2 text-sm leading-6 text-sign-muted">Add a reusable image, ISL video, document, audio file or external media link.</p>

            <div class="mt-6 grid gap-5">
                <div>
                    <label for="title" class="mb-2 block text-sm font-semibold text-sign-primary">Title</label>
                    <input
                        id="title"
                        name="title"
                        type="text"
                        value="{{ old('title', $mediaAsset->title ?? '') }}"
                        maxlength="180"
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

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="media_type" class="mb-2 block text-sm font-semibold text-sign-primary">Media type</label>
                        <select
                            id="media_type"
                            name="media_type"
                            x-model="mediaType"
                            required
                            class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light"
                        >
                            @foreach ($types as $type)
                                <option value="{{ $type }}">{{ Str::headline($type) }}</option>
                            @endforeach
                        </select>
                        @error('media_type')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="source" class="mb-2 block text-sm font-semibold text-sign-primary">Source</label>
                        <select
                            id="source"
                            name="source"
                            x-model="source"
                            required
                            class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light"
                        >
                            <option value="upload">Upload file</option>
                            <option value="external">External URL</option>
                        </select>
                        @error('source')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div x-show="mediaType === 'video'" x-cloak class="rounded-2xl border border-sign-border bg-sign-soft p-4 sm:p-5">
                    <label class="flex cursor-pointer items-start gap-3">
                        <input type="checkbox" name="is_isl" value="1" @checked(old('is_isl', $mediaAsset->is_isl ?? false)) class="mt-0.5 h-5 w-5 shrink-0 rounded border-sign-border accent-sign-primary">
                        <span>
                            <span class="block text-sm font-semibold text-sign-primary">Indian Sign Language video</span>
                            <span class="mt-1 block text-xs leading-5 text-sign-muted">Mark this when the video is learner-facing ISL content. ISL videos are prioritised when selecting lesson media.</span>
                        </span>
                    </label>

                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="language_code" class="mb-2 block text-sm font-semibold text-sign-primary">Language code</label>
                            <input id="language_code" name="language_code" type="text" value="{{ old('language_code', $mediaAsset->language_code ?? 'is') }}" maxlength="20" placeholder="is" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <p class="mt-2 text-xs leading-5 text-sign-muted">For ISL, use <strong>is</strong> unless you use another internal language code.</p>
                            @error('language_code')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="duration_seconds" class="mb-2 block text-sm font-semibold text-sign-primary">Video duration</label>
                            <div class="relative">
                                <input id="duration_seconds" name="duration_seconds" type="number" min="1" max="86400" value="{{ old('duration_seconds', $mediaAsset->duration_seconds ?? '') }}" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 pr-20 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                                <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-xs font-semibold text-sign-muted">seconds</span>
                            </div>
                            @error('duration_seconds')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div x-show="source === 'upload'" x-cloak>
                    <label for="file" class="mb-2 block text-sm font-semibold text-sign-primary">
                        {{ $editing && ($mediaAsset->file_path ?? false) ? 'Replace uploaded file' : 'Upload file' }}
                    </label>
                    <input
                        id="file"
                        name="file"
                        type="file"
                        accept=".jpg,.jpeg,.png,.webp,.gif,.mp4,.webm,.mov,.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.csv,.mp3,.wav,.m4a"
                        class="block min-h-12 w-full rounded-xl border border-sign-border bg-white px-3 py-2.5 text-sm text-sign-text file:mr-4 file:rounded-lg file:border-0 file:bg-sign-soft file:px-4 file:py-2 file:text-sm file:font-semibold file:text-sign-primary hover:file:bg-sign-light focus:outline-none focus:ring-4 focus:ring-sign-light"
                    >
                    <p class="mt-2 text-xs leading-5 text-sign-muted">Maximum 50 MB. Choose a media type that matches the uploaded file.</p>
                    @error('file')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror

                    @if ($editing && $mediaAsset->source === 'upload' && $mediaAsset->file_path)
                        <div class="mt-4 rounded-xl bg-sign-soft p-4 text-sm">
                            <p class="font-semibold text-sign-primary">Current file</p>
                            <p class="mt-1 break-all text-sign-muted">{{ $mediaAsset->original_name ?: basename($mediaAsset->file_path) }}</p>
                            <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-sign-muted">
                                @if ($mediaAsset->formattedFileSize())<span>{{ $mediaAsset->formattedFileSize() }}</span>@endif
                                @if ($mediaAsset->mime_type)<span>{{ $mediaAsset->mime_type }}</span>@endif
                                @if ($mediaAsset->formattedDuration())<span>{{ $mediaAsset->formattedDuration() }}</span>@endif
                            </div>
                            @if ($mediaAsset->publicUrl())
                                <a href="{{ $mediaAsset->publicUrl() }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex min-h-10 items-center text-xs font-semibold text-sign-primary hover:text-sign-cyan-dark">Open current file →</a>
                            @endif
                        </div>
                    @endif
                </div>

                <div x-show="source === 'external'" x-cloak>
                    <label for="external_url" class="mb-2 block text-sm font-semibold text-sign-primary">External URL</label>
                    <input
                        id="external_url"
                        name="external_url"
                        type="url"
                        value="{{ old('external_url', $mediaAsset->external_url ?? '') }}"
                        maxlength="2048"
                        placeholder="https://example.com/media"
                        class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light"
                    >
                    <p class="mt-2 text-xs leading-5 text-sign-muted">Useful for hosted ISL videos, YouTube/Vimeo pages, cloud documents or other reusable links.</p>
                    @error('external_url')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="alt_text" class="mb-2 block text-sm font-semibold text-sign-primary">Alternative text</label>
                    <input
                        id="alt_text"
                        name="alt_text"
                        type="text"
                        value="{{ old('alt_text', $mediaAsset->alt_text ?? '') }}"
                        maxlength="255"
                        placeholder="Describe the important visual information"
                        class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light"
                    >
                    <p class="mt-2 text-xs leading-5 text-sign-muted">Recommended for images. Keep it concise and meaningful for screen-reader users.</p>
                    @error('alt_text')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="caption" class="mb-2 block text-sm font-semibold text-sign-primary">Caption / notes</label>
                    <textarea
                        id="caption"
                        name="caption"
                        rows="5"
                        maxlength="5000"
                        class="w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light"
                        placeholder="Optional description, source information or usage note."
                    >{{ old('caption', $mediaAsset->caption ?? '') }}</textarea>
                    @error('caption')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        @if ($editing && $mediaAsset->publicUrl())
            <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="media-url-heading">
                <h2 id="media-url-heading" class="font-heading text-xl font-semibold text-sign-primary">Reusable media URL</h2>
                <p class="mt-2 text-sm leading-6 text-sign-muted">Use this URL in lesson ISL video or Practice & Resources fields when you want to reuse this asset.</p>
                <input type="text" readonly value="{{ $mediaAsset->publicUrl() }}" class="mt-4 min-h-12 w-full rounded-xl border border-sign-border bg-sign-soft px-4 py-3 text-sm text-sign-text outline-none focus:ring-4 focus:ring-sign-light">
            </section>
        @endif
    </div>

    <aside class="space-y-5 xl:sticky xl:top-24" aria-label="Media publishing settings">
        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Publishing</p>

            <label class="mt-4 flex cursor-pointer items-start gap-3 rounded-xl bg-sign-soft p-4">
                <input
                    type="checkbox"
                    name="is_published"
                    value="1"
                    @checked(old('is_published', $mediaAsset->is_published ?? true))
                    class="mt-0.5 h-5 w-5 shrink-0 rounded border-sign-border accent-sign-primary"
                >
                <span>
                    <span class="block text-sm font-semibold text-sign-primary">Available for use</span>
                    <span class="mt-1 block text-xs leading-5 text-sign-muted">Draft items remain in the admin library but should not be used in learner content.</span>
                </span>
            </label>
        </section>

        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Upload guidance</p>
            <ul class="mt-4 space-y-2 text-xs leading-5 text-sign-muted">
                <li>Images: JPG, PNG, WebP, GIF</li>
                <li>Video: MP4, WebM, MOV</li>
                <li>Documents: PDF, Office, TXT, CSV</li>
                <li>Audio: MP3, WAV, M4A</li>
                <li>Large videos can use an external URL.</li>
            </ul>
        </section>

        <div class="grid gap-2">
            <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">
                {{ $editing ? 'Save Changes' : 'Add Media' }}
            </button>
            <a href="{{ route('admin.media.index') }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">
                Cancel
            </a>
        </div>
    </aside>
</div>
