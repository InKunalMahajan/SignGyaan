@extends('teacher.layout')
@section('title', 'Learner Progress')
@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Learner Progress</p>
            <h1 class="mt-2 text-3xl font-black">{{ $learner->name }}</h1>
            <p class="mt-2 text-sm text-slate-600">Course mastery, chapter completion, and recommended next steps.</p>
        </div>
        <a href="{{ route('teacher.progress.index') }}" class="sg-btn-secondary">Back to Progress</a>
    </div>

    @forelse ($courses as $entry)
        @php $m = $entry['mastery']; @endphp
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">{{ $entry['class']->name }} · {{ $entry['course']->subject?->name }}</p>
                    <h2 class="mt-1 text-xl font-black">{{ $entry['course']->title }}</h2>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-black">{{ number_format((float) $m->mastery_score, 2) }}%</p>
                    <p class="text-sm font-bold text-slate-600">{{ $m->levelLabel() }}</p>
                </div>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl bg-slate-50 p-4"><p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Lessons</p><p class="mt-1 font-black">{{ $m->lessons_completed }}/{{ $m->lessons_total }} · {{ number_format((float) $m->lesson_completion_percentage, 2) }}%</p></div>
                <div class="rounded-xl bg-slate-50 p-4"><p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Assessment</p><p class="mt-1 font-black">{{ $m->assessment_percentage !== null ? number_format((float) $m->assessment_percentage, 2).'%' : 'No completed assessment' }}</p></div>
                <div class="rounded-xl bg-slate-50 p-4"><p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Evidence</p><p class="mt-1 font-black">{{ $m->evidence_status === 'assessment_supported' ? 'Assessment-supported' : 'Provisional' }}</p></div>
            </div>

            @if ($entry['recommendations'])
                <div class="mt-5 rounded-xl border border-slate-200 p-4">
                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Recommended support</p>
                    <div class="mt-3 grid gap-3 md:grid-cols-2">
                        @foreach ($entry['recommendations'] as $recommendation)
                            <div>
                                <p class="font-black">{{ $recommendation['title'] }}</p>
                                <p class="mt-1 text-sm text-slate-600">{{ $recommendation['message'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-5 space-y-3">
                @foreach ($entry['chapters'] as $chapter)
                    <div class="rounded-xl border border-slate-200 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="font-black">{{ $chapter['title'] }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $chapter['lessons_completed'] }}/{{ $chapter['lessons_total'] }} lessons · {{ $chapter['level_label'] }}</p>
                            </div>
                            <p class="font-black">{{ number_format($chapter['percentage'], 2) }}%</p>
                        </div>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-200"><div class="h-full bg-slate-950" style="width: {{ min(100, max(0, $chapter['percentage'])) }}%"></div></div>
                    </div>
                @endforeach
            </div>
        </section>
    @empty
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-sm text-slate-600">No active assigned courses are available for this learner.</div>
    @endforelse
</div>
@endsection
