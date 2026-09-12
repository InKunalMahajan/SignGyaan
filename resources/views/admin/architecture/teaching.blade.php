@extends('admin.layout')

@section('title', 'Teaching Management')

@section('content')
<div class="space-y-8">
    <section>
        <p class="text-sm font-bold uppercase tracking-[0.18em] text-cyan-700">Teaching Structure</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight">Teaching Management</h1>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">This area is only for delivery: Teacher → Class / Batch → Learners → Course Assignments. Academic curriculum content is managed separately.</p>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @foreach ([
            ['Teachers', $stats['teachers']],
            ['Classes / Batches', $stats['classes']],
            ['Active Classes', $stats['active_classes']],
            ['Enrollments', $stats['enrollments']],
            ['Course Assignments', $stats['course_assignments']],
        ] as [$label, $value])
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-bold text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-3xl font-black">{{ $value }}</p>
            </article>
        @endforeach
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="text-xl font-black">Teachers</h2>
                <p class="mt-1 text-sm text-slate-600">Teacher accounts are managed in Users & Roles.</p>
            </div>
            <a href="{{ route('admin.users.index', ['role' => 'teacher']) }}" class="rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Open Teachers</a>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div>
            <h2 class="text-xl font-black">Classes / Batches</h2>
            <p class="mt-1 text-sm text-slate-600">A Class / Batch is a real teaching group such as FYJC-A 2026-27. It is not the same as an Academic Class such as FYJC.</p>
        </div>

        <div class="mt-5 overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-3 py-3">Class / Batch</th>
                        <th class="px-3 py-3">Teacher</th>
                        <th class="px-3 py-3">Academic Year</th>
                        <th class="px-3 py-3">Learners</th>
                        <th class="px-3 py-3">Courses</th>
                        <th class="px-3 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($classes as $class)
                        <tr>
                            <td class="px-3 py-4 font-black text-slate-900">{{ $class->name }}</td>
                            <td class="px-3 py-4 text-slate-600">{{ optional($class->teacher)->name ?: 'Unassigned' }}</td>
                            <td class="px-3 py-4 text-slate-600">{{ $class->academic_year ?: '—' }}</td>
                            <td class="px-3 py-4 font-bold">{{ $class->learners_count }}</td>
                            <td class="px-3 py-4 font-bold">{{ $class->courses_count }}</td>
                            <td class="px-3 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-black {{ $class->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ $class->is_active ? 'Active' : 'Inactive' }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-3 py-8 text-center text-slate-500">No Classes / Batches yet. Teachers create and manage their teaching groups.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="rounded-3xl border border-blue-100 bg-blue-50 p-5 sm:p-6">
        <h2 class="text-lg font-black text-blue-950">Teaching flow</h2>
        <p class="mt-2 text-sm font-bold leading-7 text-blue-900">Teacher → Create Class / Batch → Enroll Learners → Assign Course → Monitor Progress</p>
    </section>
</div>
@endsection
