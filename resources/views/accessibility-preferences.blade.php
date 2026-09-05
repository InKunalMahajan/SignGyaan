@extends('layouts.app')

@section('title', 'Accessibility Preferences - SignGyaan')
@section('description', 'Choose how captions, transcripts, simple summaries and motion are presented while learning on SignGyaan.')

@section('content')
    @php
        $preferences = $user->accessibility_preferences ?? [];
        $captions = old('captions', $preferences['captions'] ?? 'manual');
        $transcript = old('transcript', $preferences['transcript'] ?? 'show');
        $simpleSummary = old('simple_summary', $preferences['simple_summary'] ?? 'show');
        $reducedMotion = old('reduced_motion', $preferences['reduced_motion'] ?? 'system');
    @endphp

    <section class="border-b border-sign-border bg-sign-soft py-8 sm:py-12">
        <x-container>
            <nav class="flex flex-wrap items-center gap-2 text-xs text-sign-muted sm:text-sm" aria-label="Breadcrumb">
                <a href="{{ route('profile') }}" class="transition hover:text-sign-primary">Profile & Account</a>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-sign-primary">Accessibility Preferences</span>
            </nav>

            <div class="mt-6 max-w-3xl">
                <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark sm:text-sm">Learning preferences</p>
                <h1 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-5xl">Accessibility Preferences</h1>
                <p class="mt-4 text-sm leading-7 text-sign-muted sm:text-base">Choose how SignGyaan presents ISL-supporting content. These preferences are saved to your account and used on learner pages.</p>
            </div>
        </x-container>
    </section>

    <section class="bg-white py-8 sm:py-12 lg:py-16">
        <x-container>
            <div class="mx-auto max-w-4xl">
                @if (session('accessibility_status'))
                    <div class="mb-6 rounded-2xl border border-sign-cyan bg-sign-light px-4 py-3 text-sm font-semibold text-sign-primary" role="status" aria-live="polite">
                        {{ session('accessibility_status') }}
                    </div>
                @endif

                @if ($errors->accessibility->any())
                    <div data-error-summary class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3" role="alert" aria-live="assertive">
                        <p class="text-sm font-semibold text-red-800">Please check your accessibility preferences.</p>
                        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
                            @foreach ($errors->accessibility->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.accessibility.update') }}" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="captions-heading">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Video support</p>
                        <h2 id="captions-heading" class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Captions</h2>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">When a lesson video contains a caption or subtitle track, SignGyaan can turn it on automatically.</p>
                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            <label class="flex cursor-pointer gap-3 rounded-2xl border border-sign-border p-4">
                                <input type="radio" name="captions" value="prefer" @checked($captions === 'prefer') class="mt-1 h-5 w-5 accent-sign-primary">
                                <span><span class="block text-sm font-semibold text-sign-primary">Prefer captions</span><span class="mt-1 block text-xs leading-5 text-sign-muted">Turn available captions on automatically.</span></span>
                            </label>
                            <label class="flex cursor-pointer gap-3 rounded-2xl border border-sign-border p-4">
                                <input type="radio" name="captions" value="manual" @checked($captions === 'manual') class="mt-1 h-5 w-5 accent-sign-primary">
                                <span><span class="block text-sm font-semibold text-sign-primary">Manual captions</span><span class="mt-1 block text-xs leading-5 text-sign-muted">Use the video controls when you want captions.</span></span>
                            </label>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="text-support-heading">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Text support</p>
                        <h2 id="text-support-heading" class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Lesson presentation</h2>
                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="transcript" class="mb-2 block text-sm font-semibold text-sign-primary">ISL transcript</label>
                                <select id="transcript" name="transcript" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                                    <option value="show" @selected($transcript === 'show')>Show when available</option>
                                    <option value="hide" @selected($transcript === 'hide')>Hide by default</option>
                                </select>
                                <p class="mt-2 text-xs leading-5 text-sign-muted">Hidden transcripts can still be made visible later by changing this preference.</p>
                            </div>
                            <div>
                                <label for="simple_summary" class="mb-2 block text-sm font-semibold text-sign-primary">Simple summary</label>
                                <select id="simple_summary" name="simple_summary" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                                    <option value="show" @selected($simpleSummary === 'show')>Show when available</option>
                                    <option value="hide" @selected($simpleSummary === 'hide')>Hide by default</option>
                                </select>
                                <p class="mt-2 text-xs leading-5 text-sign-muted">Useful if you prefer to focus on the main lesson notes first.</p>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="motion-heading">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Motion</p>
                        <h2 id="motion-heading" class="mt-2 font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Reduced motion</h2>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">SignGyaan already respects your device's reduced-motion setting. You can also force reduced motion for your account.</p>
                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            <label class="flex cursor-pointer gap-3 rounded-2xl border border-sign-border p-4">
                                <input type="radio" name="reduced_motion" value="system" @checked($reducedMotion === 'system') class="mt-1 h-5 w-5 accent-sign-primary">
                                <span><span class="block text-sm font-semibold text-sign-primary">Follow device setting</span><span class="mt-1 block text-xs leading-5 text-sign-muted">Use your browser or operating-system preference.</span></span>
                            </label>
                            <label class="flex cursor-pointer gap-3 rounded-2xl border border-sign-border p-4">
                                <input type="radio" name="reduced_motion" value="on" @checked($reducedMotion === 'on') class="mt-1 h-5 w-5 accent-sign-primary">
                                <span><span class="block text-sm font-semibold text-sign-primary">Reduce motion</span><span class="mt-1 block text-xs leading-5 text-sign-muted">Minimise transitions, animation and smooth scrolling.</span></span>
                            </label>
                        </div>
                    </section>

                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <a href="{{ route('profile') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-sign-border bg-white px-5 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Back to Profile</a>
                        <button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-xl bg-sign-primary px-6 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Save Accessibility Preferences</button>
                    </div>
                </form>
            </div>
        </x-container>
    </section>
@endsection
