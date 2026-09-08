<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="View your SignGyaan enrolled classes and assigned courses">
    <title>My Classes | SignGyaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <a href="#main-content" class="sr-only z-50 rounded-lg bg-slate-950 px-4 py-3 text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Skip to main content</a>

    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-5 py-4 sm:px-8">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl focus:outline-none focus:ring-4 focus:ring-cyan-200">
                <span class="grid size-11 place-items-center rounded-2xl bg-gradient-to-br from-blue-700 to-cyan-500 text-sm font-black text-white">SG</span>
                <span>
                    <span class="block text-lg font-black">SignGyaan</span>
                    <span class="block text-xs font-semibold text-slate-500">Learner classes</span>
                </span>
            </a>
            <div class="flex items-center gap-2">
                <a href="{{ route('learner.profile.show') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-black text-slate-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">My Profile</a>
                <a href="{{ route('dashboard.role', 'learner') }}" class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Dashboard</a>
            </div>
        </div>
    </header>

    <main id="main-content" class="mx-auto max-w-6xl px-5 py-8 sm:px-8 lg:py-10">
        <section class="rounded-3xl bg-gradient-to-br from-blue-800 via-blue-700 to-cyan-600 p-6 text-white shadow-lg sm:p-8">
            <p class="text-xs font-black uppercase tracking-[0.18em] text-cyan-100">My Learning</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">My Classes</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-blue-50 sm:text-base">Open the classes your teachers have enrolled you in and see the courses assigned to each class.</p>
        </section>

        <section class="mt-8" aria-labelledby="classes-heading">
            <div class="mb-5 flex items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Enrolments</p>
                    <h2 id="classes-heading" class="mt-1 text-2xl font-black">Your enrolled classes</h2>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600">{{ $classes->total() }} total</span>
            </div>

            @if ($classes->isEmpty())
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">
                    <p class="text-lg font-black">No classes yet.</p>
                    <p class="mt-2 text-sm text-slate-500">When a Teacher enrolls you in a class, it will appear here.</p>
                </div>
            @else
                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($classes as $class)
                        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="flex items-start justify-between gap-4">
                                <span class="rounded-full px-3 py-1 text-xs font-black {{ $class->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $class->is_active ? 'Active' : 'Archived' }}</span>
                                <span class="text-xs font-black tracking-wide text-cyan-700">{{ $class->code }}</span>
                            </div>
                            <h3 class="mt-5 text-xl font-black">{{ $class->name }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-500">{{ $class->subject ?: 'General learning' }}@if($class->level) · {{ $class->level }}@endif</p>
                            <dl class="mt-5 grid grid-cols-2 gap-3 text-sm">
                                <div class="rounded-2xl bg-slate-50 p-3">
                                    <dt class="font-bold text-slate-500">Courses</dt>
                                    <dd class="mt-1 text-lg font-black">{{ $class->active_courses_count }}</dd>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-3">
                                    <dt class="font-bold text-slate-500">Teacher</dt>
                                    <dd class="mt-1 truncate font-black">{{ $class->teacher?->name ?: '—' }}</dd>
                                </div>
                            </dl>
                            <a href="{{ route('learner.classes.show', $class) }}" class="mt-5 inline-flex w-full justify-center rounded-xl bg-blue-700 px-4 py-3 text-sm font-black text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">Open class</a>
                        </article>
                    @endforeach
                </div>

                <div class="mt-7">{{ $classes->links() }}</div>
            @endif
        </section>
    </main>
</body>
</html>
