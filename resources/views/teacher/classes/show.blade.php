@extends('teacher.layout')

@section('title', $class->name)

@section('content')
    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('teacher.classes.index') }}" class="text-sm font-black text-blue-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">← My Classes</a>
            <p class="mt-4 text-xs font-black uppercase tracking-[0.16em] text-cyan-700">{{ $class->code }}</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight">{{ $class->name }}</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">{{ $class->description ?: 'No class description has been added yet.' }}</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('teacher.classes.edit', $class) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-700 shadow-sm focus:outline-none focus:ring-4 focus:ring-cyan-200">Edit class</a>
            <form method="POST" action="{{ route('teacher.classes.status', $class) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="rounded-xl px-4 py-3 text-sm font-black focus:outline-none focus:ring-4 focus:ring-cyan-200 {{ $class->is_active ? 'bg-slate-900 text-white' : 'bg-emerald-600 text-white' }}">{{ $class->is_active ? 'Archive class' : 'Activate class' }}</button>
            </form>
        </div>
    </div>

    <section class="mb-8 grid gap-4 sm:grid-cols-4" aria-label="Class details">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-500">Status</p>
            <p class="mt-2 text-lg font-black">{{ $class->is_active ? 'Active' : 'Archived' }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-500">Subject</p>
            <p class="mt-2 text-lg font-black">{{ $class->subject ?: '—' }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-500">Level</p>
            <p class="mt-2 text-lg font-black">{{ $class->level ?: '—' }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-500">Learners</p>
            <p class="mt-2 text-lg font-black">{{ $class->learners->count() }}</p>
        </article>
    </section>

    <div class="grid gap-6 lg:grid-cols-[minmax(300px,.7fr)_minmax(0,1.3fr)]">
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-black uppercase tracking-[0.14em] text-cyan-700">Roster</p>
            <h2 class="mt-2 text-xl font-black">Add a Learner</h2>
            <p class="mt-2 text-sm leading-6 text-slate-500">Use the exact email on the active SignGyaan Learner account.</p>

            @if ($class->is_active)
                <form method="POST" action="{{ route('teacher.classes.learners.store', $class) }}" class="mt-5">
                    @csrf
                    <label class="block">
                        <span class="text-sm font-black text-slate-700">Learner email</span>
                        <input type="email" name="learner_email" value="{{ old('learner_email') }}" required placeholder="learner@example.com" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
                    </label>
                    <button type="submit" class="mt-4 w-full rounded-xl bg-blue-700 px-4 py-3 text-sm font-black text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">Add to class</button>
                </form>
            @else
                <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm font-bold text-amber-900">Activate this class before adding Learners.</div>
            @endif

            <dl class="mt-6 space-y-3 border-t border-slate-100 pt-5 text-sm">
                <div><dt class="font-bold text-slate-500">Academic year</dt><dd class="mt-1 font-black">{{ $class->academic_year ?: '—' }}</dd></div>
                <div><dt class="font-bold text-slate-500">Class code</dt><dd class="mt-1 font-black tracking-wide">{{ $class->code }}</dd></div>
            </dl>
        </section>

        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-5">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-cyan-700">Learner roster</p>
                <h2 class="mt-1 text-xl font-black">Enrolled Learners</h2>
            </div>

            @if ($class->learners->isEmpty())
                <div class="p-8 text-center">
                    <p class="font-black">No Learners enrolled yet.</p>
                    <p class="mt-2 text-sm text-slate-500">Add the first Learner using the form.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach ($class->learners as $learner)
                        <article class="flex flex-wrap items-center justify-between gap-4 px-6 py-5">
                            <div class="min-w-0">
                                <h3 class="font-black text-slate-950">{{ $learner->name }}</h3>
                                <p class="mt-1 text-sm text-slate-500">{{ $learner->email }}</p>
                                <p class="mt-2 text-xs font-bold text-cyan-700">
                                    {{ $learner->learnerProfile?->education_level ?: 'Education level not set' }}
                                    @if ($learner->learnerProfile?->class_grade)
                                        · {{ $learner->learnerProfile->class_grade }}
                                    @endif
                                </p>
                            </div>
                            <form method="POST" action="{{ route('teacher.classes.learners.destroy', [$class, $learner]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-xl border border-rose-200 bg-white px-3 py-2 text-sm font-black text-rose-700 hover:bg-rose-50 focus:outline-none focus:ring-4 focus:ring-rose-100">Remove</button>
                            </form>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
