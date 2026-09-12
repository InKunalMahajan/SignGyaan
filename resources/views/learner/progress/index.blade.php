@extends('learner.layout')
@section('title', 'Progress')
@section('header_label', 'Progress & Mastery')
@section('content')
<div class="space-y-6">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Progress & Mastery</p>
        <div class="mt-2 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-950">My Progress</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Track lesson completion, assessment performance, and your current mastery level for every active course.</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-5 py-4 text-right">
                <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Overall mastery</p>
                <p class="mt-1 text-2xl font-black">{{ number_format($averageMastery, 2) }}%</p>
                <p class="text-sm font-bold text-slate-600">{{ $overallLevel }}</p>
            </div>
        </div>
    </section>

    <div class="grid gap-4 lg:grid-cols-2">
        @forelse ($courses as $entry)
            @php $m = $entry['mastery']; @endphp
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">{{ $entry['class']->name }}</p>
                        <h2 class="mt-1 text-xl font-black text-slate-950">{{ $entry['course']->title }}</h2>
                        <p class="mt-1 text-sm text-slate-600">{{ $entry['course']->subject?->name }}</p>
                    </div>
                    <span class="rounded-full border border-slate-300 px-3 py-1 text-xs font-black">{{ $m->levelLabel() }}</span>
                </div>

                <div class="mt-5">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-bold text-slate-600">Mastery score</span>
                        <span class="font-black">{{ number_format((float) $m->mastery_score, 2) }}%</span>
                    </div>
                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full bg-slate-950" style="width: {{ min(100, max(0, (float) $m->mastery_score)) }}%"></div>
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-3 gap-3 text-sm">
                    <div class="rounded-xl bg-slate-50 p-3">
                        <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Lessons</p>
                        <p class="mt-1 font-black">{{ $m->lessons_completed }}/{{ $m->lessons_total }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3">
                        <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Assessment</p>
                        <p class="mt-1 font-black">{{ $m->assessment_percentage !== null ? number_format((float) $m->assessment_percentage, 2).'%' : 'Pending' }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3">
                        <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Evidence</p>
                        <p class="mt-1 font-black">{{ $m->evidence_status === 'assessment_supported' ? 'Supported' : 'Provisional' }}</p>
                    </div>
                </div>

                <a href="{{ route('learner.progress.courses.show', $entry['course']) }}" class="mt-5 inline-flex rounded-xl bg-slate-950 px-4 py-3 text-sm font-black text-white">View Course Progress</a>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-sm text-slate-600 lg:col-span-2">No active assigned courses are available yet.</div>
        @endforelse
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-5">
        <h2 class="font-black">Mastery levels</h2>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4 text-sm">
            <div><p class="font-black">0–39</p><p class="text-slate-600">Needs Support</p></div>
            <div><p class="font-black">40–59</p><p class="text-slate-600">Developing</p></div>
            <div><p class="font-black">60–79</p><p class="text-slate-600">Good</p></div>
            <div><p class="font-black">80–100</p><p class="text-slate-600">Mastered</p></div>
        </div>
    </section>
</div>
@endsection
