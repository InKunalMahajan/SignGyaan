<footer class="bg-sign-dark text-white">
    <x-container class="py-14 sm:py-16">

        <div class="grid gap-10 lg:grid-cols-12 lg:gap-12">

            {{-- Brand --}}
            <div class="lg:col-span-5">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-lg font-bold text-sign-primary">
                        S
                    </div>

                    <span class="font-heading text-2xl font-semibold text-white">
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
                        <a href="{{ route('learn') }}" class="text-white/70 transition hover:text-white">
                            Learn
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('subjects') }}" class="text-white/70 transition hover:text-white">
                            Subjects
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('explore') }}" class="text-white/70 transition hover:text-white">
                            Explore
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
                        <a href="{{ route('about') }}" class="text-white/70 transition hover:text-white">
                            About
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('home') }}" class="text-white/70 transition hover:text-white">
                            Home
                        </a>
                    </li>
                </ul>
            </div>

        </div>

        <div class="mt-12 flex flex-col gap-4 border-t border-white/10 pt-6 text-sm text-white/60 sm:flex-row sm:items-center sm:justify-between">
            <p>
                © {{ date('Y') }} SignGyaan. All rights reserved.
            </p>

            <p>
                Learn visually. Learn clearly.
            </p>
        </div>

    </x-container>
</footer>
