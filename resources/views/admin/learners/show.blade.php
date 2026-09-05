@extends('layouts.admin')

@section('title', 'Learner Details - SignGyaan Admin')
@section('page-title', 'Learner Details')
@section('description', 'Review a learner academic profile, progress, assessments and recent activity.')

@section('content')
<section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
    <div class="mx-auto max-w-7xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Learner management</p>
                <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">{{ $learner->name }}</h2>
                <p class="mt-2 break-all text-sm text-sign-muted">{{ $learner->email }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.users.academic-profile.edit', $learner) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary">Academic Profile</a>
                <a href="{{ route('admin.users.edit', $learner) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary">Manage Account</a>
                <a href="{{ route('admin.learners.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-4 py-3 text-sm font-semibold text-white">Back to Learners</a>
            </div>
        </div>

        <div class="mt-7 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            @foreach ([['Courses started', $learner->learningProgress->count()], ['Courses completed', $completedCourses], ['Lessons completed', $completedLessons], ['Assessments submitted', $submittedAssessments], ['Average score', $averageAssessment === null ? '—' : $averageAssessment.'%']] as [$label, $value])
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">{{ $label }}</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-7 grid gap-6 xl:grid-cols-[minmax(0,1fr)_21rem] xl:items-start">
            <div class="space-y-6">
                <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="learner-progress-heading">
                    <div class="flex items-end justify-between gap-3">
                        <div><p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Learning</p><h3 id="learner-progress-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary">Course Progress</h3></div>
                        <span class="text-sm font-semibold text-sign-muted">{{ $learner->learningProgress->count() }} courses</span>
                    </div>
                    @if($learner->learningProgress->isNotEmpty())
                        <div class="mt-6 grid gap-4 lg:grid-cols-2">
                            @foreach($learner->learningProgress as $progress)
                                <article class="rounded-2xl border border-sign-border bg-sign-soft p-5">
                                    <div class="flex items-center justify-between gap-3"><span class="rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-sign-primary">{{ $progress->subject_name }}</span><span class="text-xs font-semibold text-sign-cyan-dark">{{ $progress->progressPercent() }}%</span></div>
                                    <h4 class="mt-3 font-heading text-xl font-semibold text-sign-primary">{{ $progress->course_title }}</h4>
                                    <p class="mt-2 text-sm text-sign-muted">{{ $progress->completedLessonsCount() }} of {{ $progress->total_lessons }} lessons completed</p>
                                    <div class="mt-4 h-2 overflow-hidden rounded-full bg-white" role="progressbar" aria-label="{{ $progress->course_title }} progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $progress->progressPercent() }}"><div class="h-full rounded-full bg-sign-primary" style="width: {{ $progress->progressPercent() }}%"></div></div>
                                    <p class="mt-3 text-xs text-sign-muted">Last accessed: {{ $progress->last_accessed_at?->diffForHumans() ?? 'Not recorded' }}</p>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-6 rounded-2xl border border-dashed border-sign-border bg-sign-soft p-6 text-center"><p class="font-semibold text-sign-primary">No saved course progress yet.</p></div>
                    @endif
                </section>

                <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="learner-assessment-heading">
                    <div class="flex items-end justify-between gap-3"><div><p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Assessment</p><h3 id="learner-assessment-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary">Recent Assessment Attempts</h3></div><span class="text-sm font-semibold text-sign-muted">{{ $passedAssessments }} passed</span></div>
                    @if($learner->assessmentAttempts->isNotEmpty())
                        <div class="mt-6 overflow-x-auto">
                            <table class="min-w-full divide-y divide-sign-border text-left text-sm">
                                <thead class="text-xs uppercase tracking-wider text-sign-muted"><tr><th class="py-3 pr-4">Assessment</th><th class="py-3 pr-4">Score</th><th class="py-3 pr-4">Result</th><th class="py-3">Submitted</th></tr></thead>
                                <tbody class="divide-y divide-sign-border">
                                @foreach($learner->assessmentAttempts as $attempt)
                                    <tr><td class="py-4 pr-4 font-semibold text-sign-primary">{{ $attempt->assessment?->title ?? 'Assessment' }}</td><td class="py-4 pr-4 text-sign-text">{{ $attempt->percentage !== null ? rtrim(rtrim(number_format((float)$attempt->percentage, 2), '0'), '.').'%' : '—' }}</td><td class="py-4 pr-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $attempt->passed ? 'bg-sign-light text-sign-primary' : 'bg-gray-100 text-sign-muted' }}">{{ $attempt->isSubmitted() ? ($attempt->passed ? 'Passed' : 'Not passed') : ucfirst($attempt->status) }}</span></td><td class="py-4 text-sign-muted">{{ $attempt->submitted_at?->diffForHumans() ?? 'Not submitted' }}</td></tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="mt-6 rounded-2xl border border-dashed border-sign-border bg-sign-soft p-6 text-center"><p class="font-semibold text-sign-primary">No assessment attempts yet.</p></div>
                    @endif
                </section>

                <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="learner-activity-heading">
                    <div><p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Activity</p><h3 id="learner-activity-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary">Recent Learning Activity</h3></div>
                    @if($learner->learningActivities->isNotEmpty())
                        <div class="mt-6 space-y-3">
                            @foreach($learner->learningActivities as $activity)
                                <article class="flex flex-col gap-2 rounded-xl bg-sign-soft p-4 sm:flex-row sm:items-center sm:justify-between"><div><p class="font-semibold text-sign-primary">{{ $activity->title }}</p><p class="mt-1 text-xs text-sign-muted">{{ \Illuminate\Support\Str::headline($activity->activity_type) }}</p></div><time class="text-xs font-semibold text-sign-muted">{{ $activity->occurred_at?->diffForHumans() ?? 'Date unavailable' }}</time></article>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-6 rounded-2xl border border-dashed border-sign-border bg-sign-soft p-6 text-center"><p class="font-semibold text-sign-primary">No recent learning activity.</p></div>
                    @endif
                </section>
            </div>

            <aside class="space-y-5 xl:sticky xl:top-24" aria-label="Learner summary">
                <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Academic profile</p>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="rounded-xl bg-sign-soft px-4 py-3"><dt class="text-xs text-sign-muted">Board</dt><dd class="mt-1 font-semibold text-sign-primary">{{ $boardOptions[$learner->education_board] ?? 'Not set' }}</dd></div>
                        <div class="rounded-xl bg-sign-soft px-4 py-3"><dt class="text-xs text-sign-muted">Standard</dt><dd class="mt-1 font-semibold text-sign-primary">{{ $standardOptions[$learner->standard] ?? 'Not set' }}</dd></div>
                        <div class="rounded-xl bg-sign-soft px-4 py-3"><dt class="text-xs text-sign-muted">Academic year</dt><dd class="mt-1 font-semibold text-sign-primary">{{ $learner->academic_year ?? 'Not set' }}</dd></div>
                    </dl>
                </section>

                <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Account</p>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between gap-3"><dt class="text-sign-muted">Status</dt><dd class="font-semibold text-sign-primary">{{ $statusOptions[$learner->status] ?? ucfirst($learner->status) }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-sign-muted">Email</dt><dd class="font-semibold text-sign-primary">{{ $learner->email_verified_at ? 'Verified' : 'Unverified' }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-sign-muted">Last login</dt><dd class="text-right font-semibold text-sign-primary">{{ $learner->last_login_at?->diffForHumans() ?? 'Not recorded' }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-sign-muted">Joined</dt><dd class="font-semibold text-sign-primary">{{ $learner->created_at?->format('d M Y') }}</dd></div>
                    </dl>
                </section>
            </aside>
        </div>
    </div>
</section>
@endsection
