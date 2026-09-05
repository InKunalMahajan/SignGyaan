@extends('layouts.app')

@section('title', 'Assessment Performance - SignGyaan')
@section('description', 'Review quiz and assessment performance, scores and attempts.')

@section('content')
<section class="border-b border-sign-border bg-sign-soft py-8 sm:py-12">
    <x-container>
        <nav class="flex flex-wrap gap-2 text-sm text-sign-muted" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}" class="hover:text-sign-primary">Dashboard</a><span aria-hidden="true">/</span><span class="font-semibold text-sign-primary">Assessment Performance</span>
        </nav>
        <div class="mt-6 max-w-3xl">
            <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Quiz & assessment</p>
            <h1 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-5xl">Assessment Performance</h1>
            <p class="mt-4 text-sm leading-7 text-sign-muted sm:text-base">Track your attempts, best scores, pass rate and recent quiz results across published SignGyaan assessments.</p>
        </div>
    </x-container>
</section>

<section class="bg-white py-10 sm:py-14 lg:py-16">
    <x-container>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" aria-label="Assessment performance summary">
            <div class="rounded-2xl border border-sign-border p-5 sm:rounded-3xl"><p class="text-sm font-semibold text-sign-muted">Submitted attempts</p><p class="mt-3 font-heading text-3xl font-semibold text-sign-primary">{{ $performanceSummary['submitted'] }}</p></div>
            <div class="rounded-2xl border border-sign-border p-5 sm:rounded-3xl"><p class="text-sm font-semibold text-sign-muted">Passed attempts</p><p class="mt-3 font-heading text-3xl font-semibold text-sign-primary">{{ $performanceSummary['passed'] }}</p></div>
            <div class="rounded-2xl border border-sign-border p-5 sm:rounded-3xl"><p class="text-sm font-semibold text-sign-muted">Best score</p><p class="mt-3 font-heading text-3xl font-semibold text-sign-primary">{{ $performanceSummary['best_score'] === null ? '—' : number_format((float) $performanceSummary['best_score'], 2).'%' }}</p></div>
            <div class="rounded-2xl border border-sign-border p-5 sm:rounded-3xl"><p class="text-sm font-semibold text-sign-muted">Pass rate</p><p class="mt-3 font-heading text-3xl font-semibold text-sign-primary">{{ $performanceSummary['pass_rate'] === null ? '—' : $performanceSummary['pass_rate'].'%' }}</p></div>
        </div>

        @if ($performanceSummary['submitted'] > 0)
            <div class="mt-5 rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div><p class="text-sm font-semibold text-sign-primary">Average score</p><p class="mt-1 text-sm text-sign-muted">Across submitted quiz and exercise attempts.</p></div>
                    <p class="font-heading text-3xl font-semibold text-sign-primary">{{ number_format((float) $performanceSummary['average_score'], 2) }}%</p>
                </div>
            </div>
        @endif

        <div class="mt-10 grid gap-10 xl:grid-cols-[minmax(0,1fr)_21rem] xl:items-start">
            <div class="min-w-0">
                <section aria-labelledby="assessment-by-quiz-heading">
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">By assessment</p>
                    <h2 id="assessment-by-quiz-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Your quiz performance</h2>

                    @if ($assessmentGroups->isNotEmpty())
                        <div class="mt-5 space-y-4">
                            @foreach ($assessmentGroups as $item)
                                <article class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-6">
                                    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap gap-2 text-xs font-semibold"><span class="rounded-full bg-sign-soft px-3 py-1 text-sign-primary">{{ $item['subject'] }}</span><span class="rounded-full bg-sign-light px-3 py-1 text-sign-cyan-dark">{{ $item['latest_status'] === 'in-progress' ? 'In progress' : ucfirst($item['latest_status']) }}</span></div>
                                            <h3 class="mt-3 font-heading text-xl font-semibold text-sign-primary">{{ $item['title'] }}</h3>
                                            <p class="mt-1 text-sm text-sign-muted">{{ $item['course'] }} · {{ $item['lesson'] }}</p>
                                            <div class="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-sm text-sign-muted">
                                                <span>{{ $item['attempts'] }} attempt{{ $item['attempts'] === 1 ? '' : 's' }}</span>
                                                <span>Pass mark {{ $item['passing_percentage'] }}%</span>
                                                @if ($item['best_score'] !== null)<span>Best <strong class="text-sign-primary">{{ number_format((float) $item['best_score'], 2) }}%</strong></span>@endif
                                            </div>
                                        </div>
                                        <a href="{{ $item['action_url'] }}" class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-xl border border-sign-primary px-4 py-3 text-sm font-semibold text-sign-primary hover:bg-sign-soft">{{ $item['action_label'] }}</a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-5 rounded-2xl border border-dashed border-sign-border bg-sign-soft p-7 sm:rounded-3xl">
                            <h3 class="font-heading text-xl font-semibold text-sign-primary">No assessment performance yet</h3>
                            <p class="mt-2 text-sm leading-6 text-sign-muted">Start a published quiz or exercise from a lesson. Your performance will appear here after your first attempt.</p>
                            <a href="{{ route('my-courses') }}" class="mt-5 inline-flex min-h-11 items-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white">Go to My Courses</a>
                        </div>
                    @endif
                </section>
            </div>

            <aside class="xl:sticky xl:top-24" aria-labelledby="recent-results-heading">
                <div class="rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl">
                    <h2 id="recent-results-heading" class="font-heading text-xl font-semibold text-sign-primary">Recent Results</h2>
                    @if ($recentResults->isNotEmpty())
                        <div class="mt-4 space-y-3">
                            @foreach ($recentResults as $result)
                                <a href="{{ $result['url'] }}" class="block rounded-xl bg-white p-4 transition hover:ring-1 hover:ring-sign-cyan">
                                    <div class="flex items-center justify-between gap-3"><span class="text-xs font-semibold {{ $result['passed'] ? 'text-sign-cyan-dark' : 'text-sign-muted' }}">{{ $result['passed'] ? 'Passed' : 'Submitted' }}</span><span class="text-sm font-semibold text-sign-primary">{{ number_format((float) $result['score'], 2) }}%</span></div>
                                    <p class="mt-2 text-sm font-semibold text-sign-primary">{{ $result['title'] }}</p>
                                    <p class="mt-1 text-xs text-sign-muted">Attempt {{ $result['attempt_number'] }} · {{ $result['course'] }}</p>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-3 text-sm leading-6 text-sign-muted">Submitted assessment results will appear here.</p>
                    @endif
                </div>
            </aside>
        </div>
    </x-container>
</section>
@endsection
