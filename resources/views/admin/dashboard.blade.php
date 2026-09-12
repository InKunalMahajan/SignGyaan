@extends('admin.layout')

@section('title', 'Admin Dashboard')

@section('content')
    @php
        $users = $adminDashboard['users'];
        $curriculum = $adminDashboard['curriculum'];
        $teaching = $adminDashboard['teaching'];
        $assessments = $adminDashboard['assessments'];
        $mastery = $adminDashboard['mastery'];
    @endphp

    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-500">Platform overview</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Admin Dashboard</h1>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Monitor users, curriculum, teaching delivery, assessments, and learner mastery from real SignGyaan data.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="rounded-xl bg-slate-950 px-4 py-3 text-sm font-black text-white hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-300">Create user</a>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5" aria-label="Platform summary">
        <a href="{{ route('admin.users.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-200">
            <p class="text-xs font-black uppercase tracking-[0.12em] text-slate-500">Total users</p>
            <p class="mt-2 text-3xl font-black">{{ number_format($users['total']) }}</p>
            <p class="mt-1 text-sm text-slate-500">{{ number_format($users['active']) }} active</p>
        </a>
        <a href="{{ route('admin.curriculum.content.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-200">
            <p class="text-xs font-black uppercase tracking-[0.12em] text-slate-500">Active courses</p>
            <p class="mt-2 text-3xl font-black">{{ number_format($curriculum['active_courses']) }}</p>
            <p class="mt-1 text-sm text-slate-500">{{ number_format($curriculum['published_lessons']) }} published lessons</p>
        </a>
        <a href="{{ route('admin.teaching.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-200">
            <p class="text-xs font-black uppercase tracking-[0.12em] text-slate-500">Active classes</p>
            <p class="mt-2 text-3xl font-black">{{ number_format($teaching['active_classes']) }}</p>
            <p class="mt-1 text-sm text-slate-500">{{ number_format($teaching['learners_enrolled']) }} learners enrolled</p>
        </a>
        <a href="{{ route('admin.progress.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-200">
            <p class="text-xs font-black uppercase tracking-[0.12em] text-slate-500">Pending reviews</p>
            <p class="mt-2 text-3xl font-black">{{ number_format($assessments['pending_review']) }}</p>
            <p class="mt-1 text-sm text-slate-500">{{ number_format($assessments['completed']) }} completed attempts</p>
        </a>
        <a href="{{ route('admin.progress.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-200">
            <p class="text-xs font-black uppercase tracking-[0.12em] text-slate-500">Mastery average</p>
            <p class="mt-2 text-3xl font-black">{{ $mastery['average_score'] !== null ? $mastery['average_score'].'%' : '—' }}</p>
            <p class="mt-1 text-sm text-slate-500">{{ number_format($mastery['learners_tracked']) }} learners tracked</p>
        </a>
    </section>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.1fr_.9fr]">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">Users & roles</p>
                    <h2 class="mt-1 text-xl font-black">Account health</h2>
                </div>
                <a href="{{ route('admin.users.index') }}" class="text-sm font-black underline underline-offset-4">Manage users</a>
            </div>
            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                @foreach ($users['by_role'] as $role => $counts)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <span class="font-black">{{ $role === 'parents' ? 'Parents' : ucfirst($role) }}</span>
                            <span class="text-sm text-slate-500">{{ number_format($counts['active']) }} active</span>
                        </div>
                        <p class="mt-2 text-2xl font-black">{{ number_format($counts['total']) }}</p>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 flex flex-wrap gap-3 text-sm text-slate-600">
                <span>{{ number_format($users['new_this_month']) }} new this month</span>
                <span aria-hidden="true">·</span>
                <span>{{ number_format($users['inactive']) }} inactive</span>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">Operational alerts</p>
                    <h2 class="mt-1 text-xl font-black">Needs attention</h2>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black">{{ $adminDashboard['alerts']->count() }}</span>
            </div>
            <div class="mt-5 space-y-3">
                @forelse ($adminDashboard['alerts'] as $alert)
                    <a href="{{ route($alert['route']) }}" class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 p-4 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-200">
                        <div>
                            <p class="font-black">{{ $alert['title'] }}</p>
                            <p class="mt-1 text-xs uppercase tracking-wide text-slate-500">{{ str_replace('_', ' ', $alert['severity']) }}</p>
                        </div>
                        <span class="grid min-w-10 place-items-center rounded-lg bg-slate-950 px-2 py-2 text-sm font-black text-white">{{ number_format($alert['count']) }}</span>
                    </a>
                @empty
                    <p class="rounded-xl bg-slate-50 p-4 text-sm text-slate-600">No current operational alerts.</p>
                @endforelse
            </div>
        </section>
    </div>

    <section class="mt-6 grid gap-6 lg:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">Curriculum health</p>
            <h2 class="mt-1 text-lg font-black">Academic content</h2>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt>Boards / Academic classes</dt><dd class="font-black">{{ $curriculum['boards'] }} / {{ $curriculum['academic_classes'] }}</dd></div>
                <div class="flex justify-between gap-4"><dt>Active subjects</dt><dd class="font-black">{{ $curriculum['active_subjects'] }}</dd></div>
                <div class="flex justify-between gap-4"><dt>Active courses</dt><dd class="font-black">{{ $curriculum['active_courses'] }}</dd></div>
                <div class="flex justify-between gap-4"><dt>Active chapters</dt><dd class="font-black">{{ $curriculum['active_chapters'] }}</dd></div>
                <div class="flex justify-between gap-4"><dt>Published lessons</dt><dd class="font-black">{{ $curriculum['published_lessons'] }}</dd></div>
                <div class="flex justify-between gap-4"><dt>Draft lessons</dt><dd class="font-black">{{ $curriculum['draft_lessons'] }}</dd></div>
            </dl>
            <div class="mt-5 flex gap-3">
                <a href="{{ route('admin.curriculum.index') }}" class="text-sm font-black underline underline-offset-4">Structure</a>
                <a href="{{ route('admin.curriculum.content.index') }}" class="text-sm font-black underline underline-offset-4">Content</a>
            </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">Teaching operations</p>
            <h2 class="mt-1 text-lg font-black">Delivery health</h2>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt>Active classes</dt><dd class="font-black">{{ $teaching['active_classes'] }}</dd></div>
                <div class="flex justify-between gap-4"><dt>Unique enrolled learners</dt><dd class="font-black">{{ $teaching['learners_enrolled'] }}</dd></div>
                <div class="flex justify-between gap-4"><dt>Class enrollments</dt><dd class="font-black">{{ $teaching['class_enrollments'] }}</dd></div>
                <div class="flex justify-between gap-4"><dt>Course assignments</dt><dd class="font-black">{{ $teaching['course_assignments'] }}</dd></div>
                <div class="flex justify-between gap-4"><dt>Classes without learners</dt><dd class="font-black">{{ $teaching['classes_without_learners'] }}</dd></div>
                <div class="flex justify-between gap-4"><dt>Classes without courses</dt><dd class="font-black">{{ $teaching['classes_without_courses'] }}</dd></div>
            </dl>
            <a href="{{ route('admin.teaching.index') }}" class="mt-5 inline-block text-sm font-black underline underline-offset-4">Open teaching management</a>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">Assessment & mastery</p>
            <h2 class="mt-1 text-lg font-black">Learning outcomes</h2>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt>Published assessments</dt><dd class="font-black">{{ $assessments['published'] }}</dd></div>
                <div class="flex justify-between gap-4"><dt>Draft assessments</dt><dd class="font-black">{{ $assessments['draft'] }}</dd></div>
                <div class="flex justify-between gap-4"><dt>Pending reviews</dt><dd class="font-black">{{ $assessments['pending_review'] }}</dd></div>
                <div class="flex justify-between gap-4"><dt>Assessment average</dt><dd class="font-black">{{ $assessments['average_percentage'] !== null ? $assessments['average_percentage'].'%' : '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt>Needs Support</dt><dd class="font-black">{{ $mastery['distribution']['needs_support'] ?? 0 }}</dd></div>
                <div class="flex justify-between gap-4"><dt>Mastered</dt><dd class="font-black">{{ $mastery['distribution']['mastered'] ?? 0 }}</dd></div>
            </dl>
            <a href="{{ route('admin.progress.index') }}" class="mt-5 inline-block text-sm font-black underline underline-offset-4">Open progress & assessment</a>
        </article>
    </section>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.1fr_.9fr]">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">Recent platform activity</p>
            <h2 class="mt-1 text-xl font-black">Latest changes</h2>
            <div class="mt-5 divide-y divide-slate-200">
                @forelse ($adminDashboard['recent'] as $item)
                    @php
                        $url = $item['route_parameter'] !== null
                            ? route($item['route'], $item['route_parameter'])
                            : route($item['route']);
                    @endphp
                    <a href="{{ $url }}" class="flex items-start justify-between gap-4 py-4 first:pt-0 last:pb-0 focus:outline-none focus:ring-4 focus:ring-slate-200">
                        <div>
                            <p class="font-black">{{ $item['title'] }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $item['meta'] }}</p>
                        </div>
                        <time class="shrink-0 text-xs text-slate-500">{{ $item['timestamp']?->diffForHumans() }}</time>
                    </a>
                @empty
                    <p class="text-sm text-slate-600">No recent activity yet.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">Quick actions</p>
            <h2 class="mt-1 text-xl font-black">Manage SignGyaan</h2>
            <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                <a href="{{ route('admin.users.index') }}" class="rounded-xl border border-slate-200 px-4 py-3 font-black hover:bg-slate-50">Users & Roles</a>
                <a href="{{ route('admin.curriculum.index') }}" class="rounded-xl border border-slate-200 px-4 py-3 font-black hover:bg-slate-50">Academic Structure</a>
                <a href="{{ route('admin.curriculum.content.index') }}" class="rounded-xl border border-slate-200 px-4 py-3 font-black hover:bg-slate-50">Course Content</a>
                <a href="{{ route('admin.teaching.index') }}" class="rounded-xl border border-slate-200 px-4 py-3 font-black hover:bg-slate-50">Teaching Management</a>
                <a href="{{ route('admin.progress.index') }}" class="rounded-xl border border-slate-200 px-4 py-3 font-black hover:bg-slate-50">Progress & Assessment</a>
            </div>
        </section>
    </div>
@endsection
