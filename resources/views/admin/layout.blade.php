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

    <div class="min-h-screen lg:grid lg:grid-cols-[290px_minmax(0,1fr)]">
        <aside class="border-b border-slate-200 bg-white lg:min-h-screen lg:border-b-0 lg:border-r">
            <div class="flex items-center justify-between gap-4 px-5 py-5 lg:px-6">
                <a href="{{ route('dashboard.role', 'admin') }}" class="flex items-center gap-3 rounded-xl focus:outline-none focus:ring-4 focus:ring-cyan-200" aria-label="SignGyaan admin dashboard home">
                    <span class="grid size-11 place-items-center rounded-2xl bg-gradient-to-br from-blue-700 to-cyan-500 text-sm font-black text-white">SG</span>
                    <span>
                        <span class="block text-lg font-black">SignGyaan</span>
                        <span class="block text-xs font-medium text-slate-500">Accessible learning</span>
                    </span>
                </a>
                <span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-bold text-cyan-800 lg:hidden">Admin</span>
            </div>

            <nav class="px-4 pb-5 lg:px-5" aria-label="Admin navigation">
                <p class="mb-2 hidden px-3 text-xs font-bold uppercase tracking-[0.18em] text-slate-400 lg:block">Platform</p>
                <div class="flex gap-2 overflow-x-auto pb-1 lg:flex-col lg:overflow-visible">
                    <a href="{{ route('dashboard.role', 'admin') }}" class="flex min-w-fit items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold {{ request()->routeIs('dashboard.role') ? 'bg-blue-700 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }} focus:outline-none focus:ring-4 focus:ring-cyan-200" @if(request()->routeIs('dashboard.role')) aria-current="page" @endif>My Dashboard</a>
                    <a href="{{ route('admin.users.index') }}" class="flex min-w-fit items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold {{ request()->routeIs('admin.users.*') ? 'bg-blue-700 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }} focus:outline-none focus:ring-4 focus:ring-cyan-200" @if(request()->routeIs('admin.users.*')) aria-current="page" @endif>Users &amp; Roles</a>
                </div>

                <p class="mb-2 mt-5 hidden px-3 text-xs font-bold uppercase tracking-[0.18em] text-slate-400 lg:block">1 · Academic Content</p>
                <div class="flex gap-2 overflow-x-auto pb-1 lg:flex-col lg:overflow-visible">
                    <a href="{{ route('admin.curriculum.index') }}" class="flex min-w-fit items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold {{ request()->routeIs('admin.curriculum.index') || request()->routeIs('admin.curriculum.boards.*') || request()->routeIs('admin.curriculum.classes.*') || request()->routeIs('admin.curriculum.subjects.*') ? 'bg-blue-700 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }} focus:outline-none focus:ring-4 focus:ring-cyan-200" @if(request()->routeIs('admin.curriculum.index') || request()->routeIs('admin.curriculum.boards.*') || request()->routeIs('admin.curriculum.classes.*') || request()->routeIs('admin.curriculum.subjects.*')) aria-current="page" @endif>Academic Structure</a>
                    <a href="{{ route('admin.curriculum.content.index') }}" class="flex min-w-fit items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold {{ request()->routeIs('admin.curriculum.content.*') ? 'bg-blue-700 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }} focus:outline-none focus:ring-4 focus:ring-cyan-200" @if(request()->routeIs('admin.curriculum.content.*')) aria-current="page" @endif>Course Content</a>
                </div>

                <p class="mb-2 mt-5 hidden px-3 text-xs font-bold uppercase tracking-[0.18em] text-slate-400 lg:block">2 · Delivery & Outcomes</p>
                <div class="flex gap-2 overflow-x-auto pb-1 lg:flex-col lg:overflow-visible">
                    <a href="{{ route('admin.teaching.index') }}" class="flex min-w-fit items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold {{ request()->routeIs('admin.teaching.*') ? 'bg-blue-700 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }} focus:outline-none focus:ring-4 focus:ring-cyan-200" @if(request()->routeIs('admin.teaching.*')) aria-current="page" @endif>Teaching Management</a>
                    <a href="{{ route('admin.progress.index') }}" class="flex min-w-fit items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold {{ request()->routeIs('admin.progress.*') ? 'bg-blue-700 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }} focus:outline-none focus:ring-4 focus:ring-cyan-200" @if(request()->routeIs('admin.progress.*')) aria-current="page" @endif>Progress &amp; Assessment</a>
                    <a href="{{ route('dashboard.guest') }}" class="flex min-w-fit items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-cyan-200">Explore Public</a>
                </div>
            </nav>

            @auth
                <div class="mx-5 mb-5 hidden rounded-2xl border border-slate-200 bg-slate-50 p-4 lg:block">
                    <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-400">Signed in as</p>
                    <p class="mt-2 truncate text-sm font-black">{{ auth()->user()->name }}</p>
                    <p class="mt-1 text-xs font-bold text-cyan-700">Admin</p>
                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-black text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-cyan-200">Sign out</button>
                    </form>
                </div>
            @endauth
        </aside>

        <div class="min-w-0">
            <header class="border-b border-slate-200 bg-white px-5 py-4 sm:px-8 lg:px-10">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-700">Platform Management</p>
                        <p class="mt-1 text-sm font-semibold text-slate-500">@yield('title', 'Admin')</p>
                    </div>
                    @auth
                        <span class="hidden text-right sm:block">
                            <span class="block max-w-44 truncate text-sm font-black">{{ auth()->user()->name }}</span>
                            <span class="block text-xs font-semibold text-slate-500">Admin account</span>
                        </span>
                    @endauth
                </div>
            </header>

            <main id="main-content" class="mx-auto max-w-7xl px-5 py-8 sm:px-8 lg:px-10 lg:py-10">
                @if (session('success'))
                    <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-900" role="status">{{ session('success') }}</div>
                @endif

                @if (session('status'))
                    <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-900" role="status">{{ session('status') }}</div>
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
        </div>
    </div>
</body>
</html>
