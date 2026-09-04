<header x-data="{ mobileMenuOpen: false }" class="border-b border-gray-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="flex h-18 items-center justify-between">

            {{-- Logo --}}
            <div class="flex items-center">

                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-sign-primary text-lg font-bold text-white">
                        S
                    </div>

                    <span class="font-heading text-2xl font-semibold text-sign-primary">
                        SignGyaan
                    </span>
                </a>

            </div>


            {{-- Desktop Navigation --}}
            <nav class="hidden items-center gap-8 md:flex">

                <a href="{{ route('learn') }}" class="text-sm font-medium text-sign-text transition hover:text-sign-primary">
                    Learn
                </a>

                <a href="{{ route('subjects') }}" class="text-sm font-medium text-sign-text transition hover:text-sign-primary">
                    Subjects
                </a>

                <a href="{{ route('explore') }}" class="text-sm font-medium text-sign-text transition hover:text-sign-primary">
                    Explore
                </a>

                <a href="{{ route('about') }}" class="text-sm font-medium text-sign-text transition hover:text-sign-primary">
                    About
                </a>

            </nav>


            {{-- Desktop Actions --}}
            <div class="hidden items-center gap-4 md:flex">

                <button type="button" class="rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">
                    Search
                </button>

                <a href="#" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold">
                    Sign In
                </a>

            </div>


            {{-- Mobile Menu Button --}}
            <button type="button" class="inline-flex items-center justify-center rounded-lg p-2 md:hidden"
                @click="mobileMenuOpen = ! mobileMenuOpen" aria-label="Open navigation menu">

                <svg x-show="! mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>

                <svg x-show="mobileMenuOpen" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>

            </button>

        </div>


        {{-- Mobile Navigation --}}
        <div x-show="mobileMenuOpen" x-cloak class="border-t border-gray-100 py-4 md:hidden">

            <nav class="flex flex-col gap-1">

                <a href="{{ route('learn') }}" class="rounded-lg px-3 py-3 text-sm font-medium hover:bg-gray-100">
                    Learn
                </a>

                <a href="{{ route('subjects') }}" class="rounded-lg px-3 py-3 text-sm font-medium hover:bg-gray-100">
                    Subjects
                </a>

                <a href="{{ route('explore') }}" class="rounded-lg px-3 py-3 text-sm font-medium hover:bg-gray-100">
                    Explore
                </a>

                <a href="{{ route('about') }}" class="rounded-lg px-3 py-3 text-sm font-medium hover:bg-gray-100">
                    About
                </a>

                <hr class="my-2 border-gray-200">

                <button type="button" class="rounded-lg px-3 py-3 text-left text-sm font-medium hover:bg-gray-100">
                    Search
                </button>

                <a href="#" class="rounded-lg px-3 py-3 text-sm font-medium hover:bg-gray-100">
                    Sign In
                </a>

            </nav>

        </div>

    </div>
</header>