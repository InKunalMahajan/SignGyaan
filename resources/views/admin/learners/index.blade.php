@extends('layouts.admin')

@section('title', 'Learners - SignGyaan Admin')
@section('page-title', 'Learners')
@section('description', 'Review learner accounts, academic profiles, learning progress and assessment activity.')

@section('content')
<section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
    <div class="mx-auto max-w-7xl">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Learner management</p>
            <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Learners</h2>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-sign-muted">Monitor learner accounts, academic details, course progress and assessment participation.</p>
        </div>

        <div class="mt-7 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([['Total learners', $totalLearners], ['Active learners', $activeLearners], ['With learning progress', $learnersWithProgress], ['Academic profile complete', $learnersWithAcademicProfile]] as [$label, $value])
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">{{ $label }}</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.learners.index') }}" class="mt-7 rounded-2xl border border-sign-border bg-white p-4 sm:rounded-3xl sm:p-5" role="search">
            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-6 xl:items-end">
                <div class="xl:col-span-2">
                    <label for="learner-search" class="mb-2 block text-sm font-semibold text-sign-primary">Search learner</label>
                    <input id="learner-search" type="search" name="q" value="{{ request('q') }}" placeholder="Name or email" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                </div>
                <div>
                    <label for="learner-status" class="mb-2 block text-sm font-semibold text-sign-primary">Status</label>
                    <select id="learner-status" name="status" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                        <option value="">All statuses</option>
                        @foreach ($statusOptions as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label for="learner-board" class="mb-2 block text-sm font-semibold text-sign-primary">Board</label>
                    <select id="learner-board" name="board" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                        <option value="">All boards</option>
                        @foreach ($boardOptions as $value => $label)<option value="{{ $value }}" @selected(request('board') === $value)>{{ $label }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label for="learner-standard" class="mb-2 block text-sm font-semibold text-sign-primary">Standard</label>
                    <select id="learner-standard" name="standard" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                        <option value="">All standards</option>
                        @foreach ($standardOptions as $value => $label)<option value="{{ $value }}" @selected(request('standard') === $value)>{{ $label }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label for="learner-sort" class="mb-2 block text-sm font-semibold text-sign-primary">Sort</label>
                    <select id="learner-sort" name="sort" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                        <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest</option>
                        <option value="name" @selected(request('sort') === 'name')>Name</option>
                        <option value="activity" @selected(request('sort') === 'activity')>Recent activity</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap gap-2">
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white">Apply filters</button>
                @if(request()->hasAny(['q','status','board','standard','sort']))<a href="{{ route('admin.learners.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border px-4 py-3 text-sm font-semibold text-sign-primary">Clear</a>@endif
            </div>
        </form>

        <div class="mt-7 overflow-hidden rounded-2xl border border-sign-border bg-white sm:rounded-3xl">
            @if($learners->count())
                <div class="hidden overflow-x-auto lg:block">
                    <table class="min-w-full divide-y divide-sign-border text-left text-sm">
                        <thead class="bg-sign-soft text-xs font-semibold uppercase tracking-wider text-sign-muted">
                        <tr><th class="px-5 py-4">Learner</th><th class="px-5 py-4">Academic profile</th><th class="px-5 py-4">Learning</th><th class="px-5 py-4">Assessments</th><th class="px-5 py-4">Status</th><th class="px-5 py-4 text-right">Action</th></tr>
                        </thead>
                        <tbody class="divide-y divide-sign-border">
                        @foreach($learners as $learner)
                            <tr>
                                <td class="px-5 py-4"><p class="font-semibold text-sign-primary">{{ $learner->name }}</p><p class="mt-1 text-xs text-sign-muted">{{ $learner->email }}</p></td>
                                <td class="px-5 py-4 text-sign-muted">{{ $boardOptions[$learner->education_board] ?? 'Not set' }}<br><span class="text-xs">{{ $standardOptions[$learner->standard] ?? 'Standard not set' }} · {{ $learner->academic_year ?? 'Year not set' }}</span></td>
                                <td class="px-5 py-4"><p class="font-semibold text-sign-text">{{ $learner->learning_progress_count }} courses</p><p class="mt-1 text-xs text-sign-muted">{{ $learner->learning_progress_max_last_accessed_at ? \Illuminate\Support\Carbon::parse($learner->learning_progress_max_last_accessed_at)->diffForHumans() : 'No activity' }}</p></td>
                                <td class="px-5 py-4 text-sign-text">{{ $learner->assessment_attempts_count }} attempts</td>
                                <td class="px-5 py-4"><span class="rounded-full bg-sign-soft px-2.5 py-1 text-xs font-semibold text-sign-primary">{{ $statusOptions[$learner->status] ?? ucfirst($learner->status) }}</span></td>
                                <td class="px-5 py-4 text-right"><a href="{{ route('admin.learners.show', $learner) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary">View learner</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="divide-y divide-sign-border lg:hidden">
                    @foreach($learners as $learner)
                        <article class="p-5">
                            <div class="flex items-start justify-between gap-3"><div><h3 class="font-heading text-xl font-semibold text-sign-primary">{{ $learner->name }}</h3><p class="mt-1 break-all text-xs text-sign-muted">{{ $learner->email }}</p></div><span class="rounded-full bg-sign-soft px-2.5 py-1 text-xs font-semibold text-sign-primary">{{ $statusOptions[$learner->status] ?? ucfirst($learner->status) }}</span></div>
                            <div class="mt-4 grid grid-cols-2 gap-3 text-sm"><div class="rounded-xl bg-sign-soft p-3"><p class="text-xs text-sign-muted">Courses</p><p class="mt-1 font-semibold text-sign-primary">{{ $learner->learning_progress_count }}</p></div><div class="rounded-xl bg-sign-soft p-3"><p class="text-xs text-sign-muted">Assessments</p><p class="mt-1 font-semibold text-sign-primary">{{ $learner->assessment_attempts_count }}</p></div></div>
                            <p class="mt-4 text-xs leading-5 text-sign-muted">{{ $boardOptions[$learner->education_board] ?? 'Board not set' }} · {{ $standardOptions[$learner->standard] ?? 'Standard not set' }} · {{ $learner->academic_year ?? 'Academic year not set' }}</p>
                            <a href="{{ route('admin.learners.show', $learner) }}" class="mt-4 inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-sign-border text-sm font-semibold text-sign-primary">View learner</a>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="p-10 text-center"><h3 class="font-heading text-2xl font-semibold text-sign-primary">No learners found</h3><p class="mt-2 text-sm text-sign-muted">Try clearing the current filters.</p></div>
            @endif
        </div>

        @if($learners->hasPages())<div class="mt-6">{{ $learners->links() }}</div>@endif
    </div>
</section>
@endsection
