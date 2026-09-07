<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="View assigned SignGyaan courses for your class">
    <title>{{ $class->name }} | SignGyaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <a href="#main-content" class="sr-only z-50 rounded-lg bg-slate-950 px-4 py-3 text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Skip to main content</a>

    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-5 py-4 sm:px-8">
            <a href="{{ route('learner.classes.index') }}" class="flex items-center gap-3 rounded-xl focus:outline-none focus:ring-4 focus:ring-cyan-200">
                <span class="grid size-11 place-items-center rounded-2xl bg-gradient-to-br from-blue-700 to-cyan-500 text-sm font-black text-white">SG</span>
                <span>
                    <span class="block text-lg font-black">SignGyaan</span>
                    <span class="block text-xs font-semibold text-slate-500">My Classes</span>
                </span>
            </a>
            <a href="{{ route('dashboard.role', 'learner') }}" class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Dashboard</a>
        </div>
    </header>

    <main id="main-content" class="mx-auto max-w-6xl space-y-7 px-5 py-8 sm:px-8 lg:py-10">
        <section class="rounded-3xl bg-gradient-to-br from-blue-800 via-blue-700 to-cyan-600 p-6 text-white shadow-lg sm:p-8">
            <a href="{{ route('learner.classes.index') }}" class="text-sm font-black text-cyan-100 focus:outline-none focus:ring-4 focus:ring-white/30">← Back to My Classes</a>
            <div class="mt-5 flex flex-wrap items-start justify-between gap-5">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-cyan-100">{{ $class->code }}</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">{{ $class->name }}</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-blue-50 sm:text-base">{{ $class->description ?: 'Your Teacher has not added a class description yet.' }}</p>
                </div>
                <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-black">{{ $class->is_active ? 'Active class' : 'Archived class' }}</span>
            </div>
        </section>

        @unless ($class->is_active)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-bold text-amber-900">This class is archived. You can still review its assigned active courses, but your Teacher may no longer update the class.</div>
        @endunless

        <section class="grid gap-4 sm:grid-cols-4" aria-label="Class summary">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-bold text-slate-500">Subject</p>
                <p class="mt-2 font-black">{{ $class->subject ?: '—' }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-bold text-slate-500">Level</p>
                <p class="mt-2 font-black">{{ $class->level ?: '—' }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-bold text-slate-500">Academic year</p>
                <p class="mt-2 font-black">{{ $class->academic_year ?: '—' }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-bold text-slate-500">Assigned courses</p>
                <p class="mt-2 text-xl font-black">{{ $class->courses->count() }}</p>
            </article>
        </section>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
            <section aria-labelledby="courses-heading">
                <div class="mb-4">
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Learning path</p>
                    <h2 id="courses-heading" class="mt-1 text-2xl font-black">Assigned Courses</h2>
                </div>

                @if ($class->courses->isEmpty())
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">
                        <p class="font-black">No courses assigned yet.</p>
                        <p class="mt-2 text-sm text-slate-500">Your Teacher will add courses here when they are ready.</p>
                    </div>
                @else
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach ($class->courses as $course)
                            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-black text-cyan-800">{{ $course->subject?->name ?: 'Course' }}</span>
                                    @if ($course->level)
                                        <span class="text-xs font-bold text-slate-400">{{ $course->level }}</span>
                                    @endif
                                </div>
                                <h3 class="mt-4 text-xl font-black">{{ $course->title }}</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-500">{{ $course->description ?: 'Course lessons and learning materials will appear here as SignGyaan content is added.' }}</p>
                                <div class="mt-5 rounded-2xl bg-blue-50 px-4 py-3 text-sm font-bold text-blue-900">Course assigned to this class</div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

            <aside class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm" aria-labelledby="teacher-heading">
                <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Teacher</p>
                <h2 id="teacher-heading" class="mt-2 text-xl font-black">{{ $class->teacher?->name ?: 'Teacher' }}</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">{{ $class->teacher?->teacherProfile?->designation ?: 'SignGyaan Teacher' }}</p>

                <dl class="mt-5 space-y-4 border-t border-slate-100 pt-5 text-sm">
                    <div>
                        <dt class="font-bold text-slate-500">Communication</dt>
                        <dd class="mt-1 font-black">{{ strtoupper($class->teacher?->teacherProfile?->communication_preference ?: 'ISL + Text') }}</dd>
                    </div>
                    <div>
                        <dt class="font-bold text-slate-500">Class code</dt>
                        <dd class="mt-1 font-black tracking-wide">{{ $class->code }}</dd>
                    </div>
                </dl>
            </aside>
        </div>
    </main>
</body>
</html>
