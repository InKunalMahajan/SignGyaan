<section class="px-4 pb-6 sm:px-6 sm:pb-8 lg:px-8 lg:pb-10" data-teacher-overview-dashboard>
    <div class="mx-auto max-w-7xl rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl sm:p-7" aria-labelledby="teacher-overview-heading">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Teacher overview</p>
                <h2 id="teacher-overview-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary">Teacher Overview Dashboard</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-sign-muted">Monitor teacher accounts, professional profiles, subject assignments and course assignments.</p>
            </div>
            <a href="{{ route('admin.teachers.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Open Teacher Management →</a>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Total teachers', 'value' => $teacherStats['total'], 'text' => number_format($teacherStats['active']).' active teacher accounts'],
                ['label' => 'Assigned teachers', 'value' => $teacherStats['with_assignments'], 'text' => $teacherStats['assignment_rate'].'% have subject or course assignments'],
                ['label' => 'Subject assignments', 'value' => $teacherStats['subject_assignments'], 'text' => number_format($teacherStats['with_subjects']).' teachers assigned to subjects'],
                ['label' => 'Course assignments', 'value' => $teacherStats['course_assignments'], 'text' => number_format($teacherStats['with_courses']).' teachers assigned to courses'],
            ] as $stat)
                <article class="rounded-2xl bg-sign-soft p-5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-muted">{{ $stat['label'] }}</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ number_format($stat['value']) }}</p>
                    <p class="mt-2 text-xs leading-5 text-sign-muted">{{ $stat['text'] }}</p>
                </article>
            @endforeach
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <section class="rounded-2xl border border-sign-border p-5" aria-labelledby="teacher-account-health-heading">
                <h3 id="teacher-account-health-heading" class="font-heading text-xl font-semibold text-sign-primary">Teacher Account Health</h3>
                <dl class="mt-4 space-y-3 text-sm">
                    @foreach ([
                        ['label' => 'Active teachers', 'value' => $teacherStats['active']],
                        ['label' => 'Suspended teachers', 'value' => $teacherStats['suspended']],
                        ['label' => 'Disabled teachers', 'value' => $teacherStats['disabled']],
                        ['label' => 'Recent logins in 30 days', 'value' => $teacherStats['recent_logins_30_days']],
                        ['label' => 'Unassigned teachers', 'value' => $teacherStats['unassigned']],
                    ] as $row)
                        <div class="flex items-center justify-between gap-4 rounded-xl bg-sign-soft px-4 py-3">
                            <dt class="font-medium text-sign-muted">{{ $row['label'] }}</dt>
                            <dd class="font-semibold text-sign-primary">{{ number_format($row['value']) }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>

            <section class="rounded-2xl border border-sign-border p-5" aria-labelledby="teacher-profile-coverage-heading">
                <h3 id="teacher-profile-coverage-heading" class="font-heading text-xl font-semibold text-sign-primary">Profile & Assignment Coverage</h3>
                <div class="mt-4 space-y-4">
                    <div class="rounded-xl bg-sign-soft p-4">
                        <div class="flex items-center justify-between gap-4 text-sm"><span class="font-semibold text-sign-primary">Professional profiles</span><span class="font-semibold text-sign-cyan-dark">{{ $teacherStats['profile_completion_rate'] }}%</span></div>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-white" role="progressbar" aria-label="Teacher professional profile coverage" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $teacherStats['profile_completion_rate'] }}"><div class="h-full rounded-full bg-sign-cyan" style="width: {{ $teacherStats['profile_completion_rate'] }}%"></div></div>
                        <p class="mt-2 text-xs text-sign-muted">{{ number_format($teacherStats['with_profile']) }} of {{ number_format($teacherStats['total']) }} teachers have professional profile details.</p>
                    </div>
                    <div class="rounded-xl bg-sign-soft p-4">
                        <div class="flex items-center justify-between gap-4 text-sm"><span class="font-semibold text-sign-primary">Assignment coverage</span><span class="font-semibold text-sign-cyan-dark">{{ $teacherStats['assignment_rate'] }}%</span></div>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-white" role="progressbar" aria-label="Teacher assignment coverage" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $teacherStats['assignment_rate'] }}"><div class="h-full rounded-full bg-sign-cyan" style="width: {{ $teacherStats['assignment_rate'] }}%"></div></div>
                        <p class="mt-2 text-xs text-sign-muted">{{ number_format($teacherStats['with_assignments']) }} teachers have at least one subject or course assignment.</p>
                    </div>
                </div>
            </section>
        </div>

        <section class="mt-6 overflow-hidden rounded-2xl border border-sign-border" aria-labelledby="recent-teachers-heading">
            <div class="flex flex-col gap-2 border-b border-sign-border bg-sign-soft px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 id="recent-teachers-heading" class="font-heading text-xl font-semibold text-sign-primary">Teacher Assignment Snapshot</h3>
                    <p class="mt-1 text-xs text-sign-muted">Recently updated teacher accounts and their current academic assignments.</p>
                </div>
                <a href="{{ route('admin.teachers.index') }}" class="text-sm font-semibold text-sign-primary hover:text-sign-cyan-dark">View all teachers →</a>
            </div>

            @forelse ($recentTeachers as $teacher)
                <div class="flex flex-col gap-4 border-b border-sign-border px-5 py-4 last:border-b-0 lg:flex-row lg:items-center lg:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-semibold text-sign-primary">{{ $teacher->name }}</p>
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $teacher->isActive() ? 'bg-emerald-50 text-emerald-700' : ($teacher->isSuspended() ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">{{ ucfirst($teacher->status) }}</span>
                        </div>
                        <p class="mt-1 break-all text-xs text-sign-muted">{{ $teacher->email }}</p>
                        <p class="mt-2 text-xs text-sign-muted">
                            @if ($teacher->teacherProfile)
                                {{ $teacher->teacherProfile->specialization ?: $teacher->teacherProfile->qualification ?: 'Professional profile added' }}
                                @if ($teacher->teacherProfile->employee_code) · {{ $teacher->teacherProfile->employee_code }} @endif
                            @else
                                Professional profile not completed
                            @endif
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="rounded-full bg-sign-light px-3 py-1.5 font-semibold text-sign-primary">{{ number_format($teacher->teaching_subjects_count) }} {{ \Illuminate\Support\Str::plural('subject', $teacher->teaching_subjects_count) }}</span>
                        <span class="rounded-full bg-sign-light px-3 py-1.5 font-semibold text-sign-primary">{{ number_format($teacher->teaching_courses_count) }} {{ \Illuminate\Support\Str::plural('course', $teacher->teaching_courses_count) }}</span>
                        <a href="{{ route('admin.teachers.edit', $teacher) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border px-3 py-2 font-semibold text-sign-primary hover:bg-sign-soft">Manage</a>
                    </div>
                </div>
            @empty
                <div class="px-5 py-8 text-center">
                    <p class="text-sm font-semibold text-sign-primary">No teacher accounts yet</p>
                    <p class="mt-1 text-xs text-sign-muted">Teacher statistics and assignment details will appear here after teacher accounts are created.</p>
                </div>
            @endforelse
        </section>
    </div>
</section>
