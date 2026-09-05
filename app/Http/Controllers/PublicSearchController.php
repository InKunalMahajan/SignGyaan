<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicSearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));
        $query = mb_substr($query, 0, 120);

        $allowedTypes = ['all', 'subject', 'course', 'lesson', 'topic'];
        $activeType = in_array($request->query('type'), $allowedTypes, true)
            ? (string) $request->query('type')
            : 'all';

        $subjects = Subject::query()
            ->published()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $requestedSubject = (string) $request->query('subject', 'all');
        $activeSubject = $requestedSubject !== 'all' && $subjects->contains('slug', $requestedSubject)
            ? $requestedSubject
            : 'all';

        $terms = collect(preg_split('/\s+/u', mb_strtolower($query)) ?: [])
            ->map(fn ($term) => trim((string) $term))
            ->filter()
            ->unique()
            ->take(8)
            ->values();

        $subjectResults = collect();
        $courseResults = collect();
        $lessonResults = collect();
        $topicResults = collect();

        if ($query !== '') {
            if (in_array($activeType, ['all', 'subject'], true)) {
                $subjectQuery = Subject::query()->published();

                if ($activeSubject !== 'all') {
                    $subjectQuery->where('slug', $activeSubject);
                }

                $this->applyTerms($subjectQuery, $terms->all(), function (Builder $builder, string $term): void {
                    $builder
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('slug', 'like', "%{$term}%")
                        ->orWhere('short_description', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                });

                $subjectResults = $subjectQuery
                    ->withCount([
                        'courses as courses_count' => fn ($courseQuery) => $courseQuery->published(),
                    ])
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->limit(12)
                    ->get();
            }

            if (in_array($activeType, ['all', 'course'], true)) {
                $courseQuery = Course::query()
                    ->published()
                    ->whereHas('subject', fn ($subjectQuery) => $subjectQuery->published())
                    ->with('subject')
                    ->withCount([
                        'units as units_count' => fn ($unitQuery) => $unitQuery->published(),
                        'lessons as lessons_count' => fn ($lessonQuery) => $lessonQuery
                            ->published()
                            ->whereHas('unit', fn ($unitQuery) => $unitQuery->published()),
                    ]);

                if ($activeSubject !== 'all') {
                    $courseQuery->whereHas('subject', fn ($subjectQuery) => $subjectQuery->where('slug', $activeSubject));
                }

                $this->applyTerms($courseQuery, $terms->all(), function (Builder $builder, string $term): void {
                    $builder
                        ->where('title', 'like', "%{$term}%")
                        ->orWhere('slug', 'like', "%{$term}%")
                        ->orWhere('level', 'like', "%{$term}%")
                        ->orWhere('short_description', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%")
                        ->orWhereHas('subject', fn ($subjectQuery) => $subjectQuery
                            ->published()
                            ->where(function ($subjectMatch) use ($term) {
                                $subjectMatch
                                    ->where('name', 'like', "%{$term}%")
                                    ->orWhere('slug', 'like', "%{$term}%");
                            }));
                });

                $courseResults = $courseQuery
                    ->orderByDesc('is_featured')
                    ->orderBy('sort_order')
                    ->orderBy('title')
                    ->limit(24)
                    ->get();
            }

            if (in_array($activeType, ['all', 'lesson'], true)) {
                $lessonQuery = Lesson::query()
                    ->published()
                    ->whereHas('unit', fn ($unitQuery) => $unitQuery
                        ->published()
                        ->whereHas('course', fn ($courseQuery) => $courseQuery
                            ->published()
                            ->whereHas('subject', fn ($subjectQuery) => $subjectQuery->published())))
                    ->with([
                        'unit.course.subject',
                        'mediaAsset',
                    ]);

                if ($activeSubject !== 'all') {
                    $lessonQuery->whereHas('unit.course.subject', fn ($subjectQuery) => $subjectQuery->where('slug', $activeSubject));
                }

                $this->applyTerms($lessonQuery, $terms->all(), function (Builder $builder, string $term): void {
                    $builder
                        ->where('title', 'like', "%{$term}%")
                        ->orWhere('slug', 'like', "%{$term}%")
                        ->orWhere('short_description', 'like', "%{$term}%")
                        ->orWhere('learning_objectives', 'like', "%{$term}%")
                        ->orWhere('content', 'like', "%{$term}%")
                        ->orWhere('key_points', 'like', "%{$term}%")
                        ->orWhere('example_content', 'like', "%{$term}%")
                        ->orWhereHas('unit', fn ($unitQuery) => $unitQuery
                            ->published()
                            ->where(function ($unitMatch) use ($term) {
                                $unitMatch
                                    ->where('title', 'like', "%{$term}%")
                                    ->orWhere('short_description', 'like', "%{$term}%")
                                    ->orWhere('description', 'like', "%{$term}%");
                            }))
                        ->orWhereHas('unit.course', fn ($courseQuery) => $courseQuery
                            ->published()
                            ->where('title', 'like', "%{$term}%"))
                        ->orWhereHas('unit.course.subject', fn ($subjectQuery) => $subjectQuery
                            ->published()
                            ->where('name', 'like', "%{$term}%"))
                        ->orWhereHas('practiceResources', fn ($practiceQuery) => $practiceQuery
                            ->published()
                            ->where(function ($practiceMatch) use ($term) {
                                $practiceMatch
                                    ->where('title', 'like', "%{$term}%")
                                    ->orWhere('short_description', 'like', "%{$term}%")
                                    ->orWhere('instructions', 'like', "%{$term}%")
                                    ->orWhere('content', 'like', "%{$term}%")
                                    ->orWhere('resource_type', 'like', "%{$term}%");
                            }))
                        ->orWhereHas('mediaAsset', fn ($mediaQuery) => $mediaQuery
                            ->published()
                            ->where(function ($mediaMatch) use ($term) {
                                $mediaMatch
                                    ->where('title', 'like', "%{$term}%")
                                    ->orWhere('alt_text', 'like', "%{$term}%")
                                    ->orWhere('caption', 'like', "%{$term}%");
                            }));
                });

                $lessonResults = $lessonQuery
                    ->orderByDesc('updated_at')
                    ->orderBy('title')
                    ->limit(30)
                    ->get();
            }

            if (in_array($activeType, ['all', 'topic'], true)) {
                $topicQuery = Unit::query()
                    ->published()
                    ->whereHas('course', fn ($courseQuery) => $courseQuery
                        ->published()
                        ->whereHas('subject', fn ($subjectQuery) => $subjectQuery->published()))
                    ->with('course.subject')
                    ->withCount([
                        'lessons as lessons_count' => fn ($lessonQuery) => $lessonQuery->published(),
                    ]);

                if ($activeSubject !== 'all') {
                    $topicQuery->whereHas('course.subject', fn ($subjectQuery) => $subjectQuery->where('slug', $activeSubject));
                }

                $this->applyTerms($topicQuery, $terms->all(), function (Builder $builder, string $term): void {
                    $builder
                        ->where('title', 'like', "%{$term}%")
                        ->orWhere('slug', 'like', "%{$term}%")
                        ->orWhere('short_description', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%")
                        ->orWhereHas('course', fn ($courseQuery) => $courseQuery
                            ->published()
                            ->where('title', 'like', "%{$term}%"))
                        ->orWhereHas('course.subject', fn ($subjectQuery) => $subjectQuery
                            ->published()
                            ->where('name', 'like', "%{$term}%"));
                });

                $topicResults = $topicQuery
                    ->orderBy('course_id')
                    ->orderBy('sort_order')
                    ->orderBy('title')
                    ->limit(24)
                    ->get();
            }
        }

        $typeCounts = [
            'subject' => $subjectResults->count(),
            'course' => $courseResults->count(),
            'lesson' => $lessonResults->count(),
            'topic' => $topicResults->count(),
        ];

        $totalResults = array_sum($typeCounts);

        $suggestedCourses = Course::query()
            ->published()
            ->whereHas('subject', fn ($subjectQuery) => $subjectQuery->published())
            ->with('subject')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->limit(6)
            ->get();

        return view('pages.search', compact(
            'query',
            'subjects',
            'activeType',
            'activeSubject',
            'subjectResults',
            'courseResults',
            'lessonResults',
            'topicResults',
            'typeCounts',
            'totalResults',
            'suggestedCourses'
        ));
    }

    private function applyTerms(Builder $query, array $terms, callable $matcher): void
    {
        foreach ($terms as $term) {
            $query->where(function (Builder $builder) use ($matcher, $term): void {
                $matcher($builder, $term);
            });
        }
    }
}
