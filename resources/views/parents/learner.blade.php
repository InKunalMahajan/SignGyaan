<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="View an approved Learner summary in SignGyaan">
    <title>{{ $learner->name }} | Parent View | SignGyaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <a href="#main-content" class="sr-only z-50 rounded-lg bg-slate-950 px-4 py-3 text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Skip to main content</a>

    {{-- The fallback protected header clones this secure form into the account dropdown. --}}
    <form method="POST" action="{{ route('logout') }}" class="hidden" aria-hidden="true">
        @csrf
        <button type="submit">Sign out</button>
    </form>

    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-5 py-4 sm:px-8">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl focus:outline-none focus:ring-4 focus:ring-cyan-200">
                <span class="grid size-10 place-items-center rounded-2xl bg-gradient-to-br from-blue-700 to-cyan-500 text-sm font-black text-white">SG</span>
                <span>
                    <span class="block font-black">SignGyaan</span>
                    <span class="block text-xs font-semibold text-slate-500">Family learning</span>
                </span>
            </a>
            <a href="{{ route('parents.profile.show') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-black text-slate-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Back to Parent Profile</a>
        </div>
    </header>

    <main id="main-content" class="mx-auto max-w-5xl space-y-6 px-5 py-7 sm:px-8 lg:py-10">
        <section class="rounded-3xl bg-gradient-to-br from-blue-800 to-cyan-600 p-6 text-white shadow-lg sm:p-8">
            <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-black uppercase tracking-[0.14em]">Approved family access</span>
            <h1 class="mt-4 text-3xl font-black tracking-tight sm:text-4xl">{{ $learner->name }}</h1>
            <p class="mt-2 text-blue-50">Relationship: {{ ucfirst($link->relationship) }}</p>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3" aria-label="Learner profile summary">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-400">Education level</p>
                <p class="mt-2 text-lg font-black text-slate-950">{{ $profile?->education_level ?: 'Not added yet' }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-400">Class / Grade</p>
                <p class="mt-2 text-lg font-black text-slate-950">{{ $profile?->class_grade ?: 'Not added yet' }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-400">Institution</p>
                <p class="mt-2 text-lg font-black text-slate-950">{{ $profile?->institution ?: 'Not added yet' }}</p>
            </article>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Accessibility & communication</p>
            <h2 class="mt-1 text-xl font-black">Learner Preferences</h2>
            <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl bg-slate-50 p-4">
                    <dt class="text-xs font-black uppercase tracking-wide text-slate-400">Preferred language</dt>
                    <dd class="mt-1 font-black text-slate-900">{{ $profile?->preferred_language ? ucfirst($profile->preferred_language) : 'Not added yet' }}</dd>
                </div>
                <div class="rounded-xl bg-slate-50 p-4">
                    <dt class="text-xs font-black uppercase tracking-wide text-slate-400">Communication mode</dt>
                    <dd class="mt-1 font-black text-slate-900">{{ $profile?->communication_mode ? strtoupper($profile->communication_mode) : 'Not added yet' }}</dd>
                </div>
            </dl>
        </section>

        <section class="rounded-2xl border border-blue-100 bg-blue-50 p-5 sm:p-6">
            <p class="text-xs font-black uppercase tracking-[0.16em] text-blue-600">Learning progress</p>
            <h2 class="mt-1 text-xl font-black text-blue-950">Progress will appear here next</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-blue-900/80">This Step 5 page confirms secure family access and shows the approved Learner profile summary. Course, lesson, assessment, and learning-time data can be connected when real learning progress models are added.</p>
        </section>

        <section class="rounded-2xl border border-rose-100 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="font-black text-slate-950">Remove this link</h2>
            <p class="mt-1 text-sm text-slate-500">Removing the link immediately removes your Parent access to this Learner summary.</p>
            <form method="POST" action="{{ route('parents.learners.destroy', $link) }}" class="mt-4" onsubmit="return confirm('Remove this Learner link?')">
                @csrf
                @method('DELETE')
                <button class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-black text-rose-700 focus:outline-none focus:ring-4 focus:ring-rose-100">Remove learner link</button>
            </form>
        </section>
    </main>
</body>
</html>
