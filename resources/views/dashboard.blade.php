<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SignGyaan accessible learning dashboard preview">
    <title>{{ $dashboard['label'] }} Dashboard | SignGyaan</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <a href="#main-content" class="sr-only z-50 rounded-lg bg-slate-950 px-4 py-3 text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4">
        Skip to main content
    </a>

    <div class="min-h-screen lg:grid lg:grid-cols-[280px_minmax(0,1fr)]">
        <aside class="border-b border-slate-200 bg-white lg:min-h-screen lg:border-b-0 lg:border-r">
            <div class="flex items-center justify-between gap-4 px-5 py-5 lg:px-6">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl focus:outline-none focus:ring-4 focus:ring-cyan-200" aria-label="SignGyaan dashboard home">
                    <span class="grid size-11 place-items-center rounded-2xl bg-gradient-to-br from-blue-700 to-cyan-500 text-sm font-black tracking-wide text-white shadow-sm">SG</span>
                    <span>
                        <span class="block text-lg font-black tracking-tight text-slate-950">SignGyaan</span>
                        <span class="block text-xs font-medium text-slate-500">Accessible learning</span>
                    </span>
                </a>
                <span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-bold text-cyan-800 lg:hidden">Preview</span>
            </div>

            <nav class="px-4 pb-5 lg:px-5" aria-label="Dashboard role preview">
                <p class="mb-2 hidden px-3 text-xs font-bold uppercase tracking-[0.18em] text-slate-400 lg:block">Dashboard views</p>
                <div class="flex gap-2 overflow-x-auto pb-1 lg:flex-col lg:overflow-visible">
                    @foreach ($roles as $roleOption)
                        @php($isActive = $roleOption === $role)
                        <a
                            href="{{ route('dashboard.role', $roleOption) }}"
                            @if ($isActive) aria-current="page" @endif
                            class="group flex min-w-fit items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold transition focus:outline-none focus:ring-4 focus:ring-cyan-200 {{ $isActive ? 'bg-blue-700 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}"
                        >
                            <span class="grid size-8 place-items-center rounded-lg {{ $isActive ? 'bg-white/15 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-white' }}" aria-hidden="true">
                                @switch($roleOption)
                                    @case('learner')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="size-4" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/></svg>
                                        @break
                                    @case('parents')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="size-4" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                        @break
                                    @case('teacher')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="size-4" stroke-width="2"><path d="m3 11 9-5 9 5-9 5-9-5Z"/><path d="M7 13.22V17l5 3 5-3v-3.78"/><path d="M21 11v5"/></svg>
                                        @break
                                    @case('admin')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="size-4" stroke-width="2"><path d="M12 15.5A3.5 3.5 0 1 0 12 8a3.5 3.5 0 0 0 0 7.5Z"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06-2.83 2.83-.06-.06A1.65 1.65 0 0 0 15 19.4a1.65 1.65 0 0 0-1 .6 1.65 1.65 0 0 0-.4 1.08V21h-4v-.08A1.65 1.65 0 0 0 8 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06-2.83-2.83.06-.06A1.65 1.65 0 0 0 3.6 15a1.65 1.65 0 0 0-.6-1 1.65 1.65 0 0 0-1.08-.4H2v-4h.08A1.65 1.65 0 0 0 3.6 8a1.65 1.65 0 0 0-.33-1.82l-.06-.06 2.83-2.83.06.06A1.65 1.65 0 0 0 8 3.6a1.65 1.65 0 0 0 1-.6 1.65 1.65 0 0 0 .4-1.08V2h4v.08A1.65 1.65 0 0 0 15 3.6a1.65 1.65 0 0 0 1.82-.33l.06-.06 2.83 2.83-.06.06A1.65 1.65 0 0 0 19.4 8c.12.38.33.73.6 1 .28.27.64.48 1.08.4H21v4h-.08A1.65 1.65 0 0 0 19.4 15Z"/></svg>
                                        @break
                                    @default
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="size-4" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                                @endswitch
                            </span>
                            {{ $roleLabels[$roleOption] }}
                        </a>
                    @endforeach
                </div>
            </nav>

            <div class="mx-5 mb-5 hidden rounded-2xl border border-cyan-100 bg-cyan-50 p-4 lg:block">
                <p class="text-sm font-black text-cyan-950">Accessibility first</p>
                <p class="mt-1 text-xs leading-5 text-cyan-900/80">Large controls, clear focus states, readable contrast, and simple visual navigation are built into this dashboard foundation.</p>
            </div>
        </aside>

        <main id="main-content" class="min-w-0">
            <header class="border-b border-slate-200 bg-white/95 px-5 py-4 backdrop-blur sm:px-8 lg:px-10">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-700">{{ $dashboard['eyebrow'] }}</p>
                        <p class="mt-1 text-sm font-semibold text-slate-500">{{ $dashboard['label'] }} Dashboard</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="hidden rounded-full bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600 sm:inline-flex">Preview data</span>
                        <button type="button" class="grid size-10 place-items-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-cyan-200" aria-label="Notifications">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="size-5" stroke-width="2"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        </button>
                    </div>
                </div>
            </header>

            <div class="mx-auto max-w-7xl space-y-8 px-5 py-7 sm:px-8 lg:px-10 lg:py-10">
                <section class="overflow-hidden rounded-3xl bg-gradient-to-br from-blue-800 via-blue-700 to-cyan-600 p-6 text-white shadow-lg shadow-blue-950/10 sm:p-8 lg:p-10">
                    <div class="max-w-3xl">
                        <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-[0.14em] text-cyan-50">{{ $dashboard['label'] }} experience</span>
                        <h1 class="mt-5 text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl">{{ $dashboard['title'] }}</h1>
                        <p class="mt-4 max-w-2xl text-base leading-7 text-blue-50 sm:text-lg">{{ $dashboard['description'] }}</p>
                        <div class="mt-7 flex flex-wrap gap-3">
                            <button type="button" class="rounded-xl bg-white px-5 py-3 text-sm font-black text-blue-800 shadow-sm transition hover:bg-cyan-50 focus:outline-none focus:ring-4 focus:ring-white/40">
                                {{ $dashboard['primary_action'] }}
                            </button>
                            <a href="#workspace" class="rounded-xl border border-white/25 bg-white/10 px-5 py-3 text-sm font-bold text-white transition hover:bg-white/15 focus:outline-none focus:ring-4 focus:ring-white/30">
                                View workspace
                            </a>
                        </div>
                    </div>
                </section>

                <section aria-labelledby="overview-heading">
                    <div class="mb-4 flex items-end justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Overview</p>
                            <h2 id="overview-heading" class="mt-1 text-xl font-black tracking-tight text-slate-950">At a glance</h2>
                        </div>
                        <span class="text-xs font-semibold text-slate-400">Demo values</span>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-3">
                        @foreach ($dashboard['stats'] as $stat)
                            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                                <p class="text-sm font-bold text-slate-500">{{ $stat['label'] }}</p>
                                <p class="mt-3 text-3xl font-black tracking-tight text-slate-950">{{ $stat['value'] }}</p>
                                <p class="mt-2 text-xs font-semibold text-cyan-700">{{ $stat['helper'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section id="workspace" aria-labelledby="workspace-heading">
                    <div class="mb-4">
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Workspace</p>
                        <h2 id="workspace-heading" class="mt-1 text-xl font-black tracking-tight text-slate-950">What you can do</h2>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($dashboard['modules'] as $module)
                            <article class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-cyan-200 hover:shadow-md">
                                <div class="flex items-start justify-between gap-4">
                                    <span class="grid size-11 place-items-center rounded-xl bg-blue-50 text-blue-700" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="size-5" stroke-width="2"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
                                    </span>
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-black uppercase tracking-wide text-slate-500">{{ $module['tag'] }}</span>
                                </div>
                                <h3 class="mt-5 text-base font-black text-slate-950">{{ $module['title'] }}</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $module['description'] }}</p>
                                <button type="button" class="mt-5 inline-flex items-center gap-2 rounded-lg text-sm font-black text-blue-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">
                                    Open
                                    <span aria-hidden="true">→</span>
                                </button>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section class="grid gap-5 lg:grid-cols-[minmax(0,1.3fr)_minmax(280px,.7fr)]" aria-labelledby="activity-heading">
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Recent</p>
                                <h2 id="activity-heading" class="mt-1 text-xl font-black tracking-tight text-slate-950">Activity & next steps</h2>
                            </div>
                            <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-500">Preview</span>
                        </div>
                        <div class="mt-5 divide-y divide-slate-100">
                            @foreach ($dashboard['updates'] as $update)
                                <div class="flex gap-4 py-4 first:pt-0 last:pb-0">
                                    <span class="mt-1 size-2.5 shrink-0 rounded-full bg-cyan-500" aria-hidden="true"></span>
                                    <div>
                                        <p class="text-sm font-black text-slate-900">{{ $update['title'] }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $update['meta'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </article>

                    <aside class="rounded-2xl border border-blue-100 bg-blue-50 p-5 sm:p-6" aria-label="Dashboard implementation note">
                        <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-black uppercase tracking-wide text-blue-700 shadow-sm">Foundation</span>
                        <h2 class="mt-4 text-lg font-black text-blue-950">Ready for real data</h2>
                        <p class="mt-2 text-sm leading-6 text-blue-900/80">This first version defines the role experience and responsive UI. The cards can now be connected to authentication, courses, assessments, progress, and reporting data as those modules are added.</p>
                        <div class="mt-5 rounded-xl border border-blue-100 bg-white/70 p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.14em] text-blue-500">Current role</p>
                            <p class="mt-1 font-black text-blue-950">{{ $dashboard['label'] }}</p>
                        </div>
                    </aside>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
