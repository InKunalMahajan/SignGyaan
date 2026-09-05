@extends('layouts.admin')

@section('title', 'Manage Teacher - SignGyaan Admin')
@section('page-title', 'Manage Teacher')
@section('description', 'Update teacher profile and curriculum assignments.')

@section('content')
<section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
    <div class="mx-auto max-w-7xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Teacher management</p>
                <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">{{ $teacher->name }}</h2>
                <p class="mt-2 text-sm text-sign-muted">{{ $teacher->email }}</p>
            </div>
            <a href="{{ route('admin.teachers.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary">← Back to Teachers</a>
        </div>

        @if($errors->any())
            <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4" role="alert"><p class="font-semibold text-red-800">Please check the teacher details.</p><ul class="mt-2 list-disc pl-5 text-sm text-red-700">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}" class="mt-7 space-y-6">
            @csrf
            @method('PUT')

            <section class="rounded-2xl border border-sign-border bg-white p-5 sm:p-7" aria-labelledby="teacher-profile-heading">
                <h3 id="teacher-profile-heading" class="font-heading text-2xl font-semibold text-sign-primary">Teacher Profile</h3>
                <p class="mt-2 text-sm text-sign-muted">Add professional information used for teacher administration.</p>
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <div><label for="employee_code" class="mb-2 block text-sm font-semibold text-sign-primary">Employee code</label><input id="employee_code" name="employee_code" value="{{ old('employee_code', $teacher->teacherProfile?->employee_code) }}" maxlength="50" class="min-h-12 w-full rounded-xl border border-sign-border px-4 py-3"></div>
                    <div><label for="experience_years" class="mb-2 block text-sm font-semibold text-sign-primary">Experience (years)</label><input id="experience_years" name="experience_years" type="number" min="0" max="80" value="{{ old('experience_years', $teacher->teacherProfile?->experience_years) }}" class="min-h-12 w-full rounded-xl border border-sign-border px-4 py-3"></div>
                    <div><label for="qualification" class="mb-2 block text-sm font-semibold text-sign-primary">Qualification</label><input id="qualification" name="qualification" value="{{ old('qualification', $teacher->teacherProfile?->qualification) }}" maxlength="255" class="min-h-12 w-full rounded-xl border border-sign-border px-4 py-3"></div>
                    <div><label for="specialization" class="mb-2 block text-sm font-semibold text-sign-primary">Specialization</label><input id="specialization" name="specialization" value="{{ old('specialization', $teacher->teacherProfile?->specialization) }}" maxlength="255" class="min-h-12 w-full rounded-xl border border-sign-border px-4 py-3"></div>
                    <div class="sm:col-span-2"><label for="bio" class="mb-2 block text-sm font-semibold text-sign-primary">Teacher bio</label><textarea id="bio" name="bio" rows="5" maxlength="3000" class="w-full rounded-xl border border-sign-border px-4 py-3">{{ old('bio', $teacher->teacherProfile?->bio) }}</textarea></div>
                </div>
            </section>

            <section class="rounded-2xl border border-sign-border bg-white p-5 sm:p-7" aria-labelledby="subject-assignment-heading">
                <h3 id="subject-assignment-heading" class="font-heading text-2xl font-semibold text-sign-primary">Subject Assignments</h3>
                <p class="mt-2 text-sm text-sign-muted">Select the subjects this teacher is responsible for.</p>
                <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($subjects as $subject)
                        <label class="flex min-h-12 items-center gap-3 rounded-xl border border-sign-border bg-sign-soft px-4 py-3 text-sm font-semibold text-sign-primary"><input type="checkbox" name="subjects[]" value="{{ $subject->id }}" @checked(in_array($subject->id, old('subjects', $teacher->teachingSubjects->pluck('id')->all()))) class="h-4 w-4 rounded border-sign-border"> <span>{{ $subject->name }}</span></label>
                    @endforeach
                </div>
            </section>

            <section class="rounded-2xl border border-sign-border bg-white p-5 sm:p-7" aria-labelledby="course-assignment-heading">
                <h3 id="course-assignment-heading" class="font-heading text-2xl font-semibold text-sign-primary">Course Assignments</h3>
                <p class="mt-2 text-sm text-sign-muted">Assign specific courses the teacher can manage or teach.</p>
                <div class="mt-5 grid gap-3 lg:grid-cols-2">
                    @foreach($courses as $course)
                        <label class="flex min-h-12 items-start gap-3 rounded-xl border border-sign-border px-4 py-3"><input type="checkbox" name="courses[]" value="{{ $course->id }}" @checked(in_array($course->id, old('courses', $teacher->teachingCourses->pluck('id')->all()))) class="mt-1 h-4 w-4 rounded border-sign-border"><span><span class="block text-sm font-semibold text-sign-primary">{{ $course->title }}</span><span class="mt-1 block text-xs text-sign-muted">{{ $course->subject?->name ?? 'No subject' }}</span></span></label>
                    @endforeach
                </div>
            </section>

            <div class="flex justify-end"><button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-xl bg-sign-primary px-6 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Save Teacher</button></div>
        </form>
    </div>
</section>
@endsection
