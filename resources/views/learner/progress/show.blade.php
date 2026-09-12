@extends('learner.layout')
@section('title', $course->title.' Progress')
@section('header_label', 'Course Progress')
@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">{{ $class->name }} · {{ $course->subject?->name }}</p>
            <h1 class="mt-2 text-3xl font-black">{{ $course->title }}</h1>
            <p class="mt-2 text-sm text-slate-600">Course mastery combines lesson completion with completed assessment performance.</p>
        </div>
        <a href="{{ route('learner.progress.index') }}" class="sg-btn-secondary">Back to Progress</a>
    </div>

    <section class="grid gap-4 md:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Mastery</p>
            <p class="mt-2 text-3xl font-black">{{ number_format((float) $mastery->mastery_score, 2) }}%</p>
            <p class="mt-1 text-sm font-bold text-slate-600">{{ $mastery->levelLabel() }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Lesson completion</p>
            <p class="mt-2 text-3xl font-black">{{ number_format((float) $mastery->lesson_completion_percentage, 2) }}%</p>
            <p class="mt-1 text-sm text-slate-600">{{ $mastery->lessons_completed }}/{{ $mastery->lessons_total }} lessons</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Assessment</p>
            <p class="mt-2 text-3xl font-black">{{ $mastery->assessment_percentage !== null ? number_format((float) $mastery->assessment_percentage, 2).'%' : '—' }}</p>
            <p class="mt-1 text-sm text-slate-600">{{ $mastery->assessments_completed }} completed</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Evidence</p>
            <p class="mt-2 text-xl font-black">{{ $mastery->evidence_status === 'assessment_supported' ? 'Assessment-supported' : 'Provisional' }}</p>
            <p class="mt-1 text-sm text-slate-600">{{ $mastery->evidence_status === 'assessment_supported' ? '40% lessons + 60% assessments' : 'Lesson completion only until an assessment is completed' }}</p>
        </div>
    </section>

    @if ($recommendations)
        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Recommended next steps</p>
            <div class="mt-4 grid gap-3 md:grid-cols-2">
                @foreach ($recommendations as $recommendation)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <h2 class="font-black">{{ $recommendation['title'] }}</h2>
                        <p class="mt-1 text-sm leading-6 text-slate-600">{{ $recommendation['message'] }}</p>
                        @if (($recommendation['type'] ?? null) === 'lesson' && isset($recommendation['lesson_id']))
                            <a href="{{ route('learner.classes.courses.lessons.show', [$class, $course, $recommendation['lesson_id']]) }}" class="mt-3 inline-flex text-sm font-black underline">Open Lesson</a>
                        @elseif (($recommendation['type'] ?? null) === 'assessment_review')
                            <a href="{{ route('learner.assessments.history') }}" class="mt-3 inline-flex text-sm font-black underline">Review Assessments</a>
                        @elseif (($recommendation['type'] ?? null) === 'assessment')
                            <a href="{{ route('learner.assessments.index') }}" class="mt-3 inline-flex text-sm font-black underline">Open Assessments</a>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="space-y-4">
        <div>
            <h2 class="text-xl font-black">Chapter progress</h2>
            <p class="mt-1 text-sm text-slate-600">Only active chapters and published lessons are included.</p>
        </div>

        @forelse ($chapters as $chapter)
            <article class="rounded-2xl border border-slate-200 bg-white p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Chapter {{ $loop->iteration }}</p>
                        <h3 class="mt-1 text-lg font-black">{{ $chapter['title'] }}</h3>
                    </div>
                    <div class="text-right">
                        <p class="font-black">{{ number_format($chapter['percentage'], 2) }}%</p>
                        <p class="text-xs font-bold text-slate-500">{{ $chapter['level_label'] }}</p>
                    </div>
                </div>
                <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-200"><div class="h-full bg-slate-950" style="width: {{ min(100, max(0, $chapter['percentage'])) }}%"></div></div>
                <p class="mt-2 text-sm text-slate-600">{{ $chapter['lessons_completed'] }}/{{ $chapter['lessons_total'] }} lessons completed</p>

                <div class="mt-4 divide-y divide-slate-200 rounded-xl border border-slate-200">
                    @forelse ($chapter['lessons'] as $lesson)
                        <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3">
                            <span class="font-semibold">{{ $lesson['title'] }}</span>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-black uppercase tracking-[0.1em] text-slate-500">{{ str($lesson['status'])->replace('_', ' ')->title() }}</span>
                                <a href="{{ route('learner.classes.courses.lessons.show', [$class, $course, $lesson['id']]) }}" class="text-sm font-black underline">{{ $lesson['status'] === 'completed' ? 'Review' : 'Open' }}</a>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-3 text-sm text-slate-500">No published lessons in this chapter.</div>
                    @endforelse
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-sm text-slate-600">No active chapters are available in this course.</div>
        @endforelse
    </section>
</div>
@endsection
