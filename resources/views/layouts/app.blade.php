<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="@yield('description', 'SignGyaan - Accessible visual learning through Indian Sign Language.')"
    >

    <meta name="theme-color" content="#145886">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'SignGyaan')
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>
        a[aria-label="SignGyaan home"] {
            display: block !important;
            width: 220px;
            height: 54px;
            flex-shrink: 0;
            background-image: url("{{ asset('images/signgyaan-logo.png') }}");
            background-repeat: no-repeat;
            background-position: left center;
            background-size: contain;
        }

        a[aria-label="SignGyaan home"] > * {
            display: none !important;
        }

        @media (max-width: 639px) {
            a[aria-label="SignGyaan home"] {
                width: 168px;
                height: 44px;
            }
        }
    </style>

    @livewireStyles
    @stack('head')
</head>

@php
    $accessibilityPreferences = auth()->check()
        ? (auth()->user()->accessibility_preferences ?? [])
        : [];
@endphp

<body
    class="min-h-screen bg-white font-sans text-sign-text antialiased"
    data-public-shell
    data-prefer-captions="{{ $accessibilityPreferences['captions'] ?? 'manual' }}"
    data-prefer-transcript="{{ $accessibilityPreferences['transcript'] ?? 'show' }}"
    data-prefer-simple-summary="{{ $accessibilityPreferences['simple_summary'] ?? 'show' }}"
    data-reduced-motion="{{ $accessibilityPreferences['reduced_motion'] ?? 'system' }}"
>
    <a
        href="#main-content"
        class="fixed left-4 top-4 z-[100] -translate-y-24 rounded-lg bg-sign-dark px-4 py-3 text-sm font-semibold text-white shadow-lg transition focus:translate-y-0"
    >
        Skip to main content
    </a>

    <div class="flex min-h-screen flex-col">

        @include('partials.header')

        <main id="main-content" tabindex="-1" class="min-w-0 flex-1" data-public-main>
            @yield('content')

            @if (request()->routeIs('home'))
                @include('partials.home.how-it-works')
                @include('partials.home.isl-learning')
                @include('partials.home.popular-topics')
                @include('partials.home.final-cta')
            @endif
        </main>

        @include('partials.footer')

    </div>

    @livewireScripts
    @stack('scripts')
    <script src="{{ asset('js/learner-dashboard-accessibility.js') }}" defer></script>
    <script src="{{ asset('js/notification-accessibility.js') }}" defer></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const shell = document.body;

            if (shell.dataset.preferCaptions === 'prefer') {
                document.querySelectorAll('#lesson-video video').forEach((video) => {
                    const enablePreferredCaptions = () => {
                        const tracks = Array.from(video.textTracks || []);
                        const preferredTrack = tracks.find((track) => ['captions', 'subtitles'].includes(track.kind));

                        if (preferredTrack) {
                            tracks.forEach((track) => {
                                if (['captions', 'subtitles'].includes(track.kind)) {
                                    track.mode = track === preferredTrack ? 'showing' : 'hidden';
                                }
                            });
                        }
                    };

                    video.addEventListener('loadedmetadata', enablePreferredCaptions, { once: true });
                    enablePreferredCaptions();
                });
            }
        });
    </script>
</body>
</html>