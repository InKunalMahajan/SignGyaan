@extends('layouts.app')

@section('title', 'Explore Learning - SignGyaan')

@section('description', 'Explore SignGyaan subjects, courses and quick learning topics through a simple visual discovery experience.')

@section('content')

    @php
        $query = trim((string) request('q', ''));
        $activeCategory = request('category', 'all');

        $items = [
            [
                'title' => 'English Foundations',
                'subject' => 'English',
                'category' => 'english',
                'type' => 'Course',
                'description' => 'Build everyday vocabulary, basic grammar and simple sentence understanding.',
                'url' => route('courses.show', ['subject' => 'english', 'course' => 'english-foundations']),
            ],
            [
                'title' => 'Everyday Vocabulary',
                'subject' => 'English',
                'category' => 'english',
                'type' => 'Quick topic',
                'description' => 'Learn useful words for daily communication and common situations.',
                'url' => route('subjects.show', 'english'),
            ],
            [
                'title' => 'Everyday Mathematics',
                'subject' => 'Mathematics',
                'category' => 'mathematics',
                'type' => 'Course',
                'description' => 'Use numbers, money, time and calculations in practical daily situations.',
                'url' => route('courses.show', ['subject' => 'mathematics', 'course' => 'everyday-mathematics']),
            ],
            [
                'title' => 'Quick Calculations',
                'subject' => 'Mathematics',
                'category' => 'mathematics',
                'type' => 'Quick topic',
                'description' => 'Practise addition, subtraction and simple mental maths step by step.',
                'url' => route('subjects.show', 'mathematics'),
            ],
            [
                'title' => 'Science Foundations',
                'subject' => 'Science',
                'category' => 'science',
                'type' => 'Course',
                'description' => 'Understand matter, energy and basic scientific thinking through visual examples.',
                'url' => route('courses.show', ['subject' => 'science', 'course' => 'science-foundations']),
            ],
            [
                'title' => 'Living World',
                'subject' => 'Science',
                'category' => 'science',
                'type' => 'Course',
                'description' => 'Explore plants, animals, the human body and ecosystems visually.',
                'url' => route('courses.show', ['subject' => 'science', 'course' => 'living-world']),
            ],
            [
                'title' => 'Computer Basics',
                'subject' => 'Digital Skills',
                'category' => 'digital-skills',
                'type' => 'Course',
                'description' => 'Understand hardware, software, files, folders and everyday computer use.',
                'url' => route('courses.show', ['subject' => 'digital-skills', 'course' => 'computer-basics']),
            ],
            [
                'title' => 'Internet & Online Tools',
                'subject' => 'Digital Skills',
                'category' => 'digital-skills',
                'type' => 'Course',
                'description' => 'Learn browsing, email, useful online tools and basic digital safety.',
                'url' => route('courses.show', ['subject' => 'digital-skills', 'course' => 'internet-online-tools']),
            ],
            [
                'title' => 'India & the World',
                'subject' => 'General Knowledge',
                'category' => 'general-knowledge',
                'type' => 'Course',
                'description' => 'Learn important places, symbols, people and useful facts about India and the world.',
                'url' => route('courses.show', ['subject' => 'general-knowledge', 'course' => 'india-the-world']),
            ],
            [
                'title' => 'Time & Task Management',
                'subject' => 'Life Skills',
                'category' => 'life-skills',
                'type' => 'Course',
                'description' => 'Plan your day, organise tasks and use your time more effectively.',
                'url' => route('courses.show', ['subject' => 'life-skills', 'course' => 'time-task-management']),
            ],
            [
                'title' => 'Communication & Confidence',
                'subject' => 'Life Skills',
                'category' => 'life-skills',
                'type' => 'Course',
                'description' => 'Build clearer communication, self-expression and everyday confidence.',
                'url' => route('courses.show', ['subject' => 'life-skills', 'course' => 'communication-confidence']),
            ],
            [
                'title' => 'Everyday ISL',
                'subject' => 'ISL Learning',
                'category' => 'isl',
                'type' => 'Quick topic',
                'description' => 'Discover visual learning experiences designed around Indian Sign Language support.',
                'url' => route('learn'),
            ],
        ];

        $filteredItems = collect($items)->filter(function ($item) use ($query, $activeCategory) {
            $matchesCategory = $activeCategory === 'all' || $item['category'] === $activeCategory;

            if ($query === '') {
                return $matchesCategory;
            }

            $haystack = strtolower($item['title'] . ' ' . $item['subject'] . ' ' . $item['type'] . ' ' . $item['description']);

            return $matchesCategory && str_contains($haystack, strtolower($query));
        });

        $categories = [
            'all' => 'All',
            'english' => 'English',
            'mathematics' => 'Mathematics',
            'science' => 'Science',
            'digital-skills' => 'Digital Skills',
            'general-knowledge' => 'General Knowledge',
            'life-skills' => 'Life Skills',
            'isl' => 'ISL',
        ];
    @endphp

    {{-- Explore Hero --}}
    <section class="border-b border-sign-border bg-sign-soft py-12 sm:py-16 lg:py-20">
        <x-container>
            <div class="mx-auto max-w-4xl text-center">
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Explore SignGyaan</p>
                <h1 class="mt-3 font-heading text-4xl font-semibold tracking-tight text-sign-primary sm:text-5xl lg:text-6xl">
                    Find something useful to learn today
                </h1>
                <p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-sign-muted sm:text-lg sm:leading-8">
                    Search courses and quick topics, or browse by subject to discover your next visual learning lesson.
                </p>

                <form method="GET" action="{{ route('explore') }}" class="mx-auto mt-8 max-w-2xl">
                    @if ($activeCategory !== 'all')
                        <input type="hidden" name="category" value="{{ $activeCategory }}">
                    @endif

                    <div class="flex flex-col gap-3 rounded-2xl border border-sign-border bg-white p-2 shadow-sm sm:flex-row sm:items-center">
                        <div class="flex min-w-0 flex-1 items-center gap-3 px-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 shrink-0 text-sign-muted" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.4a6.75 6.75 0 1 1-13.5 0 6.75 6.75 0 0 1 13.5 0Z" />
                            </svg>
                            <label for="explore-search" class="sr-only">Search learning</label>
                            <input
                                id="explore-search"
                                type="search"
                                name="q"
                                value="{{ $query }}"
                                placeholder="Search English, computer basics, maths..."
                                class="min-h-11 w-full border-0 bg-transparent text-sm text-sign-text outline-none placeholder:text-sign-muted/70"
                            >
                        </div>

                        <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-6 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">
                            Search
                        </button>
                    </div>
                </form>

                <div class="mt-5 flex flex-wrap justify-center gap-x-5 gap-y-2 text-sm text-sign-muted">
                    <span>Try:</span>
                    <a href="{{ route('explore', ['q' => 'computer']) }}" class="font-semibold text-sign-primary hover:text-sign-cyan-dark">Computer</a>
                    <a href="{{ route('explore', ['q' => 'english']) }}" class="font-semibold text-sign-primary hover:text-sign-cyan-dark">English</a>
                    <a href="{{ route('explore', ['q' => 'time']) }}" class="font-semibold text-sign-primary hover:text-sign-cyan-dark">Time management</a>
                </div>
            </div>
        </x-container>
    </section>

    {{-- Browse and Results --}}
    <section class="bg-white py-12 sm:py-16 lg:py-20">
        <x-container>
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">
                        {{ $query !== '' ? 'Search results' : 'Discover learning' }}
                    </p>
                    <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">
                        @if ($query !== '')
                            Results for “{{ $query }}”
                        @else
                            Browse courses and quick topics
                        @endif
                    </h2>
                    <p class="mt-3 text-sm leading-6 text-sign-muted">
                        {{ $filteredItems->count() }} {{ Str::plural('result', $filteredItems->count()) }} available
                    </p>
                </div>

                @if ($query !== '' || $activeCategory !== 'all')
                    <a href="{{ route('explore') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-sign-primary transition hover:text-sign-cyan-dark">
                        Clear search & filters
                    </a>
                @endif
            </div>

            {{-- Category Filters --}}
            <div class="mt-8 -mx-4 overflow-x-auto px-4 pb-2 sm:mx-0 sm:px-0" aria-label="Explore categories">
                <div class="flex min-w-max gap-2 sm:flex-wrap">
                    @foreach ($categories as $categorySlug => $categoryName)
                        @php
                            $filterParams = [];
                            if ($categorySlug !== 'all') {
                                $filterParams['category'] = $categorySlug;
                            }
                            if ($query !== '') {
                                $filterParams['q'] = $query;
                            }
                            $isActive = $activeCategory === $categorySlug;
                        @endphp

                        <a
                            href="{{ route('explore', $filterParams) }}"
                            @class([
                                'inline-flex min-h-10 items-center justify-center rounded-full border px-4 py-2 text-sm font-semibold transition',
                                'border-sign-primary bg-sign-primary text-white' => $isActive,
                                'border-sign-border bg-white text-sign-primary hover:border-sign-cyan hover:bg-sign-soft' => ! $isActive,
                            ])
                        >
                            {{ $categoryName }}
                        </a>
                    @endforeach
                </div>
            </div>

            @if ($filteredItems->isNotEmpty())
                <div class="mt-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($filteredItems as $item)
                        <a href="{{ $item['url'] }}" class="group flex min-w-0 flex-col rounded-3xl border border-sign-border bg-white p-5 transition duration-200 hover:-translate-y-1 hover:border-sign-cyan hover:shadow-lg sm:p-6">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <span class="rounded-full bg-sign-soft px-3 py-1 text-xs font-semibold text-sign-primary">
                                    {{ $item['type'] }}
                                </span>
                                <span class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">
                                    {{ $item['subject'] }}
                                </span>
                            </div>

                            <h3 class="mt-5 font-heading text-xl font-semibold text-sign-primary sm:text-2xl">
                                {{ $item['title'] }}
                            </h3>

                            <p class="mt-3 flex-1 text-sm leading-6 text-sign-muted">
                                {{ $item['description'] }}
                            </p>

                            <div class="mt-6 flex items-center justify-between gap-4 border-t border-sign-border pt-4">
                                <span class="text-sm font-semibold text-sign-primary">Open learning</span>
                                <span class="text-lg text-sign-primary transition group-hover:translate-x-1" aria-hidden="true">→</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="mt-8 rounded-3xl border border-dashed border-sign-border bg-sign-soft px-6 py-12 text-center sm:px-10">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-sign-primary shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.4a6.75 6.75 0 1 1-13.5 0 6.75 6.75 0 0 1 13.5 0Z" />
                        </svg>
                    </div>
                    <h3 class="mt-5 font-heading text-2xl font-semibold text-sign-primary">No matching learning found</h3>
                    <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-sign-muted">Try a shorter keyword, choose another subject, or clear the current filters.</p>
                    <a href="{{ route('explore') }}" class="mt-6 inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Explore everything</a>
                </div>
            @endif
        </x-container>
    </section>

    {{-- Discovery Shortcuts --}}
    <section class="border-y border-sign-border bg-sign-soft py-12 sm:py-16 lg:py-20">
        <x-container>
            <div class="grid gap-8 lg:grid-cols-[0.8fr_1.2fr] lg:items-start">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Not sure where to begin?</p>
                    <h2 class="mt-3 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Choose how you want to explore</h2>
                    <p class="mt-4 max-w-xl text-base leading-7 text-sign-muted">Start from a subject, follow a complete learning path, or jump into a short topic.</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <a href="{{ route('subjects') }}" class="group rounded-3xl border border-sign-border bg-white p-5 transition hover:border-sign-cyan hover:shadow-sm">
                        <span class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">01</span>
                        <h3 class="mt-3 font-heading text-xl font-semibold text-sign-primary">Browse Subjects</h3>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">See every learning area in one place.</p>
                        <span class="mt-4 inline-block text-sm font-semibold text-sign-primary">View subjects →</span>
                    </a>

                    <a href="{{ route('learn') }}" class="group rounded-3xl border border-sign-border bg-white p-5 transition hover:border-sign-cyan hover:shadow-sm">
                        <span class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">02</span>
                        <h3 class="mt-3 font-heading text-xl font-semibold text-sign-primary">Learning Paths</h3>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">Follow structured courses step by step.</p>
                        <span class="mt-4 inline-block text-sm font-semibold text-sign-primary">View paths →</span>
                    </a>

                    <a href="{{ route('explore', ['category' => 'isl']) }}" class="group rounded-3xl border border-sign-border bg-white p-5 transition hover:border-sign-cyan hover:shadow-sm">
                        <span class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">03</span>
                        <h3 class="mt-3 font-heading text-xl font-semibold text-sign-primary">ISL Learning</h3>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">Discover visual-first learning with ISL support.</p>
                        <span class="mt-4 inline-block text-sm font-semibold text-sign-primary">Explore ISL →</span>
                    </a>
                </div>
            </div>
        </x-container>
    </section>

@endsection
