@extends('layouts.app')

@section('title', 'Admin Dashboard - SignGyaan')
@section('description', 'SignGyaan administration dashboard for learning content and platform management.')

@section('content')
    <section class="border-b border-sign-border bg-sign-dark py-8 text-white sm:py-10 lg:py-12">
        <x-container>
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem] lg:items-center">
                <div>
                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-wider text-sign-cyan">
                        <span>SignGyaan Admin</span>
                        <span aria-hidden="true">•</span>
                        <span>Content Management</span>
                    </div>
                    <h1 class="mt-3 font-heading text-3xl font-semibold tracking-tight sm:text-4xl lg:text-5xl">
                        Admin Dashboard
                    </h1>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-white/75 sm:text-base">
                        Manage SignGyaan learning content, learner accounts and platform resources from one protected workspace.
                    </p>
                </div>

                <div class="rounded-2xl border border-white/15 bg-white/10 p-5 sm:rounded-3xl">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan">Signed in as</p>
                    <p class="mt-2 break-words font-semibold">{{ auth()->user()->name }}</p>
                    <p class="mt-1 break-all text-sm text-white/70">{{ auth()->user()->email }}</p>
                    <span class="mt-4 inline-flex rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-sign-primary">Administrator</span>
                </div>
            </div>
        </x-container>
    </section>

    <section class="bg-sign-soft py-8 sm:py-12 lg:py-16">
        <x-container>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-6">
                    <p class="text-sm font-semibold text-sign-muted">Learners</p>
                    <p class="mt-3 font-heading text-3xl font-semibold text-sign-primary">{{ $learnerCount }}</p>
                    <p class="mt-1 text-xs leading-5 text-sign-muted">Registered learner accounts.</p>
                </article>

                <article class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-6">
                    <p class="text-sm font-semibold text-sign-muted">Administrators</p>
                    <p class="mt-3 font-heading text-3xl font-semibold text-sign-primary">{{ $adminCount }}</p>
                    <p class="mt-1 text-xs leading-5 text-sign-muted">Accounts with admin access.</p>
                </article>

                <article class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-6">
                    <p class="text-sm font-semibold text-sign-muted">Tracked Courses</p>
                    <p class="mt-3 font-heading text-3xl font-semibold text-sign-primary">{{ $trackedCourses }}</p>
                    <p class="mt-1 text-xs leading-5 text-sign-muted">Learner course-progress records.</p>
                </article>

                <article class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-6">
                    <p class="text-sm font-semibold text-sign-muted">Completed Courses</p>
                    <p class="mt-3 font-heading text-3xl font-semibold text-sign-primary">{{ $completedCourses }}</p>
                    <p class="mt-1 text-xs leading-5 text-sign-muted">Courses completed by learners.</p>
                </article>
            </div>

            <div class="mt-8 grid gap-6 xl:grid-cols-[minmax(0,1fr)_20rem] xl:items-start">
                <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-8" aria-labelledby="admin-content-heading">
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Content workspace</p>
                    <h2 id="admin-content-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Learning Content Management</h2>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-sign-muted">
                        The admin foundation is ready. The next steps will connect real Subjects, Courses, Units and Lessons to this workspace.
                    </p>

                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        @foreach ([
                            ['title' => 'Subjects', 'text' => 'Create and organise the main learning subject categories.', 'step' => 'Step 7C'],
                            ['title' => 'Courses', 'text' => 'Manage courses inside each subject and control publishing.', 'step' => 'Step 7D'],
                            ['title' => 'Units', 'text' => 'Structure courses into ordered learning units.', 'step' => 'Step 7E'],
                            ['title' => 'Lessons', 'text' => 'Manage lesson content, ISL video, notes and practice.', 'step' => 'Step 7F'],
                        ] as $item)
                            <article class="rounded-2xl border border-sign-border bg-sign-soft p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <h3 class="font-heading text-xl font-semibold text-sign-primary">{{ $item['title'] }}</h3>
                                    <span class="shrink-0 rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-sign-cyan-dark">{{ $item['step'] }}</span>
                                </div>
                                <p class="mt-3 text-sm leading-6 text-sign-muted">{{ $item['text'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>

                <aside class="space-y-5 xl:sticky xl:top-24" aria-label="Admin shortcuts">
                    <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Admin foundation</p>
                        <dl class="mt-4 space-y-3 text-sm">
                            <div class="flex items-center justify-between gap-3 rounded-xl bg-sign-soft px-4 py-3">
                                <dt class="text-sign-muted">Authentication</dt>
                                <dd class="font-semibold text-sign-primary">Protected</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3 rounded-xl bg-sign-soft px-4 py-3">
                                <dt class="text-sign-muted">Role check</dt>
                                <dd class="font-semibold text-sign-primary">Admin</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3 rounded-xl bg-sign-soft px-4 py-3">
                                <dt class="text-sign-muted">Admin URL</dt>
                                <dd class="font-semibold text-sign-primary">/admin</dd>
                            </div>
                        </dl>
                    </div>

                    <a href="{{ route('dashboard') }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-sign-primary bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-light">
                        Back to Learner Dashboard
                    </a>
                </aside>
            </div>
        </x-container>
    </section>
@endsection
