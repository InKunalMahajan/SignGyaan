@extends('teacher.layout')

@section('title', $course->title.' Curriculum')

@section('content')
    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('teacher.classes.index') }}" class="text-sm font-black text-blue-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">← My Classes</a>
            <p class="mt-4 text-xs font-black uppercase tracking-[0.16em] text-cyan-700">{{ $course->subject?->name ?: 'Course' }}</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight">{{ $course->title }} Curriculum</h1>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Build the learning path as ordered Units and Lessons. Draft lessons remain hidden from Learners until published.</p>
        </div>
        <span class="rounded-full px-3 py-1 text-xs font-black {{ $course->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">{{ $course->is_active ? 'Active course' : 'Inactive course' }}</span>
    </div>

    <section class="mb-8 grid gap-4 sm:grid-cols-3" aria-label="Curriculum summary">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-500">Units</p>
            <p class="mt-2 text-3xl font-black">{{ $unitCount }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-500">Lessons</p>
            <p class="mt-2 text-3xl font-black">{{ $lessonCount }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-500">Published</p>
            <p class="mt-2 text-3xl font-black">{{ $publishedLessonCount }}</p>
            <p class="mt-1 text-xs font-semibold text-slate-400">Visible to enrolled Learners</p>
        </article>
    </section>

    <section class="mb-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-7" aria-labelledby="new-unit-heading">
        <p class="text-xs font-black uppercase tracking-[0.14em] text-cyan-700">Course structure</p>
        <h2 id="new-unit-heading" class="mt-1 text-2xl font-black">Add a Unit</h2>
        <form method="POST" action="{{ route('teacher.courses.units.store', $course) }}" class="mt-5 grid gap-4 md:grid-cols-[minmax(0,1fr)_120px]">
            @csrf
            <label class="block">
                <span class="text-sm font-black text-slate-700">Unit title</span>
                <input type="text" name="title" value="{{ old('title') }}" required maxlength="160" placeholder="Unit 1 — Introduction" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
            </label>
            <label class="block">
                <span class="text-sm font-black text-slate-700">Order</span>
                <input type="number" name="position" value="{{ old('position') }}" min="1" max="999" placeholder="Auto" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
            </label>
            <label class="block md:col-span-2">
                <span class="text-sm font-black text-slate-700">Description</span>
                <textarea name="description" rows="3" maxlength="2000" placeholder="What Learners will study in this unit" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">{{ old('description') }}</textarea>
            </label>
            <button type="submit" class="md:col-span-2 rounded-xl bg-blue-700 px-4 py-3 text-sm font-black text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">Add Unit</button>
        </form>
    </section>

    <section aria-labelledby="units-heading">
        <div class="mb-4">
            <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Curriculum</p>
            <h2 id="units-heading" class="mt-1 text-2xl font-black">Units & Lessons</h2>
        </div>

        @if ($course->units->isEmpty())
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">
                <p class="font-black">No Units yet.</p>
                <p class="mt-2 text-sm text-slate-500">Create the first Unit above, then add Lessons inside it.</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach ($course->units as $unit)
                    <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 bg-slate-50 p-5 sm:p-6">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-[0.14em] text-cyan-700">Unit {{ $unit->position }}</p>
                                    <h3 class="mt-1 text-xl font-black">{{ $unit->title }}</h3>
                                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">{{ $unit->description ?: 'No Unit description yet.' }}</p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-black {{ $unit->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">{{ $unit->is_active ? 'Visible' : 'Hidden' }}</span>
                            </div>

                            <details class="mt-5 rounded-2xl border border-slate-200 bg-white p-4">
                                <summary class="cursor-pointer text-sm font-black text-blue-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Edit Unit</summary>
                                <form method="POST" action="{{ route('teacher.courses.units.update', [$course, $unit]) }}" class="mt-4 grid gap-4 sm:grid-cols-[minmax(0,1fr)_120px]">
                                    @csrf
                                    @method('PUT')
                                    <label class="block">
                                        <span class="text-sm font-black text-slate-700">Title</span>
                                        <input type="text" name="title" value="{{ $unit->title }}" required maxlength="160" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
                                    </label>
                                    <label class="block">
                                        <span class="text-sm font-black text-slate-700">Order</span>
                                        <input type="number" name="position" value="{{ $unit->position }}" min="1" max="999" required class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
                                    </label>
                                    <label class="block sm:col-span-2">
                                        <span class="text-sm font-black text-slate-700">Description</span>
                                        <textarea name="description" rows="3" maxlength="2000" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">{{ $unit->description }}</textarea>
                                    </label>
                                    <label class="flex items-center gap-3 sm:col-span-2">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" @checked($unit->is_active) class="rounded border-slate-300 text-blue-700 focus:ring-cyan-500">
                                        <span class="text-sm font-black text-slate-700">Show this Unit to Learners</span>
                                    </label>
                                    <div class="flex flex-wrap gap-2 sm:col-span-2">
                                        <button type="submit" class="rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Save Unit</button>
                                    </div>
                                </form>
                                <form method="POST" action="{{ route('teacher.courses.units.destroy', [$course, $unit]) }}" class="mt-3">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-xl border border-rose-200 bg-white px-4 py-2.5 text-sm font-black text-rose-700 hover:bg-rose-50 focus:outline-none focus:ring-4 focus:ring-rose-100">Delete Unit & Lessons</button>
                                </form>
                            </details>
                        </div>

                        <div class="p-5 sm:p-6">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <h4 class="text-lg font-black">Lessons</h4>
                                <span class="text-xs font-bold text-slate-400">{{ $unit->lessons->count() }} total</span>
                            </div>

                            @if ($unit->lessons->isNotEmpty())
                                <div class="mt-4 space-y-4">
                                    @foreach ($unit->lessons as $lesson)
                                        <details class="rounded-2xl border border-slate-200 p-4">
                                            <summary class="cursor-pointer list-none focus:outline-none focus:ring-4 focus:ring-cyan-200">
                                                <div class="flex flex-wrap items-center justify-between gap-3">
                                                    <div>
                                                        <p class="text-xs font-black uppercase tracking-wide text-cyan-700">Lesson {{ $lesson->position }}</p>
                                                        <h5 class="mt-1 font-black">{{ $lesson->title }}</h5>
                                                    </div>
                                                    <span class="rounded-full px-3 py-1 text-xs font-black {{ $lesson->status === 'published' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-800' }}">{{ ucfirst($lesson->status) }}</span>
                                                </div>
                                            </summary>

                                            <form method="POST" action="{{ route('teacher.courses.units.lessons.update', [$course, $unit, $lesson]) }}" class="mt-5 grid gap-4 sm:grid-cols-2">
                                                @csrf
                                                @method('PUT')
                                                <label class="block sm:col-span-2">
                                                    <span class="text-sm font-black text-slate-700">Lesson title</span>
                                                    <input type="text" name="title" value="{{ $lesson->title }}" required maxlength="180" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
                                                </label>
                                                <label class="block sm:col-span-2">
                                                    <span class="text-sm font-black text-slate-700">Summary</span>
                                                    <textarea name="summary" rows="2" maxlength="3000" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">{{ $lesson->summary }}</textarea>
                                                </label>
                                                <label class="block sm:col-span-2">
                                                    <span class="text-sm font-black text-slate-700">ISL video URL</span>
                                                    <input type="url" name="isl_video_url" value="{{ $lesson->isl_video_url }}" maxlength="500" placeholder="https://..." class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
                                                </label>
                                                <label class="block sm:col-span-2">
                                                    <span class="text-sm font-black text-slate-700">Lesson notes</span>
                                                    <textarea name="notes" rows="6" maxlength="20000" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">{{ $lesson->notes }}</textarea>
                                                </label>
                                                <label class="block">
                                                    <span class="text-sm font-black text-slate-700">Estimated minutes</span>
                                                    <input type="number" name="estimated_minutes" value="{{ $lesson->estimated_minutes }}" min="1" max="600" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
                                                </label>
                                                <label class="block">
                                                    <span class="text-sm font-black text-slate-700">Order</span>
                                                    <input type="number" name="position" value="{{ $lesson->position }}" min="1" max="999" required class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
                                                </label>
                                                <label class="block sm:col-span-2">
                                                    <span class="text-sm font-black text-slate-700">Status</span>
                                                    <select name="status" required class="mt-2 w-full rounded-xl border-slate-300 bg-white focus:border-cyan-500 focus:ring-cyan-500">
                                                        <option value="draft" @selected($lesson->status === 'draft')>Draft — hidden from Learners</option>
                                                        <option value="published" @selected($lesson->status === 'published')>Published — visible to Learners</option>
                                                    </select>
                                                </label>
                                                <button type="submit" class="sm:col-span-2 rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Save Lesson</button>
                                            </form>
                                            <form method="POST" action="{{ route('teacher.courses.units.lessons.destroy', [$course, $unit, $lesson]) }}" class="mt-3">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-xl border border-rose-200 bg-white px-4 py-2.5 text-sm font-black text-rose-700 hover:bg-rose-50 focus:outline-none focus:ring-4 focus:ring-rose-100">Delete Lesson</button>
                                            </form>
                                        </details>
                                    @endforeach
                                </div>
                            @else
                                <div class="mt-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-5 text-center text-sm font-bold text-slate-500">No Lessons in this Unit yet.</div>
                            @endif

                            <details class="mt-5 rounded-2xl bg-blue-50 p-5">
                                <summary class="cursor-pointer text-sm font-black text-blue-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">+ Add Lesson to {{ $unit->title }}</summary>
                                <form method="POST" action="{{ route('teacher.courses.units.lessons.store', [$course, $unit]) }}" class="mt-5 grid gap-4 sm:grid-cols-2">
                                    @csrf
                                    <label class="block sm:col-span-2">
                                        <span class="text-sm font-black text-slate-700">Lesson title</span>
                                        <input type="text" name="title" required maxlength="180" placeholder="Lesson title" class="mt-2 w-full rounded-xl border-slate-300 bg-white focus:border-cyan-500 focus:ring-cyan-500">
                                    </label>
                                    <label class="block sm:col-span-2">
                                        <span class="text-sm font-black text-slate-700">Summary</span>
                                        <textarea name="summary" rows="2" maxlength="3000" class="mt-2 w-full rounded-xl border-slate-300 bg-white focus:border-cyan-500 focus:ring-cyan-500"></textarea>
                                    </label>
                                    <label class="block sm:col-span-2">
                                        <span class="text-sm font-black text-slate-700">ISL video URL</span>
                                        <input type="url" name="isl_video_url" maxlength="500" placeholder="https://..." class="mt-2 w-full rounded-xl border-slate-300 bg-white focus:border-cyan-500 focus:ring-cyan-500">
                                    </label>
                                    <label class="block sm:col-span-2">
                                        <span class="text-sm font-black text-slate-700">Lesson notes</span>
                                        <textarea name="notes" rows="5" maxlength="20000" class="mt-2 w-full rounded-xl border-slate-300 bg-white focus:border-cyan-500 focus:ring-cyan-500"></textarea>
                                    </label>
                                    <label class="block">
                                        <span class="text-sm font-black text-slate-700">Estimated minutes</span>
                                        <input type="number" name="estimated_minutes" min="1" max="600" class="mt-2 w-full rounded-xl border-slate-300 bg-white focus:border-cyan-500 focus:ring-cyan-500">
                                    </label>
                                    <label class="block">
                                        <span class="text-sm font-black text-slate-700">Order</span>
                                        <input type="number" name="position" min="1" max="999" placeholder="Auto" class="mt-2 w-full rounded-xl border-slate-300 bg-white focus:border-cyan-500 focus:ring-cyan-500">
                                    </label>
                                    <label class="block sm:col-span-2">
                                        <span class="text-sm font-black text-slate-700">Status</span>
                                        <select name="status" required class="mt-2 w-full rounded-xl border-slate-300 bg-white focus:border-cyan-500 focus:ring-cyan-500">
                                            <option value="draft">Draft — hidden from Learners</option>
                                            <option value="published">Published — visible to Learners</option>
                                        </select>
                                    </label>
                                    <button type="submit" class="sm:col-span-2 rounded-xl bg-blue-700 px-4 py-3 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Add Lesson</button>
                                </form>
                            </details>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
