@extends('teacher.layout')
@section('title', 'Learner Progress')
@section('content')
<div class="space-y-6">
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Progress & Mastery</p>
        <h1 class="mt-2 text-3xl font-black">Learner Progress</h1>
        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">View mastery across learners and active courses in classes you teach.</p>
    </div>

    <section class="grid gap-3 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5"><p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Active classes</p><p class="mt-2 text-3xl font-black">{{ $classes->count() }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5"><p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Progress records</p><p class="mt-2 text-3xl font-black">{{ $rows->count() }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5"><p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Mastered</p><p class="mt-2 text-3xl font-black">{{ $rows->filter(fn ($row) => $row['mastery']->mastery_level === 'mastered')->count() }}</p></div>
    </section>

    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-[0.12em] text-slate-500">
                <tr><th class="px-4 py-3">Learner</th><th class="px-4 py-3">Class</th><th class="px-4 py-3">Course</th><th class="px-4 py-3">Lessons</th><th class="px-4 py-3">Assessment</th><th class="px-4 py-3">Mastery</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($rows as $row)
                    <tr>
                        <td class="px-4 py-4 font-black">{{ $row['learner']->name }}</td>
                        <td class="px-4 py-4">{{ $row['class']->name }}</td>
                        <td class="px-4 py-4">{{ $row['course']->title }}</td>
                        <td class="px-4 py-4">{{ $row['mastery']->lessons_completed }}/{{ $row['mastery']->lessons_total }}</td>
                        <td class="px-4 py-4">{{ $row['mastery']->assessment_percentage !== null ? number_format((float) $row['mastery']->assessment_percentage, 2).'%' : '—' }}</td>
                        <td class="px-4 py-4"><span class="font-black">{{ number_format((float) $row['mastery']->mastery_score, 2) }}%</span><br><span class="text-xs text-slate-500">{{ $row['mastery']->levelLabel() }}</span></td>
                        <td class="px-4 py-4"><a href="{{ route('teacher.progress.learners.show', $row['learner']) }}" class="font-black underline">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">No learner progress is available yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
