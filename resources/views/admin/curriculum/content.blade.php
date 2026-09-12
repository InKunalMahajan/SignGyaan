@extends('admin.layout')

@section('title', 'Curriculum Content')

@section('content')
<div class="space-y-8">
    <section class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-bold uppercase tracking-[0.18em] text-indigo-600">Phase 3</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Course Content</h1>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Manage Subject → Course → Chapter / Unit → Lesson while keeping teacher-owned course permissions unchanged.</p>
        </div>
        <a href="{{ route('admin.curriculum.index') }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-black text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-indigo-200">Back to Catalog</a>
    </section>

    <section class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-sm font-bold text-slate-500">Subjects</div>
            <div class="mt-1 text-3xl font-black text-slate-950">{{ $subjects->count() }}</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-sm font-bold text-slate-500">Courses</div>
            <div class="mt-1 text-3xl font-black text-slate-950">{{ $courses->count() }}</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-sm font-bold text-slate-500">Learning hierarchy</div>
            <div class="mt-2 text-sm font-black text-indigo-700">Course → Chapter / Unit → Lesson</div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h2 class="text-xl font-black text-slate-950">Create Course</h2>
        <form method="POST" action="{{ route('admin.curriculum.content.courses.store') }}" class="mt-5 grid gap-4 lg:grid-cols-2">
            @csrf
            <div>
                <label for="course-subject" class="text-sm font-bold text-slate-700">Subject</label>
                <select id="course-subject" name="subject_id" required class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                    <option value="">Select subject</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ optional(optional($subject->academicClass)->board)->short_name ?: optional(optional($subject->academicClass)->board)->name }} @if($subject->academicClass)· {{ $subject->academicClass->name }} @endif· {{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="course-title" class="text-sm font-bold text-slate-700">Course title</label>
                <input id="course-title" name="title" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-100" placeholder="FYJC Information Technology">
            </div>
            <div>
                <label for="course-level" class="text-sm font-bold text-slate-700">Level</label>
                <input id="course-level" name="level" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-100" placeholder="FYJC / 11">
            </div>
            <label class="flex items-center gap-3 self-end rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-bold text-slate-700"><input type="checkbox" name="is_active" value="1" checked class="size-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"> Active</label>
            <div class="lg:col-span-2">
                <label for="course-description" class="text-sm font-bold text-slate-700">Description</label>
                <textarea id="course-description" name="description" rows="3" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-100"></textarea>
            </div>
            <div class="lg:col-span-2">
                <button class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-black text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">Create Course</button>
            </div>
        </form>
    </section>

    <section class="space-y-5">
        <div>
            <h2 class="text-2xl font-black text-slate-950">Manage Course Content</h2>
            <p class="mt-1 text-sm text-slate-600">Open a subject, then a course, to manage chapters and lessons.</p>
        </div>

        @forelse ($subjects as $subject)
            <details class="rounded-3xl border border-slate-200 bg-white shadow-sm" open>
                <summary class="cursor-pointer list-none px-5 py-5 sm:px-6">
                    <span class="flex flex-wrap items-center justify-between gap-3">
                        <span>
                            <span class="block text-lg font-black text-slate-950">{{ $subject->name }}</span>
                            <span class="mt-1 block text-xs font-bold text-slate-500">{{ optional(optional($subject->academicClass)->board)->short_name ?: optional(optional($subject->academicClass)->board)->name }} @if($subject->academicClass)· {{ $subject->academicClass->name }} @endif · {{ $subject->courses->count() }} course{{ $subject->courses->count() === 1 ? '' : 's' }}</span>
                        </span>
                    </span>
                </summary>

                <div class="space-y-4 border-t border-slate-200 p-5 sm:p-6">
                    @forelse ($subject->courses as $course)
                        <details class="rounded-2xl border border-slate-200 bg-slate-50">
                            <summary class="cursor-pointer list-none px-4 py-4">
                                <span class="flex flex-wrap items-center justify-between gap-3">
                                    <span>
                                        <span class="font-black text-slate-900">{{ $course->title }}</span>
                                        <span class="ml-2 text-xs font-bold text-slate-500">{{ $course->units_count }} units · {{ $course->classes_count }} class assignments</span>
                                    </span>
                                    <span class="rounded-full px-3 py-1 text-xs font-black {{ $course->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">{{ $course->is_active ? 'Active' : 'Inactive' }}</span>
                                </span>
                            </summary>

                            <div class="space-y-6 border-t border-slate-200 p-4">
                                <div>
                                    <h3 class="text-sm font-black uppercase tracking-wide text-slate-500">Course settings</h3>
                                    <form method="POST" action="{{ route('admin.curriculum.content.courses.update', $course) }}" class="mt-3 grid gap-3 lg:grid-cols-2">
                                        @csrf @method('PUT')
                                        <select name="subject_id" required class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm">
                                            @foreach ($subjects as $subjectOption)
                                                <option value="{{ $subjectOption->id }}" @selected($subjectOption->id === $course->subject_id)>{{ $subjectOption->name }}</option>
                                            @endforeach
                                        </select>
                                        <input name="title" value="{{ $course->title }}" required class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm">
                                        <input name="level" value="{{ $course->level }}" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm" placeholder="Level">
                                        <label class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-bold"><input type="checkbox" name="is_active" value="1" @checked($course->is_active)> Active</label>
                                        <textarea name="description" rows="2" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm lg:col-span-2">{{ $course->description }}</textarea>
                                        <button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-black text-white">Save Course</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.curriculum.content.courses.destroy', $course) }}" class="mt-3" onsubmit="return confirm('Delete this course? It must have no units and no class assignments.');">
                                        @csrf @method('DELETE')
                                        <button class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-black text-rose-700">Delete Course</button>
                                    </form>
                                </div>

                                <div class="rounded-2xl border border-indigo-100 bg-indigo-50/50 p-4">
                                    <h3 class="font-black text-slate-950">Add Chapter / Unit</h3>
                                    <form method="POST" action="{{ route('admin.curriculum.content.units.store', $course) }}" class="mt-3 grid gap-3 lg:grid-cols-2">
                                        @csrf
                                        <input name="title" required class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm" placeholder="Chapter 1 - Basics of IT">
                                        <input type="number" min="1" max="999" name="position" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm" placeholder="Position (auto if blank)">
                                        <textarea name="description" rows="2" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm lg:col-span-2" placeholder="Description"></textarea>
                                        <label class="flex items-center gap-2 text-sm font-bold"><input type="checkbox" name="is_active" value="1" checked> Active</label>
                                        <button class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-black text-white">Add Chapter / Unit</button>
                                    </form>
                                </div>

                                <div class="space-y-4">
                                    @forelse ($course->units as $unit)
                                        <details class="rounded-2xl border border-slate-200 bg-white" open>
                                            <summary class="cursor-pointer list-none px-4 py-4">
                                                <span class="font-black text-slate-900">{{ $unit->position }}. {{ $unit->title }}</span>
                                                <span class="ml-2 text-xs font-bold text-slate-500">{{ $unit->lessons->count() }} lessons</span>
                                            </summary>
                                            <div class="space-y-5 border-t border-slate-200 p-4">
                                                <form method="POST" action="{{ route('admin.curriculum.content.units.update', [$course, $unit]) }}" class="grid gap-3 lg:grid-cols-2">
                                                    @csrf @method('PUT')
                                                    <input name="title" value="{{ $unit->title }}" required class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                                                    <input type="number" min="1" max="999" name="position" value="{{ $unit->position }}" required class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                                                    <textarea name="description" rows="2" class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm lg:col-span-2">{{ $unit->description }}</textarea>
                                                    <label class="flex items-center gap-2 text-sm font-bold"><input type="checkbox" name="is_active" value="1" @checked($unit->is_active)> Active</label>
                                                    <button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-black text-white">Save Chapter / Unit</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.curriculum.content.units.destroy', [$course, $unit]) }}" onsubmit="return confirm('Delete this chapter / unit? It must have no lessons.');">
                                                    @csrf @method('DELETE')
                                                    <button class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-black text-rose-700">Delete Chapter / Unit</button>
                                                </form>

                                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                                    <h4 class="font-black text-slate-950">Add Lesson</h4>
                                                    <form method="POST" action="{{ route('admin.curriculum.content.lessons.store', [$course, $unit]) }}" class="mt-3 grid gap-3 lg:grid-cols-2">
                                                        @csrf
                                                        <input name="title" required class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm" placeholder="Introduction to IT">
                                                        <select name="status" required class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm"><option value="draft">Draft</option><option value="published">Published</option></select>
                                                        <input type="number" min="1" max="600" name="estimated_minutes" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm" placeholder="Estimated minutes">
                                                        <input type="number" min="1" max="999" name="position" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm" placeholder="Position (auto if blank)">
                                                        <input type="url" name="isl_video_url" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm lg:col-span-2" placeholder="ISL video URL">
                                                        <textarea name="summary" rows="2" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm lg:col-span-2" placeholder="Simple lesson summary"></textarea>
                                                        <textarea name="notes" rows="3" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm lg:col-span-2" placeholder="Lesson notes"></textarea>
                                                        <button class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-black text-white lg:col-span-2">Add Lesson</button>
                                                    </form>
                                                </div>

                                                <div class="space-y-3">
                                                    @forelse ($unit->lessons as $lesson)
                                                        <details class="rounded-xl border border-slate-200 bg-white">
                                                            <summary class="cursor-pointer list-none px-4 py-3">
                                                                <span class="font-bold text-slate-900">{{ $lesson->position }}. {{ $lesson->title }}</span>
                                                                <span class="ml-2 text-xs font-bold {{ $lesson->status === 'published' ? 'text-emerald-700' : 'text-amber-700' }}">{{ ucfirst($lesson->status) }}</span>
                                                                @if($lesson->progress_count)<span class="ml-2 text-xs font-bold text-slate-500">{{ $lesson->progress_count }} progress records</span>@endif
                                                            </summary>
                                                            <div class="border-t border-slate-200 p-4">
                                                                <form method="POST" action="{{ route('admin.curriculum.content.lessons.update', [$course, $unit, $lesson]) }}" class="grid gap-3 lg:grid-cols-2">
                                                                    @csrf @method('PUT')
                                                                    <input name="title" value="{{ $lesson->title }}" required class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                                                                    <select name="status" required class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm"><option value="draft" @selected($lesson->status === 'draft')>Draft</option><option value="published" @selected($lesson->status === 'published')>Published</option></select>
                                                                    <input type="number" min="1" max="600" name="estimated_minutes" value="{{ $lesson->estimated_minutes }}" class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm" placeholder="Minutes">
                                                                    <input type="number" min="1" max="999" name="position" value="{{ $lesson->position }}" required class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                                                                    <input type="url" name="isl_video_url" value="{{ $lesson->isl_video_url }}" class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm lg:col-span-2" placeholder="ISL video URL">
                                                                    <textarea name="summary" rows="2" class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm lg:col-span-2">{{ $lesson->summary }}</textarea>
                                                                    <textarea name="notes" rows="3" class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm lg:col-span-2">{{ $lesson->notes }}</textarea>
                                                                    <button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-black text-white">Save Lesson</button>
                                                                </form>
                                                                <form method="POST" action="{{ route('admin.curriculum.content.lessons.destroy', [$course, $unit, $lesson]) }}" class="mt-3" onsubmit="return confirm('Delete this lesson? It cannot be deleted after learner progress exists.');">
                                                                    @csrf @method('DELETE')
                                                                    <button class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-black text-rose-700">Delete Lesson</button>
                                                                </form>
                                                            </div>
                                                        </details>
                                                    @empty
                                                        <p class="text-sm text-slate-500">No lessons yet.</p>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </details>
                                    @empty
                                        <p class="text-sm text-slate-500">No chapters / units yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        </details>
                    @empty
                        <p class="rounded-2xl border border-dashed border-slate-300 px-5 py-8 text-center text-sm text-slate-500">No courses for this subject yet.</p>
                    @endforelse
                </div>
            </details>
        @empty
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-5 py-12 text-center text-sm text-slate-500">Create a Board, Academic Class, and Subject first.</div>
        @endforelse
    </section>
</div>
@endsection
