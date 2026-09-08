<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Teacher Workspace') | SignGyaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <a href="#main-content" class="sr-only z-50 rounded-lg bg-slate-950 px-4 py-3 text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Skip to main content</a>
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-5 py-4 sm:px-8">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl focus:outline-none focus:ring-4 focus:ring-cyan-200">
                <span class="grid size-10 place-items-center rounded-xl bg-gradient-to-br from-blue-700 to-cyan-500 text-sm font-black text-white">SG</span>
                <span><span class="block font-black">SignGyaan</span><span class="block text-xs font-semibold text-slate-500">Teacher Workspace</span></span>
            </a>
            <nav class="flex flex-wrap items-center gap-2" aria-label="Teacher workspace">
                <a href="{{ route('teacher.classes.index') }}" class="rounded-xl px-3 py-2 text-sm font-black text-slate-700 hover:bg-slate-100">My Classes</a>
                <a href="{{ route('teacher.courses.index') }}" class="rounded-xl px-3 py-2 text-sm font-black text-slate-700 hover:bg-slate-100">My Courses</a>
                <a href="{{ route('teacher.reviews.index') }}" class="rounded-xl px-3 py-2 text-sm font-black text-slate-700 hover:bg-slate-100">Review Feedback</a>
                <a href="{{ route('notifications.index') }}" class="rounded-xl px-3 py-2 text-sm font-black text-slate-700 hover:bg-slate-100">Notifications @if(auth()->user()->unreadNotifications()->count())<span class="ml-1 rounded-full bg-blue-700 px-2 py-0.5 text-xs text-white">{{ auth()->user()->unreadNotifications()->count() }}</span>@endif</a>
                <a href="{{ route('teacher.profile.show') }}" class="rounded-xl px-3 py-2 text-sm font-black text-slate-700 hover:bg-slate-100">My Profile</a>
                <a href="{{ route('dashboard') }}" class="rounded-xl px-3 py-2 text-sm font-black text-slate-700 hover:bg-slate-100">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-black text-slate-700">Sign out</button></form>
            </nav>
        </div>
    </header>
    <main id="main-content" class="mx-auto max-w-7xl px-5 py-8 sm:px-8">
        @if(session('status'))<div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-900" role="status">{{ session('status') }}</div>@endif
        @if($errors->any())<div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-900" role="alert"><p class="font-black">Please check the highlighted information.</p><ul class="mt-2 list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        @yield('content')
    </main>
</body>
</html>
