@extends('teacher.layout')

@section('title', $class->name)

@section('content')
    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('teacher.classes.index') }}" class="text-sm font-black text-blue-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">← My Classes</a>
            <p class="mt-4 text-xs font-black uppercase tracking-[0.16em] text-cyan-700">{{ $class->code }}</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight">{{ $class->name }}</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">{{ $class->description ?: 'No class description has been added yet.' }}</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('teacher.classes.edit', $class) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-700 shadow-sm focus:outline-none focus:ring-4 focus:ring-cyan-200">Edit class</a>
            <form method="POST" action="{{ route('teacher.classes.status', $class) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="rounded-xl px-4 py-3 text-sm font-black focus:outline-none focus:ring-4 focus:ring-cyan-200 {{ $class->is_active ? 'bg-slate-900 text-white' : 'bg-emerald-600 text-white' }}">{{ $class->is_active ? 'Archive class' : 'Activate class' }}</button>
            </form>
        </div>
    </div>

    <section class="mb-8 grid gap-4 sm:grid-cols-5" aria-label="Class details">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-500">Status</p>
            <p class="mt-2 text-lg font-black">{{ $class->is_active ? 'Active' : 'Archived' }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-500">Subject</p>
            <p class="mt-2 text-lg font-black">{{ $class->subject ?: '—' }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-500">Level</p>
            <p class="mt-2 text-lg font-black">{{ $class->level ?: '—' }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-500">Learners</p>
            <p class="mt-2 text-lg font-black">{{ $class->learners->count() }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-500">Courses</p>
            <p class="mt-2 text-lg font-black">{{ $class->courses->count() }}</p>
        </article>
    </section>

    <section class="mb-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-7" aria-labelledby="courses-heading">
        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-100 pb-5">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.14em] text-cyan-700">Class learning</p>
                <h2 id="courses-heading" class="mt-1 text-2xl font-black">Assigned Courses</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">Assign existing SignGyaan courses or create a new course for this class.</p>
            </div>
            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">{{ $class->courses->count() }} assigned</span>
        </div>

        @if ($class->courses->isEmpty())
            <div class="mt-5 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center">
                <p class="font-black">No courses assigned yet.</p>
                <p class="mt-1 text-sm text-slate-500">Assign an existing course or create the first course below.</p>
            </div>
        @else
            <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($class->courses as $course)
                    <article class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <div class="flex items-start justify-between gap-3">
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-black text-cyan-800 shadow-sm">{{ $course->subject?->name ?: 'Course' }}</span>
                            <span class="rounded-full px-2.5 py-1 text-[11px] font-black {{ $course->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">{{ $course->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                        <h3 class="mt-4 text-lg font-black">{{ $course->title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">{{ $course->description ?: 'No course description yet.' }}</p>
                        @if ($course->level)
                            <p class="mt-3 text-xs font-bold text-slate-500">Level: {{ $course->level }}</p>
                        @endif
                        <form method="POST" action="{{ route('teacher.classes.courses.destroy', [$class, $course]) }}" class="mt-4">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-xl border border-rose-200 bg-white px-3 py-2 text-sm font-black text-rose-700 hover:bg-rose-50 focus:outline-none focus:ring-4 focus:ring-rose-100">Remove from class</button>
                        </form>
                    </article>
                @endforeach
            </div>
        @endif

        @if ($class->is_active)
            <div class="mt-7 grid gap-6 border-t border-slate-100 pt-6 lg:grid-cols-2">
                <section class="rounded-2xl bg-blue-50 p-5" aria-labelledby="existing-course-heading">
                    <p class="text-xs font-black uppercase tracking-[0.14em] text-blue-700">Course catalog</p>
                    <h3 id="existing-course-heading" class="mt-1 text-lg font-black">Assign an existing course</h3>

                    @if ($availableCourses->isEmpty())
                        <p class="mt-4 text-sm leading-6 text-blue-900/80">No other active courses are available. Create and assign a new course instead.</p>
                    @else
                        <form method="POST" action="{{ route('teacher.classes.courses.store', $class) }}" class="mt-4">
                            @csrf
                            <label class="block">
                                <span class="text-sm font-black text-slate-700">Course</span>
                                <select name="course_id" required class="mt-2 w-full rounded-xl border-slate-300 bg-white focus:border-cyan-500 focus:ring-cyan-500">
                                    <option value="">Select a course</option>
                                    @foreach ($availableCourses as $course)
                                        <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>{{ $course->subject?->name }} — {{ $course->title }}{{ $course->level ? ' · '.$course->level : '' }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <button type="submit" class="mt-4 w-full rounded-xl bg-blue-700 px-4 py-3 text-sm font-black text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">Assign course</button>
                        </form>
                    @endif
                </section>

                <section class="rounded-2xl bg-cyan-50 p-5" aria-labelledby="new-course-heading">
                    <p class="text-xs font-black uppercase tracking-[0.14em] text-cyan-700">Quick create</p>
                    <h3 id="new-course-heading" class="mt-1 text-lg font-black">Create & assign a new course</h3>
                    <form method="POST" action="{{ route('teacher.classes.courses.store-new', $class) }}" class="mt-4 grid gap-4 sm:grid-cols-2">
                        @csrf
                        <label class="block">
                            <span class="text-sm font-black text-slate-700">Subject</span>
                            <input type="text" name="subject_name" value="{{ old('subject_name', $class->subject) }}" required maxlength="120" placeholder="Information Technology" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
                        </label>
                        <label class="block">
                            <span class="text-sm font-black text-slate-700">Course title</span>
                            <input type="text" name="course_title" value="{{ old('course_title') }}" required maxlength="160" placeholder="Digital Basics" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
                        </label>
                        <label class="block sm:col-span-2">
                            <span class="text-sm font-black text-slate-700">Level</span>
                            <input type="text" name="course_level" value="{{ old('course_level', $class->level) }}" maxlength="100" placeholder="Beginner" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
                        </label>
                        <label class="block sm:col-span-2">
                            <span class="text-sm font-black text-slate-700">Description</span>
                            <textarea name="course_description" rows="3" maxlength="1000" placeholder="Short course description" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">{{ old('course_description') }}</textarea>
                        </label>
                        <button type="submit" class="sm:col-span-2 rounded-xl bg-cyan-700 px-4 py-3 text-sm font-black text-white hover:bg-cyan-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">Create & assign course</button>
                    </form>
                </section>
            </div>
        @else
            <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm font-bold text-amber-900">Activate this class before assigning or creating courses.</div>
        @endif
    </section>

    <div class="grid gap-6 lg:grid-cols-[minmax(300px,.7fr)_minmax(0,1.3fr)]">
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-black uppercase tracking-[0.14em] text-cyan-700">Roster</p>
            <h2 class="mt-2 text-xl font-black">Add a Learner</h2>
            <p class="mt-2 text-sm leading-6 text-slate-500">Use the exact email on the active SignGyaan Learner account.</p>

            @if ($class->is_active)
                <form method="POST" action="{{ route('teacher.classes.learners.store', $class) }}" class="mt-5">
                    @csrf
                    <label class="block">
                        <span class="text-sm font-black text-slate-700">Learner email</span>
                        <input type="email" name="learner_email" value="{{ old('learner_email') }}" required placeholder="learner@example.com" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
                    </label>
                    <button type="submit" class="mt-4 w-full rounded-xl bg-blue-700 px-4 py-3 text-sm font-black text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">Add to class</button>
                </form>
            @else
                <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm font-bold text-amber-900">Activate this class before adding Learners.</div>
            @endif

            <dl class="mt-6 space-y-3 border-t border-slate-100 pt-5 text-sm">
                <div><dt class="font-bold text-slate-500">Academic year</dt><dd class="mt-1 font-black">{{ $class->academic_year ?: '—' }}</dd></div>
                <div><dt class="font-bold text-slate-500">Class code</dt><dd class="mt-1 font-black tracking-wide">{{ $class->code }}</dd></div>
            </dl>
        </section>

        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-5">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-cyan-700">Learner roster</p>
                <h2 class="mt-1 text-xl font-black">Enrolled Learners</h2>
            </div>

            @if ($class->learners->isEmpty())
                <div class="p-8 text-center">
                    <p class="font-black">No Learners enrolled yet.</p>
                    <p class="mt-2 text-sm text-slate-500">Add the first Learner using the form.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach ($class->learners as $learner)
                        <article class="flex flex-wrap items-center justify-between gap-4 px-6 py-5">
                            <div class="min-w-0">
                                <h3 class="font-black text-slate-950">{{ $learner->name }}</h3>
                                <p class="mt-1 text-sm text-slate-500">{{ $learner->email }}</p>
                                <p class="mt-2 text-xs font-bold text-cyan-700">
                                    {{ $learner->learnerProfile?->education_level ?: 'Education level not set' }}
                                    @if ($learner->learnerProfile?->class_grade)
                                        · {{ $learner->learnerProfile->class_grade }}
                                    @endif
                                </p>
                            </div>
                            <form method="POST" action="{{ route('teacher.classes.learners.destroy', [$class, $learner]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-xl border border-rose-200 bg-white px-3 py-2 text-sm font-black text-rose-700 hover:bg-rose-50 focus:outline-none focus:ring-4 focus:ring-rose-100">Remove</button>
                            </form>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
