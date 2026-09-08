@extends('teacher.layout')

@section('title', 'Review History')

@section('content')
    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('teacher.reviews.index') }}" class="text-sm font-black text-blue-700">← Review Feedback</a>
            <p class="mt-4 text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Lesson review history</p>
            <h1 class="mt-1 text-3xl font-black">{{ $lesson->title }}</h1>
            <p class="mt-2 text-sm text-slate-500">{{ $lesson->unit?->course?->title }} · {{ $lesson->unit?->title }}</p>
        </div>
        <a href="{{ route('teacher.courses.curriculum.show', $lesson->unit->course) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-black">Open Curriculum</a>
    </div>

    <section class="rounded-2xl border border-blue-100 bg-blue-50 p-5">
        <p class="text-sm font-black text-blue-950">Current review state: {{ ucwords(str_replace('_', ' ', $lesson->review_status)) }}</p>
        <p class="mt-1 text-sm text-blue-800">This timeline is read-only. Past Admin feedback and Teacher responses remain preserved after later review cycles.</p>
    </section>

    <section class="mt-6 space-y-4">
        @forelse($events as $event)
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-700">{{ ucwords(str_replace('_', ' ', $event->event_type)) }}</span>
                        <p class="mt-3 font-black">{{ $event->actor_name }} <span class="font-semibold text-slate-400">· {{ ucfirst($event->actor_role) }}</span></p>
                    </div>
                    <time class="text-xs font-bold text-slate-400">{{ $event->occurred_at?->format('d M Y, h:i A') }}</time>
                </div>
                @if($event->from_review_status || $event->to_review_status)
                    <p class="mt-3 text-sm font-bold text-slate-600">{{ $event->from_review_status ? ucwords(str_replace('_', ' ', $event->from_review_status)) : '—' }} → {{ $event->to_review_status ? ucwords(str_replace('_', ' ', $event->to_review_status)) : '—' }}</p>
                @endif
                @if($event->review_notes)<div class="mt-3 rounded-xl bg-rose-50 p-3 text-sm text-rose-950"><strong>Admin feedback:</strong> {{ $event->review_notes }}</div>@endif
                @if($event->teacher_response)<div class="mt-3 rounded-xl bg-cyan-50 p-3 text-sm text-cyan-950"><strong>Teacher response:</strong> {{ $event->teacher_response }}</div>@endif
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center"><p class="font-black">No review history recorded yet.</p></div>
        @endforelse
    </section>

    <div class="mt-6">{{ $events->links() }}</div>
@endsection
