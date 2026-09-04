<section class="bg-white py-16 sm:py-20 lg:py-24">
    <x-container>

        <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">

            {{-- ISL Visual --}}
            <div class="relative order-2 lg:order-1">
                <div class="absolute -left-5 -top-5 h-28 w-28 rounded-full bg-sign-light blur-2xl"></div>
                <div class="absolute -bottom-6 -right-6 h-32 w-32 rounded-full bg-sign-light blur-2xl"></div>

                <div class="relative overflow-hidden rounded-3xl border border-sign-border bg-sign-soft p-5 shadow-sm sm:p-7">
                    <div class="overflow-hidden rounded-2xl bg-sign-primary">
                        <div class="flex aspect-video items-center justify-center p-8">
                            <div class="text-center text-white">
                                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-10 w-10" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h8.25A3.75 3.75 0 0 0 16.5 15V9a3.75 3.75 0 0 0-3.75-3.75H4.5A2.25 2.25 0 0 0 2.25 7.5v9A2.25 2.25 0 0 0 4.5 18.75Z" />
                                    </svg>
                                </div>

                                <p class="mt-5 text-sm font-semibold uppercase tracking-wider text-white/80">
                                    Indian Sign Language
                                </p>
                                <p class="mt-2 font-heading text-2xl font-semibold">
                                    Visual lessons made easier to follow
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-xl bg-white p-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">Watch</p>
                            <p class="mt-1 text-sm font-semibold text-sign-primary">ISL video lessons</p>
                        </div>

                        <div class="rounded-xl bg-white p-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">Read</p>
                            <p class="mt-1 text-sm font-semibold text-sign-primary">Simple visual notes</p>
                        </div>

                        <div class="rounded-xl bg-white p-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">Practise</p>
                            <p class="mt-1 text-sm font-semibold text-sign-primary">Short activities</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ISL Content --}}
            <div class="order-1 lg:order-2">
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">
                    ISL Learning
                </p>

                <h2 class="mt-3 font-heading text-3xl font-semibold tracking-tight text-sign-primary sm:text-4xl lg:text-5xl">
                    Learn with Indian Sign Language at the centre
                </h2>

                <p class="mt-5 max-w-xl text-base leading-7 text-sign-muted sm:text-lg sm:leading-8">
                    SignGyaan combines visual explanations, Indian Sign Language videos and simple written notes so learners can move through each concept with greater clarity.
                </p>

                <div class="mt-8 space-y-5">
                    <div class="flex gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-sign-primary">Visual-first explanations</h3>
                            <p class="mt-1 text-sm leading-6 text-sign-muted">Concepts are presented in a clear visual format instead of depending only on long text.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-sign-primary">ISL-supported lessons</h3>
                            <p class="mt-1 text-sm leading-6 text-sign-muted">Lessons can include Indian Sign Language video alongside notes, examples and key points.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-sign-primary">Learn at your own pace</h3>
                            <p class="mt-1 text-sm leading-6 text-sign-muted">Revisit lessons, review notes and practise again whenever you need more time.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <x-button href="{{ route('learn') }}">
                        Explore ISL Learning
                    </x-button>
                </div>
            </div>

        </div>

    </x-container>
</section>
