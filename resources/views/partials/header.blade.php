<header
    x-data="{ mobileMenuOpen: false }"
    @keydown.escape.window="mobileMenuOpen = false"
    class="sticky top-0 z-50 border-b border-sign-border bg-white/95 backdrop-blur"
>
    <x-container>

        <div class="flex h-16 items-center justify-between sm:h-20">

            {{-- Brand --}}
            <a
                href="{{ route('home') }}"
                class="flex items-center gap-2.5 rounded-lg sm:gap-3"
                aria-label="SignGyaan home"
            >
                <div
                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-sign-primary text-base font-bold text-white sm:h-10 sm:w-10 sm:text-lg"
                    aria-hidden="true"
                >
                    S
                </div>

                <span class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">
                    SignGyaan
                </span>
            </a>

            {{-- Desktop Navigation --}}
            <nav class="hidden items-center gap-7 lg:flex" aria-label="Primary navigation">

                <a
                    href="{{ route('learn') }}"
                    @class([
                        'rounded-md px-1 py-2 text-sm font-semibold transition',
                        'text-sign-primary' => request()->routeIs('learn'),
                        'text-sign-text hover:text-sign-primary' => ! request()->routeIs('learn'),
                    ])
                    @if (request()->routeIs('learn')) aria-current="page" @endif
                >
                    Learn
                </a>

                <a
                    href="{{ route('subjects') }}"
                    @class([
                        'rounded-md px-1 py-2 text-sm font-semibold transition',
                        'text-sign-primary' => request()->routeIs('subjects'),
                        'text-sign-text hover:text-sign-primary' => ! request()->routeIs('subjects'),
                    ])
                    @if (request()->routeIs('subjects')) aria-current="page" @endif
                >
                    Subjects
                </a>

                <a
                    href="{{ route('explore') }}"
                    @class([
                        'rounded-md px-1 py-2 text-sm font-semibold transition',
                        'text-sign-primary' => request()->routeIs('explore'),
                        'text-sign-text hover:text-sign-primary' => ! request()->routeIs('explore'),
                    ])
                    @if (request()->routeIs('explore')) aria-current="page" @endif
                >
                    Explore
                </a>

                <a
                    href="{{ route('about') }}"
                    @class([
                        'rounded-md px-1 py-2 text-sm font-semibold transition',
                        'text-sign-primary' => request()->routeIs('about'),
                        'text-sign-text hover:text-sign-primary' => ! request()->routeIs('about'),
                    ])
                    @if (request()->routeIs('about')) aria-current="page" @endif
                >
                    About
                </a>

            </nav>

            {{-- Desktop Actions --}}
            <div class="hidden items-center gap-3 lg:flex">

                <button
                    type="button"
                    class="min-h-11 rounded-lg px-4 py-2.5 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft"
                >
                    Search
                </button>

                <a
                    href="#"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg border border-sign-primary px-4 py-2.5 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft"
                >
                    Sign In
                </a>

            </div>

            {{-- Mobile Menu Button --}}
            <button
                type="button"
                @click="mobileMenuOpen = ! mobileMenuOpen"
                class="flex h-11 w-11 items-center justify-center rounded-lg text-sign-primary transition hover:bg-sign-soft lg:hidden"
                aria-label="Toggle navigation"
                aria-controls="mobile-navigation"
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
                    aria-hidden="true"
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
                    aria-hidden="true"
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
            id="mobile-navigation"
            x-show="mobileMenuOpen"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            @click.outside="mobileMenuOpen = false"
            class="border-t border-sign-border py-4 lg:hidden"
        >
            <nav class="flex flex-col gap-1" aria-label="Mobile navigation">

                <a
                    href="{{ route('learn') }}"
                    @click="mobileMenuOpen = false"
                    @class([
                        'rounded-lg px-4 py-3 text-sm font-semibold transition',
                        'bg-sign-soft text-sign-primary' => request()->routeIs('learn'),
                        'text-sign-text hover:bg-sign-soft hover:text-sign-primary' => ! request()->routeIs('learn'),
                    ])
                    @if (request()->routeIs('learn')) aria-current="page" @endif
                >
                    Learn
                </a>

                <a
                    href="{{ route('subjects') }}"
                    @click="mobileMenuOpen = false"
                    @class([
                        'rounded-lg px-4 py-3 text-sm font-semibold transition',
                        'bg-sign-soft text-sign-primary' => request()->routeIs('subjects'),
                        'text-sign-text hover:bg-sign-soft hover:text-sign-primary' => ! request()->routeIs('subjects'),
                    ])
                    @if (request()->routeIs('subjects')) aria-current="page" @endif
                >
                    Subjects
                </a>

                <a
                    href="{{ route('explore') }}"
                    @click="mobileMenuOpen = false"
                    @class([
                        'rounded-lg px-4 py-3 text-sm font-semibold transition',
                        'bg-sign-soft text-sign-primary' => request()->routeIs('explore'),
                        'text-sign-text hover:bg-sign-soft hover:text-sign-primary' => ! request()->routeIs('explore'),
                    ])
                    @if (request()->routeIs('explore')) aria-current="page" @endif
                >
                    Explore
                </a>

                <a
                    href="{{ route('about') }}"
                    @click="mobileMenuOpen = false"
                    @class([
                        'rounded-lg px-4 py-3 text-sm font-semibold transition',
                        'bg-sign-soft text-sign-primary' => request()->routeIs('about'),
                        'text-sign-text hover:bg-sign-soft hover:text-sign-primary' => ! request()->routeIs('about'),
                    ])
                    @if (request()->routeIs('about')) aria-current="page" @endif
                >
                    About
                </a>

                <div class="my-3 border-t border-sign-border"></div>

                <button
                    type="button"
                    class="min-h-11 rounded-lg px-4 py-3 text-left text-sm font-semibold text-sign-primary transition hover:bg-sign-soft"
                >
                    Search
                </button>

                <a
                    href="#"
                    class="inline-flex min-h-11 items-center rounded-lg px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft"
                >
                    Sign In
                </a>

            </nav>
        </div>

    </x-container>
</header>
