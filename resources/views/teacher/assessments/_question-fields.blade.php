@php
    $question = $question ?? null;
    $type = old('type', $question?->type ?? 'single_choice');
    $optionsText = old('options', $question ? implode("\n", $question->config['options'] ?? []) : '');
    $correctText = old('correct_answers', '');
    $matchingText = old('matching_pairs', '');

    if ($question && ! old('correct_answers')) {
        $correctText = match ($question->type) {
            'single_choice' => (string) ($question->answer_key['value'] ?? ''),
            'multi_select' => implode("\n", $question->answer_key['values'] ?? []),
            'true_false' => array_key_exists('value', $question->answer_key ?? []) ? (($question->answer_key['value'] ?? false) ? 'True' : 'False') : '',
            'fill_blank' => implode("\n", $question->answer_key['accepted'] ?? []),
            default => '',
        };
    }

    if ($question && $question->type === 'matching') {
        $matchingText = collect($question->config['pairs'] ?? [])->map(fn ($pair) => ($pair['left'] ?? '').' => '.($pair['right'] ?? ''))->implode("\n");
    }
@endphp

<div class="grid gap-4 sm:grid-cols-[minmax(0,1fr)_160px_110px]">
    <div>
        <label class="mb-2 block text-sm font-bold">Question type</label>
        <select name="type" class="sg-field" required>
            @foreach ($questionTypes as $value => $label)
                <option value="{{ $value }}" @selected($type === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-2 block text-sm font-bold">Marks</label>
        <input name="marks" type="number" step="0.5" min="0.5" value="{{ old('marks', $question?->marks ?? 1) }}" class="sg-field" required>
    </div>
    @if ($question)
        <div>
            <label class="mb-2 block text-sm font-bold">Position</label>
            <input name="position" type="number" min="1" value="{{ old('position', $question->position) }}" class="sg-field" required>
        </div>
    @endif
</div>

<div>
    <label class="mb-2 block text-sm font-bold">Question</label>
    <textarea name="prompt" rows="3" class="sg-field" required>{{ old('prompt', $question?->prompt) }}</textarea>
</div>

<div class="grid gap-4 lg:grid-cols-2">
    <div>
        <label class="mb-2 block text-sm font-bold">Options</label>
        <textarea name="options" rows="5" class="sg-field" placeholder="One option per line">{{ $optionsText }}</textarea>
        <p class="mt-1 text-xs text-slate-500">Use for Single Choice and Multi-select. One option per line.</p>
    </div>
    <div>
        <label class="mb-2 block text-sm font-bold">Correct / accepted answers</label>
        <textarea name="correct_answers" rows="5" class="sg-field" placeholder="One answer per line">{{ $correctText }}</textarea>
        <p class="mt-1 text-xs text-slate-500">Single Choice: one exact option. Multi-select / Fill Blank: one answer per line. True/False: type True or False.</p>
    </div>
</div>

<div>
    <label class="mb-2 block text-sm font-bold">Matching pairs</label>
    <textarea name="matching_pairs" rows="4" class="sg-field" placeholder="Keyboard => Input device&#10;Monitor => Output device">{{ $matchingText }}</textarea>
    <p class="mt-1 text-xs text-slate-500">For Match the Following only. Use: Left =&gt; Right, one pair per line.</p>
</div>

<div>
    <label class="mb-2 block text-sm font-bold">Explanation / feedback</label>
    <textarea name="explanation" rows="3" class="sg-field">{{ old('explanation', $question?->explanation) }}</textarea>
</div>
