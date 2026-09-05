@extends('layouts.app')

@section('title', 'Explore Learning - SignGyaan')
@section('description', 'Explore published SignGyaan courses and lessons through a simple visual discovery experience.')

@section('content')
    <section class="border-b border-sign-border bg-sign-soft py-12 sm:py-16 lg:py-20">
        <x-container>
            <div class="mx-auto max-w-4xl text-center">
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Explore SignGyaan</p>
                <h1 class="mt-3 font-heading text-4xl font-semibold tracking-tight text-sign-primary sm:text-5xl lg:text-6xl">Find something useful to learn today</h1>
                <p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-sign-muted sm:text-lg sm:leading-8">Search the live published catalogue, browse by subject, or switch between complete courses and individual lessons.</p>

                <form method="GET" action="{{ route('explore') }}" class="mx-auto mt-8 max-w-2xl" role="search">
                    @if ($activeCategory !== 'all')<input type="hidden" name="category" value="{{ $activeCategory }}">@endif
                    @if ($activeType !== 'all')<input type="hidden" name="type" value="{{ $activeType }}">@endif

                    <div class="flex flex-col gap-3 rounded-2xl border border-sign-border bg-white p-2 shadow-sm sm:flex-row sm:items-center">
                        <div class="flex min-w-0 flex-1 items-center gap-3 px-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 shrink-0 text-sign-muted" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.4a6.75 6.75 0 1 1-13.5 0 6.75 6.75 0 0 1 13.5 0Z" /></svg>
                            <label for="explore-search" class="sr-only">Search courses and lessons</label>
                            <input id="explore-search" type="search" name="q" value="{{ $query }}" placeholder="Search courses, lessons or subjects..." class="min-h-11 w-full border-0 bg-transparent text-base text-sign-text outline-none placeholder:text-sign-muted/70">
                        </div>
                        <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-6 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Search</button>
                    </div>
                </form>
            </div>
        </x-container>
    </section>

    <section class="bg-white py-12 sm:py-16 lg:py-20">
        <x-container>
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">{{ $query !== '' ? 'Search results' : 'Discover learning' }}</p>
                    <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">
                        {{ $query !== '' ? 'Results for “'.$query.'”' : 'Published courses and lessons' }}
                    </h2>
                    <p class="mt-3 text-sm leading-6 text-sign-muted">{{ $items->count() }} {{ $items->count() === 1 ? 'result' : 'results' }} shown</p>
                </div>

                @if ($query !== '' || $activeCategory !== 'all' || $activeType !== 'all')
                    <a href="{{ route('explore') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-sign-primary transition hover:text-sign-cyan-dark">Clear search & filters</a>
                @endif
            </div>

            <div class="mt-8 space-y-4">
                <div>
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-sign-muted">Content type</p>
                    <div class="-mx-4 overflow-x-auto px-4 pb-2 sm:mx-0 sm:px-0">
                        <div class="flex min-w-max gap-2 sm:flex-wrap">
                            @foreach (['all' => 'All', 'course' => 'Courses', 'lesson' => 'Lessons'] as $typeKey => $typeLabel)
                                @php
                                    $params = [];
                                    if ($typeKey !== 'all') $params['type'] = $typeKey;
                                    if ($activeCategory !== 'all') $params['category'] = $activeCategory;
                                    if ($query !== '') $params['q'] = $query;
                                @endphp
                                <a href="{{ route('explore', $params) }}" @class([
                                    'inline-flex min-h-10 items-center justify-center rounded-full border px-4 py-2 text-sm font-semibold transition',
                                    'border-sign-primary bg-sign-primary text-white' => $activeType === $typeKey,
                                    'border-sign-border bg-white text-sign-primary hover:border-sign-cyan hover:bg-sign-soft' => $activeType !== $typeKey,
                                ])>{{ $typeLabel }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div>
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-sign-muted">Subject</p>
                    <div class="-mx-4 overflow-x-auto px-4 pb-2 sm:mx-0 sm:px-0">
                        <div class="flex min-w-max gap-2 sm:flex-wrap">
                            @php
                                $allParams = [];
                                if ($activeType !== 'all') $allParams['type'] = $activeType;
                                if ($query !== '') $allParams['q'] = $query;
                            @endphp
                            <a href="{{ route('explore', $allParams) }}" @class([
                                'inline-flex min-h-10 items-center justify-center rounded-full border px-4 py-2 text-sm font-semibold transition',
                                'border-sign-primary bg-sign-primary text-white' => $activeCategory === 'all',
                                'border-sign-border bg-white text-sign-primary hover:border-sign-cyan hover:bg-sign-soft' => $activeCategory !== 'all',
                            ])>All subjects</a>

                            @foreach ($subjects as $subject)
                                @php
                                    $subjectParams = ['category' => $subject->slug];
                                    if ($activeType !== 'all') $subjectParams['type'] = $activeType;
                                    if ($query !== '') $subjectParams['q'] = $query;
                                @endphp
                                <a href="{{ route('explore', $subjectParams) }}" @class([
                                    'inline-flex min-h-10 items-center justify-center rounded-full border px-4 py-2 text-sm font-semibold transition',
                                    'border-sign-primary bg-sign-primary text-white' => $activeCategory === $subject->slug,
                                    'border-sign-border bg-white text-sign-primary hover:border-sign-cyan hover:bg-sign-soft' => $activeCategory !== $subject->slug,
                                ])>{{ $subject->name }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            @if ($items->isNotEmpty())
                <div class="mt-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($items as $item)
                        <a href="{{ $item['url'] }}" class="group flex min-w-0 flex-col rounded-2xl border border-sign-border bg-white p-5 transition duration-200 hover:-translate-y-1 hover:border-sign-cyan hover:shadow-lg sm:rounded-3xl sm:p-6">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-sign-soft px-3 py-1 text-xs font-semibold text-sign-primary">{{ $item['type'] }}</span>
                                    @if ($item['featured'])<span class="rounded-full bg-sign-light px-3 py-1 text-xs font-semibold text-sign-primary">Featured</span>@endif
                                </div>
                                <span class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">{{ $item['subject'] }}</span>
                            </div>

                            <h3 class="mt-5 font-heading text-xl font-semibold text-sign-primary sm:text-2xl">{{ $item['title'] }}</h3>
                            <p class="mt-3 flex-1 text-sm leading-6 text-sign-muted">{{ $item['description'] }}</p>
                            <p class="mt-4 text-xs font-semibold leading-5 text-sign-muted">{{ $item['meta'] }}</p>

                            <div class="mt-5 flex items-center justify-between gap-4 border-t border-sign-border pt-4">
                                <span class="text-sm font-semibold text-sign-primary">{{ $item['type_key'] === 'course' ? 'View course' : 'Open lesson' }}</span>
                                <span class="text-lg text-sign-primary transition group-hover:translate-x-1" aria-hidden="true">→</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="mt-8 rounded-3xl border border-dashed border-sign-border bg-sign-soft px-6 py-12 text-center sm:px-10">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-sign-primary shadow-sm" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.4a6.75 6.75 0 1 1-13.5 0 6.75 6.75 0 0 1 13.5 0Z" /></svg>
                    </div>
                    <h3 class="mt-5 font-heading text-2xl font-semibold text-sign-primary">No matching published learning found</h3>
                    <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-sign-muted">Try another keyword, content type or subject. Draft content is intentionally hidden from this page.</p>
                    <a href="{{ route('explore') }}" class="mt-6 inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Explore everything</a>
                </div>
            @endif
        </x-container>
    </section>

    <section class="border-y border-sign-border bg-sign-soft py-12 sm:py-16 lg:py-20">
        <x-container>
            <div class="grid gap-8 lg:grid-cols-[0.8fr_1.2fr] lg:items-start">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Not sure where to begin?</p>
                    <h2 class="mt-3 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Choose how you want to explore</h2>
                    <p class="mt-4 max-w-xl text-base leading-7 text-sign-muted">Start from a subject, choose a full course, or open an individual published lesson.</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <a href="{{ route('subjects') }}" class="group rounded-2xl border border-sign-border bg-white p-5 transition hover:border-sign-cyan hover:shadow-sm sm:rounded-3xl"><span class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">01</span><h3 class="mt-3 font-heading text-xl font-semibold text-sign-primary">Subjects</h3><p class="mt-2 text-sm leading-6 text-sign-muted">Browse every published learning area.</p></a>
                    <a href="{{ route('explore', ['type' => 'course']) }}" class="group rounded-2xl border border-sign-border bg-white p-5 transition hover:border-sign-cyan hover:shadow-sm sm:rounded-3xl"><span class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">02</span><h3 class="mt-3 font-heading text-xl font-semibold text-sign-primary">Courses</h3><p class="mt-2 text-sm leading-6 text-sign-muted">Follow a complete structured learning path.</p></a>
                    <a href="{{ route('explore', ['type' => 'lesson']) }}" class="group rounded-2xl border border-sign-border bg-white p-5 transition hover:border-sign-cyan hover:shadow-sm sm:rounded-3xl"><span class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">03</span><h3 class="mt-3 font-heading text-xl font-semibold text-sign-primary">Lessons</h3><p class="mt-2 text-sm leading-6 text-sign-muted">Jump directly into a focused lesson.</p></a>
                </div>
            </div>
        </x-container>
    </section>
@endsection
