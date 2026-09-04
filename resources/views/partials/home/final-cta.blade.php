<section class="bg-white py-16 sm:py-20 lg:py-24">
    <x-container>

        <div class="relative overflow-hidden rounded-3xl bg-sign-dark px-6 py-12 sm:px-10 sm:py-14 lg:px-14 lg:py-16">

            {{-- Decorative accents --}}
            <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full bg-sign-cyan/20 blur-2xl"></div>
            <div class="absolute -bottom-20 -left-16 h-52 w-52 rounded-full bg-sign-cyan/10 blur-2xl"></div>

            <div class="relative grid items-center gap-10 lg:grid-cols-[1fr_auto] lg:gap-16">

                <div class="max-w-3xl">
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan">
                        Start your learning journey
                    </p>

                    <h2 class="mt-3 font-heading text-3xl font-semibold tracking-tight text-white sm:text-4xl lg:text-5xl">
                        Learn at your own pace with clear, visual lessons.
                    </h2>

                    <p class="mt-5 max-w-2xl text-base leading-7 text-white/75 sm:text-lg">
                        Choose a subject, follow structured lessons, learn through Indian Sign Language support, and practise step by step.
                    </p>

                    <div class="mt-7 flex flex-wrap gap-x-6 gap-y-3 text-sm text-white/80">
                        <span class="inline-flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-sign-cyan"></span>
                            Visual-first learning
                        </span>

                        <span class="inline-flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-sign-cyan"></span>
                            Structured lessons
                        </span>

                        <span class="inline-flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-sign-cyan"></span>
                            Learn anytime
                        </span>
                    </div>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row lg:flex-col">
                    <a
                        href="{{ route('learn') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-white px-6 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft focus:outline-none focus:ring-2 focus:ring-sign-cyan focus:ring-offset-2 focus:ring-offset-sign-dark"
                    >
                        Start Learning
                    </a>

                    <a
                        href="{{ route('subjects') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-white/40 px-6 py-3 text-sm font-semibold text-white transition hover:border-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-sign-cyan focus:ring-offset-2 focus:ring-offset-sign-dark"
                    >
                        Explore Subjects
                    </a>
                </div>

            </div>

        </div>

    </x-container>
</section>
