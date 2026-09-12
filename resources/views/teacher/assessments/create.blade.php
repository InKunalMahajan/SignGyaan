@extends('teacher.layout')

@section('title', 'Create Assessment')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div>
        <a href="{{ route('teacher.assessments.index') }}" class="text-sm font-bold text-slate-600 hover:text-slate-950">← Back to Assessments</a>
        <h1 class="mt-3 text-3xl font-black tracking-tight">Create Assessment</h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">Create a draft first. You can add questions and preview it before publishing.</p>
    </div>

    <form method="POST" action="{{ route('teacher.assessments.store') }}" class="sg-card space-y-5">
        @csrf
        <div>
            <label for="course_id" class="mb-2 block text-sm font-bold">Course</label>
            <select id="course_id" name="course_id" class="sg-field" required>
                <option value="">Select course</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>{{ $course->title }}{{ $course->subject ? ' · '.$course->subject->name : '' }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="title" class="mb-2 block text-sm font-bold">Assessment title</label>
            <input id="title" name="title" value="{{ old('title') }}" class="sg-field" maxlength="180" required>
        </div>

        <div>
            <label for="instructions" class="mb-2 block text-sm font-bold">Instructions</label>
            <textarea id="instructions" name="instructions" rows="5" class="sg-field">{{ old('instructions') }}</textarea>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label for="total_marks" class="mb-2 block text-sm font-bold">Total marks</label>
                <input id="total_marks" name="total_marks" type="number" step="0.5" min="0.5" value="{{ old('total_marks', 20) }}" class="sg-field" required>
            </div>
            <div>
                <label for="passing_marks" class="mb-2 block text-sm font-bold">Passing marks</label>
                <input id="passing_marks" name="passing_marks" type="number" step="0.5" min="0" value="{{ old('passing_marks') }}" class="sg-field">
            </div>
            <div>
                <label for="duration_minutes" class="mb-2 block text-sm font-bold">Duration (minutes)</label>
                <input id="duration_minutes" name="duration_minutes" type="number" min="1" max="1440" value="{{ old('duration_minutes') }}" class="sg-field">
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="sg-btn-primary">Create Draft</button>
        </div>
    </form>
</div>
@endsection
