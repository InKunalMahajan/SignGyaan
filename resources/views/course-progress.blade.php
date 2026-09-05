@extends('layouts.app')

@section('title', $course->title.' Progress - SignGyaan')
@section('description', 'Review course and lesson progress for '.$course->title.'.')

@section('content')
<section class="border-b border-sign-border bg-sign-soft py-8 sm:py-12">
    <x-container>
        <nav class="flex flex-wrap gap-2 text-sm text-sign-muted" aria-label="Breadcrumb">
            <a href="{{ route('my-courses') }}" class="hover:text-sign-primary">My Courses</a><span aria-hidden="true">/</span><span class="font-semibold text-sign-primary">Course Progress</span>
        </nav>
        <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_18rem] lg:items-end">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">{{ $subject->name }}</p>
                <h1 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">{{ $course->title }}</h1>
                <p class="mt-3 text-sm text-sign-muted">{{ $completed_lessons }} of {{ $total_lessons }} lessons completed.</p>
                <div class="mt-4 h-3 max-w-2xl overflow-hidden rounded-full bg-white" role="progressbar" aria-label="Course progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $progress_percent }}"><div class="h-full rounded-full bg-sign-primary" style="width: {{ $progress_percent }}%"></div></div>
                <p class="mt-2 text-sm font-semibold text-sign-primary">{{ $progress_percent }}% complete</p>
            </div>
            <a href="{{ $resume_url }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white hover:bg-sign-dark">{{ $is_completed ? 'Review Course' : 'Continue Course' }}</a>
        </div>
    </x-container>
</section>

<section class="bg-white py-10 sm:py-14">
    <x-container>
        <div class="mx-auto max-w-5xl space-y-6">
            @foreach ($units as $unit)
                <section class="overflow-hidden rounded-2xl border border-sign-border sm:rounded-3xl" aria-labelledby="unit-progress-{{ $unit['id'] }}">
                    <div class="bg-sign-soft p-5 sm:p-6">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div><h2 id="unit-progress-{{ $unit['id'] }}" class="font-heading text-xl font-semibold text-sign-primary">{{ $unit['title'] }}</h2><p class="mt-1 text-sm text-sign-muted">{{ $unit['completed_lessons'] }} of {{ $unit['total_lessons'] }} lessons completed</p></div>
                            <span class="text-sm font-semibold text-sign-cyan-dark">{{ $unit['progress_percent'] }}%</span>
                        </div>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-white" role="progressbar" aria-label="{{ $unit['title'] }} progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $unit['progress_percent'] }}"><div class="h-full rounded-full bg-sign-primary" style="width: {{ $unit['progress_percent'] }}%"></div></div>
                    </div>
                    <ol class="divide-y divide-sign-border">
                        @foreach ($unit['lessons'] as $lesson)
                            <li class="p-4 sm:p-5 {{ $lesson['current'] ? 'bg-sign-light/40' : 'bg-white' }}">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="font-semibold text-sign-primary">{{ $lesson['title'] }}</span>
                                            @if ($lesson['completed'])<span class="rounded-full bg-sign-light px-2.5 py-1 text-xs font-semibold text-sign-primary">✓ Completed</span>@elseif ($lesson['current'])<span class="rounded-full bg-sign-soft px-2.5 py-1 text-xs font-semibold text-sign-cyan-dark">Current lesson</span>@else<span class="rounded-full border border-sign-border px-2.5 py-1 text-xs font-semibold text-sign-muted">Not completed</span>@endif
                                        </div>
                                        <div class="mt-2 flex flex-wrap gap-3 text-xs text-sign-muted">
                                            @if ($lesson['duration'])<span>{{ $lesson['duration'] }} min</span>@endif
                                            @if ($lesson['video_watched_percent'] !== null)<span>Video watched {{ $lesson['video_watched_percent'] }}%</span>@endif
                                        </div>
                                    </div>
                                    <a href="{{ $lesson['url'] }}" class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-xl border border-sign-primary px-4 py-2 text-sm font-semibold text-sign-primary hover:bg-sign-soft">{{ $lesson['completed'] ? 'Review' : ($lesson['current'] ? 'Continue' : 'Open Lesson') }}</a>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endforeach
        </div>
    </x-container>
</section>
@endsection
