@extends('layouts.app')

@section('title', $term->term.' - ISL Vocabulary - SignGyaan')
@section('description', $term->meaning ?: 'Learn this Indian Sign Language vocabulary term on SignGyaan.')

@section('content')
    @php
        $linkedMedia = $term->mediaAsset;
        $videoUrl = $linkedMedia?->publicUrl() ?: $term->isl_video_url;
        $mimeType = $linkedMedia?->mime_type;
        $path = $videoUrl ? (string) parse_url($videoUrl, PHP_URL_PATH) : '';
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $isDirectVideo = ($mimeType && str_starts_with(strtolower($mimeType), 'video/'))
            || in_array($extension, ['mp4', 'webm', 'mov', 'ogg'], true);
    @endphp

    <section class="border-b border-sign-border bg-sign-soft py-8 sm:py-12">
        <x-container>
            <nav class="flex flex-wrap items-center gap-2 text-xs text-sign-muted sm:text-sm" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="font-semibold hover:text-sign-primary">Home</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('vocabulary.index') }}" class="font-semibold hover:text-sign-primary">ISL Vocabulary</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page" class="font-semibold text-sign-primary">{{ $term->term }}</span>
            </nav>

            <div class="mt-5 max-w-3xl">
                <div class="flex flex-wrap gap-2 text-xs">
                    @if ($term->subject)
                        <a href="{{ route('vocabulary.index', ['subject' => $term->subject->slug]) }}" class="rounded-full bg-white px-3 py-1.5 font-semibold text-sign-primary ring-1 ring-sign-border">{{ $term->subject->name }}</a>
                    @endif
                    @if ($term->course)
                        <a href="{{ route('vocabulary.index', ['course' => $term->course->slug]) }}" class="rounded-full bg-sign-light px-3 py-1.5 font-semibold text-sign-primary">{{ $term->course->title }}</a>
                    @endif
                </div>
                <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Indian Sign Language vocabulary</p>
                <h1 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-5xl">{{ $term->term }}</h1>
                @if ($term->meaning)
                    <p class="mt-4 text-base leading-8 text-sign-muted sm:text-lg">{{ $term->meaning }}</p>
                @endif
            </div>
        </x-container>
    </section>

    <section class="bg-white py-8 sm:py-12 lg:py-16">
        <x-container>
            <div class="grid gap-7 lg:grid-cols-[minmax(0,1fr)_20rem] lg:items-start">
                <div class="space-y-6">
                    <section class="overflow-hidden rounded-2xl border border-sign-border bg-white shadow-sm sm:rounded-3xl" aria-labelledby="sign-video-heading">
                        @if ($videoUrl && $isDirectVideo)
                            <video controls preload="metadata" class="aspect-video w-full bg-black" aria-label="ISL sign video for {{ $term->term }}">
                                <source src="{{ $videoUrl }}" @if ($mimeType) type="{{ $mimeType }}" @endif>
                                Your browser does not support this video.
                            </video>
                        @elseif ($videoUrl)
                            <div class="flex aspect-video min-h-56 items-center justify-center bg-sign-primary p-6 text-center text-white">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-white/70">ISL sign video</p>
                                    <h2 id="sign-video-heading" class="mt-2 font-heading text-2xl font-semibold">{{ $term->term }}</h2>
                                    <a href="{{ $videoUrl }}" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Open ISL Video</a>
                                </div>
                            </div>
                        @else
                            <div class="flex min-h-56 items-center justify-center bg-sign-soft p-6 text-center">
                                <div>
                                    <h2 id="sign-video-heading" class="font-heading text-2xl font-semibold text-sign-primary">ISL video not added yet</h2>
                                    <p class="mt-2 text-sm leading-6 text-sign-muted">Use the meaning and example below while the sign video is being prepared.</p>
                                </div>
                            </div>
                        @endif

                        <div class="p-5 sm:p-6">
                            <p class="text-sm font-semibold text-sign-primary">Sign support</p>
                            <p class="mt-1 text-sm leading-6 text-sign-muted">
                                @if ($linkedMedia)
                                    Published from the SignGyaan Media Library.
                                @elseif ($videoUrl)
                                    This vocabulary term uses a fallback ISL video URL.
                                @else
                                    No published video is available for this sign yet.
                                @endif
                            </p>
                            @if ($linkedMedia?->caption)
                                <p class="mt-2 text-sm leading-6 text-sign-muted">{{ $linkedMedia->caption }}</p>
                            @endif
                        </div>
                    </section>

                    <section class="rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl sm:p-7">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Meaning</p>
                        <h2 class="mt-2 font-heading text-2xl font-semibold text-sign-primary">What this sign means</h2>
                        <p class="mt-4 whitespace-pre-line text-sm leading-7 text-sign-muted sm:text-base">{{ $term->meaning ?: 'A meaning has not been added yet.' }}</p>
                    </section>

                    @if ($term->example)
                        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7">
                            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Example</p>
                            <h2 class="mt-2 font-heading text-2xl font-semibold text-sign-primary">See it in context</h2>
                            <p class="mt-4 whitespace-pre-line text-sm leading-7 text-sign-muted sm:text-base">{{ $term->example }}</p>
                        </section>
                    @endif
                </div>

                <aside class="space-y-4 lg:sticky lg:top-28" aria-label="Vocabulary details">
                    <div class="rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Learning context</p>
                        <dl class="mt-4 space-y-4 text-sm">
                            <div>
                                <dt class="font-semibold text-sign-primary">Subject</dt>
                                <dd class="mt-1 text-sign-muted">{{ $term->subject?->name ?: 'General vocabulary' }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-sign-primary">Course</dt>
                                <dd class="mt-1 text-sign-muted">{{ $term->course?->title ?: 'All courses' }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-sign-primary">ISL video</dt>
                                <dd class="mt-1 text-sign-muted">{{ $videoUrl ? 'Available' : 'Coming soon' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <a href="{{ route('vocabulary.index') }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">← Back to vocabulary</a>
                </aside>
            </div>
        </x-container>
    </section>
@endsection
