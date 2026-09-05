@php
    $editing = isset($question);
    $currentType = old('question_type', $question->question_type ?? 'single-choice');
    $lockedByAnswers = $editing && (($question->answers_count ?? 0) > 0);

    $initialOptions = old('options');

    if ($initialOptions === null && $editing && in_array($question->question_type, ['single-choice', 'multiple-choice'], true)) {
        $initialOptions = $question->options
            ->map(fn ($option) => [
                'option_text' => $option->option_text,
                'feedback' => $option->feedback,
                'is_correct' => (bool) $option->is_correct,
            ])
            ->values()
            ->all();
    }

    if (! is_array($initialOptions) || count($initialOptions) < 2) {
        $initialOptions = [
            ['option_text' => '', 'feedback' => '', 'is_correct' => true],
            ['option_text' => '', 'feedback' => '', 'is_correct' => false],
            ['option_text' => '', 'feedback' => '', 'is_correct' => false],
            ['option_text' => '', 'feedback' => '', 'is_correct' => false],
        ];
    }

    $trueFalseAnswer = old('true_false_answer');
    if ($trueFalseAnswer === null && $editing && $question->question_type === 'true-false') {
        $trueFalseAnswer = strtolower((string) ($question->options->firstWhere('is_correct', true)?->option_text ?? 'true'));
    }
    $trueFalseAnswer = $trueFalseAnswer ?: 'true';

    $acceptedAnswers = old('accepted_answers');
    if ($acceptedAnswers === null && $editing && $question->question_type === 'fill-blank') {
        $acceptedAnswers = collect(data_get($question->answer_key, 'accepted_answers', []))->implode("\n");
    }
@endphp

@if ($errors->any())
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3" role="alert" aria-live="polite" data-error-summary>
        <p class="text-sm font-semibold text-red-800">Please check the question details.</p>
        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if ($lockedByAnswers)
    <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-6 text-amber-900">
        This question already has learner answers. Its question type, points and correct-answer configuration are protected so previous attempt history stays valid. You can still edit the wording, explanation, order and publishing settings.
    </div>
@endif

<div
    class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_20rem] xl:items-start"
    x-data='{
        questionType: @js($currentType),
        options: @js($initialOptions),
        addOption() {
            if (this.options.length >= 20) return;
            this.options.push({ option_text: "", feedback: "", is_correct: false });
        },
        removeOption(index) {
            if (this.options.length <= 2) return;
            this.options.splice(index, 1);
        }
    }'
>
    <div class="space-y-6">
        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="question-main-heading">
            <h2 id="question-main-heading" class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Question details</h2>
            <p class="mt-2 text-sm leading-6 text-sign-muted">Write the learner-facing question and choose how the learner should answer it.</p>

            <div class="mt-6 grid gap-5">
                <div>
                    <label for="question_type" class="mb-2 block text-sm font-semibold text-sign-primary">Question type</label>
                    <select id="question_type" name="question_type" x-model="questionType" required class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                        @foreach ($questionTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs leading-5 text-sign-muted">
                        <span x-show="questionType === 'single-choice'">Learner selects exactly one answer.</span>
                        <span x-show="questionType === 'multiple-choice'" x-cloak>Learner can select more than one answer.</span>
                        <span x-show="questionType === 'true-false'" x-cloak>Learner chooses True or False.</span>
                        <span x-show="questionType === 'fill-blank'" x-cloak>Learner types an answer that is checked against accepted answers.</span>
                    </p>
                    @error('question_type')<p class="mt-2 text-sm font-medium text-red-700" data-field-error>{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="prompt" class="mb-2 block text-sm font-semibold text-sign-primary">Question</label>
                    <textarea id="prompt" name="prompt" rows="5" maxlength="10000" required autofocus class="w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light" placeholder="Write the question learners will answer.">{{ old('prompt', $question->prompt ?? '') }}</textarea>
                    @error('prompt')<p class="mt-2 text-sm font-medium text-red-700" data-field-error>{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="explanation" class="mb-2 block text-sm font-semibold text-sign-primary">Explanation / feedback</label>
                    <textarea id="explanation" name="explanation" rows="4" maxlength="50000" class="w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light" placeholder="Optional explanation shown after submission when feedback is enabled.">{{ old('explanation', $question->explanation ?? '') }}</textarea>
                    @error('explanation')<p class="mt-2 text-sm font-medium text-red-700" data-field-error>{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section x-show="questionType === 'single-choice' || questionType === 'multiple-choice'" class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="choice-options-heading">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 id="choice-options-heading" class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Answer options</h2>
                    <p class="mt-2 text-sm leading-6 text-sign-muted">Add 2–20 options and mark the correct answer. Single Choice needs exactly one correct option.</p>
                </div>
                <button type="button" @click="addOption()" :disabled="options.length >= 20" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-sign-soft px-4 py-2.5 text-sm font-semibold text-sign-primary transition hover:border-sign-cyan hover:bg-sign-light disabled:cursor-not-allowed disabled:opacity-50">+ Add Option</button>
            </div>

            <div class="mt-6 space-y-4">
                <template x-for="(option, index) in options" :key="index">
                    <div class="rounded-2xl border border-sign-border bg-sign-soft p-4 sm:p-5">
                        <div class="flex items-start gap-3">
                            <span class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-xs font-semibold text-sign-primary" x-text="index + 1" aria-hidden="true"></span>
                            <div class="min-w-0 flex-1 space-y-4">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-sign-primary" :for="`option-text-${index}`">Option text</label>
                                    <input :id="`option-text-${index}`" type="text" :name="`options[${index}][option_text]`" x-model="option.option_text" maxlength="5000" required class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-sign-primary" :for="`option-feedback-${index}`">Option feedback <span class="font-normal text-sign-muted">(optional)</span></label>
                                    <textarea :id="`option-feedback-${index}`" :name="`options[${index}][feedback]`" x-model="option.feedback" rows="2" maxlength="10000" class="w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light"></textarea>
                                </div>

                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <label class="flex cursor-pointer items-center gap-3 text-sm font-semibold text-sign-primary">
                                        <input type="checkbox" :name="`options[${index}][is_correct]`" value="1" x-model="option.is_correct" class="h-5 w-5 rounded border-sign-border accent-sign-primary">
                                        Correct answer
                                    </label>
                                    <button type="button" @click="removeOption(index)" :disabled="options.length <= 2" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-40">Remove option</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            @error('options')<p class="mt-3 text-sm font-medium text-red-700" data-field-error>{{ $message }}</p>@enderror
            @error('options.*.option_text')<p class="mt-3 text-sm font-medium text-red-700" data-field-error>{{ $message }}</p>@enderror
        </section>

        <section x-show="questionType === 'true-false'" x-cloak class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="true-false-heading">
            <h2 id="true-false-heading" class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Correct answer</h2>
            <p class="mt-2 text-sm leading-6 text-sign-muted">Choose whether the statement is True or False.</p>

            <fieldset class="mt-6 grid gap-3 sm:grid-cols-2">
                <legend class="sr-only">Correct True or False answer</legend>
                <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-sign-border bg-sign-soft p-4 text-sm font-semibold text-sign-primary">
                    <input type="radio" name="true_false_answer" value="true" @checked($trueFalseAnswer === 'true') class="h-5 w-5 border-sign-border accent-sign-primary">
                    True
                </label>
                <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-sign-border bg-sign-soft p-4 text-sm font-semibold text-sign-primary">
                    <input type="radio" name="true_false_answer" value="false" @checked($trueFalseAnswer === 'false') class="h-5 w-5 border-sign-border accent-sign-primary">
                    False
                </label>
            </fieldset>
            @error('true_false_answer')<p class="mt-3 text-sm font-medium text-red-700" data-field-error>{{ $message }}</p>@enderror
        </section>

        <section x-show="questionType === 'fill-blank'" x-cloak class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="fill-blank-heading">
            <h2 id="fill-blank-heading" class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Accepted answers</h2>
            <p class="mt-2 text-sm leading-6 text-sign-muted">Enter one accepted answer per line. Duplicate answers are automatically removed.</p>

            <div class="mt-6">
                <label for="accepted_answers" class="mb-2 block text-sm font-semibold text-sign-primary">Accepted answers</label>
                <textarea id="accepted_answers" name="accepted_answers" rows="7" maxlength="20000" class="w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light" placeholder="keyboard&#10;Keyboard">{{ $acceptedAnswers }}</textarea>
                <p class="mt-2 text-xs leading-5 text-sign-muted">Automatic scoring rules for typed answers will be applied in Step 9E.</p>
                @error('accepted_answers')<p class="mt-2 text-sm font-medium text-red-700" data-field-error>{{ $message }}</p>@enderror
            </div>
        </section>
    </div>

    <aside class="space-y-5 xl:sticky xl:top-24" aria-label="Question settings">
        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Scoring</p>

            <div class="mt-4">
                <label for="points" class="mb-2 block text-sm font-semibold text-sign-primary">Points</label>
                <input id="points" name="points" type="number" min="0.01" max="10000" step="0.01" value="{{ old('points', $question->points ?? 1) }}" required class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                @error('points')<p class="mt-2 text-sm font-medium text-red-700" data-field-error>{{ $message }}</p>@enderror
            </div>

            <div class="mt-5">
                <label for="sort_order" class="mb-2 block text-sm font-semibold text-sign-primary">Question order</label>
                <input id="sort_order" name="sort_order" type="number" min="0" max="9999" value="{{ old('sort_order', $question->sort_order ?? $nextSortOrder ?? 1) }}" required class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                <p class="mt-2 text-xs leading-5 text-sign-muted">Lower numbers appear first unless question shuffling is enabled.</p>
                @error('sort_order')<p class="mt-2 text-sm font-medium text-red-700" data-field-error>{{ $message }}</p>@enderror
            </div>
        </section>

        <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
            <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Availability</p>

            <label class="mt-4 flex cursor-pointer items-start gap-3 rounded-xl bg-sign-soft p-4">
                <input type="checkbox" name="is_required" value="1" @checked(old('is_required', $question->is_required ?? true)) class="mt-0.5 h-5 w-5 shrink-0 rounded border-sign-border accent-sign-primary">
                <span>
                    <span class="block text-sm font-semibold text-sign-primary">Required question</span>
                    <span class="mt-1 block text-xs leading-5 text-sign-muted">Learners should answer this question before submitting.</span>
                </span>
            </label>

            <label class="mt-3 flex cursor-pointer items-start gap-3 rounded-xl bg-sign-soft p-4">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $question->is_published ?? true)) class="mt-0.5 h-5 w-5 shrink-0 rounded border-sign-border accent-sign-primary">
                <span>
                    <span class="block text-sm font-semibold text-sign-primary">Published question</span>
                    <span class="mt-1 block text-xs leading-5 text-sign-muted">Draft questions stay out of the learner assessment.</span>
                </span>
            </label>
        </section>

        <div class="grid gap-2">
            <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">{{ $editing ? 'Save Question' : 'Create Question' }}</button>
            <a href="{{ route('admin.assessments.questions.index', $assessment) }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Cancel</a>
        </div>
    </aside>
</div>
