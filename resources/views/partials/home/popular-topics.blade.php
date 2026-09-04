<section class="bg-white py-16 sm:py-20 lg:py-24">
    <x-container>

        <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
            <x-section-heading
                title="Popular topics"
                description="Jump into short, focused lessons when you want to learn something quickly."
            />

            <a
                href="{{ route('explore') }}"
                class="inline-flex items-center gap-2 text-sm font-semibold text-sign-primary transition hover:text-sign-cyan-dark"
            >
                Explore all topics
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>

        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

            <a href="{{ route('learn') }}" class="group flex items-center gap-4 rounded-2xl border border-sign-border bg-white p-5 transition hover:-translate-y-0.5 hover:border-sign-cyan hover:shadow-md">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                    <span class="text-sm font-bold">Aa</span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">English</p>
                    <h3 class="mt-1 font-heading text-lg font-semibold text-sign-primary">Everyday Vocabulary</h3>
                    <p class="mt-1 text-sm text-sign-muted">Useful words for daily communication.</p>
                </div>
                <span class="text-sign-primary transition group-hover:translate-x-1">→</span>
            </a>

            <a href="{{ route('learn') }}" class="group flex items-center gap-4 rounded-2xl border border-sign-border bg-white p-5 transition hover:-translate-y-0.5 hover:border-sign-cyan hover:shadow-md">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17 9 20l-1 1h8l-1-1-.75-3M3 13.5h18M5.25 4.5h13.5A2.25 2.25 0 0 1 21 6.75v7.5a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 14.25v-7.5A2.25 2.25 0 0 1 5.25 4.5Z" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">Digital Skills</p>
                    <h3 class="mt-1 font-heading text-lg font-semibold text-sign-primary">Computer Hardware</h3>
                    <p class="mt-1 text-sm text-sign-muted">Understand basic computer parts.</p>
                </div>
                <span class="text-sign-primary transition group-hover:translate-x-1">→</span>
            </a>

            <a href="{{ route('learn') }}" class="group flex items-center gap-4 rounded-2xl border border-sign-border bg-white p-5 transition hover:-translate-y-0.5 hover:border-sign-cyan hover:shadow-md">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m9-9H3" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">Mathematics</p>
                    <h3 class="mt-1 font-heading text-lg font-semibold text-sign-primary">Quick Calculations</h3>
                    <p class="mt-1 text-sm text-sign-muted">Addition, subtraction and mental maths.</p>
                </div>
                <span class="text-sign-primary transition group-hover:translate-x-1">→</span>
            </a>

            <a href="{{ route('learn') }}" class="group flex items-center gap-4 rounded-2xl border border-sign-border bg-white p-5 transition hover:-translate-y-0.5 hover:border-sign-cyan hover:shadow-md">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0c2.25-2.25 3.375-5.25 3.375-9S14.25 5.25 12 3M3.75 9h16.5m-16.5 6h16.5" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">General Knowledge</p>
                    <h3 class="mt-1 font-heading text-lg font-semibold text-sign-primary">India & the World</h3>
                    <p class="mt-1 text-sm text-sign-muted">Important places, people and facts.</p>
                </div>
                <span class="text-sign-primary transition group-hover:translate-x-1">→</span>
            </a>

            <a href="{{ route('learn') }}" class="group flex items-center gap-4 rounded-2xl border border-sign-border bg-white p-5 transition hover:-translate-y-0.5 hover:border-sign-cyan hover:shadow-md">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                        <circle cx="12" cy="12" r="9" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">Life Skills</p>
                    <h3 class="mt-1 font-heading text-lg font-semibold text-sign-primary">Time Management</h3>
                    <p class="mt-1 text-sm text-sign-muted">Plan your day and organise tasks better.</p>
                </div>
                <span class="text-sign-primary transition group-hover:translate-x-1">→</span>
            </a>

            <a href="{{ route('learn') }}" class="group flex items-center gap-4 rounded-2xl border border-sign-border bg-white p-5 transition hover:-translate-y-0.5 hover:border-sign-cyan hover:shadow-md">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3h6m-7.5 7.5 2.25-2.25h8.25A2.25 2.25 0 0 0 18.75 14.25v-7.5A2.25 2.25 0 0 0 16.5 4.5h-9A2.25 2.25 0 0 0 5.25 6.75v7.5A2.25 2.25 0 0 0 7.5 16.5v2.25Z" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">ISL</p>
                    <h3 class="mt-1 font-heading text-lg font-semibold text-sign-primary">Everyday ISL</h3>
                    <p class="mt-1 text-sm text-sign-muted">Common signs for daily situations.</p>
                </div>
                <span class="text-sign-primary transition group-hover:translate-x-1">→</span>
            </a>

        </div>

        <div class="mt-10 rounded-3xl bg-sign-primary px-6 py-8 text-white sm:flex sm:items-center sm:justify-between sm:gap-8 lg:px-10">
            <div>
                <p class="text-sm font-semibold text-sign-light">Quick learning</p>
                <h3 class="mt-2 font-heading text-2xl font-semibold sm:text-3xl">Learn one useful topic today.</h3>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-white/80">Short lessons help you build knowledge without needing to complete a full course first.</p>
            </div>

            <a href="{{ route('explore') }}" class="mt-6 inline-flex shrink-0 items-center justify-center rounded-lg bg-white px-5 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-light sm:mt-0">
                Browse Topics
            </a>
        </div>

    </x-container>
</section>
