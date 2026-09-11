<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SignGyaan admin console">
    <title>@yield('title', 'Admin') | SignGyaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <a href="#main-content" class="sr-only z-50 rounded-lg bg-slate-950 px-4 py-3 text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Skip to main content</a>

    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-5 py-4 sm:px-8">
            <a href="{{ route('dashboard.role', 'admin') }}" class="flex items-center gap-3 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-200">
                <span class="grid size-11 place-items-center rounded-2xl bg-indigo-600 text-sm font-black text-white">SG</span>
                <span>
                    <span class="block text-lg font-black tracking-tight">SignGyaan</span>
                    <span class="block text-xs font-semibold text-slate-500">Admin Console</span>
                </span>
            </a>

            <nav class="flex flex-wrap items-center gap-2" aria-label="Admin navigation">
                <a href="{{ route('dashboard.role', 'admin') }}" class="rounded-xl px-3 py-2 text-sm font-bold {{ request()->routeIs('dashboard.role') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }} focus:outline-none focus:ring-4 focus:ring-indigo-200">Dashboard</a>
                <a href="{{ route('admin.users.index') }}" class="rounded-xl px-3 py-2 text-sm font-bold {{ request()->routeIs('admin.users.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }} focus:outline-none focus:ring-4 focus:ring-indigo-200">Users & Roles</a>
                <a href="{{ route('admin.curriculum.index') }}" class="rounded-xl px-3 py-2 text-sm font-bold {{ request()->routeIs('admin.curriculum.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }} focus:outline-none focus:ring-4 focus:ring-indigo-200">Curriculum</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-indigo-200">Sign out</button>
                </form>
            </nav>
        </div>
    </header>

    <main id="main-content" class="mx-auto max-w-7xl px-5 py-8 sm:px-8 lg:py-10">
        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-900" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900" role="alert">
                <p class="font-black">Please check the following:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
