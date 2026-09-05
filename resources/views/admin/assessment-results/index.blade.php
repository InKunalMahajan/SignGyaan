@extends('layouts.admin')

@section('title', 'Assessment Results - SignGyaan Admin')
@section('page-title', 'Assessment Results')
@section('description', 'Review learner attempts, scores, pass rates and assessment performance.')

@section('content')
<section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
    <div class="mx-auto max-w-7xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Learner performance</p>
                <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Assessment Results</h2>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-sign-muted">Track learner attempts, submitted scores, pass outcomes and unfinished assessments.</p>
            </div>
            <a href="{{ route('admin.assessments.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-5 py-3 text-sm font-semibold text-sign-primary hover:bg-sign-soft">Manage Assessments</a>
        </div>

        <div class="mt-7 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            @foreach ([
                ['label' => 'Total attempts', 'value' => $summary['total_attempts']],
                ['label' => 'Submitted', 'value' => $summary['submitted']],
                ['label' => 'Passed', 'value' => $summary['passed']],
                ['label' => 'Pass rate', 'value' => $summary['pass_rate'] === null ? '—' : number_format($summary['pass_rate'], 1).'%'],
                ['label' => 'Average score', 'value' => $summary['average_score'] === null ? '—' : number_format($summary['average_score'], 1).'%'],
            ] as $stat)
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">{{ $stat['label'] }}</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $stat['value'] }}</p>
                </div>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.assessment-results.index') }}" class="mt-7 rounded-2xl border border-sign-border bg-white p-4 sm:rounded-3xl sm:p-5" role="search">
            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-[minmax(0,1fr)_13rem_12rem_12rem_12rem_auto] xl:items-end">
                <div>
                    <label for="results-search" class="mb-2 block text-sm font-semibold text-sign-primary">Search</label>
                    <input id="results-search" type="search" name="q" value="{{ request('q') }}" placeholder="Learner, email, assessment or lesson" class="min-h-12 w-full rounded-xl border border-sign-border px-4 py-3 text-base outline-none focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                </div>
                <div>
                    <label for="results-assessment" class="mb-2 block text-sm font-semibold text-sign-primary">Assessment</label>
                    <select id="results-assessment" name="assessment" class="min-h-12 w-full rounded-xl border border-sign-border px-4 py-3 text-base outline-none focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                        <option value="">All assessments</option>
                        @foreach ($assessments as $assessment)
                            <option value="{{ $assessment->id }}" @selected((string) request('assessment') === (string) $assessment->id)>{{ $assessment->practiceResource?->title ?? 'Assessment #'.$assessment->id }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="results-subject" class="mb-2 block text-sm font-semibold text-sign-primary">Subject</label>
                    <select id="results-subject" name="subject" class="min-h-12 w-full rounded-xl border border-sign-border px-4 py-3 text-base outline-none focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                        <option value="">All subjects</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected((string) request('subject') === (string) $subject->id)>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="results-status" class="mb-2 block text-sm font-semibold text-sign-primary">Status</label>
                    <select id="results-status" name="status" class="min-h-12 w-full rounded-xl border border-sign-border px-4 py-3 text-base outline-none focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                        <option value="">All statuses</option>
                        <option value="in-progress" @selected(request('status') === 'in-progress')>In Progress</option>
                        <option value="submitted" @selected(request('status') === 'submitted')>Submitted</option>
                        <option value="expired" @selected(request('status') === 'expired')>Expired</option>
                    </select>
                </div>
                <div>
                    <label for="results-outcome" class="mb-2 block text-sm font-semibold text-sign-primary">Outcome</label>
                    <select id="results-outcome" name="outcome" class="min-h-12 w-full rounded-xl border border-sign-border px-4 py-3 text-base outline-none focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                        <option value="">All outcomes</option>
                        <option value="passed" @selected(request('outcome') === 'passed')>Passed</option>
                        <option value="not-passed" @selected(request('outcome') === 'not-passed')>Not Passed</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button class="inline-flex min-h-12 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white hover:bg-sign-dark">Filter</button>
                    @if (request()->hasAny(['q','assessment','subject','status','outcome']))
                        <a href="{{ route('admin.assessment-results.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-sign-border px-4 py-3 text-sm font-semibold text-sign-primary hover:bg-sign-soft">Clear</a>
                    @endif
                </div>
            </div>
        </form>

        <div class="mt-7 overflow-hidden rounded-2xl border border-sign-border bg-white sm:rounded-3xl">
            @if ($attempts->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-sign-border text-left text-sm">
                        <thead class="bg-sign-soft text-xs font-semibold uppercase tracking-wider text-sign-muted">
                            <tr>
                                <th class="px-5 py-4">Learner</th>
                                <th class="px-5 py-4">Assessment</th>
                                <th class="px-5 py-4">Attempt</th>
                                <th class="px-5 py-4">Score</th>
                                <th class="px-5 py-4">Status</th>
                                <th class="px-5 py-4">Updated</th>
                                <th class="px-5 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sign-border">
                            @foreach ($attempts as $attempt)
                                @php
                                    $practice = $attempt->assessment?->practiceResource;
                                    $lesson = $practice?->lesson;
                                    $course = $lesson?->unit?->course;
                                @endphp
                                <tr class="align-top">
                                    <td class="px-5 py-4"><p class="font-semibold text-sign-primary">{{ $attempt->user?->name ?? 'Deleted learner' }}</p><p class="mt-1 text-xs text-sign-muted">{{ $attempt->user?->email }}</p></td>
                                    <td class="px-5 py-4"><p class="font-semibold text-sign-text">{{ $practice?->title ?? 'Missing assessment' }}</p><p class="mt-1 text-xs text-sign-muted">{{ $course?->title }} @if($lesson) · {{ $lesson->title }} @endif</p></td>
                                    <td class="px-5 py-4 font-semibold text-sign-primary">#{{ $attempt->attempt_number }}</td>
                                    <td class="px-5 py-4">
                                        @if ($attempt->status === 'submitted')
                                            <p class="font-semibold text-sign-primary">{{ number_format((float) $attempt->percentage, 2) }}%</p>
                                            <p class="mt-1 text-xs text-sign-muted">{{ number_format((float) $attempt->score_points, 2) }} / {{ number_format((float) $attempt->max_points, 2) }}</p>
                                        @else
                                            <span class="text-sign-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        <span @class([
                                            'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                            'bg-sign-light text-sign-primary' => $attempt->status === 'submitted' && $attempt->passed,
                                            'bg-red-50 text-red-700' => $attempt->status === 'submitted' && ! $attempt->passed,
                                            'bg-amber-50 text-amber-700' => $attempt->status === 'in-progress',
                                            'bg-gray-100 text-sign-muted' => $attempt->status === 'expired',
                                        ])>
                                            {{ $attempt->status === 'submitted' ? ($attempt->passed ? 'Passed' : 'Not Passed') : str($attempt->status)->headline() }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-xs text-sign-muted">{{ $attempt->updated_at?->format('d M Y, H:i') }}</td>
                                    <td class="px-5 py-4 text-right"><a href="{{ route('admin.assessment-results.show', $attempt) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary hover:bg-sign-soft">View Details</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center sm:p-12"><h3 class="font-heading text-2xl font-semibold text-sign-primary">No learner attempts found</h3><p class="mt-2 text-sm text-sign-muted">Assessment attempts will appear here after learners start quizzes or exercises.</p></div>
            @endif
        </div>

        @if ($attempts->hasPages())<div class="mt-6">{{ $attempts->links() }}</div>@endif
    </div>
</section>
@endsection
