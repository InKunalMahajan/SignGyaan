@php
    $query = trim((string) request('q', ''));
    $normalizedQuery = strtolower($query);
    $terms = array_values(array_filter(preg_split('/\s+/', $normalizedQuery) ?: []));

    $subjects = [
        ['title' => 'English', 'description' => 'Vocabulary, grammar, reading and everyday communication.', 'keywords' => 'english language vocabulary grammar reading communication', 'url' => route('subjects.show', 'english')],
        ['title' => 'Mathematics', 'description' => 'Numbers, money, measurement and practical calculations.', 'keywords' => 'mathematics maths numbers money calculation measurement', 'url' => route('subjects.show', 'mathematics')],
        ['title' => 'Science', 'description' => 'Scientific ideas, matter, energy, living world and everyday science.', 'keywords' => 'science matter energy living world scientific', 'url' => route('subjects.show', 'science')],
        ['title' => 'Digital Skills', 'description' => 'Computers, internet, software and practical digital skills.', 'keywords' => 'digital skills computer internet software technology', 'url' => route('subjects.show', 'digital-skills')],
        ['title' => 'General Knowledge', 'description' => 'India, the world, society, people, places and useful facts.', 'keywords' => 'general knowledge india world society people places facts', 'url' => route('subjects.show', 'general-knowledge')],
        ['title' => 'Life Skills', 'description' => 'Communication, organisation, confidence and everyday independence.', 'keywords' => 'life skills communication confidence organisation time independence', 'url' => route('subjects.show', 'life-skills')],
    ];

    $courses = [
        ['title' => 'English Foundations', 'subject' => 'English', 'description' => 'Build basic vocabulary, grammar and sentence understanding.', 'keywords' => 'english beginner vocabulary grammar sentence foundations', 'url' => route('courses.show', ['subject' => 'english', 'course' => 'english-foundations'])],
        ['title' => 'Everyday Communication', 'subject' => 'English', 'description' => 'Practise useful expressions and daily conversations.', 'keywords' => 'english communication conversation expressions daily', 'url' => route('courses.show', ['subject' => 'english', 'course' => 'everyday-communication'])],
        ['title' => 'Reading & Understanding', 'subject' => 'English', 'description' => 'Improve reading confidence and comprehension.', 'keywords' => 'english reading understanding comprehension text', 'url' => route('courses.show', ['subject' => 'english', 'course' => 'reading-understanding'])],
        ['title' => 'Everyday Mathematics', 'subject' => 'Mathematics', 'description' => 'Use numbers, money, time and calculations in daily life.', 'keywords' => 'math mathematics money time calculation everyday', 'url' => route('courses.show', ['subject' => 'mathematics', 'course' => 'everyday-mathematics'])],
        ['title' => 'Numbers & Operations', 'subject' => 'Mathematics', 'description' => 'Strengthen addition, subtraction, multiplication and division.', 'keywords' => 'numbers operations addition subtraction multiplication division maths', 'url' => route('courses.show', ['subject' => 'mathematics', 'course' => 'numbers-operations'])],
        ['title' => 'Science Foundations', 'subject' => 'Science', 'description' => 'Understand matter, energy and basic scientific thinking.', 'keywords' => 'science foundations matter energy scientific', 'url' => route('courses.show', ['subject' => 'science', 'course' => 'science-foundations'])],
        ['title' => 'Living World', 'subject' => 'Science', 'description' => 'Explore plants, animals, the human body and ecosystems.', 'keywords' => 'science living world plants animals human body ecosystem', 'url' => route('courses.show', ['subject' => 'science', 'course' => 'living-world'])],
        ['title' => 'Computer Basics', 'subject' => 'Digital Skills', 'description' => 'Understand hardware, software, files and folders.', 'keywords' => 'computer basics hardware software files folders digital', 'url' => route('courses.show', ['subject' => 'digital-skills', 'course' => 'computer-basics'])],
        ['title' => 'Internet & Online Tools', 'subject' => 'Digital Skills', 'description' => 'Learn browsing, email, online tools and digital safety.', 'keywords' => 'internet online tools email browsing safety digital', 'url' => route('courses.show', ['subject' => 'digital-skills', 'course' => 'internet-online-tools'])],
        ['title' => 'India & the World', 'subject' => 'General Knowledge', 'description' => 'Learn places, people, symbols and important facts.', 'keywords' => 'india world general knowledge places people symbols facts', 'url' => route('courses.show', ['subject' => 'general-knowledge', 'course' => 'india-the-world'])],
        ['title' => 'Time & Task Management', 'subject' => 'Life Skills', 'description' => 'Plan your day, prioritise tasks and manage time.', 'keywords' => 'time task management planning organisation life skills', 'url' => route('courses.show', ['subject' => 'life-skills', 'course' => 'time-task-management'])],
        ['title' => 'Communication & Confidence', 'subject' => 'Life Skills', 'description' => 'Develop clearer communication and self-confidence.', 'keywords' => 'communication confidence self expression life skills', 'url' => route('courses.show', ['subject' => 'life-skills', 'course' => 'communication-confidence'])],
    ];

    $lessons = [
        ['title' => 'Introduction: Getting Started', 'course' => 'English Foundations', 'description' => 'Start the English Foundations course with a visual introduction.', 'keywords' => 'english introduction getting started lesson isl', 'url' => route('courses.show', ['subject' => 'english', 'course' => 'english-foundations', 'lesson' => 'unit-1-lesson-1'])],
        ['title' => 'Visual Explanation: Vocabulary & Meaning', 'course' => 'English Foundations', 'description' => 'Learn vocabulary through simple visual explanation.', 'keywords' => 'english vocabulary meaning visual lesson isl', 'url' => route('courses.show', ['subject' => 'english', 'course' => 'english-foundations', 'lesson' => 'unit-2-lesson-3'])],
        ['title' => 'Guided Practice: Number Foundations', 'course' => 'Everyday Mathematics', 'description' => 'Practise basic number ideas step by step.', 'keywords' => 'math number foundations guided practice lesson', 'url' => route('courses.show', ['subject' => 'mathematics', 'course' => 'everyday-mathematics', 'lesson' => 'unit-1-lesson-5'])],
        ['title' => 'Examples: Money & Measurement', 'course' => 'Everyday Mathematics', 'description' => 'See practical examples using money and measurement.', 'keywords' => 'math money measurement examples lesson', 'url' => route('courses.show', ['subject' => 'mathematics', 'course' => 'everyday-mathematics', 'lesson' => 'unit-3-lesson-4'])],
        ['title' => 'Introduction: Scientific Thinking', 'course' => 'Science Foundations', 'description' => 'Begin learning how observation supports scientific thinking.', 'keywords' => 'science scientific thinking observation introduction lesson', 'url' => route('courses.show', ['subject' => 'science', 'course' => 'science-foundations', 'lesson' => 'unit-1-lesson-1'])],
        ['title' => 'Visual Explanation: Computer Foundations', 'course' => 'Computer Basics', 'description' => 'Understand basic computer ideas through a visual lesson.', 'keywords' => 'computer foundations hardware digital visual lesson', 'url' => route('courses.show', ['subject' => 'digital-skills', 'course' => 'computer-basics', 'lesson' => 'unit-1-lesson-3'])],
        ['title' => 'Key Ideas: Files, Folders & Software', 'course' => 'Computer Basics', 'description' => 'Understand files, folders and software clearly.', 'keywords' => 'files folders software computer key ideas lesson', 'url' => route('courses.show', ['subject' => 'digital-skills', 'course' => 'computer-basics', 'lesson' => 'unit-2-lesson-2'])],
        ['title' => 'Introduction: India Basics', 'course' => 'India & the World', 'description' => 'Start with important facts and visual knowledge about India.', 'keywords' => 'india basics general knowledge lesson', 'url' => route('courses.show', ['subject' => 'general-knowledge', 'course' => 'india-the-world', 'lesson' => 'unit-1-lesson-1'])],
        ['title' => 'Guided Practice: Time & Organisation', 'course' => 'Time & Task Management', 'description' => 'Practise planning and organising everyday tasks.', 'keywords' => 'time organisation task management practice lesson', 'url' => route('courses.show', ['subject' => 'life-skills', 'course' => 'time-task-management', 'lesson' => 'unit-3-lesson-5'])],
    ];

    $topics = [
        ['title' => 'Everyday Vocabulary', 'subject' => 'English', 'description' => 'Useful words for daily communication.', 'keywords' => 'english everyday vocabulary words communication', 'url' => route('subjects.show', 'english')],
        ['title' => 'Quick Calculations', 'subject' => 'Mathematics', 'description' => 'Short practice for everyday calculations.', 'keywords' => 'math quick calculations addition subtraction', 'url' => route('subjects.show', 'mathematics')],
        ['title' => 'Computer Hardware', 'subject' => 'Digital Skills', 'description' => 'Learn common parts of a computer.', 'keywords' => 'computer hardware parts digital skills', 'url' => route('courses.show', ['subject' => 'digital-skills', 'course' => 'computer-basics'])],
        ['title' => 'Online Safety', 'subject' => 'Digital Skills', 'description' => 'Build safer habits when using the internet.', 'keywords' => 'internet online safety privacy digital', 'url' => route('courses.show', ['subject' => 'digital-skills', 'course' => 'internet-online-tools'])],
        ['title' => 'India & the World', 'subject' => 'General Knowledge', 'description' => 'Explore useful facts about India and the wider world.', 'keywords' => 'india world facts general knowledge', 'url' => route('courses.show', ['subject' => 'general-knowledge', 'course' => 'india-the-world'])],
        ['title' => 'Time Management', 'subject' => 'Life Skills', 'description' => 'Organise time and daily tasks more clearly.', 'keywords' => 'time management task planning life skills', 'url' => route('courses.show', ['subject' => 'life-skills', 'course' => 'time-task-management'])],
        ['title' => 'Everyday ISL', 'subject' => 'ISL Learning', 'description' => 'Explore learning supported by Indian Sign Language.', 'keywords' => 'isl indian sign language visual learning everyday', 'url' => route('learn')],
    ];

    $matches = function (array $item) use ($terms, $query) {
        if ($query === '') {
            return false;
        }

        $haystack = strtolower(implode(' ', [
            $item['title'] ?? '',
            $item['subject'] ?? '',
            $item['course'] ?? '',
            $item['description'] ?? '',
            $item['keywords'] ?? '',
        ]));

        return collect($terms)->every(fn ($term) => str_contains($haystack, $term));
    };

    $subjectResults = collect($subjects)->filter($matches)->values();
    $courseResults = collect($courses)->filter($matches)->values();
    $lessonResults = collect($lessons)->filter($matches)->values();
    $topicResults = collect($topics)->filter($matches)->values();
    $totalResults = $subjectResults->count() + $courseResults->count() + $lessonResults->count() + $topicResults->count();
@endphp

@section('title', $query !== '' ? 'Search: ' . $query . ' - SignGyaan' : 'Search - SignGyaan')
@section('description', 'Search SignGyaan subjects, courses, lessons and quick learning topics.')

@section('content')
    <section class="border-b border-sign-border bg-sign-soft py-10 sm:py-12 lg:py-16">
        <x-container>
            <div class="mx-auto max-w-4xl">
                <nav class="flex flex-wrap items-center gap-2 text-sm text-sign-muted" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="transition hover:text-sign-primary">Home</a>
                    <span aria-hidden="true">/</span>
                    <span class="font-semibold text-sign-primary">Search</span>
                </nav>

                <div class="mt-7 text-center">
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Search SignGyaan</p>
                    <h1 class="mt-3 font-heading text-4xl font-semibold tracking-tight text-sign-primary sm:text-5xl">
                        Find subjects, courses and lessons
                    </h1>
                    <p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-sign-muted sm:text-lg">
                        Search across the SignGyaan learning structure and jump directly to the content you need.
                    </p>
                </div>

                <form method="GET" action="{{ route('search') }}" class="mx-auto mt-8 max-w-3xl" role="search">
                    <div class="flex flex-col gap-3 rounded-2xl border border-sign-border bg-white p-2 shadow-sm sm:flex-row sm:items-center">
                        <div class="flex min-w-0 flex-1 items-center gap-3 px-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 shrink-0 text-sign-muted" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.4a6.75 6.75 0 1 1-13.5 0 6.75 6.75 0 0 1 13.5 0Z" />
                            </svg>
                            <label for="search-page-input" class="sr-only">Search SignGyaan</label>
                            <input
                                id="search-page-input"
                                type="search"
                                name="q"
                                value="{{ $query }}"
                                placeholder="Search English, computer, time management..."
                                autocomplete="off"
                                class="min-h-11 w-full border-0 bg-transparent text-sm text-sign-text outline-none placeholder:text-sign-muted/70"
                            >
                        </div>
                        <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-6 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">
                            Search
                        </button>
                    </div>
                </form>

                <div class="mt-5 flex flex-wrap justify-center gap-x-5 gap-y-2 text-sm text-sign-muted">
                    <span>Popular:</span>
                    <a href="{{ route('search', ['q' => 'English']) }}" class="font-semibold text-sign-primary hover:text-sign-cyan-dark">English</a>
                    <a href="{{ route('search', ['q' => 'Computer']) }}" class="font-semibold text-sign-primary hover:text-sign-cyan-dark">Computer</a>
                    <a href="{{ route('search', ['q' => 'Time management']) }}" class="font-semibold text-sign-primary hover:text-sign-cyan-dark">Time management</a>
                    <a href="{{ route('search', ['q' => 'ISL']) }}" class="font-semibold text-sign-primary hover:text-sign-cyan-dark">ISL</a>
                </div>
            </div>
        </x-container>
    </section>

    <section class="bg-white py-10 sm:py-14 lg:py-16">
        <x-container>
            @if ($query === '')
                <div class="mx-auto max-w-3xl rounded-3xl border border-dashed border-sign-border bg-sign-soft px-6 py-12 text-center sm:px-10">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-sign-primary shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.4a6.75 6.75 0 1 1-13.5 0 6.75 6.75 0 0 1 13.5 0Z" />
                        </svg>
                    </div>
                    <h2 class="mt-5 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">What would you like to learn?</h2>
                    <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-sign-muted">Enter a keyword above, or explore all available subjects and learning paths.</p>
                    <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
                        <a href="{{ route('subjects') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Browse Subjects</a>
                        <a href="{{ route('explore') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-primary px-5 py-3 text-sm font-semibold text-sign-primary transition hover:bg-white">Explore Learning</a>
                    </div>
                </div>
            @else
                <div class="flex flex-col gap-4 border-b border-sign-border pb-7 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Search results</p>
                        <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">
                            Results for “{{ $query }}”
                        </h2>
                        <p class="mt-3 text-sm text-sign-muted">{{ $totalResults }} {{ Str::plural('result', $totalResults) }} across SignGyaan.</p>
                    </div>
                    <a href="{{ route('search') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-sign-primary transition hover:text-sign-cyan-dark">Clear search</a>
                </div>

                @if ($totalResults > 0)
                    <div class="mt-8 grid gap-8 xl:grid-cols-[14rem_minmax(0,1fr)] xl:items-start">
                        <aside class="rounded-3xl border border-sign-border bg-sign-soft p-5 xl:sticky xl:top-24" aria-label="Search result summary">
                            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Result types</p>
                            <div class="mt-4 space-y-2 text-sm">
                                <a href="#search-subjects" class="flex items-center justify-between rounded-xl bg-white px-3 py-2.5 font-semibold text-sign-primary">
                                    <span>Subjects</span><span>{{ $subjectResults->count() }}</span>
                                </a>
                                <a href="#search-courses" class="flex items-center justify-between rounded-xl bg-white px-3 py-2.5 font-semibold text-sign-primary">
                                    <span>Courses</span><span>{{ $courseResults->count() }}</span>
                                </a>
                                <a href="#search-lessons" class="flex items-center justify-between rounded-xl bg-white px-3 py-2.5 font-semibold text-sign-primary">
                                    <span>Lessons</span><span>{{ $lessonResults->count() }}</span>
                                </a>
                                <a href="#search-topics" class="flex items-center justify-between rounded-xl bg-white px-3 py-2.5 font-semibold text-sign-primary">
                                    <span>Topics</span><span>{{ $topicResults->count() }}</span>
                                </a>
                            </div>
                        </aside>

                        <div class="min-w-0 space-y-12">
                            <section id="search-subjects" class="scroll-mt-24">
                                <div class="flex items-center justify-between gap-4">
                                    <h3 class="font-heading text-2xl font-semibold text-sign-primary">Subjects</h3>
                                    <span class="text-sm text-sign-muted">{{ $subjectResults->count() }}</span>
                                </div>
                                @if ($subjectResults->isNotEmpty())
                                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                                        @foreach ($subjectResults as $item)
                                            <a href="{{ $item['url'] }}" class="group rounded-2xl border border-sign-border bg-white p-5 transition hover:border-sign-cyan hover:shadow-sm">
                                                <span class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Subject</span>
                                                <h4 class="mt-2 font-heading text-xl font-semibold text-sign-primary group-hover:text-sign-cyan-dark">{{ $item['title'] }}</h4>
                                                <p class="mt-2 text-sm leading-6 text-sign-muted">{{ $item['description'] }}</p>
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="mt-4 text-sm text-sign-muted">No matching subjects.</p>
                                @endif
                            </section>

                            <section id="search-courses" class="scroll-mt-24">
                                <div class="flex items-center justify-between gap-4">
                                    <h3 class="font-heading text-2xl font-semibold text-sign-primary">Courses</h3>
                                    <span class="text-sm text-sign-muted">{{ $courseResults->count() }}</span>
                                </div>
                                @if ($courseResults->isNotEmpty())
                                    <div class="mt-5 space-y-4">
                                        @foreach ($courseResults as $item)
                                            <a href="{{ $item['url'] }}" class="group flex flex-col gap-4 rounded-2xl border border-sign-border bg-white p-5 transition hover:border-sign-cyan hover:shadow-sm sm:flex-row sm:items-start sm:justify-between">
                                                <div class="min-w-0">
                                                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                                                        <span class="rounded-full bg-sign-soft px-2.5 py-1 text-sign-primary">Course</span>
                                                        <span class="text-sign-cyan-dark">{{ $item['subject'] }}</span>
                                                    </div>
                                                    <h4 class="mt-3 font-heading text-xl font-semibold text-sign-primary group-hover:text-sign-cyan-dark">{{ $item['title'] }}</h4>
                                                    <p class="mt-2 text-sm leading-6 text-sign-muted">{{ $item['description'] }}</p>
                                                </div>
                                                <span class="shrink-0 text-sign-primary transition group-hover:translate-x-1" aria-hidden="true">→</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="mt-4 text-sm text-sign-muted">No matching courses.</p>
                                @endif
                            </section>

                            <section id="search-lessons" class="scroll-mt-24">
                                <div class="flex items-center justify-between gap-4">
                                    <h3 class="font-heading text-2xl font-semibold text-sign-primary">Lessons</h3>
                                    <span class="text-sm text-sign-muted">{{ $lessonResults->count() }}</span>
                                </div>
                                @if ($lessonResults->isNotEmpty())
                                    <div class="mt-5 grid gap-4 lg:grid-cols-2">
                                        @foreach ($lessonResults as $item)
                                            <a href="{{ $item['url'] }}" class="group rounded-2xl border border-sign-border bg-white p-5 transition hover:border-sign-cyan hover:shadow-sm">
                                                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                                                    <span class="rounded-full bg-sign-light px-2.5 py-1 text-sign-primary">Lesson</span>
                                                    <span class="text-sign-cyan-dark">{{ $item['course'] }}</span>
                                                </div>
                                                <h4 class="mt-3 font-heading text-lg font-semibold text-sign-primary group-hover:text-sign-cyan-dark">{{ $item['title'] }}</h4>
                                                <p class="mt-2 text-sm leading-6 text-sign-muted">{{ $item['description'] }}</p>
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="mt-4 text-sm text-sign-muted">No matching lessons.</p>
                                @endif
                            </section>

                            <section id="search-topics" class="scroll-mt-24">
                                <div class="flex items-center justify-between gap-4">
                                    <h3 class="font-heading text-2xl font-semibold text-sign-primary">Quick topics</h3>
                                    <span class="text-sm text-sign-muted">{{ $topicResults->count() }}</span>
                                </div>
                                @if ($topicResults->isNotEmpty())
                                    <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                        @foreach ($topicResults as $item)
                                            <a href="{{ $item['url'] }}" class="group rounded-2xl border border-sign-border bg-sign-soft p-5 transition hover:border-sign-cyan hover:bg-white hover:shadow-sm">
                                                <span class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">{{ $item['subject'] }}</span>
                                                <h4 class="mt-2 font-heading text-lg font-semibold text-sign-primary group-hover:text-sign-cyan-dark">{{ $item['title'] }}</h4>
                                                <p class="mt-2 text-sm leading-6 text-sign-muted">{{ $item['description'] }}</p>
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="mt-4 text-sm text-sign-muted">No matching quick topics.</p>
                                @endif
                            </section>
                        </div>
                    </div>
                @else
                    <div class="mt-8 rounded-3xl border border-dashed border-sign-border bg-sign-soft px-6 py-12 text-center sm:px-10">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-sign-primary shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.4a6.75 6.75 0 1 1-13.5 0 6.75 6.75 0 0 1 13.5 0Z" />
                            </svg>
                        </div>
                        <h3 class="mt-5 font-heading text-2xl font-semibold text-sign-primary">No results for “{{ $query }}”</h3>
                        <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-sign-muted">Try a shorter keyword, check the spelling, or explore available subjects and courses.</p>
                        <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
                            <a href="{{ route('search') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">New Search</a>
                            <a href="{{ route('explore') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-primary px-5 py-3 text-sm font-semibold text-sign-primary transition hover:bg-white">Explore Learning</a>
                        </div>
                    </div>
                @endif
            @endif
        </x-container>
    </section>
@endsection
