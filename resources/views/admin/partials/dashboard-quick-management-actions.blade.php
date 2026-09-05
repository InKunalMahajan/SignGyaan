<section class="mx-auto mt-7 max-w-7xl px-4 pb-2 sm:px-6 lg:px-8" aria-labelledby="quick-management-actions-heading" data-quick-management-actions-dashboard>
    <div class="rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl sm:p-7">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Quick management</p>
                <h2 id="quick-management-actions-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary">Quick Management Actions</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-sign-muted">Jump directly to common content, people and platform-management tasks without navigating through multiple admin pages.</p>
            </div>
            <a href="{{ route('admin.settings.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Platform Settings →</a>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-3">
            <section class="rounded-2xl border border-sign-border p-5" aria-labelledby="quick-academic-actions-heading">
                <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Academic content</p>
                <h3 id="quick-academic-actions-heading" class="mt-2 font-heading text-xl font-semibold text-sign-primary">Create Learning Content</h3>
                <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                    @foreach ([
                        ['label' => 'Add Subject', 'description' => 'Create a new subject category.', 'route' => 'admin.subjects.create'],
                        ['label' => 'Add Course', 'description' => 'Create a course inside a subject.', 'route' => 'admin.courses.create'],
                        ['label' => 'Add Unit', 'description' => 'Add an ordered unit to a course.', 'route' => 'admin.units.create'],
                        ['label' => 'Add Lesson', 'description' => 'Create a new learning lesson.', 'route' => 'admin.lessons.create'],
                    ] as $action)
                        <a href="{{ route($action['route']) }}" class="group flex min-h-20 items-center justify-between gap-4 rounded-xl bg-sign-soft px-4 py-3 transition hover:bg-sign-light focus:outline-none focus:ring-4 focus:ring-sign-light">
                            <span class="min-w-0"><span class="block font-semibold text-sign-primary">{{ $action['label'] }}</span><span class="mt-1 block text-xs leading-5 text-sign-muted">{{ $action['description'] }}</span></span>
                            <span class="shrink-0 text-lg text-sign-cyan-dark" aria-hidden="true">→</span>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="rounded-2xl border border-sign-border p-5" aria-labelledby="quick-learning-actions-heading">
                <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Learning tools</p>
                <h3 id="quick-learning-actions-heading" class="mt-2 font-heading text-xl font-semibold text-sign-primary">Build Learning Support</h3>
                <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                    @foreach ([
                        ['label' => 'Add Practice Resource', 'description' => 'Create practice or supporting material.', 'route' => 'admin.practice.create'],
                        ['label' => 'Add Assessment', 'description' => 'Create a quiz or assessment.', 'route' => 'admin.assessments.create'],
                        ['label' => 'Add Media', 'description' => 'Upload learning images, videos or files.', 'route' => 'admin.media.create'],
                        ['label' => 'Add Vocabulary', 'description' => 'Create an ISL vocabulary term.', 'route' => 'admin.vocabulary.create'],
                    ] as $action)
                        <a href="{{ route($action['route']) }}" class="group flex min-h-20 items-center justify-between gap-4 rounded-xl bg-sign-soft px-4 py-3 transition hover:bg-sign-light focus:outline-none focus:ring-4 focus:ring-sign-light">
                            <span class="min-w-0"><span class="block font-semibold text-sign-primary">{{ $action['label'] }}</span><span class="mt-1 block text-xs leading-5 text-sign-muted">{{ $action['description'] }}</span></span>
                            <span class="shrink-0 text-lg text-sign-cyan-dark" aria-hidden="true">→</span>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="rounded-2xl border border-sign-border p-5" aria-labelledby="quick-people-actions-heading">
                <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">People & results</p>
                <h3 id="quick-people-actions-heading" class="mt-2 font-heading text-xl font-semibold text-sign-primary">Manage Platform Activity</h3>
                <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                    @foreach ([
                        ['label' => 'Manage Users', 'description' => 'Roles, status and account administration.', 'route' => 'admin.users.index'],
                        ['label' => 'Manage Teachers', 'description' => 'Teacher profiles and assignments.', 'route' => 'admin.teachers.index'],
                        ['label' => 'Manage Learners', 'description' => 'Learner profiles and learning progress.', 'route' => 'admin.learners.index'],
                        ['label' => 'Assessment Results', 'description' => 'Review submitted learner assessment results.', 'route' => 'admin.assessment-results.index'],
                    ] as $action)
                        <a href="{{ route($action['route']) }}" class="group flex min-h-20 items-center justify-between gap-4 rounded-xl bg-sign-soft px-4 py-3 transition hover:bg-sign-light focus:outline-none focus:ring-4 focus:ring-sign-light">
                            <span class="min-w-0"><span class="block font-semibold text-sign-primary">{{ $action['label'] }}</span><span class="mt-1 block text-xs leading-5 text-sign-muted">{{ $action['description'] }}</span></span>
                            <span class="shrink-0 text-lg text-sign-cyan-dark" aria-hidden="true">→</span>
                        </a>
                    @endforeach
                </div>
            </section>
        </div>

        <div class="mt-6 flex flex-col gap-3 rounded-2xl bg-sign-soft p-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-sign-primary">Bulk user operations</p>
                <p class="mt-1 text-xs leading-5 text-sign-muted">Import, export or update multiple user accounts from one workspace.</p>
            </div>
            <a href="{{ route('admin.users.bulk.index') }}" class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-xl bg-sign-primary px-4 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark focus:outline-none focus:ring-4 focus:ring-sign-light">Open Bulk User Management</a>
        </div>
    </div>
</section>
