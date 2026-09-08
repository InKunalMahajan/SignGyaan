@extends('teacher.layout')

@section('title', 'Review Feedback')

@section('content')
    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Content review</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight">Review Feedback & Resubmission</h1>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">See Admin decisions for Lessons you created, fix requested changes, and deliberately resubmit when the Lesson is ready.</p>
        </div>
        <a href="{{ route('teacher.courses.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-black text-slate-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">My Courses</a>
    </div>

    <section class="mb-6 grid gap-4 sm:grid-cols-3" aria-label="Review summary">
        <a href="{{ route('teacher.reviews.index', ['review' => 'changes_requested']) }}" class="rounded-2xl border border-rose-200 bg-rose-50 p-5 focus:outline-none focus:ring-4 focus:ring-rose-100"><p class="text-sm font-black text-rose-700">Changes Requested</p><p class="mt-2 text-3xl font-black text-rose-950">{{ $changesRequestedCount }}</p><p class="mt-1 text-xs font-bold text-rose-700/80">Action needed</p></a>
        <a href="{{ route('teacher.reviews.index', ['review' => 'pending']) }}" class="rounded-2xl border border-amber-200 bg-amber-50 p-5 focus:outline-none focus:ring-4 focus:ring-amber-100"><p class="text-sm font-black text-amber-700">Pending Review</p><p class="mt-2 text-3xl font-black text-amber-950">{{ $pendingCount }}</p><p class="mt-1 text-xs font-bold text-amber-700/80">Waiting for Admin</p></a>
        <a href="{{ route('teacher.reviews.index', ['review' => 'approved']) }}" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 focus:outline-none focus:ring-4 focus:ring-emerald-100"><p class="text-sm font-black text-emerald-700">Approved</p><p class="mt-2 text-3xl font-black text-emerald-950">{{ $approvedCount }}</p><p class="mt-1 text-xs font-bold text-emerald-700/80">Learner-ready when published</p></a>
    </section>

    <form method="GET" action="{{ route('teacher.reviews.index') }}" class="mb-6 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[minmax(0,1fr)_220px_auto]">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search Lesson, Unit or Course" class="w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
        <select name="review" class="w-full rounded-xl border-slate-300 bg-white focus:border-cyan-500 focus:ring-cyan-500"><option value="">All review states</option><option value="changes_requested" @selected(request('review') === 'changes_requested')>Changes Requested</option><option value="pending" @selected(request('review') === 'pending')>Pending</option><option value="approved" @selected(request('review') === 'approved')>Approved</option></select>
        <button class="rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Filter</button>
    </form>

    @if ($lessons->isEmpty())
        <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center"><p class="font-black">No Lessons match this review view.</p><p class="mt-2 text-sm text-slate-500">Published Lessons and Admin decisions will appear here.</p></div>
    @else
        <div class="space-y-5">
            @foreach ($lessons as $lesson)
                @php
                    $course = $lesson->unit?->course;
                    $statusClass = match ($lesson->review_status) { 'approved' => 'bg-emerald-50 text-emerald-700', 'changes_requested' => 'bg-rose-50 text-rose-700', default => 'bg-amber-50 text-amber-800' };
                    $statusLabel = match ($lesson->review_status) { 'approved' => 'Approved', 'changes_requested' => 'Changes Requested', default => 'Pending Review' };
                @endphp
                <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4"><div><p class="text-xs font-black uppercase tracking-[0.14em] text-cyan-700">{{ $course?->subject?->name ?: 'Course' }} · {{ $course?->title ?: 'Unknown course' }}</p><h2 class="mt-1 text-xl font-black">{{ $lesson->title }}</h2><p class="mt-1 text-sm text-slate-500">{{ $lesson->unit?->title }} · {{ ucfirst($lesson->status) }}</p></div><span class="rounded-full px-3 py-1 text-xs font-black {{ $statusClass }}">{{ $statusLabel }}</span></div>

                    <div class="mt-5 grid gap-4 lg:grid-cols-[minmax(0,1fr)_260px]">
                        <div class="space-y-4">
                            @if ($lesson->review_notes)
                                <div class="rounded-2xl border {{ $lesson->changesRequested() ? 'border-rose-200 bg-rose-50' : 'border-slate-200 bg-slate-50' }} p-4"><p class="text-xs font-black uppercase tracking-wide {{ $lesson->changesRequested() ? 'text-rose-700' : 'text-slate-500' }}">Admin feedback</p><p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-800">{{ $lesson->review_notes }}</p>@if ($lesson->reviewer || $lesson->reviewed_at)<p class="mt-3 text-xs font-semibold text-slate-500">Reviewed{{ $lesson->reviewer ? ' by '.$lesson->reviewer->name : '' }}{{ $lesson->reviewed_at ? ' · '.$lesson->reviewed_at->diffForHumans() : '' }}</p>@endif</div>
                            @elseif ($lesson->review_status === 'pending')
                                <div class="rounded-2xl bg-amber-50 p-4 text-sm font-semibold text-amber-900">Waiting for Admin review. You can still edit the Lesson; changes remain Pending.</div>
                            @elseif ($lesson->review_status === 'approved')
                                <div class="rounded-2xl bg-emerald-50 p-4 text-sm font-semibold text-emerald-900">Approved for Learner access while the Lesson remains published.</div>
                            @endif

                            @if ($lesson->teacher_response)<div class="rounded-2xl border border-blue-200 bg-blue-50 p-4"><p class="text-xs font-black uppercase tracking-wide text-blue-700">Your last response</p><p class="mt-2 whitespace-pre-line text-sm text-blue-950">{{ $lesson->teacher_response }}</p></div>@endif

                            @if ($lesson->changesRequested())
                                <form method="POST" action="{{ route('teacher.reviews.resubmit', $lesson) }}" class="rounded-2xl border border-blue-200 bg-blue-50 p-4">@csrf @method('PATCH')<label class="block"><span class="text-sm font-black text-blue-950">Response to Admin <span class="font-semibold text-blue-700">(optional)</span></span><textarea name="teacher_response" rows="3" maxlength="3000" placeholder="Example: Added ISL video and expanded the notes as requested." class="mt-2 w-full rounded-xl border-blue-200 bg-white focus:border-cyan-500 focus:ring-cyan-500">{{ old('teacher_response') }}</textarea></label><button class="mt-3 rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Resubmit for Review</button><p class="mt-2 text-xs font-semibold text-blue-800">Edit the Lesson first if needed. Resubmission changes the review state to Pending.</p></form>
                            @endif
                        </div>

                        <aside class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-black uppercase tracking-wide text-slate-500">Review timeline</p>
                            <dl class="mt-3 space-y-3 text-sm"><div><dt class="text-slate-500">Submitted</dt><dd class="font-black">{{ $lesson->review_submitted_at?->format('d M Y, h:i A') ?: 'Not submitted' }}</dd></div><div><dt class="text-slate-500">Last reviewed</dt><dd class="font-black">{{ $lesson->reviewed_at?->format('d M Y, h:i A') ?: 'Not reviewed yet' }}</dd></div></dl>
                            <a href="{{ route('teacher.reviews.history', $lesson) }}" class="mt-5 block rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-2.5 text-center text-sm font-black text-cyan-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">View Full History</a>
                            @if ($course)<a href="{{ route('teacher.courses.curriculum.show', $course) }}" class="mt-2 block rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-center text-sm font-black text-blue-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Open & Edit Lesson</a>@endif
                        </aside>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-6">{{ $lessons->links() }}</div>
    @endif
@endsection
