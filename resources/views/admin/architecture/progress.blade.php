@extends('admin.layout')

@section('title', 'Progress & Assessment')

@section('content')
<div class="space-y-8">
    <section>
        <p class="text-sm font-bold uppercase tracking-[0.18em] text-cyan-700">Progress Structure</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight">Progress & Assessment</h1>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">This area tracks learning outcomes separately from curriculum content and teaching delivery: Lesson Progress → Scores → Mastery → Recommendations.</p>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @foreach ([
            ['Active Learners', $stats['learners']],
            ['Progress Records', $stats['records']],
            ['In Progress', $stats['in_progress']],
            ['Completed', $stats['completed']],
            ['Completion Rate', $stats['completion_rate'].'%'],
        ] as [$label, $value])
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-bold text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-3xl font-black">{{ $value }}</p>
            </article>
        @endforeach
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h2 class="text-xl font-black">Mastery model</h2>
        <p class="mt-1 text-sm text-slate-600">Assessment scoring will map to the SignGyaan mastery bands.</p>
        <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 p-4"><p class="text-2xl font-black">0–39</p><p class="mt-1 text-sm font-bold text-slate-600">Needs Support</p></div>
            <div class="rounded-2xl border border-slate-200 p-4"><p class="text-2xl font-black">40–59</p><p class="mt-1 text-sm font-bold text-slate-600">Developing</p></div>
            <div class="rounded-2xl border border-slate-200 p-4"><p class="text-2xl font-black">60–79</p><p class="mt-1 text-sm font-bold text-slate-600">Good</p></div>
            <div class="rounded-2xl border border-slate-200 p-4"><p class="text-2xl font-black">80–100</p><p class="mt-1 text-sm font-bold text-slate-600">Mastered</p></div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h2 class="text-xl font-black">Recent Lesson Progress</h2>
        <div class="mt-5 overflow-x-auto">
            <table class="w-full min-w-[700px] text-left text-sm">
                <thead class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                    <tr><th class="px-3 py-3">Learner</th><th class="px-3 py-3">Lesson</th><th class="px-3 py-3">Status</th><th class="px-3 py-3">Last Updated</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recent as $progress)
                        <tr>
                            <td class="px-3 py-4 font-bold">{{ optional($progress->learner)->name ?: 'Unknown learner' }}</td>
                            <td class="px-3 py-4 text-slate-600">{{ optional($progress->lesson)->title ?: 'Unknown lesson' }}</td>
                            <td class="px-3 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-black {{ $progress->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">{{ str_replace('_', ' ', ucfirst($progress->status)) }}</span></td>
                            <td class="px-3 py-4 text-slate-500">{{ optional($progress->updated_at)->format('d M Y, H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-3 py-8 text-center text-slate-500">No learner progress has been recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="rounded-3xl border border-blue-100 bg-blue-50 p-5 sm:p-6">
        <h2 class="text-lg font-black text-blue-950">Learning outcome flow</h2>
        <p class="mt-2 text-sm font-bold leading-7 text-blue-900">Lesson Progress → Practice → Assessment → Score → Mastery → Recommendation</p>
    </section>
</div>
@endsection
