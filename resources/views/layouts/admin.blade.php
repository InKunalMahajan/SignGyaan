<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('description', 'SignGyaan administration workspace for managing learning content and platform users.')">
    <meta name="theme-color" content="#103F60">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin - SignGyaan')</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @livewireStyles
    @stack('head')
</head>
<body
    x-data="{
        adminNavOpen: false,
        openAdminNav() {
            this.adminNavOpen = true;
            this.$nextTick(() => {
                document.querySelector('#admin-mobile-navigation [aria-label=\'Close admin navigation\']')?.focus();
            });
        },
        closeAdminNav(returnFocus = false) {
            this.adminNavOpen = false;
            if (returnFocus) {
                this.$nextTick(() => this.$refs.adminNavTrigger?.focus());
            }
        }
    }"
    x-effect="document.documentElement.classList.toggle('admin-nav-open', adminNavOpen)"
    @keydown.escape.window="if (adminNavOpen) closeAdminNav(true)"
    data-admin-shell
    class="min-h-screen bg-sign-soft font-sans text-sign-text antialiased"
>
    <a href="#admin-main-content" class="fixed left-4 top-4 z-[100] -translate-y-24 rounded-lg bg-sign-dark px-4 py-3 text-sm font-semibold text-white shadow-lg transition focus:translate-y-0">Skip to admin content</a>

    <aside class="fixed inset-y-0 left-0 z-40 hidden w-72 border-r border-white/10 bg-sign-dark text-white lg:flex lg:flex-col" aria-label="Admin sidebar">
        @include('partials.admin.sidebar', ['mobile' => false])
    </aside>

    <div x-show="adminNavOpen" x-cloak x-transition.opacity @click="closeAdminNav(true)" class="fixed inset-0 z-40 bg-sign-dark/60 backdrop-blur-sm lg:hidden" aria-hidden="true"></div>

    <aside id="admin-mobile-navigation" x-show="adminNavOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" :aria-hidden="(!adminNavOpen).toString()" class="fixed inset-y-0 left-0 z-50 flex w-[min(19rem,calc(100vw-2rem))] flex-col bg-sign-dark text-white shadow-2xl lg:hidden" aria-label="Admin navigation">
        @include('partials.admin.sidebar', ['mobile' => true])
    </aside>

    <div class="min-h-screen lg:pl-72">
        <header class="sticky top-0 z-30 border-b border-sign-border bg-white/95 backdrop-blur">
            <div class="flex min-h-16 items-center gap-3 px-4 sm:min-h-20 sm:px-6 lg:px-8">
                <button x-ref="adminNavTrigger" type="button" @click="openAdminNav()" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-sign-primary transition hover:bg-sign-soft lg:hidden" aria-label="Open admin navigation" aria-controls="admin-mobile-navigation" :aria-expanded="adminNavOpen.toString()"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg></button>
                <div class="min-w-0 flex-1"><p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">SignGyaan Admin</p><h1 class="truncate font-heading text-lg font-semibold text-sign-primary sm:text-xl">@yield('page-title', 'Dashboard')</h1></div>
                <div class="flex items-center gap-2 sm:gap-3">
                    <a href="{{ route('home') }}" class="hidden min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-2.5 text-sm font-semibold text-sign-primary transition hover:border-sign-cyan hover:bg-sign-soft sm:inline-flex">View Website</a>
                    @php $adminLayoutUser = auth()->user(); $adminLayoutInitial = strtoupper(substr(trim($adminLayoutUser->name), 0, 1)); @endphp
                    <a href="{{ route('profile') }}" class="flex min-h-11 items-center gap-2 rounded-xl border border-sign-border bg-white px-2 py-1.5 transition hover:border-sign-cyan hover:bg-sign-soft sm:px-3" aria-label="Open profile and account for {{ $adminLayoutUser->name }}"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sign-primary font-heading text-sm font-semibold text-white" aria-hidden="true">{{ $adminLayoutInitial }}</span><span class="hidden max-w-36 truncate text-sm font-semibold text-sign-primary md:block">{{ $adminLayoutUser->name }}</span></a>
                </div>
            </div>
        </header>

        <main id="admin-main-content" tabindex="-1" class="min-w-0" data-admin-main>
            @if (session('status'))
                <div class="px-4 pt-5 sm:px-6 lg:px-8"><div class="rounded-2xl border border-sign-cyan bg-sign-light px-4 py-3 text-sm font-semibold text-sign-primary" role="status" aria-live="polite" data-admin-status>{{ session('status') }}</div></div>
            @endif
            @yield('content')
        </main>
    </div>

    @livewireScripts
    @stack('scripts')
    <script src="{{ asset('js/admin-media-picker.js') }}" defer></script>
    <script src="{{ asset('js/admin-course-builder-duplicate.js') }}" defer></script>
    <script src="{{ asset('js/admin-authoring-accessibility.js') }}" defer></script>
</body>
</html>
