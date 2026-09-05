@extends('layouts.admin')

@section('title', 'Question Builder - SignGyaan Admin')
@section('page-title', 'Question Builder')
@section('description', 'Build assessment questions, answer options and scoring rules.')

@section('content')
    @php
        $practice = $assessment->practiceResource;
        $lesson = $practice?->lesson;
        $course = $lesson?->unit?->course;
        $subject = $course?->subject;
    @endphp

    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Assessment question builder</p>
                    <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">{{ $practice?->title ?? 'Assessment' }}</h2>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-sign-muted">
                        {{ $subject?->name ?? 'Subject' }} · {{ $course?->title ?? 'Course' }} · {{ $lesson?->title ?? 'Lesson' }}
                    </p>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row">
                    <a href="{{ route('admin.assessments.edit', $assessment) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Assessment Settings</a>
                    <a href="{{ route('admin.assessments.questions.create', $assessment) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">+ Add Question</a>
                </div>
            </div>

            @if ($errors->any())
                <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3" role="alert" aria-live="polite">
                    <p class="text-sm font-semibold text-red-800">The question action could not be completed.</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-7 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">Total questions</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $questions->count() }}</p>
                </div>
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">Published questions</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $publishedQuestions }}</p>
                </div>
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">Published points</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ number_format($totalPoints, 2) }}</p>
                </div>
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">Assessment status</p>
                    <p class="mt-2 text-lg font-semibold text-sign-primary">{{ $assessment->is_published ? 'Published' : 'Draft' }}</p>
                </div>
            </div>

            @if ($assessment->is_published && $publishedQuestions === 0)
                <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-900">
                    This assessment is marked Published but has no published questions. Learners should not start it until at least one question is ready.
                </div>
            @endif

            <div class="mt-7 overflow-hidden rounded-2xl border border-sign-border bg-white sm:rounded-3xl">
                @if ($questions->isNotEmpty())
                    <div class="hidden overflow-x-auto lg:block">
                        <table class="min-w-full divide-y divide-sign-border text-left text-sm">
                            <thead class="bg-sign-soft text-xs font-semibold uppercase tracking-wider text-sign-muted">
                                <tr>
                                    <th class="px-5 py-4">Order</th>
                                    <th class="px-5 py-4">Question</th>
                                    <th class="px-5 py-4">Type</th>
                                    <th class="px-5 py-4">Answer setup</th>
                                    <th class="px-5 py-4">Points</th>
                                    <th class="px-5 py-4">Status</th>
                                    <th class="px-5 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-sign-border">
                                @foreach ($questions as $question)
                                    @php
                                        $correctOptions = $question->options->where('is_correct', true)->count();
                                        $acceptedAnswers = collect(data_get($question->answer_key, 'accepted_answers', []));
                                    @endphp
                                    <tr class="align-top">
                                        <td class="px-5 py-4 font-semibold text-sign-muted">{{ $question->sort_order }}</td>
                                        <td class="px-5 py-4">
                                            <p class="max-w-2xl whitespace-pre-line font-semibold leading-6 text-sign-primary">{{ $question->prompt }}</p>
                                            <div class="mt-2 flex flex-wrap gap-2">
                                                <span class="rounded-full bg-sign-soft px-2.5 py-1 text-[11px] font-semibold text-sign-muted">{{ $question->is_required ? 'Required' : 'Optional' }}</span>
                                                @if ($question->answers_count > 0)
                                                    <span class="rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-800">{{ $question->answers_count }} learner {{ $question->answers_count === 1 ? 'answer' : 'answers' }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex rounded-full bg-sign-light px-2.5 py-1 text-xs font-semibold text-sign-primary">{{ $questionTypes[$question->question_type] ?? $question->question_type }}</span>
                                        </td>
                                        <td class="px-5 py-4 text-xs leading-6 text-sign-muted">
                                            @if (in_array($question->question_type, ['single-choice', 'multiple-choice'], true))
                                                <p>{{ $question->options->count() }} options</p>
                                                <p>{{ $correctOptions }} correct</p>
                                            @elseif ($question->question_type === 'true-false')
                                                <p>{{ $question->options->firstWhere('is_correct', true)?->option_text ?? 'Not configured' }}</p>
                                            @elseif ($question->question_type === 'fill-blank')
                                                <p>{{ $acceptedAnswers->count() }} accepted {{ $acceptedAnswers->count() === 1 ? 'answer' : 'answers' }}</p>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 font-semibold text-sign-primary">{{ number_format((float) $question->points, 2) }}</td>
                                        <td class="px-5 py-4">
                                            <span @class([
                                                'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                                'bg-sign-light text-sign-primary' => $question->is_published,
                                                'bg-gray-100 text-sign-muted' => ! $question->is_published,
                                            ])>{{ $question->is_published ? 'Published' : 'Draft' }}</span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('admin.assessments.questions.edit', [$assessment, $question]) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary transition hover:bg-sign-soft">Edit</a>
                                                <form method="POST" action="{{ route('admin.assessments.questions.destroy', [$assessment, $question]) }}" onsubmit="return confirm('Delete this question and all of its answer options?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-50">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="divide-y divide-sign-border lg:hidden">
                        @foreach ($questions as $question)
                            <article class="p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">Question {{ $loop->iteration }} · Order {{ $question->sort_order }}</p>
                                        <h3 class="mt-2 whitespace-pre-line font-heading text-lg font-semibold leading-6 text-sign-primary">{{ $question->prompt }}</h3>
                                    </div>
                                    <span @class([
                                        'shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold',
                                        'bg-sign-light text-sign-primary' => $question->is_published,
                                        'bg-gray-100 text-sign-muted' => ! $question->is_published,
                                    ])>{{ $question->is_published ? 'Published' : 'Draft' }}</span>
                                </div>

                                <div class="mt-4 grid grid-cols-2 gap-3 text-xs">
                                    <div class="rounded-xl bg-sign-soft p-3"><span class="block text-sign-muted">Type</span><strong class="mt-1 block text-sign-primary">{{ $questionTypes[$question->question_type] ?? $question->question_type }}</strong></div>
                                    <div class="rounded-xl bg-sign-soft p-3"><span class="block text-sign-muted">Points</span><strong class="mt-1 block text-sign-primary">{{ number_format((float) $question->points, 2) }}</strong></div>
                                    <div class="rounded-xl bg-sign-soft p-3"><span class="block text-sign-muted">Options</span><strong class="mt-1 block text-sign-primary">{{ $question->options->count() }}</strong></div>
                                    <div class="rounded-xl bg-sign-soft p-3"><span class="block text-sign-muted">Learner answers</span><strong class="mt-1 block text-sign-primary">{{ $question->answers_count }}</strong></div>
                                </div>

                                <div class="mt-4 flex flex-wrap gap-2 border-t border-sign-border pt-4">
                                    <a href="{{ route('admin.assessments.questions.edit', [$assessment, $question]) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary">Edit</a>
                                    <form method="POST" action="{{ route('admin.assessments.questions.destroy', [$assessment, $question]) }}" onsubmit="return confirm('Delete this question?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700">Delete</button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center sm:p-12">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-sign-light text-sign-primary" aria-hidden="true">?</div>
                        <h3 class="mt-5 font-heading text-2xl font-semibold text-sign-primary">No questions yet</h3>
                        <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-sign-muted">Add the first question, choose its answer type and configure the correct answer before publishing the assessment to learners.</p>
                        <a href="{{ route('admin.assessments.questions.create', $assessment) }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white">Add First Question</a>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
