<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Curriculum Review | SignGyaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <main class="mx-auto max-w-7xl space-y-6 px-5 py-8 sm:px-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div><p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Admin curriculum</p><h1 class="mt-1 text-3xl font-black">Curriculum Management & Content Review</h1></div>
            <div class="flex gap-2">
                <a href="{{ route('notifications.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-black">Notifications @if(auth()->user()->unreadNotifications()->count())<span class="ml-1 rounded-full bg-blue-700 px-2 py-0.5 text-xs text-white">{{ auth()->user()->unreadNotifications()->count() }}</span>@endif</a>
                <a href="{{ route('dashboard') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-black">Back to Dashboard</a>
            </div>
        </div>
        @if(session('status'))<div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-900">{{ session('status') }}</div>@endif
        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <article class="rounded-2xl border bg-white p-5"><p class="text-sm text-slate-500">Subjects</p><p class="mt-2 text-3xl font-black">{{ $subjectCount }}</p></article>
            <article class="rounded-2xl border bg-white p-5"><p class="text-sm text-slate-500">Courses</p><p class="mt-2 text-3xl font-black">{{ $courseCount }}</p></article>
            <article class="rounded-2xl border bg-white p-5"><p class="text-sm text-slate-500">Lessons</p><p class="mt-2 text-3xl font-black">{{ $lessonCount }}</p></article>
            <article class="rounded-2xl border border-amber-200 bg-amber-50 p-5"><p class="text-sm text-amber-800">Pending Review</p><p class="mt-2 text-3xl font-black text-amber-950">{{ $reviewCounts['pending'] ?? 0 }}</p></article>
        </section>
        <form method="GET" class="grid gap-3 rounded-2xl border bg-white p-5 sm:grid-cols-[1fr_220px_auto]">
            <input name="q" value="{{ request('q') }}" placeholder="Search course, subject, or teacher" class="rounded-xl border border-slate-300 px-4 py-3">
            <select name="review" class="rounded-xl border border-slate-300 px-4 py-3"><option value="">All review states</option><option value="pending" @selected(request('review')==='pending')>Pending</option><option value="approved" @selected(request('review')==='approved')>Approved</option><option value="changes_requested" @selected(request('review')==='changes_requested')>Changes requested</option></select>
            <button class="rounded-xl bg-blue-700 px-5 py-3 font-black text-white">Filter</button>
        </form>
        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse($courses as $course)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-start justify-between gap-3"><div><p class="text-xs font-black uppercase text-cyan-700">{{ $course->subject?->name ?: 'No subject' }}</p><h2 class="mt-1 text-lg font-black">{{ $course->title }}</h2></div><span class="rounded-full px-2.5 py-1 text-xs font-black {{ $course->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $course->is_active ? 'Active' : 'Inactive' }}</span></div><p class="mt-3 text-sm text-slate-500">Teacher: {{ $course->creator?->name ?: 'Unknown' }}</p><p class="mt-1 text-sm text-slate-500">{{ $course->units_count }} units</p><a href="{{ route('admin.curriculum.courses.show',$course) }}" class="mt-5 inline-flex rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-black text-white">Review Course</a></article>
            @empty
                <div class="rounded-2xl border border-dashed bg-white p-8 text-center md:col-span-2 xl:col-span-3"><p class="font-black">No curriculum matches this filter.</p></div>
            @endforelse
        </section>
        {{ $courses->links() }}
    </main>
</body>
</html>
