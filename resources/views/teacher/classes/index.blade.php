@extends('teacher.layout')

@section('title', 'My Classes')

@section('content')
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Teaching workspace</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight">My Classes</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Create classes, manage rosters, assign courses, and keep learner access organised.</p>
        </div>
        <a href="{{ route('teacher.classes.create') }}" class="rounded-xl bg-blue-700 px-4 py-3 text-sm font-black text-white shadow-sm hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">Create class</a>
    </div>

    <section class="mb-8 grid gap-4 sm:grid-cols-3" aria-label="Class summary">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-500">Total classes</p>
            <p class="mt-2 text-3xl font-black">{{ $classes->total() }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-500">Active classes</p>
            <p class="mt-2 text-3xl font-black">{{ $activeCount }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-500">Roster seats</p>
            <p class="mt-2 text-3xl font-black">{{ $totalLearners }}</p>
            <p class="mt-1 text-xs font-semibold text-slate-400">Across all classes</p>
        </article>
    </section>

    @if ($classes->isEmpty())
        <section class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">
            <h2 class="text-xl font-black">No classes yet</h2>
            <p class="mt-2 text-sm text-slate-500">Create your first class, add Learners, and assign the courses they should study.</p>
            <a href="{{ route('teacher.classes.create') }}" class="mt-5 inline-flex rounded-xl bg-blue-700 px-4 py-3 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Create first class</a>
        </section>
    @else
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($classes as $class)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-black uppercase tracking-wide text-cyan-700">{{ $class->code }}</p>
                            <h2 class="mt-2 text-lg font-black">{{ $class->name }}</h2>
                        </div>
                        <span class="rounded-full px-2.5 py-1 text-xs font-black {{ $class->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $class->is_active ? 'Active' : 'Archived' }}</span>
                    </div>
                    <dl class="mt-5 space-y-2 text-sm">
                        <div class="flex justify-between gap-4"><dt class="font-bold text-slate-500">Subject</dt><dd class="text-right font-black">{{ $class->subject ?: '—' }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="font-bold text-slate-500">Level</dt><dd class="text-right font-black">{{ $class->level ?: '—' }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="font-bold text-slate-500">Learners</dt><dd class="text-right font-black">{{ $class->learners_count }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="font-bold text-slate-500">Courses</dt><dd class="text-right font-black">{{ $class->courses_count }}</dd></div>
                    </dl>
                    <div class="mt-5 flex gap-2">
                        <a href="{{ route('teacher.classes.show', $class) }}" class="rounded-xl bg-blue-700 px-3 py-2 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Open class</a>
                        <a href="{{ route('teacher.classes.edit', $class) }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-black text-slate-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Edit</a>
                    </div>
                </article>
            @endforeach
        </section>

        <div class="mt-8">{{ $classes->links() }}</div>
    @endif
@endsection
