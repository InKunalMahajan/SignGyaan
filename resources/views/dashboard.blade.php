@extends('layouts.app')

@section('title', 'Dashboard - SignGyaan')
@section('description', 'Your SignGyaan learning dashboard with course progress, assessment attempts and quick access to continue learning.')

@section('content')
    @php
        $user = auth()->user();
        $firstName = trim(explode(' ', $user->name)[0] ?? $user->name);
        $initial = strtoupper(substr(trim($user->name), 0, 1));
    @endphp

    <section class="border-b border-sign-border bg-sign-soft py-8 sm:py-10 lg:py-12">
        <x-container>
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem] lg:items-center">
                <div>
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-sign-primary font-heading text-xl font-semibold text-white sm:h-16 sm:w-16 sm:text-2xl" aria-hidden="true">{{ $initial }}</div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Your dashboard</p>
                            <h1 class="mt-1 font-heading text-3xl font-semibold tracking-tight text-sign-primary sm:text-4xl lg:text-5xl">Welcome, {{ $firstName }}.</h1>
                        </div>
                    </div>
                    <p class="mt-5 max-w-2xl text-sm leading-7 text-sign-muted sm:text-base">Continue your courses, finish open assessments and review your latest results from one place.</p>
                </div>

                <div class="rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Continue quickly</p>
                    @if ($activeAssessment)
                        @php
                            $activeAssessmentModel = $activeAssessment->assessment;
                            $activePractice = $activeAssessmentModel->practiceResource;
                        @endphp
                        <h2 class="mt-2 font-heading text-xl font-semibold text-sign-primary">{{ $activePractice->title }}</h2>
                        <p class="mt-2 text-sm text-sign-muted">Assessment attempt {{ $activeAssessment->attempt_number }} is in progress.</p>
                        <a href="{{ route('assessment-attempts.show', [$activeAssessmentModel, $activeAssessment]) }}" class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Continue Assessment</a>
                    @elseif ($primaryContinueLearning)
                        <h2 class="mt-2 font-heading text-xl font-semibold text-sign-primary">{{ $primaryContinueLearning['course_title'] }}</h2>
                        <p class="mt-2 text-xs font-semibold uppercase tracking-wide text-sign-muted">Resume lesson</p>
                        <p class="mt-1 text-sm font-semibold text-sign-primary">{{ $primaryContinueLearning['lesson_title'] ?: 'Current lesson' }}</p>
                        @if ($primaryContinueLearning['unit_title'])
                            <p class="mt-1 text-xs text-sign-muted">{{ $primaryContinueLearning['unit_title'] }}</p>
                        @endif
                        <p class="mt-3 text-sm text-sign-muted">{{ $primaryContinueLearning['progress_percent'] }}% course progress</p>
                        <a href="{{ $primaryContinueLearning['resume_url'] }}" class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Continue Learning</a>
                    @else
                        <h2 class="mt-2 font-heading text-xl font-semibold text-sign-primary">Start your first course</h2>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">Choose a subject and begin learning.</p>
                        <a href="{{ route('subjects') }}" class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Browse Subjects</a>
                    @endif
                </div>
            </div>
        </x-container>
    </section>

    <section class="bg-white py-10 sm:py-14 lg:py-16">
        <x-container>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" aria-label="Learning overview">
                <div class="rounded-2xl border border-sign-border p-5 sm:rounded-3xl sm:p-6">
                    <p class="text-sm font-semibold text-sign-muted">Courses in progress</p>
                    <p class="mt-3 font-heading text-3xl font-semibold text-sign-primary">{{ $activeCourses->count() }}</p>
                    <p class="mt-1 text-xs leading-5 text-sign-muted">Courses with saved progress.</p>
                </div>
                <div class="rounded-2xl border border-sign-border p-5 sm:rounded-3xl sm:p-6">
                    <p class="text-sm font-semibold text-sign-muted">Lessons completed</p>
                    <p class="mt-3 font-heading text-3xl font-semibold text-sign-primary">{{ $completedLessons }}</p>
                    <p class="mt-1 text-xs leading-5 text-sign-muted">Completed lessons across your courses.</p>
                </div>
                <div class="rounded-2xl border border-sign-border p-5 sm:rounded-3xl sm:p-6">
                    <p class="text-sm font-semibold text-sign-muted">Assessment attempts</p>
                    <p class="mt-3 font-heading text-3xl font-semibold text-sign-primary">{{ $assessmentSummary['total_attempts'] }}</p>
                    <p class="mt-1 text-xs leading-5 text-sign-muted">{{ $assessmentSummary['passed'] }} passed · {{ $assessmentSummary['in_progress'] }} in progress.</p>
                </div>
                <div class="rounded-2xl border border-sign-border p-5 sm:rounded-3xl sm:p-6">
                    <p class="text-sm font-semibold text-sign-muted">Overall course progress</p>
                    <p class="mt-3 font-heading text-3xl font-semibold text-sign-primary">{{ $overallProgress }}%</p>
                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-sign-soft" role="progressbar" aria-label="Overall learning progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $overallProgress }}">
                        <div class="h-full rounded-full bg-sign-primary" style="width: {{ $overallProgress }}%"></div>
                    </div>
                </div>
            </div>

            <div class="mt-10 grid gap-8 xl:grid-cols-[minmax(0,1fr)_19rem] xl:items-start">
                <div class="min-w-0 space-y-12">
                    @include('partials.learning.continue-learning')

                    @include('partials.learning.recommended-next-lessons')

                    @include('partials.learning.assessment-progress', ['assessmentLimit' => 4])

                    @if ($progressRecords->isEmpty() && $starterCourses->isNotEmpty())
                        <section aria-labelledby="dashboard-start-heading">
                            <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Recommended</p>
                            <h2 id="dashboard-start-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Good places to start</h2>
                            <div class="mt-5 grid gap-4 md:grid-cols-3">
                                @foreach ($starterCourses as $course)
                                    <a href="{{ $course['url'] }}" class="group rounded-2xl border border-sign-border p-5 transition hover:border-sign-cyan hover:shadow-sm sm:rounded-3xl">
                                        <div class="flex flex-wrap gap-2 text-xs font-semibold"><span class="rounded-full bg-sign-soft px-2.5 py-1 text-sign-primary">{{ $course['level'] }}</span><span class="text-sign-cyan-dark">{{ $course['subject'] }}</span></div>
                                        <h3 class="mt-4 font-heading text-xl font-semibold text-sign-primary group-hover:text-sign-cyan-dark">{{ $course['title'] }}</h3>
                                        <p class="mt-3 text-xs text-sign-muted">{{ $course['units'] }} units · {{ $course['lessons'] }} lessons</p>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if ($completedCourses->isNotEmpty())
                        <section aria-labelledby="dashboard-completed-heading">
                            <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Completed</p>
                            <h2 id="dashboard-completed-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Courses you finished</h2>
                            <div class="mt-5 grid gap-4 md:grid-cols-2">
                                @foreach ($completedCourses->take(4) as $progress)
                                    <a href="{{ route('courses.show', ['subject' => $progress->subject_slug, 'course' => $progress->course_slug]) }}" class="rounded-2xl border border-sign-border bg-sign-soft p-5 transition hover:border-sign-cyan sm:rounded-3xl">
                                        <span class="text-xs font-semibold text-sign-cyan-dark">✓ Completed</span>
                                        <h3 class="mt-2 font-heading text-xl font-semibold text-sign-primary">{{ $progress->course_title }}</h3>
                                        <p class="mt-2 text-sm text-sign-muted">{{ $progress->total_lessons }} lessons completed</p>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </div>

                <aside class="space-y-5 xl:sticky xl:top-24" aria-label="Dashboard quick actions">
                    <div class="rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Quick actions</p>
                        <nav class="mt-4 space-y-2">
                            <a href="{{ route('my-learning') }}" class="flex min-h-11 items-center justify-between rounded-xl bg-white px-4 py-3 text-sm font-semibold text-sign-primary hover:bg-sign-light"><span>My Learning</span><span aria-hidden="true">→</span></a>
                            <a href="{{ route('subjects') }}" class="flex min-h-11 items-center justify-between rounded-xl bg-white px-4 py-3 text-sm font-semibold text-sign-primary hover:bg-sign-light"><span>Browse Subjects</span><span aria-hidden="true">→</span></a>
                            <a href="{{ route('explore') }}" class="flex min-h-11 items-center justify-between rounded-xl bg-white px-4 py-3 text-sm font-semibold text-sign-primary hover:bg-sign-light"><span>Explore Learning</span><span aria-hidden="true">→</span></a>
                        </nav>
                    </div>

                    @if ($assessmentSummary['submitted'] > 0)
                        <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Assessment scores</p>
                            <p class="mt-3 text-sm text-sign-muted">Best score</p>
                            <p class="mt-1 font-heading text-3xl font-semibold text-sign-primary">{{ number_format((float) $assessmentSummary['best_score'], 2) }}%</p>
                            <p class="mt-3 text-sm text-sign-muted">Average {{ number_format((float) $assessmentSummary['average_score'], 2) }}%</p>
                        </div>
                    @endif

                    <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Account</p>
                        <p class="mt-3 font-semibold text-sign-primary">{{ $user->name }}</p>
                        <p class="mt-1 break-all text-sm text-sign-muted">{{ $user->email }}</p>
                        <a href="{{ route('profile') }}" class="mt-4 inline-flex min-h-11 items-center text-sm font-semibold text-sign-primary hover:text-sign-cyan-dark">Manage account →</a>
                    </div>
                </aside>
            </div>
        </x-container>
    </section>
@endsection
