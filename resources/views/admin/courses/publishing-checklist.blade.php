@extends('layouts.admin')

@section('title', 'Publishing Checklist - ' . $course->title . ' - SignGyaan Admin')
@section('page-title', 'Publishing Checklist')
@section('description', 'Review course readiness before publishing SignGyaan learning content.')

@section('content')
<section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
    <div class="mx-auto max-w-6xl">
        <nav class="flex flex-wrap items-center gap-2 text-xs text-sign-muted sm:text-sm" aria-label="Breadcrumb">
            <a href="{{ route('admin.courses.index') }}" class="transition hover:text-sign-primary">Courses</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('admin.courses.builder', $course) }}" class="transition hover:text-sign-primary">{{ $course->title }}</a>
            <span aria-hidden="true">/</span>
            <span class="font-semibold text-sign-primary">Publishing Checklist</span>
        </nav>

        <div class="mt-5 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Pre-publish review</p>
                <h1 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">{{ $course->title }}</h1>
                <p class="mt-3 text-sm leading-7 text-sign-muted sm:text-base">Check required publishing blockers first, then review recommended accessibility and learner-experience improvements.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.courses.preview', $course) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-2.5 text-sm font-semibold text-sign-primary">Preview Course</a>
                <a href="{{ route('admin.courses.builder', $course) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-4 py-2.5 text-sm font-semibold text-white">Back to Builder</a>
            </div>
        </div>

        <section class="mt-7 rounded-2xl border p-5 sm:rounded-3xl sm:p-7 {{ $checklist['ready'] ? 'border-sign-cyan bg-sign-light/40' : 'border-red-200 bg-red-50' }}" aria-labelledby="readiness-heading">
            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider {{ $checklist['ready'] ? 'text-sign-cyan-dark' : 'text-red-700' }}">Publishing readiness</p>
                    <h2 id="readiness-heading" class="mt-2 font-heading text-2xl font-semibold {{ $checklist['ready'] ? 'text-sign-primary' : 'text-red-900' }}">
                        {{ $checklist['ready'] ? 'Required checks are complete' : count($checklist['blockers']) . ' required check' . (count($checklist['blockers']) === 1 ? '' : 's') . ' need attention' }}
                    </h2>
                    <p class="mt-2 text-sm leading-6 {{ $checklist['ready'] ? 'text-sign-muted' : 'text-red-800' }}">
                        {{ $checklist['ready'] ? 'This course has no checklist blockers. Review the recommendations before publishing.' : 'Resolve the required items below before treating this course as ready to publish.' }}
                    </p>
                </div>

                <div class="grid grid-cols-3 gap-2 text-center sm:gap-3">
                    <div class="rounded-2xl bg-white px-4 py-3 shadow-sm"><p class="font-heading text-2xl font-semibold text-sign-primary">{{ $checklist['passed_count'] }}/{{ $checklist['total_count'] }}</p><p class="mt-1 text-[11px] font-semibold uppercase tracking-wide text-sign-muted">Passed</p></div>
                    <div class="rounded-2xl bg-white px-4 py-3 shadow-sm"><p class="font-heading text-2xl font-semibold text-red-700">{{ count($checklist['blockers']) }}</p><p class="mt-1 text-[11px] font-semibold uppercase tracking-wide text-sign-muted">Blockers</p></div>
                    <div class="rounded-2xl bg-white px-4 py-3 shadow-sm"><p class="font-heading text-2xl font-semibold text-sign-cyan-dark">{{ $checklist['warning_count'] }}</p><p class="mt-1 text-[11px] font-semibold uppercase tracking-wide text-sign-muted">Suggestions</p></div>
                </div>
            </div>
        </section>

        <div class="mt-8 grid gap-8 lg:grid-cols-2 lg:items-start">
            <section aria-labelledby="required-heading">
                <div class="flex items-center justify-between gap-3">
                    <div><p class="text-xs font-semibold uppercase tracking-wider text-red-700">Required</p><h2 id="required-heading" class="mt-1 font-heading text-2xl font-semibold text-sign-primary">Publishing blockers</h2></div>
                    <span class="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-sign-primary ring-1 ring-sign-border">{{ $checklist['required_passed'] }}/{{ $checklist['required_total'] }} complete</span>
                </div>

                <div class="mt-4 space-y-3">
                    @foreach ($checklist['required'] as $check)
                        <article class="rounded-2xl border bg-white p-4 sm:p-5 {{ $check['passed'] ? 'border-sign-border' : 'border-red-200' }}">
                            <div class="flex gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-sm font-bold {{ $check['passed'] ? 'bg-sign-light text-sign-primary' : 'bg-red-100 text-red-700' }}" aria-hidden="true">{{ $check['passed'] ? '✓' : '!' }}</span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2"><h3 class="font-semibold text-sign-primary">{{ $check['title'] }}</h3><span class="rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide {{ $check['passed'] ? 'bg-sign-light text-sign-primary' : 'bg-red-100 text-red-700' }}">{{ $check['passed'] ? 'Passed' : 'Required' }}</span></div>
                                    <p class="mt-1 text-sm leading-6 text-sign-muted">{{ $check['description'] }}</p>
                                    @if ($check['detail'])<p class="mt-2 text-sm font-semibold {{ $check['passed'] ? 'text-sign-muted' : 'text-red-700' }}">{{ $check['detail'] }}</p>@endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section aria-labelledby="recommended-heading">
                <div class="flex items-center justify-between gap-3">
                    <div><p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Recommended</p><h2 id="recommended-heading" class="mt-1 font-heading text-2xl font-semibold text-sign-primary">Accessibility & learner experience</h2></div>
                </div>

                <div class="mt-4 space-y-3">
                    @foreach ($checklist['recommended'] as $check)
                        <article class="rounded-2xl border border-sign-border bg-white p-4 sm:p-5">
                            <div class="flex gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-sm font-bold {{ $check['passed'] ? 'bg-sign-light text-sign-primary' : 'bg-sign-soft text-sign-cyan-dark' }}" aria-hidden="true">{{ $check['passed'] ? '✓' : 'i' }}</span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2"><h3 class="font-semibold text-sign-primary">{{ $check['title'] }}</h3><span class="rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide {{ $check['passed'] ? 'bg-sign-light text-sign-primary' : 'bg-sign-soft text-sign-muted' }}">{{ $check['passed'] ? 'Complete' : 'Recommended' }}</span></div>
                                    <p class="mt-1 text-sm leading-6 text-sign-muted">{{ $check['description'] }}</p>
                                    @if ($check['detail'])<p class="mt-2 text-sm font-semibold text-sign-cyan-dark">{{ $check['detail'] }}</p>@endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        </div>

        <section class="mt-8 rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div><h2 class="font-heading text-xl font-semibold text-sign-primary">What happens next?</h2><p class="mt-1 text-sm leading-6 text-sign-muted">This checklist does not publish anything by itself. It is a readiness review before the publishing controls in the next workflow step.</p></div>
                <a href="{{ route('admin.courses.preview', $course) }}" class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-xl border border-sign-primary bg-white px-5 py-3 text-sm font-semibold text-sign-primary">Review Draft Preview</a>
            </div>
        </section>
    </div>
</section>
@endsection
