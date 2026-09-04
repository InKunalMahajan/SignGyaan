@extends('layouts.app')


@section('title', 'SignGyaan')


@section('content')

    <section class="py-24">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="mx-auto max-w-3xl text-center">


                <p class="text-sm font-semibold uppercase tracking-wider text-gray-500">
                    SignGyaan
                </p>

                <h1
                    class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl"
                >
                    Learn visually.
                    Learn clearly.
                </h1>

                <p
                    class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-gray-600"
                >
                    Accessible education designed around visual learning
                    and Indian Sign Language.
                </p>

                <div
                    class="mt-8 flex flex-col justify-center gap-3 sm:flex-row"
                >

                    <a
                        href="{{ route('learn') }}"
                        class="rounded-lg bg-gray-950 px-6 py-3 text-sm font-semibold text-white"
                    >
                        Start Learning
                    </a>

                    <a
                        href="{{ route('subjects') }}"
                        class="rounded-lg border border-gray-300 px-6 py-3 text-sm font-semibold"
                    >
                        Explore Subjects
                    </a>

                </div>

            </div>

        </div>

    </section>

@endsection