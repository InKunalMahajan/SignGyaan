@php
    $richLesson = $currentLessonModel;
    $richBlocks = $richLesson
        ? $richLesson->contentBlocks()
            ->published()
            ->with([
                'mediaAsset' => fn ($query) => $query->published(),
                'practiceResource' => fn ($query) => $query
                    ->published()
                    ->with(['mediaAsset' => fn ($mediaQuery) => $mediaQuery->published()]),
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
        : collect();

    $cleanRichLines = function (?string $value) {
        return collect(preg_split('/\R/u', (string) $value) ?: [])
            ->map(fn ($line) => trim((string) preg_replace('/^(?:[-*•]|\d+[.)])\s*/u', '', trim((string) $line))))
            ->filter()
            ->values();
    };

    $directRichVideo = function (?string $url, ?string $mimeType = null): bool {
        if ($mimeType && str_starts_with(strtolower($mimeType), 'video/')) return true;
        if (! $url) return false;
        $extension = strtolower(pathinfo((string) parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        return in_array($extension, ['mp4', 'webm', 'mov', 'ogg'], true);
    };
@endphp

@if ($richBlocks->isNotEmpty())
<section id="lesson-rich-content" class="border-t border-sign-border bg-sign-soft py-8 sm:py-12 lg:py-16 print:bg-white print:py-4" aria-labelledby="lesson-rich-content-heading">
    <x-container>
        <div class="mx-auto max-w-4xl">
            <div class="mb-6 sm:mb-8">
                <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Structured lesson content</p>
                <h2 id="lesson-rich-content-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Learn step by step</h2>
                <p class="mt-2 text-sm leading-6 text-sign-muted">Follow these published learning blocks in the order prepared by the teacher.</p>
            </div>

            <div class="space-y-5 sm:space-y-6">
                @foreach ($richBlocks as $blockIndex => $block)
                    @php
                        $media = $block->mediaAsset;
                        $activity = $block->practiceResource;
                        $mediaUrl = $media?->publicUrl();
                        $activityMedia = $activity?->mediaAsset;
                        $activityUrl = $activityMedia?->publicUrl() ?: $activity?->resource_url;
                        $blockTitle = $block->title;
                    @endphp

                    @if ($block->type === 'text')
                        <article class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-8">
                            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Explanation {{ $blockIndex + 1 }}</p>
                            @if ($blockTitle)<h3 class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-2xl">{{ $blockTitle }}</h3>@endif
                            <div class="mt-4 whitespace-pre-line text-sm leading-7 text-sign-muted sm:text-base sm:leading-8">{{ $block->body }}</div>
                        </article>
                    @elseif ($block->type === 'key_points')
                        @php $lines = $cleanRichLines($block->body); @endphp
                        <article class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-8">
                            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Key points</p>
                            <h3 class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-2xl">{{ $blockTitle ?: 'Remember these ideas' }}</h3>
                            <div class="mt-5 space-y-3">
                                @foreach ($lines as $lineIndex => $line)
                                    <div class="flex gap-3 rounded-2xl bg-sign-soft p-4"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white text-sm font-semibold text-sign-primary">{{ $lineIndex + 1 }}</span><p class="pt-1 text-sm leading-6 text-sign-muted">{{ $line }}</p></div>
                                @endforeach
                            </div>
                        </article>
                    @elseif ($block->type === 'example')
                        <article class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-8">
                            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Example</p>
                            <h3 class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-2xl">{{ $blockTitle ?: 'See the idea in context' }}</h3>
                            <div class="mt-5 whitespace-pre-line rounded-2xl bg-sign-soft p-5 text-sm leading-7 text-sign-muted sm:text-base">{{ $block->body }}</div>
                        </article>
                    @elseif ($block->type === 'image' && $media && $media->media_type === 'image' && $mediaUrl)
                        <figure class="overflow-hidden rounded-2xl border border-sign-border bg-white sm:rounded-3xl">
                            <img src="{{ $mediaUrl }}" alt="{{ $media->alt_text ?: ($blockTitle ?: $media->title) }}" class="h-auto w-full object-contain">
                            <figcaption class="border-t border-sign-border p-4 sm:p-5"><p class="font-semibold text-sign-primary">{{ $blockTitle ?: $media->title }}</p>@if($block->body)<p class="mt-1 text-sm leading-6 text-sign-muted">{{ $block->body }}</p>@elseif($media->caption)<p class="mt-1 text-sm leading-6 text-sign-muted">{{ $media->caption }}</p>@endif</figcaption>
                        </figure>
                    @elseif ($block->type === 'isl_video' && $media && $media->media_type === 'video' && $mediaUrl)
                        <article class="overflow-hidden rounded-2xl border border-sign-border bg-white sm:rounded-3xl print:hidden">
                            @if ($directRichVideo($mediaUrl, $media->mime_type))
                                <video controls preload="metadata" class="aspect-video w-full bg-black" aria-label="{{ $blockTitle ?: $media->title }}"><source src="{{ $mediaUrl }}" @if($media->mime_type) type="{{ $media->mime_type }}" @endif>Your browser does not support this video.</video>
                            @else
                                <div class="flex aspect-video min-h-52 items-center justify-center bg-sign-primary p-6 text-center text-white"><div><p class="text-xs font-semibold uppercase tracking-wider text-white/75">ISL video</p><h3 class="mt-2 font-heading text-xl font-semibold">{{ $blockTitle ?: $media->title }}</h3><a href="{{ $mediaUrl }}" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex min-h-11 items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-sign-primary">Open video</a></div></div>
                            @endif
                            @if($block->body || $media->caption)<div class="p-4 text-sm leading-6 text-sign-muted sm:p-5">{{ $block->body ?: $media->caption }}</div>@endif
                        </article>
                    @elseif ($block->type === 'transcript')
                        <article class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-8">
                            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Transcript</p>
                            <h3 class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-2xl">{{ $blockTitle ?: 'Read the signed content' }}</h3>
                            <div class="mt-4 whitespace-pre-line text-sm leading-7 text-sign-muted sm:text-base sm:leading-8">{{ $block->body }}</div>
                        </article>
                    @elseif ($block->type === 'vocabulary')
                        @php $lines = $cleanRichLines($block->body); @endphp
                        <article class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-8">
                            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Vocabulary</p>
                            <h3 class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-2xl">{{ $blockTitle ?: 'Important words' }}</h3>
                            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                                @foreach($lines as $line)
                                    @php $parts = preg_split('/\s+[—–-]\s+/u', $line, 2); @endphp
                                    <div class="rounded-2xl bg-sign-soft p-4"><p class="font-semibold text-sign-primary">{{ trim((string)($parts[0] ?? $line)) }}</p>@if(isset($parts[1]))<p class="mt-1 text-sm leading-6 text-sign-muted">{{ trim((string)$parts[1]) }}</p>@endif</div>
                                @endforeach
                            </div>
                        </article>
                    @elseif (in_array($block->type, ['practice', 'resource'], true) && $activity && $activity->kind === $block->type)
                        <article class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-6 print:hidden">
                            <div class="flex flex-wrap items-center gap-2"><span class="rounded-full bg-sign-light px-3 py-1 text-xs font-semibold text-sign-primary">{{ ucfirst($activity->kind) }}</span><span class="text-xs font-semibold text-sign-muted">{{ ucfirst(str_replace('-', ' ', $activity->resource_type)) }}</span></div>
                            <h3 class="mt-3 font-heading text-xl font-semibold text-sign-primary">{{ $blockTitle ?: $activity->title }}</h3>
                            @if($block->body)<p class="mt-2 text-sm leading-6 text-sign-muted">{{ $block->body }}</p>@elseif($activity->short_description)<p class="mt-2 text-sm leading-6 text-sign-muted">{{ $activity->short_description }}</p>@endif
                            @if($activityUrl)<a href="{{ $activityUrl }}" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white">Open {{ $activity->kind }}</a>@endif
                        </article>
                    @endif
                @endforeach
            </div>
        </div>
    </x-container>
</section>
@endif
