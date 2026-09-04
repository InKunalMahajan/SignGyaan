@extends('layouts.app')

@section('title', 'SignGyaan - Learn Visually')

@section('description', 'Accessible visual learning through Indian Sign Language.')

@section('content')

    <section class="overflow-hidden bg-white py-16 sm:py-20 lg:py-28">
        <x-container>

            <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">

                {{-- Hero Content --}}
                <div class="max-w-2xl">

                    <div
                        class="inline-flex items-center rounded-full bg-sign-soft px-4 py-2 text-sm font-semibold text-sign-primary"
                    >
                        Visual learning through Indian Sign Language
                    </div>

                    <h1
                        class="mt-6 font-heading text-4xl font-semibold leading-tight tracking-tight text-sign-primary sm:text-5xl lg:text-6xl"
                    >
                        Learn visually.
                        <span class="text-sign-cyan-dark">Learn clearly.</span>
                    </h1>

                    <p class="mt-6 max-w-xl text-base leading-7 text-sign-muted sm:text-lg sm:leading-8">
                        SignGyaan makes learning simple, visual and accessible with structured lessons designed for Deaf learners.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <x-button href="{{ route('learn') }}">
                            Start Learning
                        </x-button>

                        <x-button href="{{ route('subjects') }}" class="border border-sign-primary bg-white text-sign-primary hover:bg-sign-soft">
                            Explore Subjects
                        </x-button>
                    </div>

                    <div class="mt-8 flex flex-wrap gap-x-6 gap-y-3 text-sm text-sign-muted">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-sign-cyan"></span>
                            ISL-first learning
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-sign-cyan"></span>
                            Simple explanations
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-sign-cyan"></span>
                            Practice-focused
                        </div>
                    </div>

                </div>

                {{-- Hero Visual --}}
                <div class="relative">

                    <div class="absolute -left-6 -top-6 h-28 w-28 rounded-full bg-sign-light blur-2xl"></div>
                    <div class="absolute -bottom-6 -right-6 h-32 w-32 rounded-full bg-sign-light blur-2xl"></div>

                    <div class="relative overflow-hidden rounded-3xl border border-sign-border bg-sign-soft p-5 shadow-sm sm:p-7">

                        <div class="rounded-2xl bg-white p-4 shadow-sm sm:p-5">

                            <div class="flex aspect-video items-center justify-center rounded-xl bg-sign-primary">
                                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        class="ml-1 h-7 w-7 text-sign-primary"
                                        aria-hidden="true"
                                    >
                                        <path d="M8.25 5.433c0-1.178 1.296-1.896 2.295-1.272l9.067 5.666c.94.588.94 1.958 0 2.546l-9.067 5.666c-.999.624-2.295-.094-2.295-1.272V5.433Z" />
                                    </svg>
                                </div>
                            </div>

                            <div class="mt-5">
                                <div class="h-3 w-24 rounded-full bg-sign-light"></div>
                                <div class="mt-3 h-4 w-3/4 rounded-full bg-sign-primary/15"></div>
                                <div class="mt-2 h-4 w-1/2 rounded-full bg-sign-primary/10"></div>
                            </div>

                        </div>

                        <div class="mt-4 grid grid-cols-3 gap-3">
                            <div class="rounded-xl bg-white p-3 text-center shadow-sm">
                                <div class="mx-auto h-8 w-8 rounded-full bg-sign-light"></div>
                                <p class="mt-2 text-xs font-semibold text-sign-primary">Watch</p>
                            </div>

                            <div class="rounded-xl bg-white p-3 text-center shadow-sm">
                                <div class="mx-auto h-8 w-8 rounded-full bg-sign-light"></div>
                                <p class="mt-2 text-xs font-semibold text-sign-primary">Learn</p>
                            </div>

                            <div class="rounded-xl bg-white p-3 text-center shadow-sm">
                                <div class="mx-auto h-8 w-8 rounded-full bg-sign-light"></div>
                                <p class="mt-2 text-xs font-semibold text-sign-primary">Practice</p>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </x-container>
    </section>

@endsection
