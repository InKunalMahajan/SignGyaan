@php
    $richBlocks = $currentLessonModel
        ? $currentLessonModel->contentBlocks()
            ->with(['mediaAsset', 'practiceResource.mediaAsset'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
        : collect();

    $cleanLines = fn (?string $value) => collect(preg_split('/\R/u', (string) $value) ?: [])
        ->map(fn ($line) => trim((string) preg_replace('/^(?:[-*•]|\d+[.)])\s*/u', '', trim((string) $line))))
        ->filter()
        ->values();

    $isDirectVideo = function (?string $url, ?string $mimeType = null): bool {
        if ($mimeType && str_starts_with(strtolower($mimeType), 'video/')) return true;
        if (! $url) return false;
        return in_array(strtolower(pathinfo((string) parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION)), ['mp4', 'webm', 'mov', 'ogg'], true);
    };
@endphp

@if ($richBlocks->isNotEmpty())
<section id="lesson-rich-content" class="border-t border-sign-border bg-sign-soft py-8 sm:py-12 lg:py-16" aria-labelledby="preview-rich-content-heading">
    <x-container>
        <div class="mx-auto max-w-4xl">
            <div class="mb-6 sm:mb-8">
                <div class="flex flex-wrap items-center gap-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Structured lesson content</p>
                    <span class="rounded-full bg-white px-2.5 py-1 text-[11px] font-semibold text-sign-primary ring-1 ring-sign-border">Draft blocks included</span>
                </div>
                <h2 id="preview-rich-content-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Learn step by step</h2>
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

                    <article class="overflow-hidden rounded-2xl border border-sign-border bg-white sm:rounded-3xl">
                        <div class="flex flex-wrap items-center gap-2 border-b border-sign-border bg-white px-5 py-3 sm:px-8">
                            <span class="text-xs font-semibold text-sign-cyan-dark">Block {{ $blockIndex + 1 }}</span>
                            <span class="rounded-full bg-sign-soft px-2.5 py-1 text-[11px] font-semibold text-sign-primary">{{ ucfirst(str_replace('_', ' ', $block->type)) }}</span>
                            <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $block->is_published ? 'bg-sign-light text-sign-primary' : 'bg-gray-100 text-sign-muted' }}">{{ $block->is_published ? 'Published' : 'Draft' }}</span>
                        </div>

                        @if ($block->type === 'text')
                            <div class="p-5 sm:p-8">@if($blockTitle)<h3 class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">{{ $blockTitle }}</h3>@endif<div class="{{ $blockTitle ? 'mt-4' : '' }} whitespace-pre-line text-sm leading-7 text-sign-muted sm:text-base sm:leading-8">{{ $block->body }}</div></div>
                        @elseif ($block->type === 'key_points')
                            @php $lines = $cleanLines($block->body); @endphp
                            <div class="p-5 sm:p-8"><h3 class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">{{ $blockTitle ?: 'Remember these ideas' }}</h3><div class="mt-5 space-y-3">@foreach($lines as $i => $line)<div class="flex gap-3 rounded-2xl bg-sign-soft p-4"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white text-sm font-semibold text-sign-primary">{{ $i + 1 }}</span><p class="pt-1 text-sm leading-6 text-sign-muted">{{ $line }}</p></div>@endforeach</div></div>
                        @elseif ($block->type === 'example')
                            <div class="p-5 sm:p-8"><h3 class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">{{ $blockTitle ?: 'Example' }}</h3><div class="mt-5 whitespace-pre-line rounded-2xl bg-sign-soft p-5 text-sm leading-7 text-sign-muted sm:text-base">{{ $block->body }}</div></div>
                        @elseif ($block->type === 'image' && $media && $media->media_type === 'image' && $mediaUrl)
                            <figure><img src="{{ $mediaUrl }}" alt="{{ $media->alt_text ?: ($blockTitle ?: $media->title) }}" class="h-auto w-full object-contain"><figcaption class="border-t border-sign-border p-5 sm:px-8"><p class="font-semibold text-sign-primary">{{ $blockTitle ?: $media->title }}</p>@if($block->body || $media->caption)<p class="mt-1 text-sm leading-6 text-sign-muted">{{ $block->body ?: $media->caption }}</p>@endif</figcaption></figure>
                        @elseif ($block->type === 'isl_video' && $media && $media->media_type === 'video' && $mediaUrl)
                            @if($isDirectVideo($mediaUrl, $media->mime_type))<video controls preload="metadata" class="aspect-video w-full bg-black" aria-label="{{ $blockTitle ?: $media->title }}"><source src="{{ $mediaUrl }}" @if($media->mime_type) type="{{ $media->mime_type }}" @endif>Your browser does not support this video.</video>@else<div class="flex aspect-video min-h-52 items-center justify-center bg-sign-primary p-6 text-center text-white"><div><p class="text-xs font-semibold uppercase tracking-wider text-white/75">ISL video</p><h3 class="mt-2 font-heading text-xl font-semibold">{{ $blockTitle ?: $media->title }}</h3><a href="{{ $mediaUrl }}" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex min-h-11 items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-sign-primary">Open video</a></div></div>@endif
                            @if($block->body || $media->caption)<div class="p-5 text-sm leading-6 text-sign-muted sm:px-8">{{ $block->body ?: $media->caption }}</div>@endif
                        @elseif ($block->type === 'transcript')
                            <div class="p-5 sm:p-8"><h3 class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">{{ $blockTitle ?: 'Read the signed content' }}</h3><div class="mt-4 whitespace-pre-line text-sm leading-7 text-sign-muted sm:text-base sm:leading-8">{{ $block->body }}</div></div>
                        @elseif ($block->type === 'vocabulary')
                            @php $lines = $cleanLines($block->body); @endphp
                            <div class="p-5 sm:p-8"><h3 class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">{{ $blockTitle ?: 'Important words' }}</h3><div class="mt-5 grid gap-3 sm:grid-cols-2">@foreach($lines as $line)@php $parts = preg_split('/\s+[—–-]\s+/u', $line, 2); @endphp<div class="rounded-2xl bg-sign-soft p-4"><p class="font-semibold text-sign-primary">{{ trim((string)($parts[0] ?? $line)) }}</p>@if(isset($parts[1]))<p class="mt-1 text-sm leading-6 text-sign-muted">{{ trim((string)$parts[1]) }}</p>@endif</div>@endforeach</div></div>
                        @elseif (in_array($block->type, ['practice', 'resource'], true) && $activity && $activity->kind === $block->type)
                            <div class="p-5 sm:p-8"><div class="flex flex-wrap items-center gap-2"><span class="rounded-full bg-sign-light px-3 py-1 text-xs font-semibold text-sign-primary">{{ ucfirst($activity->kind) }}</span><span class="text-xs font-semibold text-sign-muted">{{ ucfirst(str_replace('-', ' ', $activity->resource_type)) }}</span><span class="rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-semibold text-sign-muted">{{ $activity->is_published ? 'Published' : 'Draft activity' }}</span></div><h3 class="mt-3 font-heading text-xl font-semibold text-sign-primary">{{ $blockTitle ?: $activity->title }}</h3>@if($block->body || $activity->short_description)<p class="mt-2 text-sm leading-6 text-sign-muted">{{ $block->body ?: $activity->short_description }}</p>@endif@if($activityUrl)<a href="{{ $activityUrl }}" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white">Open {{ $activity->kind }}</a>@endif</div>
                        @else
                            <div class="p-5 text-sm text-sign-muted sm:px-8">This block is not fully configured yet.</div>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </x-container>
</section>
@endif
