<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Moderation Logs | SignGyaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <main class="mx-auto max-w-7xl space-y-6 px-5 py-8 sm:px-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Permanent audit trail</p>
                <h1 class="mt-1 text-3xl font-black">Review History & Moderation Logs</h1>
                <p class="mt-2 max-w-3xl text-sm text-slate-500">Every submission, resubmission, approval, change request and return-to-pending action is stored as an immutable event snapshot.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.reviews.dashboard') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-black">Review Dashboard</a>
                <a href="{{ route('admin.curriculum.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-black">Curriculum</a>
            </div>
        </div>

        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <article class="rounded-2xl border bg-white p-5"><p class="text-sm text-slate-500">Audit Events</p><p class="mt-2 text-3xl font-black">{{ $totalEvents }}</p></article>
            <article class="rounded-2xl border bg-white p-5"><p class="text-sm text-slate-500">Submissions</p><p class="mt-2 text-3xl font-black">{{ $submissionCount }}</p></article>
            <article class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5"><p class="text-sm text-emerald-800">Approvals</p><p class="mt-2 text-3xl font-black text-emerald-950">{{ $approvalCount }}</p></article>
            <article class="rounded-2xl border border-rose-200 bg-rose-50 p-5"><p class="text-sm text-rose-800">Changes Requested</p><p class="mt-2 text-3xl font-black text-rose-950">{{ $changesRequestedCount }}</p></article>
        </section>

        <form method="GET" class="grid gap-3 rounded-2xl border bg-white p-5 lg:grid-cols-[1fr_240px_180px_auto]">
            <input name="q" value="{{ request('q') }}" placeholder="Search lesson, course, actor, notes, response" class="rounded-xl border border-slate-300 px-4 py-3">
            <select name="event" class="rounded-xl border border-slate-300 px-4 py-3">
                <option value="">All event types</option>
                @foreach($eventTypes as $eventType)
                    <option value="{{ $eventType }}" @selected(request('event') === $eventType)>{{ ucwords(str_replace('_', ' ', $eventType)) }}</option>
                @endforeach
            </select>
            <select name="actor_role" class="rounded-xl border border-slate-300 px-4 py-3">
                <option value="">All actors</option>
                <option value="teacher" @selected(request('actor_role') === 'teacher')>Teacher</option>
                <option value="admin" @selected(request('actor_role') === 'admin')>Admin</option>
                <option value="system" @selected(request('actor_role') === 'system')>System</option>
            </select>
            <button class="rounded-xl bg-blue-700 px-5 py-3 font-black text-white">Filter</button>
        </form>

        <section class="space-y-4">
            @forelse($events as $event)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-800">{{ ucwords(str_replace('_', ' ', $event->event_type)) }}</span>
                                <span class="text-xs font-bold text-slate-400">{{ $event->occurred_at?->format('d M Y, h:i A') }}</span>
                            </div>
                            <h2 class="mt-2 text-lg font-black">{{ $event->lesson_title }}</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ $event->course_title ?: 'Course unavailable' }} @if($event->unit_title) · {{ $event->unit_title }} @endif</p>
                        </div>
                        <div class="text-right text-sm"><p class="font-black">{{ $event->actor_name }}</p><p class="text-slate-500">{{ ucfirst($event->actor_role) }}</p></div>
                    </div>

                    @if($event->from_review_status || $event->to_review_status)
                        <p class="mt-4 text-sm font-bold text-slate-600">Review state: {{ $event->from_review_status ? str_replace('_', ' ', ucfirst($event->from_review_status)) : '—' }} → {{ $event->to_review_status ? str_replace('_', ' ', ucfirst($event->to_review_status)) : '—' }}</p>
                    @endif
                    @if($event->review_notes)<div class="mt-3 rounded-xl bg-slate-50 p-3 text-sm"><strong>Admin notes:</strong> {{ $event->review_notes }}</div>@endif
                    @if($event->teacher_response)<div class="mt-3 rounded-xl bg-cyan-50 p-3 text-sm text-cyan-950"><strong>Teacher response:</strong> {{ $event->teacher_response }}</div>@endif
                    @if($event->lesson?->unit?->course_id)<a href="{{ route('admin.curriculum.courses.show', $event->lesson->unit->course_id) }}" class="mt-4 inline-flex text-sm font-black text-blue-700">Open current review context →</a>@endif
                </article>
            @empty
                <div class="rounded-2xl border border-dashed bg-white p-10 text-center"><p class="font-black">No audit events match this filter.</p></div>
            @endforelse
        </section>

        {{ $events->links() }}
    </main>
</body>
</html>
