@extends('layouts.app')

@section('title', 'Subjects - SignGyaan')
@section('description', 'Browse SignGyaan subjects and start learning through visual, structured and ISL-supported lessons.')

@section('content')
    <section class="border-b border-sign-border bg-sign-soft py-10 sm:py-14 lg:py-20">
        <x-container>
            <div class="max-w-3xl">
                <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark sm:text-sm">Subjects</p>
                <h1 class="mt-3 font-heading text-3xl font-semibold leading-tight tracking-tight text-sign-primary sm:text-5xl">Choose what you want to learn</h1>
                <p class="mt-4 max-w-2xl text-sm leading-7 text-sign-muted sm:mt-5 sm:text-lg sm:leading-8">Explore published SignGyaan subjects created and managed from the Admin Console.</p>
                <div class="mt-6 flex flex-wrap gap-2 text-xs text-sign-muted sm:mt-7 sm:gap-3 sm:text-sm">
                    <span class="rounded-full border border-sign-border bg-white px-3 py-2 sm:px-4">Visual lessons</span>
                    <span class="rounded-full border border-sign-border bg-white px-3 py-2 sm:px-4">ISL-supported learning</span>
                    <span class="rounded-full border border-sign-border bg-white px-3 py-2 sm:px-4">Practice activities</span>
                </div>
            </div>
        </x-container>
    </section>

    <section class="bg-white py-10 sm:py-14 lg:py-20">
        <x-container>
            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <x-section-heading title="Browse subjects" description="Start with a subject, then move through its published courses, units and lessons." />
                <a href="{{ route('learn') }}" class="inline-flex min-h-11 items-center gap-2 self-start text-sm font-semibold text-sign-primary transition hover:text-sign-cyan-dark sm:self-auto">View learning paths <span aria-hidden="true">→</span></a>
            </div>

            @php
                $subjectCodes = [
                    'english' => 'Aa',
                    'mathematics' => '123',
                    'science' => 'SCI',
                    'digital-skills' => 'PC',
                    'general-knowledge' => 'GK',
                    'life-skills' => 'LS',
                ];
            @endphp

            @if ($subjects->isNotEmpty())
                <div class="mt-8 grid gap-4 sm:mt-10 sm:grid-cols-2 lg:grid-cols-3 lg:gap-5">
                    @foreach ($subjects as $subject)
                        @php
                            $code = $subjectCodes[$subject->slug] ?? strtoupper(substr($subject->name, 0, 2));
                        @endphp
                        <article class="group flex h-full flex-col rounded-2xl border border-sign-border bg-white p-5 transition duration-200 hover:-translate-y-1 hover:border-sign-cyan hover:shadow-lg sm:rounded-3xl sm:p-6">
                            <div class="flex h-12 min-w-12 w-fit items-center justify-center rounded-xl bg-sign-light px-3 text-sm font-bold text-sign-primary">{{ $code }}</div>
                            <div class="mt-4 flex items-start justify-between gap-3 sm:mt-5">
                                <h2 class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">{{ $subject->name }}</h2>
                                <span class="shrink-0 rounded-full bg-sign-soft px-2.5 py-1 text-xs font-semibold text-sign-primary">{{ $subject->courses_count }} {{ $subject->courses_count === 1 ? 'course' : 'courses' }}</span>
                            </div>
                            <p class="mt-2 flex-1 text-sm leading-6 text-sign-muted sm:mt-3">{{ $subject->short_description ?: ($subject->description ?: 'Structured visual learning with clear courses and lessons.') }}</p>
                            <a href="{{ route('subjects.show', $subject->slug) }}" class="mt-5 inline-flex min-h-11 items-center gap-2 text-sm font-semibold text-sign-primary sm:mt-6">Explore {{ $subject->name }} <span class="transition group-hover:translate-x-1" aria-hidden="true">→</span></a>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="mt-10 rounded-2xl border border-dashed border-sign-border bg-sign-soft p-8 text-center sm:rounded-3xl sm:p-12">
                    <h2 class="font-heading text-2xl font-semibold text-sign-primary">No published subjects yet</h2>
                    <p class="mt-3 text-sm leading-6 text-sign-muted">Published subjects from the Admin Console will appear here automatically.</p>
                </div>
            @endif
        </x-container>
    </section>

    <section class="border-t border-sign-border bg-sign-soft py-10 sm:py-14 lg:py-20">
        <x-container>
            <div class="grid gap-8 lg:grid-cols-[0.8fr_1.2fr] lg:items-center">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark sm:text-sm">Learning structure</p>
                    <h2 class="mt-3 font-heading text-2xl font-semibold text-sign-primary sm:text-4xl">Learn in a clear sequence</h2>
                    <p class="mt-4 text-sm leading-7 text-sign-muted sm:text-base">SignGyaan organises learning so you always know what comes next.</p>
                </div>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @foreach (['Subject', 'Course', 'Unit', 'Lesson'] as $index => $label)
                        <div class="rounded-2xl border border-sign-border bg-white p-4 sm:p-5">
                            <span class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <p class="mt-2 font-semibold text-sign-primary">{{ $label }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-container>
    </section>
@endsection
