@extends('layouts.admin')

@section('title', 'Teachers - SignGyaan Admin')
@section('page-title', 'Teachers')
@section('description', 'Manage SignGyaan teacher profiles and teaching assignments.')

@section('content')
<section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
    <div class="mx-auto max-w-7xl">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">User management</p>
            <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Teacher Management</h2>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-sign-muted">Manage teacher profiles, subject responsibilities and course assignments.</p>
        </div>

        <div class="mt-7 grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-sign-border bg-white p-5"><p class="text-sm font-semibold text-sign-muted">Total teachers</p><p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $totalTeachers }}</p></div>
            <div class="rounded-2xl border border-sign-border bg-white p-5"><p class="text-sm font-semibold text-sign-muted">Active teachers</p><p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $activeTeachers }}</p></div>
            <div class="rounded-2xl border border-sign-border bg-white p-5"><p class="text-sm font-semibold text-sign-muted">With assignments</p><p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $assignedTeachers }}</p></div>
        </div>

        <form method="GET" action="{{ route('admin.teachers.index') }}" class="mt-7 grid gap-3 rounded-2xl border border-sign-border bg-white p-4 md:grid-cols-[minmax(0,1fr)_13rem_auto] md:items-end">
            <div><label for="teacher-search" class="mb-2 block text-sm font-semibold text-sign-primary">Search</label><input id="teacher-search" name="q" type="search" value="{{ request('q') }}" placeholder="Name, email or employee code" class="min-h-12 w-full rounded-xl border border-sign-border px-4 py-3 text-base outline-none focus:border-sign-cyan focus:ring-4 focus:ring-sign-light"></div>
            <div><label for="teacher-status" class="mb-2 block text-sm font-semibold text-sign-primary">Status</label><select id="teacher-status" name="status" class="min-h-12 w-full rounded-xl border border-sign-border px-4 py-3 text-base outline-none focus:border-sign-cyan focus:ring-4 focus:ring-sign-light"><option value="">All statuses</option><option value="active" @selected(request('status') === 'active')>Active</option><option value="suspended" @selected(request('status') === 'suspended')>Suspended</option><option value="disabled" @selected(request('status') === 'disabled')>Disabled</option></select></div>
            <div class="flex gap-2"><button class="inline-flex min-h-12 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white">Filter</button>@if(request()->hasAny(['q','status']))<a href="{{ route('admin.teachers.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-sign-border px-4 py-3 text-sm font-semibold text-sign-primary">Clear</a>@endif</div>
        </form>

        <div class="mt-7 overflow-hidden rounded-2xl border border-sign-border bg-white">
            @forelse($teachers as $teacher)
                <article class="flex flex-col gap-4 border-b border-sign-border p-5 last:border-b-0 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2"><h3 class="font-heading text-xl font-semibold text-sign-primary">{{ $teacher->name }}</h3><span class="rounded-full bg-sign-soft px-2.5 py-1 text-xs font-semibold text-sign-primary">{{ ucfirst($teacher->status) }}</span></div>
                        <p class="mt-1 break-all text-sm text-sign-muted">{{ $teacher->email }}</p>
                        <p class="mt-2 text-xs text-sign-muted">Employee code: {{ $teacher->teacherProfile?->employee_code ?: 'Not set' }} · {{ $teacher->teaching_subjects_count }} subjects · {{ $teacher->teaching_courses_count }} courses</p>
                    </div>
                    <a href="{{ route('admin.teachers.edit', $teacher) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Manage Teacher</a>
                </article>
            @empty
                <div class="p-10 text-center"><h3 class="font-heading text-2xl font-semibold text-sign-primary">No teachers found</h3><p class="mt-2 text-sm text-sign-muted">Assign the Teacher role to a user first, or change the current filters.</p></div>
            @endforelse
        </div>

        @if($teachers->hasPages())<div class="mt-6">{{ $teachers->links() }}</div>@endif
    </div>
</section>
@endsection
