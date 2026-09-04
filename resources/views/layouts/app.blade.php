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

    <title>
        @yield('title', 'SignGyaan')
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @livewireStyles
</head>

<body class="min-h-screen bg-white font-sans text-sign-text antialiased">
    <div class="min-h-screen flex flex-col">

        @include('partials.header')

        <main class="flex-1">
            @yield('content')

            @if (request()->routeIs('home'))
                @include('partials.home.how-it-works')
                @include('partials.home.isl-learning')
            @endif
        </main>

        @include('partials.footer')

    </div>

    @livewireScripts

</body>
</html>