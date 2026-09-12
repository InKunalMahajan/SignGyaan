@extends('learner.layout')
@section('title', 'Assessments')
@section('header_label', 'Assessments')
@section('content')
<div class="space-y-6">
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Assessment System</p>
        <h1 class="mt-2 text-3xl font-black text-slate-950">My Assessments</h1>
        <p class="mt-2 text-sm text-slate-600">Only published assessments from your active learning courses appear here.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($assessments as $assessment)
            @php
                $latest = optional($attempts->get($assessment->id))->first();
                $active = optional($attempts->get($assessment->id))->firstWhere('status', 'in_progress');
                $submitted = $latest && $latest->isSubmitted();
            @endphp
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">{{ $assessment->course->title }}</p>
                        <h2 class="mt-2 text-xl font-black">{{ $assessment->title }}</h2>
                    </div>
                    <span class="rounded-full border border-slate-300 px-2.5 py-1 text-xs font-bold">{{ $assessment->questions_count }} questions</span>
                </div>
                <div class="mt-4 grid grid-cols-3 gap-2 text-sm">
                    <div><p class="text-slate-500">Marks</p><p class="font-black">{{ $assessment->total_marks }}</p></div>
                    <div><p class="text-slate-500">Pass</p><p class="font-black">{{ $assessment->passing_marks ?? '—' }}</p></div>
                    <div><p class="text-slate-500">Time</p><p class="font-black">{{ $assessment->duration_minutes ? $assessment->duration_minutes.' min' : '—' }}</p></div>
                </div>
                @if ($latest)
                    <p class="mt-4 text-sm font-semibold text-slate-600">Latest status: {{ str($latest->status)->replace('_', ' ')->title() }}</p>
                @endif
                <div class="mt-5 grid gap-2">
                    @if ($submitted)
                        <a href="{{ route('learner.assessments.attempts.result', $latest) }}" class="w-full rounded-xl bg-slate-950 px-4 py-3 text-center text-sm font-black text-white">View Result</a>
                    @endif
                    <form method="POST" action="{{ route('learner.assessments.start', $assessment) }}">
                        @csrf
                        <button class="w-full rounded-xl {{ $submitted ? 'border border-slate-300 bg-white text-slate-950' : 'bg-slate-950 text-white' }} px-4 py-3 text-sm font-black">
                            {{ $active ? 'Resume Attempt' : ($submitted ? 'Start New Attempt' : 'Start Attempt') }}
                        </button>
                    </form>
                </div>
            </section>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-sm text-slate-600 md:col-span-2 xl:col-span-3">No published assessments are available in your active courses yet.</div>
        @endforelse
    </div>
</div>
@endsection
