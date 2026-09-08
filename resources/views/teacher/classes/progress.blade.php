@extends('teacher.layout')

@section('title', $class->name.' Progress')

@section('content')
    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('teacher.classes.show', $class) }}" class="text-sm font-black text-blue-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">← Back to {{ $class->name }}</a>
            <p class="mt-4 text-xs font-black uppercase tracking-[0.16em] text-cyan-700">{{ $class->code }}</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight">Learner Progress</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Track completion across published Lessons in active Courses assigned to this class.</p>
        </div>
        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">{{ $class->name }}</span>
    </div>

    <section class="mb-8 grid gap-4 sm:grid-cols-4" aria-label="Class progress summary">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-500">Learners</p>
            <p class="mt-2 text-3xl font-black">{{ $class->learners->count() }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-500">Active Courses</p>
            <p class="mt-2 text-3xl font-black">{{ $class->courses->count() }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-500">Published Lessons</p>
            <p class="mt-2 text-3xl font-black">{{ $publishedLessonCount }}</p>
        </article>
        <article class="rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm">
            <p class="text-sm font-bold text-blue-700">Average completion</p>
            <p class="mt-2 text-3xl font-black text-blue-950">{{ $averagePercent }}%</p>
        </article>
    </section>

    @if ($class->learners->isEmpty())
        <section class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">
            <h2 class="text-xl font-black">No Learners enrolled</h2>
            <p class="mt-2 text-sm text-slate-500">Add Learners to this class before tracking Lesson progress.</p>
        </section>
    @elseif ($publishedLessonCount === 0)
        <section class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">
            <h2 class="text-xl font-black">No published Lessons yet</h2>
            <p class="mt-2 text-sm text-slate-500">Progress will appear after published Lessons are available in assigned active Courses.</p>
        </section>
    @else
        <section class="space-y-5" aria-labelledby="learner-progress-heading">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Class report</p>
                <h2 id="learner-progress-heading" class="mt-1 text-2xl font-black">Learner completion</h2>
            </div>

            @foreach ($learnerProgress as $item)
                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
                    <div class="flex flex-wrap items-start justify-between gap-5">
                        <div>
                            <h3 class="text-xl font-black">{{ $item['learner']->name }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $item['learner']->email }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-black text-blue-700">{{ $item['percent'] }}%</p>
                            <p class="mt-1 text-xs font-bold text-slate-500">{{ $item['completed'] }} / {{ $item['total'] }} completed</p>
                        </div>
                    </div>

                    <div class="mt-5 h-3 overflow-hidden rounded-full bg-slate-100" aria-label="{{ $item['percent'] }} percent complete">
                        <div class="h-full rounded-full bg-blue-700" style="width: {{ $item['percent'] }}%"></div>
                    </div>

                    <div class="mt-5 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl bg-emerald-50 p-4">
                            <p class="text-xs font-black uppercase tracking-wide text-emerald-700">Completed</p>
                            <p class="mt-1 text-2xl font-black text-emerald-950">{{ $item['completed'] }}</p>
                        </div>
                        <div class="rounded-2xl bg-amber-50 p-4">
                            <p class="text-xs font-black uppercase tracking-wide text-amber-700">In progress</p>
                            <p class="mt-1 text-2xl font-black text-amber-950">{{ $item['in_progress'] }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-100 p-4">
                            <p class="text-xs font-black uppercase tracking-wide text-slate-600">Not started</p>
                            <p class="mt-1 text-2xl font-black">{{ $item['not_started'] }}</p>
                        </div>
                    </div>

                    <details class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <summary class="cursor-pointer font-black text-blue-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">View Course breakdown</summary>
                        <div class="mt-4 space-y-3">
                            @foreach ($item['courses'] as $course)
                                <div class="rounded-xl bg-white p-4">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <p class="text-xs font-black uppercase tracking-wide text-cyan-700">{{ $course['subject'] ?: 'Course' }}</p>
                                            <p class="mt-1 font-black">{{ $course['title'] }}</p>
                                        </div>
                                        <span class="text-sm font-black text-blue-700">{{ $course['percent'] }}%</span>
                                    </div>
                                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full bg-blue-700" style="width: {{ $course['percent'] }}%"></div>
                                    </div>
                                    <p class="mt-2 text-xs font-bold text-slate-500">{{ $course['completed'] }} of {{ $course['total'] }} Lessons completed</p>
                                </div>
                            @endforeach
                        </div>
                    </details>
                </article>
            @endforeach
        </section>
    @endif
@endsection
