@extends('teacher.layout')

@section('title', 'AI Assistant')

@section('content')
    <div class="space-y-6">
        <section class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-500">Phase 11 · Teacher drafting assistant</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight">AI Assistant</h1>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Create reviewable teaching drafts with OpenAI. Nothing is automatically published, graded, or saved into course content.</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm">
                <p class="font-black">{{ strtoupper((string) $provider) }} · {{ $model }}</p>
                <p class="mt-1 text-xs font-semibold {{ $configured ? 'text-slate-600' : 'text-slate-900' }}">{{ $configured ? 'API key configured' : 'OPENAI_API_KEY not configured' }}</p>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
            <form method="POST" action="{{ route('teacher.ai.generate') }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                @csrf

                <div>
                    <label for="task" class="block text-sm font-black text-slate-900">What do you want to draft?</label>
                    <select id="task" name="task" required class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-950 focus:border-slate-950 focus:outline-none focus:ring-2 focus:ring-slate-300">
                        @foreach ($tasks as $value => $label)
                            <option value="{{ $value }}" @selected(old('task') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-5">
                    <label for="prompt" class="block text-sm font-black text-slate-900">Teacher request</label>
                    <textarea id="prompt" name="prompt" rows="7" required maxlength="4000" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-6 text-slate-950 placeholder:text-slate-400 focus:border-slate-950 focus:outline-none focus:ring-2 focus:ring-slate-300" placeholder="Example: Create a 30-minute visual lesson plan for FYJC students about database primary keys. Use simple English.">{{ old('prompt') }}</textarea>
                    <p class="mt-2 text-xs text-slate-500">Do not include passwords, API keys, or unnecessary learner personal information.</p>
                </div>

                <div class="mt-5">
                    <label for="context" class="block text-sm font-black text-slate-900">Optional teaching context</label>
                    <textarea id="context" name="context" rows="4" maxlength="2000" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-6 text-slate-950 placeholder:text-slate-400 focus:border-slate-950 focus:outline-none focus:ring-2 focus:ring-slate-300" placeholder="Example: Topic already covered: tables and fields. Class duration: 30 minutes.">{{ old('context') }}</textarea>
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <button type="submit" class="rounded-xl bg-slate-950 px-5 py-3 text-sm font-black text-white hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-300">Generate draft</button>
                    <span class="text-xs font-semibold text-slate-500">Teacher review required before classroom use.</span>
                </div>
            </form>

            <aside class="space-y-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">Safe use</p>
                    <h2 class="mt-1 text-lg font-black">Teacher stays in control</h2>
                    <ul class="mt-4 space-y-3 text-sm leading-6 text-slate-600">
                        <li>• AI output is a draft, not final curriculum.</li>
                        <li>• AI cannot publish lessons or change marks.</li>
                        <li>• Avoid learner personal or medical information.</li>
                        <li>• Check facts, accessibility, and board requirements before use.</li>
                    </ul>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">Accessibility</p>
                    <p class="mt-2 text-sm leading-6 text-slate-600">The assistant is instructed to prefer simple English, visual steps, captions, demonstrations, and teacher-led ISL support where relevant. Text output is not treated as an ISL translation.</p>
                </div>
            </aside>
        </section>

        @if ($result)
            <section class="rounded-2xl border border-slate-300 bg-white p-6 shadow-sm" aria-labelledby="ai-result-heading">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">AI-generated draft</p>
                        <h2 id="ai-result-heading" class="mt-1 text-xl font-black">Review before use</h2>
                    </div>
                    <span class="rounded-full border border-slate-300 bg-slate-50 px-3 py-1 text-xs font-black">Not published</span>
                </div>
                <div class="mt-5 whitespace-pre-wrap rounded-xl bg-slate-50 p-5 text-sm leading-7 text-slate-900">{{ $result }}</div>
            </section>
        @endif
    </div>
@endsection
