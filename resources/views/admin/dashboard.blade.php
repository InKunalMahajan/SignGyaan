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

            <section class="mt-7 rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl sm:p-7" aria-labelledby="user-statistics-heading">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">User statistics</p>
                        <h2 id="user-statistics-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary">User Statistics Dashboard</h2>
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-sign-muted">Monitor account health, verification, recent registrations and login activity across the SignGyaan user base.</p>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Open User Management →</a>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <article class="rounded-2xl bg-sign-soft p-5">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-muted">Total users</p>
                        <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ number_format($userStats['total']) }}</p>
                        <p class="mt-2 text-xs text-sign-muted">{{ number_format($userStats['new_7_days']) }} joined in the last 7 days</p>
                    </article>
                    <article class="rounded-2xl bg-sign-soft p-5">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-muted">Active accounts</p>
                        <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ number_format($userStats['active']) }}</p>
                        <p class="mt-2 text-xs text-sign-muted">{{ $userStats['active_rate'] }}% of all accounts</p>
                    </article>
                    <article class="rounded-2xl bg-sign-soft p-5">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-muted">Verified email</p>
                        <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ number_format($userStats['verified']) }}</p>
                        <p class="mt-2 text-xs text-sign-muted">{{ $userStats['verification_rate'] }}% verification rate</p>
                    </article>
                    <article class="rounded-2xl bg-sign-soft p-5">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-muted">Recent logins</p>
                        <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ number_format($userStats['logged_in_30_days']) }}</p>
                        <p class="mt-2 text-xs text-sign-muted">Logged in during the last 30 days</p>
                    </article>
                </div>

                <div class="mt-6 grid gap-6 lg:grid-cols-2">
                    <section class="rounded-2xl border border-sign-border p-5" aria-labelledby="account-health-heading">
                        <h3 id="account-health-heading" class="font-heading text-xl font-semibold text-sign-primary">Account Health</h3>
                        <dl class="mt-4 space-y-3 text-sm">
                            @foreach ([
                                ['label' => 'Active', 'value' => $userStats['active']],
                                ['label' => 'Suspended', 'value' => $userStats['suspended']],
                                ['label' => 'Disabled', 'value' => $userStats['disabled']],
                                ['label' => 'Unverified email', 'value' => $userStats['unverified']],
                                ['label' => 'New in 30 days', 'value' => $userStats['new_30_days']],
                            ] as $row)
                                <div class="flex items-center justify-between gap-4 rounded-xl bg-sign-soft px-4 py-3">
                                    <dt class="font-medium text-sign-muted">{{ $row['label'] }}</dt>
                                    <dd class="font-semibold text-sign-primary">{{ number_format($row['value']) }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </section>

                    <section class="rounded-2xl border border-sign-border p-5" aria-labelledby="role-breakdown-heading">
                        <h3 id="role-breakdown-heading" class="font-heading text-xl font-semibold text-sign-primary">Role Breakdown</h3>
                        <dl class="mt-4 space-y-3 text-sm">
                            @foreach ([
                                ['label' => 'Learners', 'value' => $userStats['learners']],
                                ['label' => 'Teachers', 'value' => $userStats['teachers']],
                                ['label' => 'Administrators', 'value' => $userStats['administrators']],
                            ] as $row)
                                <div class="flex items-center justify-between gap-4 rounded-xl bg-sign-soft px-4 py-3">
                                    <dt class="font-medium text-sign-muted">{{ $row['label'] }}</dt>
                                    <dd class="font-semibold text-sign-primary">{{ number_format($row['value']) }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </section>
                </div>

                <section class="mt-6 overflow-hidden rounded-2xl border border-sign-border" aria-labelledby="recent-users-heading">
                    <div class="flex items-center justify-between gap-4 border-b border-sign-border bg-sign-soft px-5 py-4">
                        <div>
                            <h3 id="recent-users-heading" class="font-heading text-xl font-semibold text-sign-primary">Recent Users</h3>
                            <p class="mt-1 text-xs text-sign-muted">Latest registered accounts</p>
                        </div>
                        <a href="{{ route('admin.users.index', ['sort' => 'newest']) }}" class="text-sm font-semibold text-sign-primary hover:text-sign-cyan-dark">View all →</a>
                    </div>

                    @if ($recentUsers->isNotEmpty())
                        <div class="divide-y divide-sign-border">
                            @foreach ($recentUsers as $recentUser)
                                <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-sign-primary">{{ $recentUser->name }}</p>
                                        <p class="mt-1 break-all text-xs text-sign-muted">{{ $recentUser->email }}</p>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 text-xs">
                                        <span class="rounded-full bg-sign-light px-2.5 py-1 font-semibold text-sign-primary">{{ ucfirst(str_replace('_', ' ', $recentUser->role)) }}</span>
                                        <span class="rounded-full bg-gray-100 px-2.5 py-1 font-semibold text-sign-muted">{{ ucfirst($recentUser->status) }}</span>
                                        <span class="text-sign-muted">Joined {{ $recentUser->created_at?->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="px-5 py-8 text-center text-sm text-sign-muted">No user accounts have been created yet.</p>
                    @endif
                </section>
            </section>

            <section class="mt-7 rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl sm:p-7" aria-labelledby="academic-statistics-heading">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Academic statistics</p>
                        <h2 id="academic-statistics-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary">Academic Statistics Dashboard</h2>
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-sign-muted">Track the size, publishing status and ISL coverage of the learning catalogue.</p>
                    </div>
                    <a href="{{ route('admin.subjects.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Open Academic Content →</a>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                    @foreach ([
                        ['label' => 'Subjects', 'value' => $academicStats['subjects'], 'rate' => $academicStats['subjects_published_rate']],
                        ['label' => 'Courses', 'value' => $academicStats['courses'], 'rate' => $academicStats['courses_published_rate']],
                        ['label' => 'Units', 'value' => $academicStats['units'], 'rate' => $academicStats['units_published_rate']],
                        ['label' => 'Lessons', 'value' => $academicStats['lessons'], 'rate' => $academicStats['lessons_published_rate']],
                        ['label' => 'Assessments', 'value' => $academicStats['assessments'], 'rate' => $academicStats['assessments_published_rate']],
                    ] as $stat)
                        <article class="rounded-2xl bg-sign-soft p-5">
                            <p class="text-xs font-semibold uppercase tracking-wider text-sign-muted">{{ $stat['label'] }}</p>
                            <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ number_format($stat['value']) }}</p>
                            <p class="mt-2 text-xs text-sign-muted">{{ $stat['rate'] }}% published</p>
                        </article>
                    @endforeach
                </div>

                <div class="mt-6 grid gap-6 lg:grid-cols-2">
                    <section class="rounded-2xl border border-sign-border p-5" aria-labelledby="publishing-health-heading">
                        <h3 id="publishing-health-heading" class="font-heading text-xl font-semibold text-sign-primary">Publishing Health</h3>
                        <dl class="mt-4 space-y-3 text-sm">
                            @foreach ([
                                ['label' => 'Published subjects', 'value' => $academicStats['subjects_published'], 'draft' => $academicStats['subjects_draft']],
                                ['label' => 'Published courses', 'value' => $academicStats['courses_published'], 'draft' => $academicStats['courses_draft']],
                                ['label' => 'Published units', 'value' => $academicStats['units_published'], 'draft' => $academicStats['units_draft']],
                                ['label' => 'Published lessons', 'value' => $academicStats['lessons_published'], 'draft' => $academicStats['lessons_draft']],
                                ['label' => 'Published assessments', 'value' => $academicStats['assessments_published'], 'draft' => $academicStats['assessments_draft']],
                            ] as $row)
                                <div class="flex items-center justify-between gap-4 rounded-xl bg-sign-soft px-4 py-3">
                                    <dt class="font-medium text-sign-muted">{{ $row['label'] }}</dt>
                                    <dd class="text-right"><span class="font-semibold text-sign-primary">{{ number_format($row['value']) }}</span><span class="ml-2 text-xs text-sign-muted">{{ number_format($row['draft']) }} draft</span></dd>
                                </div>
                            @endforeach
                        </dl>
                    </section>

                    <section class="rounded-2xl border border-sign-border p-5" aria-labelledby="academic-coverage-heading">
                        <h3 id="academic-coverage-heading" class="font-heading text-xl font-semibold text-sign-primary">Academic Coverage</h3>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-xl bg-sign-soft p-4">
                                <p class="text-xs font-semibold text-sign-muted">ISL-enabled lessons</p>
                                <p class="mt-2 font-heading text-2xl font-semibold text-sign-primary">{{ number_format($academicStats['lessons_with_isl']) }}</p>
                                <p class="mt-1 text-xs text-sign-muted">{{ $academicStats['isl_lesson_rate'] }}% of all lessons</p>
                            </div>
                            <div class="rounded-xl bg-sign-soft p-4">
                                <p class="text-xs font-semibold text-sign-muted">Featured courses</p>
                                <p class="mt-2 font-heading text-2xl font-semibold text-sign-primary">{{ number_format($academicStats['courses_featured']) }}</p>
                                <p class="mt-1 text-xs text-sign-muted">Highlighted in the catalogue</p>
                            </div>
                        </div>
                        <nav class="mt-4 grid gap-2 sm:grid-cols-2" aria-label="Academic management links">
                            <a href="{{ route('admin.courses.index') }}" class="flex min-h-11 items-center justify-between rounded-xl bg-sign-soft px-4 py-3 text-sm font-semibold text-sign-primary hover:bg-sign-light"><span>Manage Courses</span><span aria-hidden="true">→</span></a>
                            <a href="{{ route('admin.lessons.index') }}" class="flex min-h-11 items-center justify-between rounded-xl bg-sign-soft px-4 py-3 text-sm font-semibold text-sign-primary hover:bg-sign-light"><span>Manage Lessons</span><span aria-hidden="true">→</span></a>
                        </nav>
                    </section>
                </div>

                <section class="mt-6 overflow-hidden rounded-2xl border border-sign-border" aria-labelledby="recent-courses-heading">
                    <div class="flex items-center justify-between gap-4 border-b border-sign-border bg-sign-soft px-5 py-4">
                        <div>
                            <h3 id="recent-courses-heading" class="font-heading text-xl font-semibold text-sign-primary">Recently Updated Courses</h3>
                            <p class="mt-1 text-xs text-sign-muted">Latest academic catalogue changes</p>
                        </div>
                        <a href="{{ route('admin.courses.index') }}" class="text-sm font-semibold text-sign-primary hover:text-sign-cyan-dark">View all →</a>
                    </div>

                    @if ($recentCourses->isNotEmpty())
                        <div class="divide-y divide-sign-border">
                            @foreach ($recentCourses as $recentCourse)
                                <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-sign-primary">{{ $recentCourse->title }}</p>
                                        <p class="mt-1 text-xs text-sign-muted">{{ $recentCourse->subject?->name ?? 'No subject' }} · Updated {{ $recentCourse->updated_at?->diffForHumans() }}</p>
                                    </div>
                                    <div class="flex flex-wrap gap-2 text-xs">
                                        <span class="rounded-full px-2.5 py-1 font-semibold {{ $recentCourse->is_published ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-sign-muted' }}">{{ $recentCourse->is_published ? 'Published' : 'Draft' }}</span>
                                        @if ($recentCourse->is_featured)<span class="rounded-full bg-sign-light px-2.5 py-1 font-semibold text-sign-primary">Featured</span>@endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="px-5 py-8 text-center text-sm text-sign-muted">No courses have been created yet.</p>
                    @endif
                </section>
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
