@extends('teacher.layout')
@section('title', 'Assessment Analytics')
@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Assessment Analytics</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight">{{ $assessment->title }}</h1>
            <p class="mt-2 text-sm text-slate-600">{{ $assessment->course->title }} · question-wise performance and result summary.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('teacher.assessments.attempts.index', $assessment) }}" class="sg-btn-secondary">Learner Attempts</a>
            <a href="{{ route('teacher.assessments.index') }}" class="sg-btn-secondary">Back</a>
        </div>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
        @php
            $cards = [
                ['Appeared', $summary['appeared']],
                ['Completed', $summary['completed']],
                ['Passed', $summary['passed']],
                ['Pending Review', $summary['pending_review']],
                ['Pass %', $summary['pass_percentage'] !== null ? number_format($summary['pass_percentage'], 2).'%' : '—'],
                ['Average %', $summary['average_percentage'] !== null ? number_format($summary['average_percentage'], 2).'%' : '—'],
                ['Average Marks', $summary['average_marks'] !== null ? number_format($summary['average_marks'], 2) : '—'],
                ['Highest %', $summary['highest_percentage'] !== null ? number_format($summary['highest_percentage'], 2).'%' : '—'],
                ['Lowest %', $summary['lowest_percentage'] !== null ? number_format($summary['lowest_percentage'], 2).'%' : '—'],
                ['Failed', $summary['failed']],
            ];
        @endphp
        @foreach ($cards as [$label, $value])
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">{{ $label }}</p>
                <p class="mt-2 text-2xl font-black text-slate-950">{{ $value }}</p>
            </div>
        @endforeach
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="border-b border-slate-200 p-5">
            <h2 class="text-xl font-black">Question-wise Analysis</h2>
            <p class="mt-1 text-sm text-slate-500">Accuracy is calculated only from scored answers. Answers still waiting for review are shown separately.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-[0.1em] text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Question</th>
                        <th class="px-5 py-3">Type</th>
                        <th class="px-5 py-3">Responses</th>
                        <th class="px-5 py-3">Correct</th>
                        <th class="px-5 py-3">Incorrect</th>
                        <th class="px-5 py-3">Pending</th>
                        <th class="px-5 py-3">Accuracy</th>
                        <th class="px-5 py-3">Avg Marks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($questions as $question)
                        <tr>
                            <td class="max-w-xl px-5 py-4 align-top">
                                <p class="font-black text-slate-950">Q{{ $question['position'] }}. {{ $question['prompt'] }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ number_format($question['marks'], 2) }} marks</p>
                            </td>
                            <td class="px-5 py-4 align-top font-semibold">{{ str($question['type'])->replace('_', ' ')->title() }}</td>
                            <td class="px-5 py-4 align-top font-black">{{ $question['responses'] }}</td>
                            <td class="px-5 py-4 align-top font-black">{{ $question['correct'] }}</td>
                            <td class="px-5 py-4 align-top font-black">{{ $question['incorrect'] }}</td>
                            <td class="px-5 py-4 align-top font-black">{{ $question['pending_review'] }}</td>
                            <td class="px-5 py-4 align-top font-black">{{ $question['accuracy_percentage'] !== null ? number_format($question['accuracy_percentage'], 2).'%' : '—' }}</td>
                            <td class="px-5 py-4 align-top font-black">{{ $question['average_marks'] !== null ? number_format($question['average_marks'], 2) : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-10 text-center text-slate-500">No questions available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
