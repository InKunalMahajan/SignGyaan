<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SignGyaan teacher workspace">
    <title>@yield('title', 'Teacher') | SignGyaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <a href="#main-content" class="sr-only z-50 rounded-lg bg-slate-950 px-4 py-3 text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Skip to main content</a>

    <div class="min-h-screen lg:grid lg:grid-cols-[280px_minmax(0,1fr)]">
        <aside class="border-b border-slate-200 bg-white lg:min-h-screen lg:border-b-0 lg:border-r">
            <div class="px-5 py-5 lg:px-6">
                <a href="{{ route('dashboard.role', 'teacher') }}" class="flex items-center gap-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-900" aria-label="SignGyaan teacher dashboard">
                    <span class="grid size-11 place-items-center rounded-xl bg-slate-950 text-sm font-black text-white">SG</span>
                    <span>
                        <span class="block text-lg font-black">SignGyaan</span>
                        <span class="block text-xs font-medium text-slate-500">Accessible learning</span>
                    </span>
                </a>
            </div>

            <nav class="px-4 pb-5 lg:px-5" aria-label="Teacher navigation">
                <p class="mb-2 hidden px-3 text-xs font-bold uppercase tracking-[0.18em] text-slate-400 lg:block">Navigation</p>
                <div class="flex gap-2 overflow-x-auto pb-1 lg:flex-col lg:overflow-visible">
                    <a href="{{ route('dashboard.role', 'teacher') }}" class="flex min-w-fit items-center rounded-xl px-3 py-3 text-sm font-bold {{ request()->routeIs('dashboard.role') ? 'bg-slate-950 text-white' : 'text-slate-700 hover:bg-slate-100' }}">My dashboard</a>
                    <a href="{{ route('teacher.classes.index') }}" class="flex min-w-fit items-center rounded-xl px-3 py-3 text-sm font-bold {{ request()->routeIs('teacher.classes.*') ? 'bg-slate-950 text-white' : 'text-slate-700 hover:bg-slate-100' }}">My Classes</a>
                    <a href="{{ route('teacher.courses.index') }}" class="flex min-w-fit items-center rounded-xl px-3 py-3 text-sm font-bold {{ request()->routeIs('teacher.courses.*') ? 'bg-slate-950 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Course Content</a>
                    <a href="{{ route('teacher.profile.show') }}" class="flex min-w-fit items-center rounded-xl px-3 py-3 text-sm font-bold {{ request()->routeIs('teacher.profile.*') ? 'bg-slate-950 text-white' : 'text-slate-700 hover:bg-slate-100' }}">My profile</a>
                    <a href="{{ route('dashboard.guest') }}" class="flex min-w-fit items-center rounded-xl px-3 py-3 text-sm font-bold text-slate-700 hover:bg-slate-100">Explore public</a>
                </div>
            </nav>

            <div class="mx-5 mb-5 hidden rounded-xl border border-slate-200 bg-slate-50 p-4 lg:block">
                <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-400">Signed in as</p>
                <p class="mt-2 truncate text-sm font-black">{{ auth()->user()->name }}</p>
                <p class="mt-1 text-xs font-semibold text-slate-500">Teacher</p>
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm font-bold text-slate-800 hover:bg-slate-100">Sign out</button>
                </form>
            </div>
        </aside>

        <main id="main-content" class="min-w-0">
            <header class="border-b border-slate-200 bg-white px-5 py-4 sm:px-8 lg:px-10">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Teaching Workspace</p>
                        <p class="mt-1 text-sm font-semibold text-slate-500">@yield('title', 'Teacher')</p>
                    </div>
                    <x-app-account-menu role="teacher" />
                </div>
            </header>

            <div class="mx-auto max-w-7xl px-5 py-8 sm:px-8 lg:px-10 lg:py-10">
                @if (session('status'))
                    <div class="mb-6 rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-bold text-slate-900" role="status">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-slate-300 bg-slate-100 px-4 py-4 text-sm text-slate-900" role="alert">
                        <p class="font-black">Please check the highlighted information.</p>
                        <ul class="mt-2 list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
