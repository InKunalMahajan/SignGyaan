@extends('layouts.admin')

@section('title', 'Admin Dashboard - SignGyaan')
@section('description', 'SignGyaan administration dashboard for learning content and platform management.')
@section('page-title', 'Dashboard')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                <div class="max-w-3xl">
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Administration workspace</p>
                    <h2 class="mt-2 font-heading text-3xl font-semibold tracking-tight text-sign-primary sm:text-4xl">
                        Welcome to SignGyaan Admin
                    </h2>
                    <p class="mt-3 text-sm leading-7 text-sign-muted sm:text-base">
                        Manage learning content, learner accounts and platform resources from one protected workspace.
                    </p>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row">
                    <a href="{{ route('home') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-primary bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-light">
                        View Website
                    </a>
                    <a href="{{ route('dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-4 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">
                        Learner Dashboard
                    </a>
                </div>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl sm:p-6">
                    <div class="flex items-center justify-between gap-4">
                        <p class="text-sm font-semibold text-sign-muted">Learners</p>
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sign-soft text-sign-primary" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72M12 12.75a5.995 5.995 0 0 0-5.058 2.772A3 3 0 0 0 2.26 18.24 11.944 11.944 0 0 0 12 21c2.17 0 4.207-.576 5.963-1.584A6 6 0 0 0 12 12.75ZM15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                        </span>
                    </div>
                    <p class="mt-4 font-heading text-3xl font-semibold text-sign-primary">{{ $learnerCount }}</p>
                    <p class="mt-1 text-xs leading-5 text-sign-muted">Registered learner accounts.</p>
                </article>

                <article class="rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl sm:p-6">
                    <div class="flex items-center justify-between gap-4">
                        <p class="text-sm font-semibold text-sign-muted">Administrators</p>
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sign-soft text-sign-primary" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m6 2.25c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9Z" /></svg>
                        </span>
                    </div>
                    <p class="mt-4 font-heading text-3xl font-semibold text-sign-primary">{{ $adminCount }}</p>
                    <p class="mt-1 text-xs leading-5 text-sign-muted">Accounts with admin access.</p>
                </article>

                <article class="rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl sm:p-6">
                    <div class="flex items-center justify-between gap-4">
                        <p class="text-sm font-semibold text-sign-muted">Tracked Courses</p>
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sign-soft text-sign-primary" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25A8.966 8.966 0 0 1 18 3.75c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>
                        </span>
                    </div>
                    <p class="mt-4 font-heading text-3xl font-semibold text-sign-primary">{{ $trackedCourses }}</p>
                    <p class="mt-1 text-xs leading-5 text-sign-muted">Learner course-progress records.</p>
                </article>

                <article class="rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl sm:p-6">
                    <div class="flex items-center justify-between gap-4">
                        <p class="text-sm font-semibold text-sign-muted">Completed Courses</p>
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sign-soft text-sign-primary" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        </span>
                    </div>
                    <p class="mt-4 font-heading text-3xl font-semibold text-sign-primary">{{ $completedCourses }}</p>
                    <p class="mt-1 text-xs leading-5 text-sign-muted">Courses completed by learners.</p>
                </article>
            </div>

            <div class="mt-8 grid gap-6 xl:grid-cols-[minmax(0,1fr)_21rem] xl:items-start">
                <section class="rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl sm:p-8" aria-labelledby="admin-content-heading">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Content management</p>
                            <h2 id="admin-content-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Manage SignGyaan Content</h2>
                        </div>
                        <span class="w-fit rounded-full bg-sign-soft px-3 py-1.5 text-xs font-semibold text-sign-primary">Task 7</span>
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2 2xl:grid-cols-3">
                        @foreach ([
                            ['title' => 'Subjects', 'text' => 'Create and organise learning subject categories.', 'route' => 'admin.subjects.index', 'step' => '7C'],
                            ['title' => 'Courses', 'text' => 'Manage courses, levels and publishing.', 'route' => 'admin.courses.index', 'step' => '7D'],
                            ['title' => 'Units', 'text' => 'Build ordered units inside each course.', 'route' => 'admin.units.index', 'step' => '7E'],
                            ['title' => 'Lessons', 'text' => 'Manage ISL lessons, notes and examples.', 'route' => 'admin.lessons.index', 'step' => '7F'],
                            ['title' => 'Practice & Resources', 'text' => 'Add practice and supporting resources.', 'route' => 'admin.practice.index', 'step' => '7G'],
                            ['title' => 'Media', 'text' => 'Organise learning images and videos.', 'route' => 'admin.media.index', 'step' => '7H'],
                        ] as $item)
                            <a href="{{ route($item['route']) }}" class="group rounded-2xl border border-sign-border bg-sign-soft p-5 transition hover:-translate-y-0.5 hover:border-sign-cyan hover:bg-white hover:shadow-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <h3 class="font-heading text-xl font-semibold text-sign-primary group-hover:text-sign-cyan-dark">{{ $item['title'] }}</h3>
                                    <span class="shrink-0 rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-sign-cyan-dark">{{ $item['step'] }}</span>
                                </div>
                                <p class="mt-3 text-sm leading-6 text-sign-muted">{{ $item['text'] }}</p>
                                <span class="mt-4 inline-flex text-sm font-semibold text-sign-primary">Open workspace →</span>
                            </a>
                        @endforeach
                    </div>
                </section>

                <aside class="space-y-5 xl:sticky xl:top-24" aria-label="Admin shortcuts">
                    <div class="rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Admin account</p>
                        <p class="mt-3 break-words font-semibold text-sign-primary">{{ auth()->user()->name }}</p>
                        <p class="mt-1 break-all text-sm text-sign-muted">{{ auth()->user()->email }}</p>
                        <span class="mt-4 inline-flex rounded-full bg-sign-light px-3 py-1.5 text-xs font-semibold text-sign-primary">Administrator</span>
                    </div>

                    <div class="rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Quick links</p>
                        <nav class="mt-4 space-y-2" aria-label="Admin quick links">
                            <a href="{{ route('admin.users.index') }}" class="flex min-h-11 items-center justify-between rounded-xl bg-sign-soft px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-light"><span>Users</span><span aria-hidden="true">→</span></a>
                            <a href="{{ route('admin.settings.index') }}" class="flex min-h-11 items-center justify-between rounded-xl bg-sign-soft px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-light"><span>Settings</span><span aria-hidden="true">→</span></a>
                            <a href="{{ route('profile') }}" class="flex min-h-11 items-center justify-between rounded-xl bg-sign-soft px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-light"><span>Profile</span><span aria-hidden="true">→</span></a>
                        </nav>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
