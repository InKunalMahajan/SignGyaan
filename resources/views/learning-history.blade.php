@extends('layouts.app')

@section('title', 'Learning History - SignGyaan')
@section('description', 'Review your SignGyaan lesson, video and assessment learning history.')

@section('content')
<section class="border-b border-sign-border bg-sign-soft py-8 sm:py-12">
    <x-container>
        <nav class="flex flex-wrap items-center gap-2 text-sm text-sign-muted" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}" class="hover:text-sign-primary">Dashboard</a>
            <span aria-hidden="true">/</span>
            <span class="font-semibold text-sign-primary">Learning History</span>
        </nav>
        <div class="mt-6 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Your activity</p>
                <h1 class="mt-2 font-heading text-3xl font-semibold tracking-tight text-sign-primary sm:text-4xl lg:text-5xl">Learning History</h1>
                <p class="mt-4 max-w-2xl text-sm leading-7 text-sign-muted sm:text-base">Review saved lesson progress, completed lessons, video learning and submitted assessments in chronological order.</p>
            </div>
            <a href="{{ route('my-courses') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white hover:bg-sign-dark">My Courses</a>
        </div>
    </x-container>
</section>

<section class="bg-white py-8 sm:py-12 lg:py-16">
    <x-container>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" aria-label="Learning history summary">
            <div class="rounded-2xl border border-sign-border p-5 sm:rounded-3xl"><p class="text-sm font-semibold text-sign-muted">Total events</p><p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $historySummary['total_events'] }}</p></div>
            <div class="rounded-2xl border border-sign-border p-5 sm:rounded-3xl"><p class="text-sm font-semibold text-sign-muted">Lesson activity</p><p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $historySummary['lesson_events'] }}</p></div>
            <div class="rounded-2xl border border-sign-border p-5 sm:rounded-3xl"><p class="text-sm font-semibold text-sign-muted">Assessments</p><p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $historySummary['assessment_events'] }}</p></div>
            <div class="rounded-2xl border border-sign-border p-5 sm:rounded-3xl"><p class="text-sm font-semibold text-sign-muted">Active days</p><p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $historySummary['active_days'] }}</p></div>
        </div>

        @if ($historyByDate->isNotEmpty())
            <div class="mx-auto mt-10 max-w-5xl space-y-8">
                @foreach ($historyByDate as $date => $items)
                    <section aria-labelledby="history-date-{{ $loop->index }}">
                        <h2 id="history-date-{{ $loop->index }}" class="font-heading text-xl font-semibold text-sign-primary">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h2>
                        <ol class="mt-4 space-y-3">
                            @foreach ($items as $item)
                                <li class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-6">
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="rounded-full bg-sign-soft px-3 py-1 text-xs font-semibold text-sign-primary">{{ ucfirst(str_replace('_', ' ', $item['type'])) }}</span>
                                                <span class="text-xs font-semibold text-sign-cyan-dark">{{ $item['detail'] }}</span>
                                            </div>
                                            <h3 class="mt-3 font-heading text-lg font-semibold text-sign-primary">{{ $item['title'] }}</h3>
                                            @if ($item['lesson_title'])<p class="mt-1 text-sm text-sign-muted">{{ $item['lesson_title'] }}</p>@endif
                                            @if ($item['course_title'])<p class="mt-1 text-xs text-sign-muted">{{ $item['course_title'] }}</p>@endif
                                        </div>
                                        <div class="shrink-0 text-left sm:text-right">
                                            @if ($item['occurred_at'])<time class="text-xs font-semibold text-sign-muted" datetime="{{ $item['occurred_at']->toIso8601String() }}">{{ $item['occurred_at']->format('h:i A') }}</time>@endif
                                            @if ($item['url'])<a href="{{ $item['url'] }}" class="mt-2 inline-flex min-h-11 items-center text-sm font-semibold text-sign-primary hover:text-sign-cyan-dark">Open →</a>@endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    </section>
                @endforeach
            </div>
        @else
            <div class="mt-10 rounded-2xl border border-dashed border-sign-border bg-sign-soft p-8 text-center sm:rounded-3xl">
                <h2 class="font-heading text-2xl font-semibold text-sign-primary">No learning history yet</h2>
                <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-sign-muted">Save a lesson, complete a lesson, watch an ISL video, or submit an assessment. Your activity will appear here.</p>
                <a href="{{ route('subjects') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white">Browse Subjects</a>
            </div>
        @endif
    </x-container>
</section>
@endsection
