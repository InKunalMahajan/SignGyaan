@extends('layouts.admin')

@section('title', 'Publishing Checklist - ' . $course->title . ' - SignGyaan Admin')
@section('page-title', 'Publishing Checklist')
@section('description', 'Review course readiness, publishing status and bulk publishing controls.')

@section('content')
<section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
    <div class="mx-auto max-w-6xl">
        <nav class="flex flex-wrap items-center gap-2 text-xs text-sign-muted sm:text-sm" aria-label="Breadcrumb">
            <a href="{{ route('admin.courses.index') }}" class="transition hover:text-sign-primary">Courses</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('admin.courses.builder', $course) }}" class="transition hover:text-sign-primary">{{ $course->title }}</a>
            <span aria-hidden="true">/</span>
            <span class="font-semibold text-sign-primary">Publishing</span>
        </nav>

        <div class="mt-5 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Publishing workflow</p>
                <h1 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">{{ $course->title }}</h1>
                <p class="mt-3 text-sm leading-7 text-sign-muted sm:text-base">Review readiness, see the current course status, then publish or move the complete managed course structure back to draft.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.courses.preview', $course) }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-2.5 text-sm font-semibold text-sign-primary">Preview Course</a>
                <a href="{{ route('admin.courses.builder', $course) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-4 py-2.5 text-sm font-semibold text-white">Back to Builder</a>
            </div>
        </div>

        @if ($errors->has('publishing'))
            <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-800" role="alert" data-error-summary>{{ $errors->first('publishing') }}</div>
        @endif

        <section class="mt-7 rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="course-status-heading">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="max-w-2xl">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Course status</p>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $publishingStatus['key'] === 'published' ? 'bg-sign-light text-sign-primary' : ($publishingStatus['key'] === 'partial' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-sign-muted') }}">{{ $publishingStatus['label'] }}</span>
                    </div>
                    <h2 id="course-status-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary">{{ $publishingStatus['published'] }} of {{ $publishingStatus['total'] }} managed items published</h2>
                    <p class="mt-2 text-sm leading-6 text-sign-muted">{{ $publishingStatus['description'] }}</p>
                    <div class="mt-4 h-2 overflow-hidden rounded-full bg-sign-soft" aria-label="Publishing progress {{ $publishingStatus['percentage'] }} percent">
                        <div class="h-full rounded-full bg-sign-primary" style="width: {{ $publishingStatus['percentage'] }}%"></div>
                    </div>
                    <p class="mt-2 text-xs font-semibold text-sign-muted">{{ $publishingStatus['percentage'] }}% published · {{ $publishingStatus['draft'] }} draft</p>
                </div>

                <div class="flex w-full flex-col gap-2 sm:w-auto sm:min-w-64">
                    <form method="POST" action="{{ route('admin.courses.publish-all', $course) }}" onsubmit="return confirm('Publish this course and all managed units, lessons, content blocks, activities, assessments, questions and course vocabulary?');">
                        @csrf
                        <button type="submit" @disabled(! $checklist['ready'] || $publishingStatus['fully_published']) class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-50">Publish All</button>
                    </form>
                    <form method="POST" action="{{ route('admin.courses.unpublish-all', $course) }}" onsubmit="return confirm('Move this course and all managed learning content back to draft? Shared media and the subject will not be changed.');">
                        @csrf
                        <button type="submit" @disabled(! $publishingStatus['has_published_content']) class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-sign-border bg-white px-5 py-3 text-sm font-semibold text-sign-primary disabled:cursor-not-allowed disabled:opacity-50">Move All to Draft</button>
                    </form>
                    <p class="text-xs leading-5 text-sign-muted">Bulk publishing never changes the parent subject or shared Media Library publishing status.</p>
                </div>
            </div>

            <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($publishingStatus['groups'] as $group)
                    <div class="rounded-2xl bg-sign-soft p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-sign-muted">{{ $group['label'] }}</p>
                        <p class="mt-2 font-heading text-xl font-semibold text-sign-primary">{{ $group['published'] }}/{{ $group['total'] }}</p>
                        <p class="mt-1 text-xs text-sign-muted">published</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="mt-7 rounded-2xl border p-5 sm:rounded-3xl sm:p-7 {{ $checklist['ready'] ? 'border-sign-cyan bg-sign-light/40' : 'border-red-200 bg-red-50' }}" aria-labelledby="readiness-heading">
            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider {{ $checklist['ready'] ? 'text-sign-cyan-dark' : 'text-red-700' }}">Publishing readiness</p>
                    <h2 id="readiness-heading" class="mt-2 font-heading text-2xl font-semibold {{ $checklist['ready'] ? 'text-sign-primary' : 'text-red-900' }}">{{ $checklist['ready'] ? 'Required checks are complete' : count($checklist['blockers']) . ' required check' . (count($checklist['blockers']) === 1 ? '' : 's') . ' need attention' }}</h2>
                    <p class="mt-2 text-sm leading-6 {{ $checklist['ready'] ? 'text-sign-muted' : 'text-red-800' }}">{{ $checklist['ready'] ? 'The course can be bulk published. Recommendations are optional improvements.' : 'Resolve the required items below before Publish All becomes available.' }}</p>
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
                <div class="flex items-center justify-between gap-3"><div><p class="text-xs font-semibold uppercase tracking-wider text-red-700">Required</p><h2 id="required-heading" class="mt-1 font-heading text-2xl font-semibold text-sign-primary">Publishing blockers</h2></div><span class="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-sign-primary ring-1 ring-sign-border">{{ $checklist['required_passed'] }}/{{ $checklist['required_total'] }} complete</span></div>
                <div class="mt-4 space-y-3">
                    @foreach ($checklist['required'] as $check)
                        <article class="rounded-2xl border bg-white p-4 sm:p-5 {{ $check['passed'] ? 'border-sign-border' : 'border-red-200' }}">
                            <div class="flex gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-sm font-bold {{ $check['passed'] ? 'bg-sign-light text-sign-primary' : 'bg-red-100 text-red-700' }}" aria-hidden="true">{{ $check['passed'] ? '✓' : '!' }}</span><div class="min-w-0"><div class="flex flex-wrap items-center gap-2"><h3 class="font-semibold text-sign-primary">{{ $check['title'] }}</h3><span class="rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide {{ $check['passed'] ? 'bg-sign-light text-sign-primary' : 'bg-red-100 text-red-700' }}">{{ $check['passed'] ? 'Passed' : 'Required' }}</span></div><p class="mt-1 text-sm leading-6 text-sign-muted">{{ $check['description'] }}</p>@if ($check['detail'])<p class="mt-2 text-sm font-semibold {{ $check['passed'] ? 'text-sign-muted' : 'text-red-700' }}">{{ $check['detail'] }}</p>@endif</div></div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section aria-labelledby="recommended-heading">
                <div><p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Recommended</p><h2 id="recommended-heading" class="mt-1 font-heading text-2xl font-semibold text-sign-primary">Accessibility & learner experience</h2></div>
                <div class="mt-4 space-y-3">
                    @foreach ($checklist['recommended'] as $check)
                        <article class="rounded-2xl border border-sign-border bg-white p-4 sm:p-5">
                            <div class="flex gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-sm font-bold {{ $check['passed'] ? 'bg-sign-light text-sign-primary' : 'bg-sign-soft text-sign-cyan-dark' }}" aria-hidden="true">{{ $check['passed'] ? '✓' : 'i' }}</span><div class="min-w-0"><div class="flex flex-wrap items-center gap-2"><h3 class="font-semibold text-sign-primary">{{ $check['title'] }}</h3><span class="rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide {{ $check['passed'] ? 'bg-sign-light text-sign-primary' : 'bg-sign-soft text-sign-muted' }}">{{ $check['passed'] ? 'Complete' : 'Recommended' }}</span></div><p class="mt-1 text-sm leading-6 text-sign-muted">{{ $check['description'] }}</p>@if ($check['detail'])<p class="mt-2 text-sm font-semibold text-sign-cyan-dark">{{ $check['detail'] }}</p>@endif</div></div>
                        </article>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
</section>
@endsection
