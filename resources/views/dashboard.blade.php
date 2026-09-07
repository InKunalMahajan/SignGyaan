<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SignGyaan accessible learning dashboard">
    <title>{{ $dashboard['label'] }} Dashboard | SignGyaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <a href="#main-content" class="sr-only z-50 rounded-lg bg-slate-950 px-4 py-3 text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Skip to main content</a>

    <div class="min-h-screen lg:grid lg:grid-cols-[280px_minmax(0,1fr)]">
        <aside class="border-b border-slate-200 bg-white lg:min-h-screen lg:border-b-0 lg:border-r">
            <div class="flex items-center justify-between gap-4 px-5 py-5 lg:px-6">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl focus:outline-none focus:ring-4 focus:ring-cyan-200" aria-label="SignGyaan dashboard home">
                    <span class="grid size-11 place-items-center rounded-2xl bg-gradient-to-br from-blue-700 to-cyan-500 text-sm font-black text-white">SG</span>
                    <span>
                        <span class="block text-lg font-black">SignGyaan</span>
                        <span class="block text-xs font-medium text-slate-500">Accessible learning</span>
                    </span>
                </a>
                <span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-bold text-cyan-800 lg:hidden">{{ $dashboard['label'] }}</span>
            </div>

            <nav class="px-4 pb-5 lg:px-5" aria-label="Dashboard navigation">
                <p class="mb-2 hidden px-3 text-xs font-bold uppercase tracking-[0.18em] text-slate-400 lg:block">Navigation</p>
                <div class="flex gap-2 overflow-x-auto pb-1 lg:flex-col lg:overflow-visible">
                    @auth
                        <a href="{{ route('dashboard') }}" class="flex min-w-fit items-center gap-3 rounded-xl bg-blue-700 px-3 py-3 text-sm font-bold text-white shadow-sm focus:outline-none focus:ring-4 focus:ring-cyan-200" aria-current="page">My dashboard</a>

                        @if (auth()->user()->hasRole('learner'))
                            <a href="{{ route('learner.classes.index') }}" class="flex min-w-fit items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-cyan-200">My Classes</a>
                            <a href="{{ route('learner.profile.show') }}" class="flex min-w-fit items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-cyan-200">My profile</a>
                            <a href="{{ route('learner.parent-links.index') }}" class="flex min-w-fit items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-cyan-200">Parent access</a>
                        @endif

                        @if (auth()->user()->hasRole('parents'))
                            <a href="{{ route('parents.profile.show') }}" class="flex min-w-fit items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-cyan-200">Parent profile</a>
                        @endif

                        @if (auth()->user()->hasRole('teacher'))
                            <a href="{{ route('teacher.classes.index') }}" class="flex min-w-fit items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-cyan-200">My Classes</a>
                            <a href="{{ route('teacher.profile.show') }}" class="flex min-w-fit items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-cyan-200">Teacher profile</a>
                        @endif

                        @if (auth()->user()->hasRole('admin'))
                            <a href="{{ route('admin.users.index') }}" class="flex min-w-fit items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-cyan-200">Users & Roles</a>
                        @endif

                        <a href="{{ route('dashboard.guest') }}" class="flex min-w-fit items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-cyan-200">Explore public</a>
                    @else
                        <a href="{{ route('dashboard.guest') }}" class="flex min-w-fit items-center gap-3 rounded-xl bg-blue-700 px-3 py-3 text-sm font-bold text-white shadow-sm focus:outline-none focus:ring-4 focus:ring-cyan-200" aria-current="page">Guest dashboard</a>
                        <a href="{{ route('login') }}" class="flex min-w-fit items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-cyan-200">Sign in</a>
                        <a href="{{ route('register') }}" class="flex min-w-fit items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-cyan-200">Create account</a>
                    @endauth
                </div>
            </nav>

            @auth
                <div class="mx-5 mb-5 hidden rounded-2xl border border-slate-200 bg-slate-50 p-4 lg:block">
                    <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-400">Signed in as</p>
                    <p class="mt-2 truncate text-sm font-black">{{ auth()->user()->name }}</p>
                    <p class="mt-1 text-xs font-bold text-cyan-700">{{ ucfirst(auth()->user()->role) }}</p>
                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-black text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-cyan-200">Sign out</button>
                    </form>
                </div>
            @endauth
        </aside>

        <main id="main-content" class="min-w-0">
            <header class="border-b border-slate-200 bg-white px-5 py-4 sm:px-8 lg:px-10">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-700">{{ $dashboard['eyebrow'] }}</p>
                        <p class="mt-1 text-sm font-semibold text-slate-500">{{ $dashboard['label'] }} Dashboard</p>
                    </div>
                    @auth
                        <span class="hidden text-right sm:block">
                            <span class="block max-w-44 truncate text-sm font-black">{{ auth()->user()->name }}</span>
                            <span class="block text-xs font-semibold text-slate-500">{{ ucfirst(auth()->user()->role) }} account</span>
                        </span>
                    @endauth
                </div>
            </header>

            <div class="mx-auto max-w-7xl space-y-8 px-5 py-7 sm:px-8 lg:px-10 lg:py-10">
                <section class="overflow-hidden rounded-3xl bg-gradient-to-br from-blue-800 via-blue-700 to-cyan-600 p-6 text-white shadow-lg sm:p-8 lg:p-10">
                    <div class="max-w-3xl">
                        <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-[0.14em] text-cyan-50">{{ $dashboard['label'] }} experience</span>
                        <h1 class="mt-5 text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl">{{ $dashboard['title'] }}</h1>
                        <p class="mt-4 max-w-2xl text-base leading-7 text-blue-50 sm:text-lg">{{ $dashboard['description'] }}</p>
                        <div class="mt-7 flex flex-wrap gap-3">
                            @if ($role === 'guest' && ! auth()->check())
                                <a href="{{ route('register') }}" class="rounded-xl bg-white px-5 py-3 text-sm font-black text-blue-800 focus:outline-none focus:ring-4 focus:ring-white/40">Join SignGyaan</a>
                            @elseif ($role === 'admin')
                                <a href="{{ route('admin.users.index') }}" class="rounded-xl bg-white px-5 py-3 text-sm font-black text-blue-800 focus:outline-none focus:ring-4 focus:ring-white/40">Manage users & roles</a>
                            @elseif ($role === 'parents')
                                <a href="{{ route('parents.profile.show') }}" class="rounded-xl bg-white px-5 py-3 text-sm font-black text-blue-800 focus:outline-none focus:ring-4 focus:ring-white/40">Manage linked learners</a>
                            @elseif ($role === 'teacher')
                                <a href="{{ route('teacher.classes.index') }}" class="rounded-xl bg-white px-5 py-3 text-sm font-black text-blue-800 focus:outline-none focus:ring-4 focus:ring-white/40">Open My Classes</a>
                            @elseif ($role === 'learner')
                                <a href="{{ route('learner.classes.index') }}" class="rounded-xl bg-white px-5 py-3 text-sm font-black text-blue-800 focus:outline-none focus:ring-4 focus:ring-white/40">Open My Classes</a>
                            @else
                                <button type="button" class="rounded-xl bg-white px-5 py-3 text-sm font-black text-blue-800 focus:outline-none focus:ring-4 focus:ring-white/40">{{ $dashboard['primary_action'] }}</button>
                            @endif
                            <a href="#workspace" class="rounded-xl border border-white/25 bg-white/10 px-5 py-3 text-sm font-bold text-white focus:outline-none focus:ring-4 focus:ring-white/30">View workspace</a>
                        </div>
                    </div>
                </section>

                <section aria-labelledby="overview-heading">
                    <div class="mb-4 flex items-end justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Overview</p>
                            <h2 id="overview-heading" class="mt-1 text-xl font-black">At a glance</h2>
                        </div>
                        <span class="text-xs font-semibold text-slate-400">Demo values</span>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-3">
                        @foreach ($dashboard['stats'] as $stat)
                            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                                <p class="text-sm font-bold text-slate-500">{{ $stat['label'] }}</p>
                                <p class="mt-3 text-3xl font-black">{{ $stat['value'] }}</p>
                                <p class="mt-2 text-xs font-semibold text-cyan-700">{{ $stat['helper'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section id="workspace" aria-labelledby="workspace-heading">
                    <div class="mb-4">
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Workspace</p>
                        <h2 id="workspace-heading" class="mt-1 text-xl font-black">What you can do</h2>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($dashboard['modules'] as $module)
                            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                                <div class="flex items-start justify-between gap-4">
                                    <span class="grid size-11 place-items-center rounded-xl bg-blue-50 text-blue-700" aria-hidden="true">→</span>
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-black uppercase tracking-wide text-slate-500">{{ $module['tag'] }}</span>
                                </div>
                                <h3 class="mt-5 text-base font-black">{{ $module['title'] }}</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $module['description'] }}</p>

                                @if ($role === 'admin' && $module['title'] === 'Users & Roles')
                                    <a href="{{ route('admin.users.index') }}" class="mt-5 inline-flex rounded-lg text-sm font-black text-blue-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Open →</a>
                                @elseif ($role === 'parents' && $module['title'] === 'Learner Progress')
                                    <a href="{{ route('parents.profile.show') }}" class="mt-5 inline-flex rounded-lg text-sm font-black text-blue-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Open →</a>
                                @elseif ($role === 'teacher' && $module['title'] === 'My Classes')
                                    <a href="{{ route('teacher.classes.index') }}" class="mt-5 inline-flex rounded-lg text-sm font-black text-blue-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Open →</a>
                                @elseif ($role === 'learner' && in_array($module['title'], ['Continue Learning', 'My Courses'], true))
                                    <a href="{{ route('learner.classes.index') }}" class="mt-5 inline-flex rounded-lg text-sm font-black text-blue-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Open →</a>
                                @else
                                    <button type="button" class="mt-5 inline-flex rounded-lg text-sm font-black text-blue-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Open →</button>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </section>

                <section class="grid gap-5 lg:grid-cols-[minmax(0,1.3fr)_minmax(280px,.7fr)]" aria-labelledby="activity-heading">
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Recent</p>
                        <h2 id="activity-heading" class="mt-1 text-xl font-black">Activity & next steps</h2>
                        <div class="mt-5 divide-y divide-slate-100">
                            @foreach ($dashboard['updates'] as $update)
                                <div class="flex gap-4 py-4 first:pt-0 last:pb-0">
                                    <span class="mt-1 size-2.5 shrink-0 rounded-full bg-cyan-500" aria-hidden="true"></span>
                                    <div>
                                        <p class="text-sm font-black">{{ $update['title'] }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $update['meta'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </article>

                    <aside class="rounded-2xl border border-blue-100 bg-blue-50 p-5 sm:p-6" aria-label="Dashboard authentication status">
                        @auth
                            <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-black uppercase tracking-wide text-emerald-700 shadow-sm">Authenticated</span>
                            <h2 class="mt-4 text-lg font-black text-blue-950">Role access is active</h2>
                            <p class="mt-2 text-sm leading-6 text-blue-900/80">Your account is automatically routed to its assigned dashboard. Other private role dashboards are blocked.</p>
                            <div class="mt-5 rounded-xl border border-blue-100 bg-white/70 p-4">
                                <p class="text-xs font-bold uppercase tracking-[0.14em] text-blue-500">Account role</p>
                                <p class="mt-1 font-black text-blue-950">{{ $dashboard['label'] }}</p>
                            </div>
                        @else
                            <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-black uppercase tracking-wide text-blue-700 shadow-sm">Guest access</span>
                            <h2 class="mt-4 text-lg font-black text-blue-950">Explore before joining</h2>
                            <p class="mt-2 text-sm leading-6 text-blue-900/80">Guest pages stay public. Create an account or sign in to open a protected role dashboard.</p>
                            <div class="mt-5 flex flex-wrap gap-2">
                                <a href="{{ route('login') }}" class="rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Sign in</a>
                                <a href="{{ route('register') }}" class="rounded-xl border border-blue-200 bg-white px-4 py-2.5 text-sm font-black text-blue-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Create account</a>
                            </div>
                        @endauth
                    </aside>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
