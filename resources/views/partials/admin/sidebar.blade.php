@php
    $mobile = $mobile ?? false;

    $adminNavigation = [
        [
            'label' => 'Dashboard',
            'route' => 'admin.dashboard',
            'matches' => ['admin.dashboard'],
            'icon' => 'dashboard',
        ],
        [
            'label' => 'Subjects',
            'route' => 'admin.subjects.index',
            'matches' => ['admin.subjects.*'],
            'icon' => 'subjects',
        ],
        [
            'label' => 'Courses',
            'route' => 'admin.courses.index',
            'matches' => ['admin.courses.*'],
            'icon' => 'courses',
        ],
        [
            'label' => 'Units',
            'route' => 'admin.units.index',
            'matches' => ['admin.units.*'],
            'icon' => 'units',
        ],
        [
            'label' => 'Lessons',
            'route' => 'admin.lessons.index',
            'matches' => ['admin.lessons.*'],
            'icon' => 'lessons',
        ],
        [
            'label' => 'ISL Vocabulary',
            'route' => 'admin.vocabulary.index',
            'matches' => ['admin.vocabulary.*'],
            'icon' => 'vocabulary',
        ],
        [
            'label' => 'Practice & Resources',
            'route' => 'admin.practice.index',
            'matches' => ['admin.practice.*'],
            'icon' => 'practice',
        ],
        [
            'label' => 'Assessments',
            'route' => 'admin.assessments.index',
            'matches' => ['admin.assessments.*'],
            'icon' => 'assessments',
        ],
        [
            'label' => 'Media',
            'route' => 'admin.media.index',
            'matches' => ['admin.media.*'],
            'icon' => 'media',
        ],
        [
            'label' => 'Users',
            'route' => 'admin.users.index',
            'matches' => ['admin.users.*'],
            'icon' => 'users',
        ],
        [
            'label' => 'Teachers',
            'route' => 'admin.teachers.index',
            'matches' => ['admin.teachers.*'],
            'icon' => 'users',
        ],
        [
            'label' => 'Settings',
            'route' => 'admin.settings.index',
            'matches' => ['admin.settings.*'],
            'icon' => 'settings',
        ],
    ];
@endphp

<div class="flex h-full min-h-0 flex-col">
    <div class="flex min-h-20 items-center justify-between gap-3 border-b border-white/10 px-5">
        <a
            href="{{ route('admin.dashboard') }}"
            class="flex min-w-0 items-center gap-3 rounded-xl"
            aria-label="SignGyaan Admin home"
            @if ($mobile) @click="adminNavOpen = false" @endif
        >
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white font-heading text-lg font-bold text-sign-primary" aria-hidden="true">S</span>
            <span class="min-w-0">
                <span class="block truncate font-heading text-xl font-semibold">SignGyaan</span>
                <span class="block text-[11px] font-semibold uppercase tracking-[0.18em] text-sign-cyan">Admin Console</span>
            </span>
        </a>

        @if ($mobile)
            <button
                type="button"
                @click="adminNavOpen = false"
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-white/80 transition hover:bg-white/10 hover:text-white"
                aria-label="Close admin navigation"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        @endif
    </div>

    <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain px-3 py-5">
        <p class="px-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-white/45">Management</p>

        <nav class="mt-3 space-y-1" aria-label="Admin management navigation">
            @foreach ($adminNavigation as $item)
                @php
                    $active = request()->routeIs(...$item['matches']);
                @endphp

                <a
                    href="{{ route($item['route']) }}"
                    @if ($mobile) @click="adminNavOpen = false" @endif
                    @class([
                        'group flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition',
                        'bg-white text-sign-dark shadow-sm' => $active,
                        'text-white/75 hover:bg-white/10 hover:text-white' => ! $active,
                    ])
                    @if ($active) aria-current="page" @endif
                >
                    <span
                        @class([
                            'flex h-8 w-8 shrink-0 items-center justify-center rounded-lg transition',
                            'bg-sign-light text-sign-primary' => $active,
                            'bg-white/10 text-sign-cyan group-hover:bg-white/15' => ! $active,
                        ])
                        aria-hidden="true"
                    >
                        @switch($item['icon'])
                            @case('dashboard')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25V21m0-4.5h7.5m0 0H18a2.25 2.25 0 0 0 2.25-2.25V3m-4.5 18v-4.5m0 0h-7.5M12 6.75h.008v.008H12V6.75Zm0 3h.008v.008H12V9.75Zm0 3h.008v.008H12v-.008Zm3-6h.008v.008H15V6.75Zm0 3h.008v.008H15V9.75Zm0 3h.008v.008H15v-.008Zm-6-6h.008v.008H9V6.75Zm0 3h.008v.008H9V9.75Zm0 3h.008v.008H9v-.008Z" /></svg>
                                @break
                            @case('subjects')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75h15M4.5 12h15m-15 5.25h15" /></svg>
                                @break
                            @case('courses')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25A8.966 8.966 0 0 1 18 3.75c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>
                                @break
                            @case('units')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h12A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6Zm4.5 1.5h7.5m-7.5 4.5h7.5m-7.5 4.5h4.5" /></svg>
                                @break
                            @case('lessons')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.399 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" /></svg>
                                @break
                            @case('vocabulary')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 5.25h15A2.25 2.25 0 0 1 21.75 7.5v9A2.25 2.25 0 0 1 19.5 18.75h-15A2.25 2.25 0 0 1 2.25 16.5v-9A2.25 2.25 0 0 1 4.5 5.25Zm1.5 4.5h4.5m-4.5 3h3m4.5-3h4.5m-4.5 3h4.5" /></svg>
                                @break
                            @case('practice')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m6 2.25a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                @break
                            @case('assessments')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-7.5-6h9A2.25 2.25 0 0 1 18.75 6v12A2.25 2.25 0 0 1 16.5 20.25h-9A2.25 2.25 0 0 1 5.25 18V6A2.25 2.25 0 0 1 7.5 3.75Z" /></svg>
                                @break
                            @case('media')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5A2.25 2.25 0 0 0 22.5 17.25V6.75A2.25 2.25 0 0 0 20.25 4.5H3.75A2.25 2.25 0 0 0 1.5 6.75v10.5A2.25 2.25 0 0 0 3.75 19.5Zm10.5-11.25h.008v.008h-.008V8.25Z" /></svg>
                                @break
                            @case('users')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197A5.991 5.991 0 0 0 6 18.719m.941-3.197a5.995 5.995 0 0 1 10.117 0M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" /></svg>
                                @break
                            @case('settings')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25a3.75 3.75 0 1 0 0 7.5 3.75 3.75 0 0 0 0-7.5Zm0-5.25v2.25m0 12.75V21M3 12h2.25m13.5 0H21m-3.636-6.364-1.591 1.591M8.227 15.773l-1.591 1.591m0-11.728 1.591 1.591m7.546 7.546 1.591 1.591" /></svg>
                                @break
                            @default
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75h.008v.008H12V6.75Zm0 5.25h.008v.008H12V12Zm0 5.25h.008v.008H12v-.008Z" /></svg>
                        @endswitch
                    </span>

                    <span class="min-w-0 flex-1 truncate">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>
    </div>

    <div class="border-t border-white/10 p-3">
        <div class="rounded-2xl bg-white/5 p-3">
            <p class="px-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-white/45">Admin account</p>
            <p class="mt-2 truncate px-2 text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
            <p class="mt-0.5 truncate px-2 text-xs text-white/55">{{ auth()->user()->email }}</p>

            <div class="mt-3 grid gap-1">
                <a href="{{ route('dashboard') }}" @if ($mobile) @click="adminNavOpen = false" @endif class="flex min-h-10 items-center rounded-lg px-2 text-xs font-semibold text-white/70 transition hover:bg-white/10 hover:text-white">Learner Dashboard</a>
                <a href="{{ route('profile') }}" @if ($mobile) @click="adminNavOpen = false" @endif class="flex min-h-10 items-center rounded-lg px-2 text-xs font-semibold text-white/70 transition hover:bg-white/10 hover:text-white">Profile & Account</a>
                <a href="{{ route('home') }}" @if ($mobile) @click="adminNavOpen = false" @endif class="flex min-h-10 items-center rounded-lg px-2 text-xs font-semibold text-white/70 transition hover:bg-white/10 hover:text-white">View Website</a>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="mt-2 border-t border-white/10 pt-2">
                @csrf
                <button type="submit" class="flex min-h-10 w-full items-center justify-between rounded-lg px-2 text-left text-xs font-semibold text-white/70 transition hover:bg-white/10 hover:text-white">
                    <span>Sign Out</span>
                    <span aria-hidden="true">→</span>
                </button>
            </form>
        </div>
    </div>
</div>