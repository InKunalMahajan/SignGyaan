@extends('teacher.layout')

@section('title', 'Manage Assessment')

@section('content')
<div class="space-y-7">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('teacher.assessments.index') }}" class="text-sm font-bold text-slate-600 hover:text-slate-950">← Back to Assessments</a>
            <div class="mt-3 flex flex-wrap items-center gap-3">
                <h1 class="text-3xl font-black tracking-tight">{{ $assessment->title }}</h1>
                <span class="rounded-full border border-slate-300 px-3 py-1 text-xs font-bold capitalize">{{ $assessment->status }}</span>
            </div>
            <p class="mt-2 text-sm text-slate-600">{{ $assessment->course->title }} · {{ $assessment->questions->count() }} questions</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('teacher.assessments.preview', $assessment) }}" class="sg-btn-secondary">Preview</a>
            @if ($assessment->status !== 'published' && ! $isLocked)
                <form method="POST" action="{{ route('teacher.assessments.publish', $assessment) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="sg-btn-primary">Publish</button>
                </form>
            @endif
            @if ($assessment->status !== 'archived')
                <form method="POST" action="{{ route('teacher.assessments.archive', $assessment) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="sg-btn-secondary">Archive</button>
                </form>
            @endif
        </div>
    </div>

    @if ($isLocked)
        <div class="rounded-xl border border-slate-300 bg-slate-100 p-4 text-sm text-slate-800">
            <p class="font-black">Assessment structure locked</p>
            <p class="mt-1">Learner attempts already exist. Questions and assessment details cannot be changed because that could corrupt results.</p>
        </div>
    @endif

    <section class="sg-card">
        <div class="mb-5">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Step 1</p>
            <h2 class="mt-1 text-xl font-black">Assessment details</h2>
        </div>
        <form method="POST" action="{{ route('teacher.assessments.update', $assessment) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label class="mb-2 block text-sm font-bold">Course</label>
                <select name="course_id" class="sg-field" required @disabled($isLocked)>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" @selected(old('course_id', $assessment->course_id) == $course->id)>{{ $course->title }}{{ $course->subject ? ' · '.$course->subject->name : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-bold">Title</label>
                <input name="title" value="{{ old('title', $assessment->title) }}" class="sg-field" required @disabled($isLocked)>
            </div>
            <div>
                <label class="mb-2 block text-sm font-bold">Instructions</label>
                <textarea name="instructions" rows="4" class="sg-field" @disabled($isLocked)>{{ old('instructions', $assessment->instructions) }}</textarea>
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="mb-2 block text-sm font-bold">Total marks</label>
                    <input name="total_marks" type="number" step="0.5" min="0.5" value="{{ old('total_marks', $assessment->total_marks) }}" class="sg-field" required @disabled($isLocked)>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-bold">Passing marks</label>
                    <input name="passing_marks" type="number" step="0.5" min="0" value="{{ old('passing_marks', $assessment->passing_marks) }}" class="sg-field" @disabled($isLocked)>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-bold">Duration (minutes)</label>
                    <input name="duration_minutes" type="number" min="1" max="1440" value="{{ old('duration_minutes', $assessment->duration_minutes) }}" class="sg-field" @disabled($isLocked)>
                </div>
            </div>
            @unless ($isLocked)
                <div class="flex justify-end">
                    <button type="submit" class="sg-btn-primary">Save Details</button>
                </div>
            @endunless
        </form>
    </section>

    <section class="space-y-4">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Step 2</p>
                <h2 class="mt-1 text-xl font-black">Question Builder</h2>
                <p class="mt-1 text-sm text-slate-600">Question marks must add up to {{ $assessment->total_marks }} before publishing.</p>
            </div>
            <p class="text-sm font-black">Current question marks: {{ $assessment->questions->sum(fn ($question) => (float) $question->marks) }}</p>
        </div>

        @foreach ($assessment->questions as $question)
            <article class="sg-card space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Question {{ $loop->iteration }}</p>
                        <p class="mt-1 text-sm font-bold">{{ $questionTypes[$question->type] ?? $question->type }} · {{ $question->marks }} marks</p>
                    </div>
                    @unless ($isLocked)
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('teacher.assessments.questions.move', [$assessment, $question]) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="direction" value="up">
                                <button class="sg-btn-secondary" type="submit" @disabled($loop->first)>↑</button>
                            </form>
                            <form method="POST" action="{{ route('teacher.assessments.questions.move', [$assessment, $question]) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="direction" value="down">
                                <button class="sg-btn-secondary" type="submit" @disabled($loop->last)>↓</button>
                            </form>
                        </div>
                    @endunless
                </div>

                @if ($isLocked)
                    <p class="text-sm leading-6 text-slate-700">{{ $question->prompt }}</p>
                @else
                    <form method="POST" action="{{ route('teacher.assessments.questions.update', [$assessment, $question]) }}" class="space-y-5">
                        @csrf
                        @method('PUT')
                        @include('teacher.assessments._question-fields', ['question' => $question])
                        <div class="flex flex-wrap justify-between gap-3">
                            <button type="submit" class="sg-btn-primary">Save Question</button>
                        </div>
                    </form>
                    <form method="POST" action="{{ route('teacher.assessments.questions.destroy', [$assessment, $question]) }}" onsubmit="return confirm('Remove this question?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm font-bold text-slate-600 underline hover:text-slate-950">Remove question</button>
                    </form>
                @endif
            </article>
        @endforeach

        @if ($assessment->questions->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-600">No questions yet. Add the first question below.</div>
        @endif
    </section>

    @unless ($isLocked)
        <section class="sg-card space-y-5">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Add Question</p>
                <h2 class="mt-1 text-xl font-black">New question</h2>
            </div>
            <form method="POST" action="{{ route('teacher.assessments.questions.store', $assessment) }}" class="space-y-5">
                @csrf
                @include('teacher.assessments._question-fields', ['question' => null])
                <div class="flex justify-end">
                    <button type="submit" class="sg-btn-primary">Add Question</button>
                </div>
            </form>
        </section>
    @endunless

    <section class="rounded-2xl border border-slate-300 bg-slate-100 p-5">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="font-black">Ready to check the learner view?</p>
                <p class="mt-1 text-sm text-slate-600">Preview the full assessment before publishing it.</p>
            </div>
            <a href="{{ route('teacher.assessments.preview', $assessment) }}" class="sg-btn-primary">Preview Assessment</a>
        </div>
    </section>
</div>
@endsection
