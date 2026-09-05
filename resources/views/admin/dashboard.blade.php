@extends('layouts.admin')

@section('title', 'Admin Dashboard - SignGyaan')
@section('description', 'SignGyaan administration dashboard for platform, users, learning content and management workflows.')
@section('page-title', 'Dashboard')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10" data-admin-dashboard>
        <div class="mx-auto max-w-7xl">
            <div class="rounded-3xl bg-sign-primary px-5 py-7 text-white shadow-sm sm:px-8 sm:py-9">
                <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
                    <div class="max-w-3xl">
                        <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan">Management system</p>
                        <h2 class="mt-2 font-heading text-3xl font-semibold tracking-tight sm:text-4xl">SignGyaan Admin Dashboard</h2>
                        <p class="mt-3 text-sm leading-7 text-white/75 sm:text-base">A central workspace for managing users, teachers, learners, learning content, assessments and platform operations.</p>
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <a href="{{ route('admin.users.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-light">Manage Users</a>
                        <a href="{{ route('home') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-white/25 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/10">View Website</a>
                    </div>
                </div>
            </div>

            <section class="mt-7" aria-labelledby="platform-overview-heading">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Overview</p>
                        <h2 id="platform-overview-heading" class="mt-1 font-heading text-2xl font-semibold text-sign-primary">Platform Overview</h2>
                    </div>
                    <p class="text-xs text-sign-muted">Live totals from the SignGyaan database</p>
                </div>

                <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ([
                        ['label' => 'Learners', 'value' => $userStats['learners'], 'text' => 'Registered learner accounts'],
                        ['label' => 'Teachers', 'value' => $userStats['teachers'], 'text' => 'Teacher accounts'],
                        ['label' => 'Courses', 'value' => $contentStats['courses'], 'text' => 'Courses in the catalogue'],
                        ['label' => 'Lessons', 'value' => $contentStats['lessons'], 'text' => 'Learning lessons'],
                    ] as $stat)
                        <article class="rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl">
                            <p class="text-sm font-semibold text-sign-muted">{{ $stat['label'] }}</p>
                            <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ number_format($stat['value']) }}</p>
                            <p class="mt-2 text-xs leading-5 text-sign-muted">{{ $stat['text'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <div class="mt-7 grid gap-6 xl:grid-cols-[minmax(0,1.4fr)_minmax(18rem,.6fr)]">
                <section class="rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl sm:p-7" aria-labelledby="management-heading">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Management</p>
                        <h2 id="management-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary">Management Workspaces</h2>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">Open the main areas used to manage the SignGyaan platform.</p>
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        @foreach ($managementAreas as $area)
                            <a href="{{ route($area['route']) }}" class="group rounded-2xl border border-sign-border bg-sign-soft p-5 transition hover:-translate-y-0.5 hover:border-sign-cyan hover:bg-white hover:shadow-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <h3 class="font-heading text-xl font-semibold text-sign-primary group-hover:text-sign-cyan-dark">{{ $area['label'] }}</h3>
                                    <span class="text-lg text-sign-cyan-dark" aria-hidden="true">→</span>
                                </div>
                                <p class="mt-2 text-sm leading-6 text-sign-muted">{{ $area['description'] }}</p>
                            </a>
                        @endforeach
                    </div>
                </section>

                <div class="space-y-6">
                    <section class="rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl sm:p-6" aria-labelledby="system-summary-heading">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">System summary</p>
                        <h2 id="system-summary-heading" class="mt-2 font-heading text-xl font-semibold text-sign-primary">Current Totals</h2>

                        <dl class="mt-5 space-y-3 text-sm">
                            @foreach ([
                                ['label' => 'Total users', 'value' => $userStats['total']],
                                ['label' => 'Active accounts', 'value' => $userStats['active']],
                                ['label' => 'Administrators', 'value' => $userStats['administrators']],
                                ['label' => 'Subjects', 'value' => $contentStats['subjects']],
                                ['label' => 'Assessments', 'value' => $contentStats['assessments']],
                            ] as $row)
                                <div class="flex items-center justify-between gap-4 rounded-xl bg-sign-soft px-4 py-3">
                                    <dt class="font-medium text-sign-muted">{{ $row['label'] }}</dt>
                                    <dd class="font-semibold text-sign-primary">{{ number_format($row['value']) }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </section>

                    <section class="rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl sm:p-6" aria-labelledby="learning-summary-heading">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Learning system</p>
                        <h2 id="learning-summary-heading" class="mt-2 font-heading text-xl font-semibold text-sign-primary">Progress Snapshot</h2>
                        <div class="mt-5 grid grid-cols-2 gap-3">
                            <div class="rounded-xl bg-sign-soft p-4">
                                <p class="text-xs font-semibold text-sign-muted">Tracked courses</p>
                                <p class="mt-2 font-heading text-2xl font-semibold text-sign-primary">{{ number_format($learningStats['tracked_courses']) }}</p>
                            </div>
                            <div class="rounded-xl bg-sign-soft p-4">
                                <p class="text-xs font-semibold text-sign-muted">Completed</p>
                                <p class="mt-2 font-heading text-2xl font-semibold text-sign-primary">{{ number_format($learningStats['completed_courses']) }}</p>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl sm:p-6" aria-labelledby="quick-links-heading">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Quick access</p>
                        <h2 id="quick-links-heading" class="mt-2 font-heading text-xl font-semibold text-sign-primary">Common Actions</h2>
                        <nav class="mt-4 space-y-2" aria-label="Admin quick actions">
                            <a href="{{ route('admin.courses.create') }}" class="flex min-h-11 items-center justify-between rounded-xl bg-sign-soft px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-light"><span>Add Course</span><span aria-hidden="true">→</span></a>
                            <a href="{{ route('admin.lessons.create') }}" class="flex min-h-11 items-center justify-between rounded-xl bg-sign-soft px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-light"><span>Add Lesson</span><span aria-hidden="true">→</span></a>
                            <a href="{{ route('admin.assessments.create') }}" class="flex min-h-11 items-center justify-between rounded-xl bg-sign-soft px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-light"><span>Add Assessment</span><span aria-hidden="true">→</span></a>
                        </nav>
                    </section>
                </div>
            </div>
        </div>
    </section>
@endsection
