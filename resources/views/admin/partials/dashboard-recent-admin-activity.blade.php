<section class="mx-auto mt-7 max-w-7xl px-4 pb-0 sm:px-6 lg:px-8" aria-labelledby="recent-admin-activity-heading" data-admin-activity-dashboard>
    <div class="rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl sm:p-7">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Administration activity</p>
                <h2 id="recent-admin-activity-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary">Recent Admin Activity Dashboard</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-sign-muted">Review recently created or updated records across user, course, lesson and assessment management.</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Open Management →</a>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Changes in 24 hours', 'value' => $adminActivityStats['changes_24_hours'], 'text' => 'Recent managed records'],
                ['label' => 'Changes in 7 days', 'value' => $adminActivityStats['changes_7_days'], 'text' => 'Users and academic records'],
                ['label' => 'Lessons changed', 'value' => $adminActivityStats['lessons_7_days'], 'text' => 'Lesson records in 7 days'],
                ['label' => 'Courses changed', 'value' => $adminActivityStats['courses_7_days'], 'text' => 'Course records in 7 days'],
            ] as $stat)
                <article class="rounded-2xl bg-sign-soft p-5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-muted">{{ $stat['label'] }}</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ number_format($stat['value']) }}</p>
                    <p class="mt-2 text-xs leading-5 text-sign-muted">{{ $stat['text'] }}</p>
                </article>
            @endforeach
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
            <section class="overflow-hidden rounded-2xl border border-sign-border" aria-labelledby="recent-management-changes-heading">
                <div class="border-b border-sign-border bg-sign-soft px-5 py-4">
                    <h3 id="recent-management-changes-heading" class="font-heading text-xl font-semibold text-sign-primary">Recent Management Changes</h3>
                    <p class="mt-1 text-xs text-sign-muted">Latest record updates across core admin-managed areas.</p>
                </div>

                @forelse ($recentAdminActivity as $activity)
                    <div class="flex flex-col gap-3 border-b border-sign-border px-5 py-4 last:border-b-0 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-sign-light px-2.5 py-1 text-xs font-semibold text-sign-primary">{{ $activity['type'] }}</span>
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-sign-muted">{{ $activity['action'] }}</span>
                            </div>
                            <p class="mt-2 truncate font-semibold text-sign-primary">{{ $activity['title'] }}</p>
                            <p class="mt-1 text-xs text-sign-muted">{{ $activity['description'] }}</p>
                        </div>
                        <div class="flex shrink-0 flex-col items-start gap-2 sm:items-end">
                            <time class="text-xs font-medium text-sign-muted" datetime="{{ $activity['occurred_at']?->toIso8601String() }}">{{ $activity['occurred_at']?->diffForHumans() }}</time>
                            <a href="{{ $activity['url'] }}" class="text-sm font-semibold text-sign-primary hover:text-sign-cyan-dark">Open record →</a>
                        </div>
                    </div>
                @empty
                    <p class="px-5 py-8 text-center text-sm text-sign-muted">No recent management changes are available yet.</p>
                @endforelse
            </section>

            <aside class="rounded-2xl border border-sign-border p-5" aria-labelledby="activity-breakdown-heading">
                <h3 id="activity-breakdown-heading" class="font-heading text-xl font-semibold text-sign-primary">7-Day Breakdown</h3>
                <dl class="mt-4 space-y-3 text-sm">
                    @foreach ([
                        ['label' => 'User records', 'value' => $adminActivityStats['users_7_days']],
                        ['label' => 'Course records', 'value' => $adminActivityStats['courses_7_days']],
                        ['label' => 'Lesson records', 'value' => $adminActivityStats['lessons_7_days']],
                        ['label' => 'Assessment records', 'value' => $adminActivityStats['assessments_7_days']],
                    ] as $row)
                        <div class="flex items-center justify-between gap-4 rounded-xl bg-sign-soft px-4 py-3">
                            <dt class="font-medium text-sign-muted">{{ $row['label'] }}</dt>
                            <dd class="font-semibold text-sign-primary">{{ number_format($row['value']) }}</dd>
                        </div>
                    @endforeach
                </dl>
                <p class="mt-4 text-xs leading-5 text-sign-muted">This dashboard summarizes record timestamps. It is not a security audit log of which administrator performed each change.</p>
            </aside>
        </div>
    </div>
</section>
