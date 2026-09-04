<header
    x-data="{ mobileMenuOpen: false, searchOpen: false, accountOpen: false }"
    @keydown.escape.window="mobileMenuOpen = false; searchOpen = false; accountOpen = false"
    class="sticky top-0 z-50 border-b border-sign-border bg-white/95 backdrop-blur"
>
    <x-container>

        <div class="flex h-16 items-center justify-between sm:h-20">

            {{-- Brand --}}
            <a
                href="{{ route('home') }}"
                class="flex shrink-0 items-center gap-2.5 rounded-lg sm:gap-3"
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
            <nav class="hidden items-center gap-5 lg:flex xl:gap-7" aria-label="Primary navigation">

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
                        'text-sign-primary' => request()->routeIs('subjects', 'subjects.show', 'courses.show'),
                        'text-sign-text hover:text-sign-primary' => ! request()->routeIs('subjects', 'subjects.show', 'courses.show'),
                    ])
                    @if (request()->routeIs('subjects', 'subjects.show', 'courses.show')) aria-current="page" @endif
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
            <div class="relative hidden items-center gap-2 lg:flex xl:gap-3">

                <button
                    type="button"
                    @click="searchOpen = ! searchOpen; accountOpen = false; mobileMenuOpen = false; if (searchOpen) { $nextTick(() => $refs.desktopSearch.focus()) }"
                    @class([
                        'inline-flex min-h-11 items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold transition xl:px-4',
                        'bg-sign-soft text-sign-primary' => request()->routeIs('search'),
                        'text-sign-primary hover:bg-sign-soft' => ! request()->routeIs('search'),
                    ])
                    :aria-expanded="searchOpen"
                    aria-controls="desktop-global-search"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.4a6.75 6.75 0 1 1-13.5 0 6.75 6.75 0 0 1 13.5 0Z" />
                    </svg>
                    Search
                </button>

                @guest
                    <a
                        href="{{ route('login') }}"
                        @class([
                            'inline-flex min-h-11 items-center justify-center rounded-lg border px-3 py-2.5 text-sm font-semibold transition xl:px-4',
                            'border-sign-primary bg-sign-soft text-sign-primary' => request()->routeIs('login'),
                            'border-sign-primary text-sign-primary hover:bg-sign-soft' => ! request()->routeIs('login'),
                        ])
                    >
                        Sign In
                    </a>

                    <a
                        href="{{ route('register') }}"
                        class="hidden min-h-11 items-center justify-center rounded-lg bg-sign-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sign-dark xl:inline-flex"
                    >
                        Create Account
                    </a>
                @endguest

                @auth
                    @php
                        $headerUser = auth()->user();
                        $headerInitial = strtoupper(substr(trim($headerUser->name), 0, 1));
                    @endphp

                    <div class="relative">
                        <button
                            x-ref="accountButton"
                            type="button"
                            @click="accountOpen = ! accountOpen; searchOpen = false; mobileMenuOpen = false"
                            @class([
                                'inline-flex min-h-11 items-center gap-2 rounded-xl border px-2.5 py-2 text-sm font-semibold transition xl:px-3',
                                'border-sign-primary bg-sign-soft text-sign-primary' => request()->routeIs('dashboard', 'my-learning', 'profile'),
                                'border-sign-border bg-white text-sign-primary hover:border-sign-cyan hover:bg-sign-soft' => ! request()->routeIs('dashboard', 'my-learning', 'profile'),
                            ])
                            :aria-expanded="accountOpen"
                            aria-controls="desktop-account-menu"
                            aria-label="Open account menu"
                        >
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sign-primary font-heading text-sm font-semibold text-white" aria-hidden="true">
                                {{ $headerInitial }}
                            </span>
                            <span class="hidden max-w-28 truncate xl:inline">{{ $headerUser->name }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 shrink-0 transition" :class="accountOpen ? 'rotate-180' : ''" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>

                        <div
                            id="desktop-account-menu"
                            x-show="accountOpen"
                            x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-1"
                            @click.outside="accountOpen = false"
                            class="absolute right-0 top-full mt-3 w-72 overflow-hidden rounded-2xl border border-sign-border bg-white shadow-xl"
                        >
                            <div class="border-b border-sign-border bg-sign-soft px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sign-primary font-heading text-base font-semibold text-white" aria-hidden="true">
                                        {{ $headerInitial }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-sign-primary">{{ $headerUser->name }}</p>
                                        <p class="mt-0.5 truncate text-xs text-sign-muted">{{ $headerUser->email }}</p>
                                    </div>
                                </div>
                            </div>

                            <nav class="p-2" aria-label="Account navigation">
                                <a
                                    href="{{ route('dashboard') }}"
                                    @class([
                                        'flex min-h-11 items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold transition',
                                        'bg-sign-soft text-sign-primary' => request()->routeIs('dashboard'),
                                        'text-sign-text hover:bg-sign-soft hover:text-sign-primary' => ! request()->routeIs('dashboard'),
                                    ])
                                    @if (request()->routeIs('dashboard')) aria-current="page" @endif
                                >
                                    <span>Dashboard</span>
                                    <span aria-hidden="true">→</span>
                                </a>

                                <a
                                    href="{{ route('my-learning') }}"
                                    @class([
                                        'flex min-h-11 items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold transition',
                                        'bg-sign-soft text-sign-primary' => request()->routeIs('my-learning'),
                                        'text-sign-text hover:bg-sign-soft hover:text-sign-primary' => ! request()->routeIs('my-learning'),
                                    ])
                                    @if (request()->routeIs('my-learning')) aria-current="page" @endif
                                >
                                    <span>My Learning</span>
                                    <span aria-hidden="true">→</span>
                                </a>

                                <a
                                    href="{{ route('profile') }}"
                                    @class([
                                        'flex min-h-11 items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold transition',
                                        'bg-sign-soft text-sign-primary' => request()->routeIs('profile'),
                                        'text-sign-text hover:bg-sign-soft hover:text-sign-primary' => ! request()->routeIs('profile'),
                                    ])
                                    @if (request()->routeIs('profile')) aria-current="page" @endif
                                >
                                    <span>Profile & Account</span>
                                    <span aria-hidden="true">→</span>
                                </a>
                            </nav>

                            <div class="border-t border-sign-border p-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex min-h-11 w-full items-center justify-between rounded-xl px-3 py-2.5 text-left text-sm font-semibold text-sign-muted transition hover:bg-sign-soft hover:text-sign-primary">
                                        <span>Sign Out</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3-3H9m9.75 0-3-3m3 3-3 3" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endauth

                {{-- Desktop Global Search --}}
                <div
                    id="desktop-global-search"
                    x-show="searchOpen"
                    x-cloak
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1"
                    @click.outside="searchOpen = false"
                    class="absolute right-0 top-full mt-3 max-h-[calc(100vh-7rem)] w-[32rem] max-w-[calc(100vw-2rem)] overflow-y-auto overscroll-contain rounded-2xl border border-sign-border bg-white p-4 shadow-xl"
                >
                    <form method="GET" action="{{ route('search') }}" role="search">
                        <label for="desktop-search-input" class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">
                            Search SignGyaan
                        </label>

                        <div class="mt-3 flex items-center gap-2 rounded-xl border border-sign-border bg-sign-soft p-2 focus-within:border-sign-cyan">
                            <div class="flex min-w-0 flex-1 items-center gap-3 px-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 shrink-0 text-sign-muted" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.4a6.75 6.75 0 1 1-13.5 0 6.75 6.75 0 0 1 13.5 0Z" />
                                </svg>

                                <input
                                    x-ref="desktopSearch"
                                    id="desktop-search-input"
                                    type="search"
                                    name="q"
                                    value="{{ request()->routeIs('search') ? request('q') : '' }}"
                                    placeholder="Search subjects, courses, lessons..."
                                    autocomplete="off"
                                    class="min-h-11 w-full min-w-0 border-0 bg-transparent text-sm text-sign-text outline-none placeholder:text-sign-muted/70"
                                >
                            </div>

                            <button
                                type="submit"
                                class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-lg bg-sign-primary px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-sign-dark"
                            >
                                Search
                            </button>
                        </div>

                        <div class="mt-3 flex flex-wrap gap-x-4 gap-y-2 text-xs text-sign-muted">
                            <span>Try:</span>
                            <a href="{{ route('search', ['q' => 'computer']) }}" class="font-semibold text-sign-primary hover:text-sign-cyan-dark">Computer</a>
                            <a href="{{ route('search', ['q' => 'english']) }}" class="font-semibold text-sign-primary hover:text-sign-cyan-dark">English</a>
                            <a href="{{ route('search', ['q' => 'ISL']) }}" class="font-semibold text-sign-primary hover:text-sign-cyan-dark">ISL</a>
                        </div>
                    </form>
                </div>

            </div>

            {{-- Mobile Menu Button --}}
            <button
                type="button"
                @click="mobileMenuOpen = ! mobileMenuOpen; searchOpen = false; accountOpen = false"
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg text-sign-primary transition hover:bg-sign-soft lg:hidden"
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
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
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
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
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
            class="max-h-[calc(100vh-4rem)] overflow-y-auto overscroll-contain border-t border-sign-border py-4 lg:hidden"
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
                >Learn</a>

                <a
                    href="{{ route('subjects') }}"
                    @click="mobileMenuOpen = false"
                    @class([
                        'rounded-lg px-4 py-3 text-sm font-semibold transition',
                        'bg-sign-soft text-sign-primary' => request()->routeIs('subjects', 'subjects.show', 'courses.show'),
                        'text-sign-text hover:bg-sign-soft hover:text-sign-primary' => ! request()->routeIs('subjects', 'subjects.show', 'courses.show'),
                    ])
                    @if (request()->routeIs('subjects', 'subjects.show', 'courses.show')) aria-current="page" @endif
                >Subjects</a>

                <a
                    href="{{ route('explore') }}"
                    @click="mobileMenuOpen = false"
                    @class([
                        'rounded-lg px-4 py-3 text-sm font-semibold transition',
                        'bg-sign-soft text-sign-primary' => request()->routeIs('explore'),
                        'text-sign-text hover:bg-sign-soft hover:text-sign-primary' => ! request()->routeIs('explore'),
                    ])
                    @if (request()->routeIs('explore')) aria-current="page" @endif
                >Explore</a>

                <a
                    href="{{ route('about') }}"
                    @click="mobileMenuOpen = false"
                    @class([
                        'rounded-lg px-4 py-3 text-sm font-semibold transition',
                        'bg-sign-soft text-sign-primary' => request()->routeIs('about'),
                        'text-sign-text hover:bg-sign-soft hover:text-sign-primary' => ! request()->routeIs('about'),
                    ])
                    @if (request()->routeIs('about')) aria-current="page" @endif
                >About</a>

                <div class="my-3 border-t border-sign-border"></div>

                {{-- Mobile Global Search --}}
                <form method="GET" action="{{ route('search') }}" role="search" class="min-w-0 px-1 pb-2">
                    <label for="mobile-search-input" class="sr-only">Search SignGyaan</label>
                    <div class="flex items-center gap-2 rounded-xl border border-sign-border bg-sign-soft p-2 focus-within:border-sign-cyan">
                        <div class="flex min-w-0 flex-1 items-center gap-2 px-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 shrink-0 text-sign-muted" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.4a6.75 6.75 0 1 1-13.5 0 6.75 6.75 0 0 1 13.5 0Z" />
                            </svg>
                            <input
                                id="mobile-search-input"
                                type="search"
                                name="q"
                                value="{{ request()->routeIs('search') ? request('q') : '' }}"
                                placeholder="Search learning..."
                                autocomplete="off"
                                class="min-h-11 w-full min-w-0 border-0 bg-transparent text-sm text-sign-text outline-none placeholder:text-sign-muted/70"
                            >
                        </div>

                        <button
                            type="submit"
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-sign-primary text-white transition hover:bg-sign-dark"
                            aria-label="Search"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.4a6.75 6.75 0 1 1-13.5 0 6.75 6.75 0 0 1 13.5 0Z" />
                            </svg>
                        </button>
                    </div>
                </form>

                @guest
                    <div class="mt-2 grid gap-2 sm:grid-cols-2">
                        <a
                            href="{{ route('login') }}"
                            @click="mobileMenuOpen = false"
                            class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-primary px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft"
                        >
                            Sign In
                        </a>
                        <a
                            href="{{ route('register') }}"
                            @click="mobileMenuOpen = false"
                            class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-4 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark"
                        >
                            Create Account
                        </a>
                    </div>
                @endguest

                @auth
                    @php
                        $mobileUser = auth()->user();
                        $mobileInitial = strtoupper(substr(trim($mobileUser->name), 0, 1));
                    @endphp

                    <div class="mt-2 rounded-2xl border border-sign-border bg-sign-soft p-3">
                        <div class="flex items-center gap-3 px-2 py-2">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sign-primary font-heading text-base font-semibold text-white" aria-hidden="true">
                                {{ $mobileInitial }}
                            </span>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-sign-primary">{{ $mobileUser->name }}</p>
                                <p class="mt-0.5 truncate text-xs text-sign-muted">{{ $mobileUser->email }}</p>
                            </div>
                        </div>

                        <nav class="mt-2 space-y-1" aria-label="Mobile account navigation">
                            <a
                                href="{{ route('dashboard') }}"
                                @click="mobileMenuOpen = false"
                                @class([
                                    'flex min-h-11 items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold transition',
                                    'bg-white text-sign-primary' => request()->routeIs('dashboard'),
                                    'text-sign-text hover:bg-white hover:text-sign-primary' => ! request()->routeIs('dashboard'),
                                ])
                                @if (request()->routeIs('dashboard')) aria-current="page" @endif
                            ><span>Dashboard</span><span aria-hidden="true">→</span></a>

                            <a
                                href="{{ route('my-learning') }}"
                                @click="mobileMenuOpen = false"
                                @class([
                                    'flex min-h-11 items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold transition',
                                    'bg-white text-sign-primary' => request()->routeIs('my-learning'),
                                    'text-sign-text hover:bg-white hover:text-sign-primary' => ! request()->routeIs('my-learning'),
                                ])
                                @if (request()->routeIs('my-learning')) aria-current="page" @endif
                            ><span>My Learning</span><span aria-hidden="true">→</span></a>

                            <a
                                href="{{ route('profile') }}"
                                @click="mobileMenuOpen = false"
                                @class([
                                    'flex min-h-11 items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold transition',
                                    'bg-white text-sign-primary' => request()->routeIs('profile'),
                                    'text-sign-text hover:bg-white hover:text-sign-primary' => ! request()->routeIs('profile'),
                                ])
                                @if (request()->routeIs('profile')) aria-current="page" @endif
                            ><span>Profile & Account</span><span aria-hidden="true">→</span></a>
                        </nav>

                        <form method="POST" action="{{ route('logout') }}" class="mt-2 border-t border-sign-border pt-2">
                            @csrf
                            <button type="submit" class="flex min-h-11 w-full items-center justify-between rounded-xl px-3 py-2.5 text-left text-sm font-semibold text-sign-muted transition hover:bg-white hover:text-sign-primary">
                                <span>Sign Out</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3-3H9m9.75 0-3-3m3 3-3 3" />
                                </svg>
                            </button>
                        </form>
                    </div>
                @endauth

            </nav>
        </div>

    </x-container>
</header>
