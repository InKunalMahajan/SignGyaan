@extends($layout)

@section('title', 'Reports')
@section('header_label', 'Reports')

@section('content')
    <section aria-labelledby="reports-heading" class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Performance and progress</p>
                    <h1 id="reports-heading" class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">{{ $report['title'] }}</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">This report only includes information your account is allowed to access.</p>
                </div>
                <a href="{{ route('reports.export') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-900 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-900">Export CSV</a>
            </div>
        </div>

        <section aria-label="Report summary" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            @foreach ($report['summary'] as $label => $value)
                <article class="rounded-2xl border border-slate-200 bg-white p-5">
                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">{{ $label }}</p>
                    <p class="mt-2 text-2xl font-black text-slate-950">{{ $value }}</p>
                </article>
            @endforeach
        </section>

        <section aria-labelledby="report-table-heading" class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 id="report-table-heading" class="text-lg font-black text-slate-950">Detailed report</h2>
                    <p class="mt-1 text-sm text-slate-600">Lesson completion, assessment performance and mastery.</p>
                </div>
                <span class="rounded-full border border-slate-300 bg-slate-50 px-3 py-1 text-xs font-bold text-slate-700">{{ $report['rows']->count() }} rows</span>
            </div>

            @if ($report['rows']->isEmpty())
                <div class="mt-6 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center" role="status">
                    <p class="font-black text-slate-950">No report data yet</p>
                    <p class="mt-2 text-sm text-slate-600">Report rows will appear when learning progress or mastery data is available.</p>
                </div>
            @else
                <div class="mt-5 overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <caption class="sr-only">{{ $report['title'] }} details</caption>
                        <thead class="bg-slate-100 text-xs uppercase tracking-[0.08em] text-slate-600">
                            <tr>
                                @foreach (array_keys($report['rows']->first()) as $heading)
                                    <th scope="col" class="px-4 py-3 font-black">{{ $heading }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach ($report['rows'] as $row)
                                <tr>
                                    @foreach ($row as $value)
                                        <td class="px-4 py-3 text-slate-800">{{ $value }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </section>
@endsection
