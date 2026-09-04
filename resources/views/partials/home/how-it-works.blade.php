<section class="bg-sign-soft py-16 sm:py-20 lg:py-24">
    <x-container>

        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">
                How SignGyaan Works
            </p>

            <h2 class="mt-3 font-heading text-3xl font-semibold tracking-tight text-sign-primary sm:text-4xl">
                A simple way to learn step by step
            </h2>

            <p class="mt-4 text-base leading-7 text-sign-muted sm:text-lg">
                Choose what you want to learn, study through clear visual lessons, and practise what you understood.
            </p>
        </div>

        <div class="mt-12 grid gap-6 md:grid-cols-3">

            {{-- Step 1 --}}
            <div class="relative rounded-3xl border border-sign-border bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sign-light text-sign-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25A8.966 8.966 0 0 1 18 3.75c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>

                    <span class="font-heading text-4xl font-semibold text-sign-light">01</span>
                </div>

                <h3 class="mt-6 font-heading text-xl font-semibold text-sign-primary">
                    Choose a subject
                </h3>

                <p class="mt-3 text-sm leading-6 text-sign-muted">
                    Browse subjects and learning paths, then select the topic you want to understand.
                </p>
            </div>

            {{-- Step 2 --}}
            <div class="relative rounded-3xl border border-sign-border bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sign-light text-sign-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h8.25A3.75 3.75 0 0 0 16.5 15V9a3.75 3.75 0 0 0-3.75-3.75H4.5A2.25 2.25 0 0 0 2.25 7.5v9A2.25 2.25 0 0 0 4.5 18.75Z" />
                        </svg>
                    </div>

                    <span class="font-heading text-4xl font-semibold text-sign-light">02</span>
                </div>

                <h3 class="mt-6 font-heading text-xl font-semibold text-sign-primary">
                    Learn visually
                </h3>

                <p class="mt-3 text-sm leading-6 text-sign-muted">
                    Watch ISL-first lessons and use simple notes, examples and visuals to understand each concept.
                </p>
            </div>

            {{-- Step 3 --}}
            <div class="relative rounded-3xl border border-sign-border bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sign-light text-sign-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>

                    <span class="font-heading text-4xl font-semibold text-sign-light">03</span>
                </div>

                <h3 class="mt-6 font-heading text-xl font-semibold text-sign-primary">
                    Practise and improve
                </h3>

                <p class="mt-3 text-sm leading-6 text-sign-muted">
                    Reinforce your learning with short practice activities, questions and revision.
                </p>
            </div>

        </div>

        <div class="mt-10 flex justify-center">
            <x-button href="{{ route('learn') }}">
                Start Learning
            </x-button>
        </div>

    </x-container>
</section>
