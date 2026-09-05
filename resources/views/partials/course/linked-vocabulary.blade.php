@php
    $linkedVocabulary = $currentLessonModel->vocabularyTerms()
        ->published()
        ->where(function ($query) use ($subjectModel) {
            $query->whereNull('vocabulary_terms.subject_id')
                ->orWhere('vocabulary_terms.subject_id', $subjectModel->id);
        })
        ->where(function ($query) use ($courseModel) {
            $query->whereNull('vocabulary_terms.course_id')
                ->orWhere('vocabulary_terms.course_id', $courseModel->id);
        })
        ->with([
            'mediaAsset' => fn ($query) => $query
                ->published()
                ->where('media_type', 'video'),
        ])
        ->get();
@endphp

@if ($linkedVocabulary->isNotEmpty())
    <section id="lesson-isl-vocabulary" class="border-t border-sign-border bg-sign-soft py-8 sm:py-10 print:bg-white print:py-5" aria-labelledby="lesson-isl-vocabulary-heading">
        <x-container>
            <div class="mx-auto max-w-6xl">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">ISL vocabulary</p>
                        <h2 id="lesson-isl-vocabulary-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Signs used in this lesson</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-sign-muted">Review the important signs linked to this lesson. Open any term for its full meaning, example and ISL video.</p>
                    </div>
                    <a href="{{ route('vocabulary.index', ['subject' => $subjectModel->slug, 'course' => $courseModel->slug]) }}" class="inline-flex min-h-11 w-fit items-center justify-center rounded-xl border border-sign-primary bg-white px-4 py-2.5 text-sm font-semibold text-sign-primary transition hover:bg-sign-light print:hidden">
                        Browse ISL Vocabulary
                    </a>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($linkedVocabulary as $term)
                        @php
                            $publishedMedia = $term->mediaAsset;
                            $signVideoUrl = $publishedMedia?->publicUrl() ?: $term->isl_video_url;
                        @endphp
                        <article class="flex h-full flex-col rounded-2xl border border-sign-border bg-white p-5 shadow-sm print:break-inside-avoid print:shadow-none">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-[11px] font-semibold uppercase tracking-wider text-sign-cyan-dark">ISL sign</p>
                                    <h3 class="mt-1 font-heading text-xl font-semibold text-sign-primary">{{ $term->term }}</h3>
                                </div>
                                <span class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $signVideoUrl ? 'bg-sign-light text-sign-primary' : 'bg-gray-100 text-sign-muted' }}">
                                    {{ $signVideoUrl ? 'Video available' : 'Text only' }}
                                </span>
                            </div>

                            @if ($term->meaning)
                                <p class="mt-3 flex-1 text-sm leading-6 text-sign-muted">{{ $term->meaning }}</p>
                            @else
                                <p class="mt-3 flex-1 text-sm leading-6 text-sign-muted">Open this term to review its SignGyaan vocabulary entry.</p>
                            @endif

                            @if ($term->example)
                                <div class="mt-4 rounded-xl bg-sign-soft p-3">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-sign-cyan-dark">Example</p>
                                    <p class="mt-1 text-xs leading-5 text-sign-muted">{{ $term->example }}</p>
                                </div>
                            @endif

                            <a href="{{ route('vocabulary.show', $term->slug) }}" class="mt-4 inline-flex min-h-11 items-center justify-between rounded-xl border border-sign-border px-4 py-2.5 text-sm font-semibold text-sign-primary transition hover:border-sign-cyan hover:bg-sign-soft print:hidden">
                                <span>{{ $signVideoUrl ? 'View ISL sign' : 'View vocabulary' }}</span>
                                <span aria-hidden="true">→</span>
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        </x-container>
    </section>
@endif
