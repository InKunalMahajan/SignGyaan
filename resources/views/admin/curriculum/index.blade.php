@extends('admin.layout')

@section('title', 'Curriculum Catalog')

@section('content')
<div class="space-y-8">
    <section class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-bold uppercase tracking-[0.18em] text-indigo-600">Phase 3</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Curriculum Catalog</h1>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Manage the learning hierarchy: Board → Academic Class → Subject → Course → Unit / Chapter → Lesson.</p>
        </div>
        <div class="grid grid-cols-3 gap-2 text-center">
            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                <div class="text-2xl font-black text-slate-950">{{ $boards->count() }}</div>
                <div class="text-xs font-bold text-slate-500">Boards</div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                <div class="text-2xl font-black text-slate-950">{{ $academicClasses->count() }}</div>
                <div class="text-xs font-bold text-slate-500">Classes</div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                <div class="text-2xl font-black text-slate-950">{{ $subjects->count() }}</div>
                <div class="text-xs font-bold text-slate-500">Subjects</div>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-black text-slate-950">Curriculum hierarchy</h2>
                <p class="mt-1 text-sm text-slate-600">A quick read-only overview of the current structure.</p>
            </div>
        </div>

        <div class="mt-5 space-y-4">
            @forelse ($boards as $board)
                <details class="rounded-2xl border border-slate-200 bg-slate-50" open>
                    <summary class="cursor-pointer list-none px-4 py-4 font-black text-slate-900">
                        <span class="flex flex-wrap items-center justify-between gap-3">
                            <span>{{ $board->name }} @if($board->short_name)<span class="text-slate-500">({{ $board->short_name }})</span>@endif</span>
                            <span class="rounded-full px-3 py-1 text-xs font-black {{ $board->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">{{ $board->is_active ? 'Active' : 'Inactive' }}</span>
                        </span>
                    </summary>
                    <div class="border-t border-slate-200 px-4 py-4">
                        @forelse ($board->academicClasses as $academicClass)
                            <div class="mb-3 rounded-xl border border-slate-200 bg-white p-4 last:mb-0">
                                <div class="font-black text-slate-900">{{ $academicClass->name }}</div>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @forelse ($academicClass->subjects as $subject)
                                        <span class="rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">{{ $subject->name }} · {{ $subject->courses_count }} course{{ $subject->courses_count === 1 ? '' : 's' }}</span>
                                    @empty
                                        <span class="text-sm text-slate-500">No subjects yet.</span>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No academic classes yet.</p>
                        @endforelse
                    </div>
                </details>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 px-5 py-10 text-center text-sm text-slate-500">No curriculum structure has been created yet.</div>
            @endforelse
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-xl font-black text-slate-950">Add Board</h2>
            <form method="POST" action="{{ route('admin.curriculum.boards.store') }}" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label for="board-name" class="text-sm font-bold text-slate-700">Board name</label>
                    <input id="board-name" name="name" value="{{ old('name') }}" required class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-100" placeholder="Maharashtra State Board">
                </div>
                <div>
                    <label for="board-short-name" class="text-sm font-bold text-slate-700">Short name</label>
                    <input id="board-short-name" name="short_name" value="{{ old('short_name') }}" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-100" placeholder="MSBSHSE">
                </div>
                <div>
                    <label for="board-sort" class="text-sm font-bold text-slate-700">Sort order</label>
                    <input id="board-sort" type="number" min="0" name="sort_order" value="{{ old('sort_order', 0) }}" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                </div>
                <div>
                    <label for="board-description" class="text-sm font-bold text-slate-700">Description</label>
                    <textarea id="board-description" name="description" rows="3" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-100">{{ old('description') }}</textarea>
                </div>
                <label class="flex items-center gap-3 text-sm font-bold text-slate-700"><input type="checkbox" name="is_active" value="1" checked class="size-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"> Active</label>
                <button class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-black text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">Create Board</button>
            </form>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-xl font-black text-slate-950">Add Academic Class</h2>
            <form method="POST" action="{{ route('admin.curriculum.classes.store') }}" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label for="class-board" class="text-sm font-bold text-slate-700">Board</label>
                    <select id="class-board" name="board_id" required class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                        <option value="">Select board</option>
                        @foreach ($boards as $board)
                            <option value="{{ $board->id }}">{{ $board->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="class-name" class="text-sm font-bold text-slate-700">Class name</label>
                    <input id="class-name" name="name" required class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-100" placeholder="FYJC">
                </div>
                <div>
                    <label for="class-level" class="text-sm font-bold text-slate-700">Level</label>
                    <input id="class-level" name="level" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-100" placeholder="11">
                </div>
                <div>
                    <label for="class-sort" class="text-sm font-bold text-slate-700">Sort order</label>
                    <input id="class-sort" type="number" min="0" name="sort_order" value="0" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                </div>
                <label class="flex items-center gap-3 text-sm font-bold text-slate-700"><input type="checkbox" name="is_active" value="1" checked class="size-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"> Active</label>
                <button class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-black text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">Create Academic Class</button>
            </form>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-xl font-black text-slate-950">Add Subject</h2>
            <form method="POST" action="{{ route('admin.curriculum.subjects.store') }}" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label for="subject-class" class="text-sm font-bold text-slate-700">Academic class</label>
                    <select id="subject-class" name="academic_class_id" required class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                        <option value="">Select class</option>
                        @foreach ($academicClasses as $academicClass)
                            <option value="{{ $academicClass->id }}">{{ $academicClass->board->short_name ?: $academicClass->board->name }} — {{ $academicClass->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="subject-name" class="text-sm font-bold text-slate-700">Subject name</label>
                    <input id="subject-name" name="name" required class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-100" placeholder="Information Technology">
                </div>
                <div>
                    <label for="subject-description" class="text-sm font-bold text-slate-700">Description</label>
                    <textarea id="subject-description" name="description" rows="3" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-100"></textarea>
                </div>
                <label class="flex items-center gap-3 text-sm font-bold text-slate-700"><input type="checkbox" name="is_active" value="1" checked class="size-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"> Active</label>
                <button class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-black text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">Create Subject</button>
            </form>
        </div>
    </section>

    <section class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-xl font-black text-slate-950">Manage Boards</h2>
            <div class="mt-5 space-y-3">
                @forelse ($boards as $board)
                    <details class="rounded-2xl border border-slate-200">
                        <summary class="cursor-pointer list-none px-4 py-4 font-bold text-slate-900">{{ $board->name }} <span class="ml-2 text-xs text-slate-500">{{ $board->academic_classes_count }} class{{ $board->academic_classes_count === 1 ? '' : 'es' }}</span></summary>
                        <div class="border-t border-slate-200 p-4">
                            <form method="POST" action="{{ route('admin.curriculum.boards.update', $board) }}" class="grid gap-3 md:grid-cols-2">
                                @csrf @method('PUT')
                                <input name="name" value="{{ $board->name }}" required class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                                <input name="short_name" value="{{ $board->short_name }}" class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm" placeholder="Short name">
                                <input type="number" min="0" name="sort_order" value="{{ $board->sort_order }}" class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                                <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-bold"><input type="checkbox" name="is_active" value="1" @checked($board->is_active)> Active</label>
                                <textarea name="description" rows="2" class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm md:col-span-2">{{ $board->description }}</textarea>
                                <button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-black text-white">Save Board</button>
                            </form>
                            <form method="POST" action="{{ route('admin.curriculum.boards.destroy', $board) }}" class="mt-3" onsubmit="return confirm('Delete this board?');">
                                @csrf @method('DELETE')
                                <button class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-black text-rose-700">Delete Board</button>
                            </form>
                        </div>
                    </details>
                @empty
                    <p class="text-sm text-slate-500">No boards available.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-xl font-black text-slate-950">Manage Academic Classes</h2>
            <div class="mt-5 space-y-3">
                @forelse ($academicClasses as $academicClass)
                    <details class="rounded-2xl border border-slate-200">
                        <summary class="cursor-pointer list-none px-4 py-4 font-bold text-slate-900">{{ $academicClass->board->short_name ?: $academicClass->board->name }} — {{ $academicClass->name }} <span class="ml-2 text-xs text-slate-500">{{ $academicClass->subjects_count }} subjects</span></summary>
                        <div class="border-t border-slate-200 p-4">
                            <form method="POST" action="{{ route('admin.curriculum.classes.update', $academicClass) }}" class="grid gap-3 md:grid-cols-2">
                                @csrf @method('PUT')
                                <select name="board_id" required class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                                    @foreach ($boards as $board)
                                        <option value="{{ $board->id }}" @selected($academicClass->board_id === $board->id)>{{ $board->name }}</option>
                                    @endforeach
                                </select>
                                <input name="name" value="{{ $academicClass->name }}" required class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                                <input name="level" value="{{ $academicClass->level }}" class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm" placeholder="Level">
                                <input type="number" min="0" name="sort_order" value="{{ $academicClass->sort_order }}" class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                                <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-bold md:col-span-2"><input type="checkbox" name="is_active" value="1" @checked($academicClass->is_active)> Active</label>
                                <button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-black text-white">Save Academic Class</button>
                            </form>
                            <form method="POST" action="{{ route('admin.curriculum.classes.destroy', $academicClass) }}" class="mt-3" onsubmit="return confirm('Delete this academic class?');">
                                @csrf @method('DELETE')
                                <button class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-black text-rose-700">Delete Academic Class</button>
                            </form>
                        </div>
                    </details>
                @empty
                    <p class="text-sm text-slate-500">No academic classes available.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-xl font-black text-slate-950">Manage Subjects</h2>
            <div class="mt-5 space-y-3">
                @forelse ($subjects as $subject)
                    <details class="rounded-2xl border border-slate-200">
                        <summary class="cursor-pointer list-none px-4 py-4 font-bold text-slate-900">{{ $subject->name }} <span class="ml-2 text-xs text-slate-500">{{ $subject->academicClass?->board?->short_name ?: $subject->academicClass?->board?->name }} · {{ $subject->academicClass?->name ?: 'Unassigned' }} · {{ $subject->courses_count }} courses</span></summary>
                        <div class="border-t border-slate-200 p-4">
                            <form method="POST" action="{{ route('admin.curriculum.subjects.update', $subject) }}" class="grid gap-3 md:grid-cols-2">
                                @csrf @method('PUT')
                                <select name="academic_class_id" required class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                                    @foreach ($academicClasses as $academicClass)
                                        <option value="{{ $academicClass->id }}" @selected($subject->academic_class_id === $academicClass->id)>{{ $academicClass->board->short_name ?: $academicClass->board->name }} — {{ $academicClass->name }}</option>
                                    @endforeach
                                </select>
                                <input name="name" value="{{ $subject->name }}" required class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                                <textarea name="description" rows="2" class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm md:col-span-2">{{ $subject->description }}</textarea>
                                <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-bold md:col-span-2"><input type="checkbox" name="is_active" value="1" @checked($subject->is_active)> Active</label>
                                <button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-black text-white">Save Subject</button>
                            </form>
                            <form method="POST" action="{{ route('admin.curriculum.subjects.destroy', $subject) }}" class="mt-3" onsubmit="return confirm('Delete this subject?');">
                                @csrf @method('DELETE')
                                <button class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-black text-rose-700">Delete Subject</button>
                            </form>
                        </div>
                    </details>
                @empty
                    <p class="text-sm text-slate-500">No subjects available.</p>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection
