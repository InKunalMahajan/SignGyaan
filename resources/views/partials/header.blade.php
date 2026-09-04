<header
    x-data="{ mobileMenuOpen: false }"
    class="sticky top-0 z-50 border-b border-sign-border bg-white/95 backdrop-blur"
>
    <x-container>

        <div class="flex h-20 items-center justify-between">

            {{-- Brand --}}
            <a
                href="{{ route('home') }}"
                class="flex items-center gap-3"
            >
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-sign-primary text-lg font-bold text-white"
                >
                    S
                </div>

                <span class="font-heading text-2xl font-semibold text-sign-primary">
                    SignGyaan
                </span>
            </a>


            {{-- Desktop Navigation --}}
            <nav class="hidden items-center gap-8 lg:flex">

                <a
                    href="{{ route('learn') }}"
                    class="text-sm font-medium text-sign-text transition hover:text-sign-primary"
                >
                    Learn
                </a>

                <a
                    href="{{ route('subjects') }}"
                    class="text-sm font-medium text-sign-text transition hover:text-sign-primary"
                >
                    Subjects
                </a>

                <a
                    href="{{ route('explore') }}"
                    class="text-sm font-medium text-sign-text transition hover:text-sign-primary"
                >
                    Explore
                </a>

                <a
                    href="{{ route('about') }}"
                    class="text-sm font-medium text-sign-text transition hover:text-sign-primary"
                >
                    About
                </a>

            </nav>


            {{-- Desktop Actions --}}
            <div class="hidden items-center gap-3 lg:flex">

                <button
                    type="button"
                    class="rounded-lg px-4 py-2.5 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft focus:outline-none focus:ring-2 focus:ring-sign-cyan focus:ring-offset-2"
                >
                    Search
                </button>

                <a
                    href="#"
                    class="inline-flex items-center justify-center rounded-lg border border-sign-primary px-4 py-2.5 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft focus:outline-none focus:ring-2 focus:ring-sign-cyan focus:ring-offset-2"
                >
                    Sign In
                </a>

            </div>


            {{-- Mobile Menu Button --}}
            <button
                type="button"
                @click="mobileMenuOpen = ! mobileMenuOpen"
                class="flex h-11 w-11 items-center justify-center rounded-lg text-sign-primary transition hover:bg-sign-soft focus:outline-none focus:ring-2 focus:ring-sign-cyan focus:ring-offset-2 lg:hidden"
                aria-label="Toggle navigation"
                :aria-expanded="mobileMenuOpen"
            >
                <svg
                    x-show="! mobileMenuOpen"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                    stroke="currentColor"
                    class="h-6 w-6"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>

                <svg
                    x-show="mobileMenuOpen"
                    x-cloak
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                    stroke="currentColor"
                    class="h-6 w-6"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6 18 18 6M6 6l12 12"
                    />
                </svg>
            </button>

        </div>


        {{-- Mobile Navigation --}}
        <div
            x-show="mobileMenuOpen"
            x-cloak
            class="border-t border-sign-border py-4 lg:hidden"
        >
            <nav class="flex flex-col gap-1">

                <a
                    href="{{ route('learn') }}"
                    class="rounded-lg px-4 py-3 text-sm font-medium text-sign-text transition hover:bg-sign-soft hover:text-sign-primary"
                >
                    Learn
                </a>

                <a
                    href="{{ route('subjects') }}"
                    class="rounded-lg px-4 py-3 text-sm font-medium text-sign-text transition hover:bg-sign-soft hover:text-sign-primary"
                >
                    Subjects
                </a>

                <a
                    href="{{ route('explore') }}"
                    class="rounded-lg px-4 py-3 text-sm font-medium text-sign-text transition hover:bg-sign-soft hover:text-sign-primary"
                >
                    Explore
                </a>

                <a
                    href="{{ route('about') }}"
                    class="rounded-lg px-4 py-3 text-sm font-medium text-sign-text transition hover:bg-sign-soft hover:text-sign-primary"
                >
                    About
                </a>

                <div class="my-3 border-t border-sign-border"></div>

                <button
                    type="button"
                    class="rounded-lg px-4 py-3 text-left text-sm font-semibold text-sign-primary transition hover:bg-sign-soft"
                >
                    Search
                </button>

                <a
                    href="#"
                    class="rounded-lg px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft"
                >
                    Sign In
                </a>

            </nav>
        </div>

    </x-container>
</header>
