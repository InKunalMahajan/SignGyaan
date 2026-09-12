@extends('learner.layout')

@section('title', 'My Classes')
@section('meta_description', 'View your SignGyaan enrolled classes and assigned courses')
@section('header_label', 'My Classes')

@section('content')
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-500">My Learning</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">My Classes</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">Open the classes your teachers have enrolled you in and see the courses assigned to each class.</p>
    </section>

    <section class="mt-8" aria-labelledby="classes-heading">
        <div class="mb-5 flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-500">Enrolments</p>
                <h2 id="classes-heading" class="mt-1 text-2xl font-black">Your enrolled classes</h2>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600">{{ $classes->total() }} total</span>
        </div>

        @if ($classes->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                <p class="text-lg font-black">No classes yet.</p>
                <p class="mt-2 text-sm text-slate-500">When a Teacher enrolls you in a class, it will appear here.</p>
            </div>
        @else
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($classes as $class)
                    <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-700">{{ $class->is_active ? 'Active' : 'Archived' }}</span>
                            <span class="text-xs font-black tracking-wide text-slate-600">{{ $class->code }}</span>
                        </div>
                        <h3 class="mt-5 text-xl font-black">{{ $class->name }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">{{ $class->subject ?: 'General learning' }}@if($class->level) · {{ $class->level }}@endif</p>
                        <dl class="mt-5 grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-xl bg-slate-50 p-3">
                                <dt class="font-bold text-slate-500">Courses</dt>
                                <dd class="mt-1 text-lg font-black">{{ $class->active_courses_count }}</dd>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-3">
                                <dt class="font-bold text-slate-500">Teacher</dt>
                                <dd class="mt-1 truncate font-black">{{ $class->teacher?->name ?: '—' }}</dd>
                            </div>
                        </dl>
                        <a href="{{ route('learner.classes.show', $class) }}" class="mt-5 inline-flex w-full justify-center rounded-lg bg-slate-950 px-4 py-3 text-sm font-black text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-slate-900">Open class</a>
                    </article>
                @endforeach
            </div>

            <div class="mt-7">{{ $classes->links() }}</div>
        @endif
    </section>
@endsection
