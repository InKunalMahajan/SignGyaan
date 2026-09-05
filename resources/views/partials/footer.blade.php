<footer class="bg-sign-dark text-white">
    <x-container class="py-12 sm:py-16">

        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-12 lg:gap-12">

            {{-- Brand --}}
            <div class="sm:col-span-2 lg:col-span-5">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 rounded-lg" aria-label="SignGyaan home">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-base font-bold text-sign-primary sm:h-11 sm:w-11 sm:text-lg" aria-hidden="true">
                        S
                    </div>

                    <span class="font-heading text-xl font-semibold text-white sm:text-2xl">
                        SignGyaan
                    </span>
                </a>

                <p class="mt-5 max-w-md text-sm leading-7 text-white/70">
                    Accessible visual learning through Indian Sign Language, simple explanations and structured practice.
                </p>

                <div class="mt-6 flex flex-wrap gap-2">
                    <span class="rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-semibold text-white/80">
                        ISL-first learning
                    </span>
                    <span class="rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-semibold text-white/80">
                        Visual lessons
                    </span>
                    <span class="rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-semibold text-white/80">
                        Practice focused
                    </span>
                </div>
            </div>

            {{-- Learning --}}
            <div class="lg:col-span-3 lg:col-start-8">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-sign-cyan">
                    Learning
                </h2>

                <ul class="mt-5 space-y-3 text-sm">
                    <li>
                        <a href="{{ route('learn') }}" class="inline-flex rounded-md py-1 text-white/70 transition hover:text-white">
                            Learn
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('subjects') }}" class="inline-flex rounded-md py-1 text-white/70 transition hover:text-white">
                            Subjects
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('explore') }}" class="inline-flex rounded-md py-1 text-white/70 transition hover:text-white">
                            Explore
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('vocabulary.index') }}" class="inline-flex rounded-md py-1 text-white/70 transition hover:text-white">
                            ISL Vocabulary
                        </a>
                    </li>
                </ul>
            </div>

            {{-- SignGyaan --}}
            <div class="lg:col-span-2">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-sign-cyan">
                    SignGyaan
                </h2>

                <ul class="mt-5 space-y-3 text-sm">
                    <li>
                        <a href="{{ route('about') }}" class="inline-flex rounded-md py-1 text-white/70 transition hover:text-white">
                            About
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('home') }}" class="inline-flex rounded-md py-1 text-white/70 transition hover:text-white">
                            Home
                        </a>
                    </li>
                </ul>
            </div>

        </div>

        <div class="mt-10 flex flex-col gap-3 border-t border-white/10 pt-6 text-center text-sm text-white/60 sm:mt-12 sm:flex-row sm:items-center sm:justify-between sm:text-left">
            <p>
                © {{ date('Y') }} SignGyaan. All rights reserved.
            </p>

            <p>
                Learn visually. Learn clearly.
            </p>
        </div>

    </x-container>
</footer>
