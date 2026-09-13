@extends($layout)

@section('title', 'Search')
@section('header_label', 'Search')

@section('content')
    <section aria-labelledby="search-heading" class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Find content</p>
            <h1 id="search-heading" class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">Search SignGyaan</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Search only the classes, courses, lessons, assessments, learners, or users that your account is allowed to access.</p>

            <form method="GET" action="{{ route('search.index') }}" role="search" class="mt-5 flex flex-col gap-3 sm:flex-row">
                <label for="global-search-page" class="sr-only">Search SignGyaan</label>
                <input
                    id="global-search-page"
                    name="q"
                    type="search"
                    value="{{ $query }}"
                    maxlength="100"
                    autocomplete="off"
                    placeholder="Search classes, courses, lessons..."
                    class="min-h-11 flex-1 rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-950 focus:border-slate-950 focus:outline-none focus:ring-2 focus:ring-slate-900/10"
                >
                <button type="submit" class="min-h-11 rounded-xl bg-slate-950 px-5 py-3 text-sm font-bold text-white hover:bg-black">Search</button>
            </form>

            @if ($query !== '' && mb_strlen($query) < 2)
                <p class="mt-3 text-sm font-semibold text-slate-600" role="status">Enter at least 2 characters to search.</p>
            @endif
        </div>

        @if (mb_strlen($query) >= 2)
            @php($total = collect($results)->sum(fn ($items) => count($items)))
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-black text-slate-950">Results for “{{ $query }}”</h2>
                    <p class="mt-1 text-sm text-slate-600" role="status">{{ $total }} {{ Str::plural('result', $total) }} found.</p>
                </div>
            </div>

            @forelse ($results as $group => $items)
                <section aria-labelledby="group-{{ Str::slug($group) }}" class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6">
                    <div class="flex items-center justify-between gap-3">
                        <h2 id="group-{{ Str::slug($group) }}" class="text-lg font-black text-slate-950">{{ $group }}</h2>
                        <span class="rounded-full border border-slate-300 bg-slate-50 px-2.5 py-1 text-xs font-bold text-slate-700">{{ count($items) }}</span>
                    </div>

                    <ul class="mt-4 divide-y divide-slate-200" role="list">
                        @foreach ($items as $item)
                            <li>
                                <a href="{{ $item['url'] }}" class="group flex items-start justify-between gap-4 rounded-lg px-2 py-4 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-900">
                                    <span class="min-w-0">
                                        <span class="block text-xs font-bold uppercase tracking-[0.12em] text-slate-500">{{ $item['type'] }}</span>
                                        <span class="mt-1 block font-black text-slate-950 group-hover:underline">{{ $item['title'] }}</span>
                                        @if (! empty($item['context']))
                                            <span class="mt-1 block text-sm text-slate-600">{{ $item['context'] }}</span>
                                        @endif
                                    </span>
                                    <span aria-hidden="true" class="mt-3 text-lg text-slate-500">→</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @empty
                <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center" role="status">
                    <h2 class="text-lg font-black text-slate-950">No results found</h2>
                    <p class="mt-2 text-sm text-slate-600">Try a shorter keyword, a class name, course title, lesson title, learner name, or email.</p>
                </div>
            @endforelse
        @else
            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                <h2 class="text-lg font-black text-slate-950">Start with a keyword</h2>
                <p class="mt-2 text-sm text-slate-600">Examples: database, FYJC, HTML, assessment, or a learner name.</p>
            </div>
        @endif
    </section>
@endsection
