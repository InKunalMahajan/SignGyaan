<section class="px-4 pb-10 sm:px-6 lg:px-8" aria-labelledby="dashboard-filters-reports-heading">
    <div class="mx-auto max-w-7xl rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl sm:p-7">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Filters & reports</p>
                <h2 id="dashboard-filters-reports-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary">Dashboard Filters & Reports</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-sign-muted">Use academic profile filters to move quickly to the learners you need, then open focused management reports.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Reset dashboard view</a>
        </div>

        <form method="GET" action="{{ route('admin.learners.index') }}" class="mt-6 rounded-2xl bg-sign-soft p-5" aria-labelledby="academic-filter-heading">
            <h3 id="academic-filter-heading" class="font-heading text-lg font-semibold text-sign-primary">Academic Learner Filter</h3>
            <p class="mt-1 text-xs text-sign-muted">Open Learner Management with board and standard filters already applied.</p>
            <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div>
                    <label for="dashboard-board" class="mb-1.5 block text-sm font-semibold text-sign-primary">Board</label>
                    <select id="dashboard-board" name="board" class="min-h-11 w-full rounded-xl border border-sign-border bg-white px-3 py-2 text-sm text-sign-text focus:border-sign-cyan focus:outline-none focus:ring-2 focus:ring-sign-light">
                        <option value="">All boards</option>
                        @foreach ($dashboardFilterOptions['boards'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="dashboard-standard" class="mb-1.5 block text-sm font-semibold text-sign-primary">Standard</label>
                    <select id="dashboard-standard" name="standard" class="min-h-11 w-full rounded-xl border border-sign-border bg-white px-3 py-2 text-sm text-sign-text focus:border-sign-cyan focus:outline-none focus:ring-2 focus:ring-sign-light">
                        <option value="">All standards</option>
                        @foreach ($dashboardFilterOptions['standards'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="dashboard-status" class="mb-1.5 block text-sm font-semibold text-sign-primary">Account status</label>
                    <select id="dashboard-status" name="status" class="min-h-11 w-full rounded-xl border border-sign-border bg-white px-3 py-2 text-sm text-sign-text focus:border-sign-cyan focus:outline-none focus:ring-2 focus:ring-sign-light">
                        <option value="">All statuses</option>
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                        <option value="disabled">Disabled</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-sign-primary px-4 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Apply learner filters</button>
                </div>
            </div>
        </form>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Report shortcuts">
            @foreach ([
                ['title' => 'Learner Progress Report', 'text' => 'Review learner course progress, completion and recent learning.', 'route' => 'admin.learners.index'],
                ['title' => 'Assessment Results Report', 'text' => 'Review assessment attempts, scores, pass status and learner results.', 'route' => 'admin.assessment-results.index'],
                ['title' => 'Teacher Assignment Report', 'text' => 'Review teacher profiles and assigned subjects or courses.', 'route' => 'admin.teachers.index'],
                ['title' => 'Academic Content Report', 'text' => 'Review subjects and continue into course publishing management.', 'route' => 'admin.subjects.index'],
            ] as $report)
                <a href="{{ route($report['route']) }}" class="group rounded-2xl border border-sign-border p-5 transition hover:border-sign-cyan hover:bg-sign-soft focus:outline-none focus:ring-2 focus:ring-sign-cyan focus:ring-offset-2">
                    <p class="font-heading text-lg font-semibold text-sign-primary">{{ $report['title'] }}</p>
                    <p class="mt-2 text-sm leading-6 text-sign-muted">{{ $report['text'] }}</p>
                    <span class="mt-4 inline-flex text-sm font-semibold text-sign-primary">Open report <span class="ml-1 transition group-hover:translate-x-1" aria-hidden="true">→</span></span>
                </a>
            @endforeach
        </div>

        <div class="mt-6 rounded-2xl border border-sign-border p-5">
            <h3 class="font-heading text-lg font-semibold text-sign-primary">Available Academic Years</h3>
            <div class="mt-3 flex flex-wrap gap-2" aria-label="Academic year options">
                @foreach ($dashboardFilterOptions['academic_years'] as $value => $label)
                    <span class="rounded-full bg-sign-light px-3 py-1.5 text-xs font-semibold text-sign-primary">{{ $label }}</span>
                @endforeach
            </div>
            <p class="mt-3 text-xs leading-5 text-sign-muted">Academic year is shown as a reporting reference. Learner Management currently supports board, standard and account-status filtering.</p>
        </div>
    </div>
</section>
