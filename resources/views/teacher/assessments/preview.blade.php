@extends('teacher.layout')

@section('title', 'Assessment Preview')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('teacher.assessments.edit', $assessment) }}" class="text-sm font-bold text-slate-600 hover:text-slate-950">← Back to editor</a>
            <p class="mt-4 text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Teacher Preview</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight">{{ $assessment->title }}</h1>
            <p class="mt-2 text-sm text-slate-600">{{ $assessment->course->title }} · {{ $assessment->questions->count() }} questions · {{ $assessment->total_marks }} marks</p>
        </div>
        <span class="rounded-full border border-slate-300 px-3 py-1 text-xs font-bold capitalize">{{ $assessment->status }}</span>
    </div>

    @if ($assessment->instructions)
        <section class="sg-card">
            <h2 class="text-lg font-black">Instructions</h2>
            <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-700">{{ $assessment->instructions }}</p>
        </section>
    @endif

    <section class="grid gap-3 sm:grid-cols-3">
        <div class="sg-card"><p class="text-xs font-bold uppercase text-slate-500">Total Marks</p><p class="mt-2 text-2xl font-black">{{ $assessment->total_marks }}</p></div>
        <div class="sg-card"><p class="text-xs font-bold uppercase text-slate-500">Passing Marks</p><p class="mt-2 text-2xl font-black">{{ $assessment->passing_marks ?? '—' }}</p></div>
        <div class="sg-card"><p class="text-xs font-bold uppercase text-slate-500">Duration</p><p class="mt-2 text-2xl font-black">{{ $assessment->duration_minutes ? $assessment->duration_minutes.' min' : 'No limit' }}</p></div>
    </section>

    <div class="space-y-4">
        @forelse ($assessment->questions as $question)
            <article class="sg-card">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Question {{ $loop->iteration }} · {{ $questionTypes[$question->type] ?? $question->type }}</p>
                        <h2 class="mt-2 text-lg font-black leading-7">{{ $question->prompt }}</h2>
                    </div>
                    <span class="rounded-full border border-slate-300 px-3 py-1 text-xs font-bold">{{ $question->marks }} marks</span>
                </div>

                @if (in_array($question->type, ['single_choice', 'multi_select'], true))
                    <div class="mt-4 space-y-2">
                        @foreach ($question->config['options'] ?? [] as $option)
                            <div class="rounded-xl border border-slate-200 px-4 py-3 text-sm">{{ $option }}</div>
                        @endforeach
                    </div>
                @elseif ($question->type === 'true_false')
                    <div class="mt-4 grid gap-2 sm:grid-cols-2">
                        <div class="rounded-xl border border-slate-200 px-4 py-3 text-sm">True</div>
                        <div class="rounded-xl border border-slate-200 px-4 py-3 text-sm">False</div>
                    </div>
                @elseif ($question->type === 'matching')
                    <div class="mt-4 space-y-2">
                        @foreach ($question->config['pairs'] ?? [] as $pair)
                            <div class="grid gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm sm:grid-cols-2"><span>{{ $pair['left'] ?? '' }}</span><span class="font-bold">{{ $pair['right'] ?? '' }}</span></div>
                        @endforeach
                    </div>
                @else
                    <div class="mt-4 rounded-xl border border-dashed border-slate-300 px-4 py-5 text-sm text-slate-500">Learner answer field</div>
                @endif
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-600">No questions have been added yet.</div>
        @endforelse
    </div>

    <div class="sg-card flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="font-black">Question marks total: {{ $questionMarks }}</p>
            <p class="mt-1 text-sm text-slate-600">Assessment total: {{ $assessment->total_marks }}</p>
        </div>
        <a href="{{ route('teacher.assessments.edit', $assessment) }}" class="sg-btn-primary">Return to Editor</a>
    </div>
</div>
@endsection
