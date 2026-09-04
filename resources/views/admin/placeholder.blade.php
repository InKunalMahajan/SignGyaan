@extends('layouts.admin')

@section('title', $title . ' - SignGyaan Admin')
@section('description', $description)
@section('page-title', $title)

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-7xl">
            <nav class="flex flex-wrap items-center gap-2 text-xs text-sign-muted sm:text-sm" aria-label="Breadcrumb">
                <a href="{{ route('admin.dashboard') }}" class="font-semibold text-sign-primary transition hover:text-sign-cyan-dark">Admin</a>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-sign-text">{{ $title }}</span>
            </nav>

            <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1fr)_20rem] xl:items-start">
                <div class="rounded-2xl border border-sign-border bg-white p-6 shadow-sm sm:rounded-3xl sm:p-8">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sign-light text-sign-primary" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25A8.966 8.966 0 0 1 18 3.75c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>

                    <p class="mt-6 text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Admin workspace</p>
                    <h2 class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">{{ $title }}</h2>
                    <p class="mt-3 max-w-2xl text-sm leading-7 text-sign-muted sm:text-base">{{ $description }}</p>

                    <div class="mt-7 rounded-2xl border border-dashed border-sign-border bg-sign-soft p-5 sm:p-6">
                        <p class="font-semibold text-sign-primary">Management tools are ready for the next step.</p>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">
                            The admin layout, navigation and protected route are already connected. CRUD tools and database-backed management will be added in the matching Task 7 step.
                        </p>
                    </div>
                </div>

                <aside class="space-y-4 xl:sticky xl:top-24" aria-label="Section status">
                    <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Section status</p>
                        <div class="mt-4 flex items-center justify-between gap-3 rounded-xl bg-sign-soft px-4 py-3 text-sm">
                            <span class="text-sign-muted">Admin access</span>
                            <span class="font-semibold text-sign-primary">Protected</span>
                        </div>
                        <div class="mt-2 flex items-center justify-between gap-3 rounded-xl bg-sign-soft px-4 py-3 text-sm">
                            <span class="text-sign-muted">Navigation</span>
                            <span class="font-semibold text-sign-primary">Connected</span>
                        </div>
                    </div>

                    <a href="{{ route('admin.dashboard') }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-sign-primary bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-light">
                        Back to Admin Dashboard
                    </a>
                </aside>
            </div>
        </div>
    </section>
@endsection
