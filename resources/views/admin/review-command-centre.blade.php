<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Review Command Centre | SignGyaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <main class="mx-auto max-w-7xl space-y-7 px-5 py-8 sm:px-8">
        <section class="flex flex-wrap items-start justify-between gap-4">
            <div><p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Admin moderation</p><h1 class="mt-1 text-3xl font-black">Review Queue & Notification Dashboard</h1><p class="mt-2 max-w-3xl text-sm text-slate-600">Prioritise resubmissions, new submissions, unread alerts, and recent moderation decisions from one command centre.</p></div>
            <div class="flex flex-wrap gap-2"><a href="{{ route('admin.reviews.history') }}" class="rounded-xl border bg-white px-4 py-2.5 text-sm font-black">Audit Trail</a><a href="{{ route('admin.curriculum.index') }}" class="rounded-xl border bg-white px-4 py-2.5 text-sm font-black">Curriculum Review</a><a href="{{ route('notifications.index') }}" class="rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-black text-white">Notifications @if($unreadCount)<span class="ml-1 rounded-full bg-white px-2 py-0.5 text-blue-700">{{ $unreadCount }}</span>@endif</a></div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-amber-200 bg-amber-50 p-5"><p class="text-sm font-bold text-amber-800">Pending Review</p><p class="mt-2 text-3xl font-black">{{ $pendingCount }}</p></article>
            <article class="rounded-2xl border border-cyan-200 bg-cyan-50 p-5"><p class="text-sm font-bold text-cyan-800">Resubmitted</p><p class="mt-2 text-3xl font-black">{{ $resubmittedCount }}</p></article>
            <article class="rounded-2xl border border-rose-200 bg-rose-50 p-5"><p class="text-sm font-bold text-rose-800">Changes Requested</p><p class="mt-2 text-3xl font-black">{{ $changesRequestedCount }}</p></article>
            <article class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5"><p class="text-sm font-bold text-emerald-800">Approved</p><p class="mt-2 text-3xl font-black">{{ $approvedCount }}</p></article>
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(320px,1fr)]">
            <div>
                <div class="mb-4"><p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Priority queue</p><h2 class="mt-1 text-xl font-black">What to review next</h2></div>
                <div class="space-y-4">
                    @forelse($pendingLessons as $lesson)
                        @php($course = $lesson->unit?->course)
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="flex flex-wrap items-start justify-between gap-3"><div><p class="text-xs font-black uppercase text-cyan-700">{{ $lesson->teacher_response ? 'Resubmission' : 'New / updated submission' }}</p><h3 class="mt-1 text-lg font-black">{{ $lesson->title }}</h3><p class="mt-1 text-sm text-slate-500">{{ $course?->subject?->name }} · {{ $course?->title }} · Teacher: {{ $course?->creator?->name ?: 'Unknown' }}</p></div><span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-black text-amber-800">Pending</span></div>
                            @if($lesson->teacher_response)<div class="mt-4 rounded-xl bg-cyan-50 p-3 text-sm text-cyan-950"><strong>Teacher response:</strong> {{ $lesson->teacher_response }}</div>@endif
                            <div class="mt-4 flex flex-wrap items-center justify-between gap-3"><p class="text-xs font-semibold text-slate-400">Submitted {{ $lesson->review_submitted_at?->diffForHumans() ?: $lesson->updated_at?->diffForHumans() }}</p>@if($course)<a href="{{ route('admin.curriculum.courses.show', $course) }}" class="rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-black text-white">Review Course</a>@endif</div>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed bg-white p-8 text-center"><p class="font-black">Review queue is clear.</p><p class="mt-2 text-sm text-slate-500">New Teacher submissions will appear here.</p></div>
                    @endforelse
                </div>
            </div>

            <aside class="space-y-6">
                <section class="rounded-2xl border bg-white p-5 shadow-sm"><div class="flex items-center justify-between gap-3"><div><p class="text-xs font-black uppercase tracking-wide text-cyan-700">Alerts</p><h2 class="mt-1 text-lg font-black">Recent notifications</h2></div><span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">{{ $unreadCount }} unread</span></div><div class="mt-4 space-y-3">@forelse($notifications as $notification)<div class="rounded-xl p-3 {{ $notification->read_at ? 'bg-slate-50' : 'bg-blue-50' }}"><p class="text-sm font-black">{{ $notification->data['title'] ?? 'Review alert' }}</p><p class="mt-1 text-xs text-slate-600">{{ $notification->data['message'] ?? '' }}</p><p class="mt-1 text-xs font-semibold text-slate-400">{{ $notification->created_at?->diffForHumans() }}</p></div>@empty<p class="text-sm text-slate-500">No review notifications yet.</p>@endforelse</div></section>

                <section class="rounded-2xl border bg-white p-5 shadow-sm"><p class="text-xs font-black uppercase tracking-wide text-cyan-700">Moderation history</p><h2 class="mt-1 text-lg font-black">Recent decisions</h2><div class="mt-4 space-y-3">@forelse($recentDecisions as $lesson)<div class="border-t border-slate-100 pt-3 first:border-0 first:pt-0"><p class="text-sm font-black">{{ $lesson->title }}</p><p class="mt-1 text-xs text-slate-500">{{ $lesson->review_status === 'approved' ? 'Approved' : 'Changes requested' }} · {{ $lesson->reviewer?->name ?: 'Admin' }}</p><p class="mt-1 text-xs font-semibold text-slate-400">{{ $lesson->reviewed_at?->diffForHumans() }}</p></div>@empty<p class="text-sm text-slate-500">No moderation decisions yet.</p>@endforelse</div></section>
            </aside>
        </section>
    </main>
</body>
</html>
