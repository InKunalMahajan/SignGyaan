@extends('teacher.layout')

@section('title', 'My Courses')

@section('content')
    <div class="mb-8">
        <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Curriculum workspace</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight">My Courses</h1>
        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Manage Units and Lessons for Courses you created. Shared Courses created by another Teacher remain read-only to you.</p>
    </div>

    @if ($courses->isEmpty())
        <section class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">
            <h2 class="text-xl font-black">No Courses created yet</h2>
            <p class="mt-2 text-sm text-slate-500">Create a Course from one of your classes, then return here to build its curriculum.</p>
            <a href="{{ route('teacher.classes.index') }}" class="mt-5 inline-flex rounded-xl bg-blue-700 px-4 py-3 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Open My Classes</a>
        </section>
    @else
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($courses as $course)
                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-black text-cyan-800">{{ $course->subject?->name ?: 'Course' }}</span>
                        <span class="rounded-full px-2.5 py-1 text-xs font-black {{ $course->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $course->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                    <h2 class="mt-4 text-xl font-black">{{ $course->title }}</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">{{ $course->description ?: 'No course description yet.' }}</p>
                    <div class="mt-5 flex items-center justify-between gap-4 rounded-2xl bg-slate-50 px-4 py-3">
                        <span class="text-sm font-bold text-slate-500">Units</span>
                        <span class="text-lg font-black">{{ $course->units_count }}</span>
                    </div>
                    <a href="{{ route('teacher.courses.curriculum.show', $course) }}" class="mt-5 inline-flex rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-black text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">Manage Curriculum →</a>
                </article>
            @endforeach
        </section>

        <div class="mt-8">{{ $courses->links() }}</div>
    @endif
@endsection
