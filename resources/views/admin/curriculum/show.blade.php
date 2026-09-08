<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $course->title }} Review | SignGyaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <main class="mx-auto max-w-7xl space-y-6 px-5 py-8 sm:px-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div><p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Content review</p><h1 class="mt-1 text-3xl font-black">{{ $course->title }}</h1><p class="mt-2 text-sm text-slate-500">{{ $course->subject?->name }} · Teacher: {{ $course->creator?->name ?: 'Unknown' }}</p></div>
            <a href="{{ route('admin.curriculum.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-black">Back to Curriculum</a>
        </div>

        @if (session('status'))<div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-900">{{ session('status') }}</div>@endif
        @if ($errors->any())<div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-900"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <article class="rounded-2xl border bg-white p-5"><p class="text-sm text-slate-500">Pending</p><p class="mt-2 text-3xl font-black">{{ $pendingCount }}</p></article>
            <article class="rounded-2xl border bg-white p-5"><p class="text-sm text-slate-500">Approved</p><p class="mt-2 text-3xl font-black">{{ $approvedCount }}</p></article>
            <article class="rounded-2xl border bg-white p-5"><p class="text-sm text-slate-500">Changes Requested</p><p class="mt-2 text-3xl font-black">{{ $changesRequestedCount }}</p></article>
            <article class="rounded-2xl border bg-white p-5"><p class="text-sm text-slate-500">Assigned Classes</p><p class="mt-2 text-3xl font-black">{{ $course->classes->count() }}</p></article>
        </section>

        <section class="grid gap-4 lg:grid-cols-2">
            <article class="rounded-2xl border bg-white p-5">
                <h2 class="font-black">Subject status</h2><p class="mt-2 text-sm text-slate-500">{{ $course->subject?->is_active ? 'Active' : 'Inactive' }}</p>
                @if($course->subject)<form method="POST" action="{{ route('admin.curriculum.subjects.status', $course->subject) }}" class="mt-4">@csrf @method('PATCH')<button class="rounded-xl border px-4 py-2.5 text-sm font-black">{{ $course->subject->is_active ? 'Deactivate Subject' : 'Activate Subject' }}</button></form>@endif
            </article>
            <article class="rounded-2xl border bg-white p-5">
                <h2 class="font-black">Course status</h2><p class="mt-2 text-sm text-slate-500">{{ $course->is_active ? 'Active' : 'Inactive' }}</p>
                <form method="POST" action="{{ route('admin.curriculum.courses.status', $course) }}" class="mt-4">@csrf @method('PATCH')<button class="rounded-xl border px-4 py-2.5 text-sm font-black">{{ $course->is_active ? 'Deactivate Course' : 'Activate Course' }}</button></form>
            </article>
        </section>

        @forelse ($course->units as $unit)
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3"><div><p class="text-xs font-black uppercase text-cyan-700">Unit {{ $unit->position }}</p><h2 class="mt-1 text-xl font-black">{{ $unit->title }}</h2></div><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black">{{ $unit->is_active ? 'Visible' : 'Hidden' }}</span></div>

                <div class="mt-5 space-y-4">
                    @forelse ($unit->lessons as $lesson)
                        @php
                            $checks = [
                                'ISL video' => filled($lesson->isl_video_url),
                                'Summary' => filled($lesson->summary),
                                'Notes' => filled($lesson->notes),
                                'Duration' => filled($lesson->estimated_minutes),
                            ];
                            $ready = collect($checks)->filter()->count();
                        @endphp
                        <article class="rounded-xl border border-slate-200 p-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div><p class="text-xs font-black uppercase text-slate-400">Lesson {{ $lesson->position }}</p><h3 class="mt-1 font-black">{{ $lesson->title }}</h3><p class="mt-1 text-sm text-slate-500">Teacher status: {{ ucfirst($lesson->status) }} · Review: {{ str_replace('_', ' ', ucfirst($lesson->review_status)) }}</p></div>
                                <span class="rounded-full px-3 py-1 text-xs font-black {{ $lesson->review_status === 'approved' ? 'bg-emerald-50 text-emerald-700' : ($lesson->review_status === 'changes_requested' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-800') }}">{{ $ready }}/4 readiness</span>
                            </div>

                            <div class="mt-4 grid gap-2 sm:grid-cols-4">
                                @foreach($checks as $label => $ok)<div class="rounded-lg px-3 py-2 text-xs font-bold {{ $ok ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-800' }}">{{ $ok ? '✓' : '!' }} {{ $label }}</div>@endforeach
                            </div>

                            @if($lesson->review_notes)<div class="mt-4 rounded-xl bg-slate-50 p-3 text-sm"><strong>Review notes:</strong> {{ $lesson->review_notes }}</div>@endif
                            @if($lesson->teacher_response)
                                <div class="mt-4 rounded-xl border border-blue-200 bg-blue-50 p-3 text-sm text-blue-950">
                                    <strong>Teacher response:</strong> {{ $lesson->teacher_response }}
                                    @if($lesson->review_submitted_at)<div class="mt-1 text-xs font-semibold text-blue-700">Resubmitted {{ $lesson->review_submitted_at->diffForHumans() }}</div>@endif
                                </div>
                            @elseif($lesson->review_submitted_at)
                                <div class="mt-4 text-xs font-semibold text-slate-500">Submitted for review {{ $lesson->review_submitted_at->diffForHumans() }}</div>
                            @endif

                            <form method="POST" action="{{ route('admin.curriculum.lessons.review', $lesson) }}" class="mt-4 grid gap-3 lg:grid-cols-[220px_1fr_auto]">
                                @csrf @method('PATCH')
                                <select name="review_status" class="rounded-xl border border-slate-300 px-3 py-2.5">
                                    <option value="pending" @selected($lesson->review_status === 'pending')>Pending</option>
                                    <option value="approved" @selected($lesson->review_status === 'approved')>Approved</option>
                                    <option value="changes_requested" @selected($lesson->review_status === 'changes_requested')>Changes requested</option>
                                </select>
                                <input name="review_notes" value="{{ $lesson->review_notes }}" placeholder="Reviewer note (required for changes requested)" class="rounded-xl border border-slate-300 px-3 py-2.5">
                                <button class="rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-black text-white">Save Review</button>
                            </form>
                        </article>
                    @empty
                        <p class="text-sm text-slate-500">No Lessons in this Unit.</p>
                    @endforelse
                </div>
            </section>
        @empty
            <section class="rounded-2xl border border-dashed bg-white p-8 text-center"><p class="font-black">No Units yet.</p></section>
        @endforelse
    </main>
</body>
</html>
