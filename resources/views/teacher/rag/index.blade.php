@extends('teacher.layout')

@section('title', 'Knowledge Assistant')

@section('content')
<div class="space-y-8">
    <section class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8" aria-labelledby="rag-heading">
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Phase 12 · Grounded AI</p>
        <h1 id="rag-heading" class="mt-2 text-3xl font-black tracking-tight">Knowledge Assistant</h1>
        <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600">Ask questions using only your indexed SignGyaan sources. Answers include source labels so you can verify the information before using it.</p>
        <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm leading-6 text-slate-700">
            <strong>Teacher stays in control.</strong> RAG answers are drafts. They do not publish content, change marks, or update learner records.
        </div>
        @unless ($configured)
            <div class="mt-4 rounded-xl border border-slate-300 bg-white p-4 text-sm" role="status">
                OpenAI is not configured yet. Add <code>OPENAI_API_KEY</code> to your local <code>.env</code> before indexing or asking questions.
            </div>
        @endunless
    </section>

    <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_minmax(360px,.75fr)]">
        <article class="rounded-2xl border border-slate-200 bg-white p-6" aria-labelledby="ask-heading">
            <h2 id="ask-heading" class="text-xl font-black">Ask your knowledge base</h2>
            <form method="POST" action="{{ route('teacher.rag.ask') }}" class="mt-5 space-y-5">
                @csrf
                <div>
                    <label for="rag-course" class="text-sm font-bold">Course filter <span class="font-normal text-slate-500">(optional)</span></label>
                    <select id="rag-course" name="course_id" class="mt-2 w-full border border-slate-300 px-3 py-2.5">
                        <option value="">All my indexed sources</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}" @selected((string) old('course_id') === (string) $course->id)>{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="rag-question" class="text-sm font-bold">Question</label>
                    <textarea id="rag-question" name="question" rows="5" required maxlength="3000" class="mt-2 w-full border border-slate-300 px-3 py-2.5" placeholder="Example: Explain primary key in simple English using my course notes.">{{ old('question') }}</textarea>
                </div>
                <button type="submit" class="sg-btn-primary" @disabled(! $configured)>Ask with sources</button>
            </form>

            @if ($result)
                <div class="mt-7 border-t border-slate-200 pt-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h3 class="text-lg font-black">Grounded answer</h3>
                        <span class="rounded-full border border-slate-300 px-3 py-1 text-xs font-bold">Review before use</span>
                    </div>
                    <div class="mt-4 whitespace-pre-wrap text-sm leading-7 text-slate-800">{{ $result['answer'] }}</div>

                    <div class="mt-6">
                        <h4 class="text-sm font-black uppercase tracking-[0.12em] text-slate-500">Retrieved sources</h4>
                        <div class="mt-3 grid gap-3">
                            @forelse ($result['sources'] as $source)
                                <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <p class="font-black">[{{ $source['label'] }}] {{ $source['title'] }}</p>
                                        <span class="text-xs text-slate-500">Match {{ number_format($source['score'] * 100, 0) }}%</span>
                                    </div>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ \Illuminate\Support\Str::limit($source['excerpt'], 420) }}</p>
                                </article>
                            @empty
                                <p class="text-sm text-slate-600">No matching indexed source was found.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif
        </article>

        <aside class="rounded-2xl border border-slate-200 bg-white p-6" aria-labelledby="add-source-heading">
            <h2 id="add-source-heading" class="text-xl font-black">Add a text source</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Paste teacher notes, approved reference material, or your own learning content. Do not add unnecessary learner personal data.</p>
            <form method="POST" action="{{ route('teacher.rag.sources.store') }}" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label for="source-title" class="text-sm font-bold">Source title</label>
                    <input id="source-title" name="title" type="text" required maxlength="180" value="{{ old('title') }}" class="mt-2 w-full border border-slate-300 px-3 py-2.5" placeholder="Example: FYJC DBMS notes">
                </div>
                <div>
                    <label for="source-course" class="text-sm font-bold">Course <span class="font-normal text-slate-500">(optional)</span></label>
                    <select id="source-course" name="course_id" class="mt-2 w-full border border-slate-300 px-3 py-2.5">
                        <option value="">General teacher knowledge</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="source-content" class="text-sm font-bold">Content</label>
                    <textarea id="source-content" name="content" rows="8" required maxlength="50000" class="mt-2 w-full border border-slate-300 px-3 py-2.5" placeholder="Paste the source text here.">{{ old('content') }}</textarea>
                </div>
                <button type="submit" class="sg-btn-primary" @disabled(! $configured)>Add and index</button>
            </form>
        </aside>
    </section>

    <section aria-labelledby="course-index-heading">
        <div class="mb-4">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Course Content</p>
            <h2 id="course-index-heading" class="mt-1 text-xl font-black">Index published lessons</h2>
            <p class="mt-2 text-sm text-slate-600">Create or refresh lesson knowledge sources from the published lessons in a course you manage.</p>
        </div>
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($courses as $course)
                <article class="rounded-2xl border border-slate-200 bg-white p-5">
                    <h3 class="font-black">{{ $course->title }}</h3>
                    <form method="POST" action="{{ route('teacher.rag.courses.index', $course) }}" class="mt-4">
                        @csrf
                        <button type="submit" class="sg-btn-secondary" @disabled(! $configured)>Index published lessons</button>
                    </form>
                </article>
            @empty
                <div class="rounded-2xl border border-slate-200 bg-white p-5 md:col-span-2 xl:col-span-3">
                    <p class="text-sm text-slate-600">No active courses owned by your teacher account are available for indexing.</p>
                </div>
            @endforelse
        </div>
    </section>

    <section aria-labelledby="sources-heading">
        <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Knowledge Base</p>
                <h2 id="sources-heading" class="mt-1 text-xl font-black">My indexed sources</h2>
            </div>
            <p class="text-xs font-semibold text-slate-500">{{ $sources->count() }} source(s)</p>
        </div>
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($sources as $source)
                <article class="rounded-2xl border border-slate-200 bg-white p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">{{ ucfirst($source->source_type) }}</p>
                            <h3 class="mt-2 font-black">{{ $source->title }}</h3>
                        </div>
                        <span class="rounded-full border border-slate-300 px-2.5 py-1 text-xs font-bold">{{ ucfirst($source->status) }}</span>
                    </div>
                    <p class="mt-3 text-sm text-slate-600">{{ $source->course?->title ?? 'General knowledge' }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $source->chunks_count }} chunk(s)@if($source->indexed_at) · Indexed {{ $source->indexed_at->diffForHumans() }}@endif</p>
                    @if ($source->last_error)
                        <p class="mt-3 text-xs leading-5 text-slate-600">Last indexing error: {{ \Illuminate\Support\Str::limit($source->last_error, 160) }}</p>
                    @endif
                    <div class="mt-5 flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('teacher.rag.sources.reindex', $source) }}">
                            @csrf
                            <button type="submit" class="sg-btn-secondary" @disabled(! $configured)>Re-index</button>
                        </form>
                        <form method="POST" action="{{ route('teacher.rag.sources.destroy', $source) }}" onsubmit="return confirm('Remove this knowledge source?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="sg-btn-secondary">Remove</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-slate-200 bg-white p-6 md:col-span-2 xl:col-span-3">
                    <p class="font-black">No knowledge sources yet.</p>
                    <p class="mt-2 text-sm text-slate-600">Add text above or index published lessons from one of your courses.</p>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
