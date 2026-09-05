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

    <title>
        @yield('title', 'SignGyaan')
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @livewireStyles
    @stack('head')
</head>

<body class="min-h-screen bg-white font-sans text-sign-text antialiased" data-public-shell>
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
</body>
</html>
