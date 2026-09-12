@extends('teacher.layout')
@section('title', 'Assessment Attempts')
@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Assessment Review</p>
            <h1 class="mt-2 text-3xl font-black">{{ $assessment->title }}</h1>
            <p class="mt-2 text-sm text-slate-600">Review submitted attempts and complete manual scoring.</p>
        </div>
        <a href="{{ route('teacher.assessments.index') }}" class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-black">Back to Assessments</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-100 text-left text-xs uppercase tracking-[0.12em] text-slate-500">
                    <tr><th class="px-4 py-3">Learner</th><th class="px-4 py-3">Attempt</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Score</th><th class="px-4 py-3">Submitted</th><th class="px-4 py-3">Action</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($attempts as $attempt)
                        <tr>
                            <td class="px-4 py-4 font-black">{{ $attempt->learner->name }}</td>
                            <td class="px-4 py-4">#{{ $attempt->attempt_number }}</td>
                            <td class="px-4 py-4">{{ str($attempt->status)->replace('_',' ')->title() }}</td>
                            <td class="px-4 py-4">{{ $attempt->earned_marks ?? 0 }} / {{ $attempt->total_marks_snapshot }}</td>
                            <td class="px-4 py-4">{{ $attempt->submitted_at?->format('d M Y, h:i A') ?? '—' }}</td>
                            <td class="px-4 py-4"><a href="{{ route('teacher.assessments.attempts.review', $attempt) }}" class="font-black underline">Review</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">No submitted attempts yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $attempts->links() }}
</div>
@endsection
