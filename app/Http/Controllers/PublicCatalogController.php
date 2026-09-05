<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Subject;
use App\Services\LearningProgressCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicCatalogController extends Controller
{
    public function home(Request $request, LearningProgressCatalog $progressCatalog): View
    {
        $subjects = Subject::query()
            ->published()
            ->withCount([
                'courses as courses_count' => fn ($query) => $query->published(),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit(6)
            ->get();

        $featuredCourses = Course::query()
            ->published()
            ->whereHas('subject', fn ($query) => $query->published())
            ->with('subject')
            ->withCount([
                'units as units_count' => fn ($query) => $query->published(),
                'lessons as lessons_count' => fn ($query) => $query
                    ->published()
                    ->whereHas('unit', fn ($unitQuery) => $unitQuery->published()),
            ])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->limit(6)
            ->get();

        $popularLessons = Lesson::query()
            ->published()
            ->whereHas('unit', fn ($unitQuery) => $unitQuery
                ->published()
                ->whereHas('course', fn ($courseQuery) => $courseQuery
                    ->published()
                    ->whereHas('subject', fn ($subjectQuery) => $subjectQuery->published())))
            ->with('unit.course.subject')
            ->orderByDesc('updated_at')
            ->orderBy('title')
            ->limit(6)
            ->get();

        $continueLearning = collect();

        if ($request->user()) {
            $progressItems = $request->user()
                ->learningProgress()
                ->latest('last_accessed_at')
                ->limit(25)
                ->get();

            $continueLearning = $progressItems
                ->map(function ($progress) use ($progressCatalog) {
                    $state = $progressCatalog->resolve($progress->subject_slug, $progress->course_slug);

                    if (! $state || $state['entries']->isEmpty()) {
                        return null;
                    }

                    $progress = $progressCatalog->synchronizeRecord($progress, $state);

                    if ($progress->completed_at || ! $progress->current_lesson_key) {
                        return null;
                    }

                    $course = $state['course'];

                    return [
                        'progress' => $progress,
                        'course' => $course,
                        'url' => route('courses.show', [
                            'subject' => $state['subject']->slug,
                            'course' => $course->slug,
                            'lesson' => $progress->current_lesson_key,
                        ]),
                    ];
                })
                ->filter()
                ->take(3)
                ->values();
        }

        return view('home', compact(
            'subjects',
            'featuredCourses',
            'popularLessons',
            'continueLearning'
        ));
    }

    public function learn(): View
    {
        $subjects = Subject::query()
            ->published()
            ->with([
                'courses' => fn ($query) => $query
                    ->published()
                    ->withCount([
                        'units as units_count' => fn ($unitQuery) => $unitQuery->published(),
                        'lessons as lessons_count' => fn ($lessonQuery) => $lessonQuery
                            ->published()
                            ->whereHas('unit', fn ($unitQuery) => $unitQuery->published()),
                    ])
                    ->orderBy('sort_order')
                    ->orderBy('title'),
            ])
            ->withCount([
                'courses as courses_count' => fn ($query) => $query->published(),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $learningPaths = Course::query()
            ->published()
            ->whereHas('subject', fn ($query) => $query->published())
            ->with('subject')
            ->withCount([
                'units as units_count' => fn ($query) => $query->published(),
                'lessons as lessons_count' => fn ($query) => $query
                    ->published()
                    ->whereHas('unit', fn ($unitQuery) => $unitQuery->published()),
            ])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->limit(9)
            ->get();

        $catalogCounts = [
            'subjects' => $subjects->count(),
            'courses' => $subjects->sum('courses_count'),
            'lessons' => $subjects->sum(fn ($subject) => $subject->courses->sum('lessons_count')),
        ];

        return view('pages.learn', compact('subjects', 'learningPaths', 'catalogCounts'));
    }

    public function explore(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));
        $activeType = in_array($request->query('type'), ['course', 'lesson'], true)
            ? (string) $request->query('type')
            : 'all';

        $subjects = Subject::query()
            ->published()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $requestedCategory = (string) $request->query('category', 'all');
        $activeCategory = $requestedCategory !== 'all' && $subjects->contains('slug', $requestedCategory)
            ? $requestedCategory
            : 'all';

        $items = collect();

        if ($activeType !== 'lesson') {
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

            if ($activeCategory !== 'all') {
                $courseQuery->whereHas('subject', fn ($subjectQuery) => $subjectQuery->where('slug', $activeCategory));
            }

            if ($query !== '') {
                $courseQuery->where(function ($builder) use ($query) {
                    $builder
                        ->where('title', 'like', "%{$query}%")
                        ->orWhere('short_description', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%")
                        ->orWhereHas('subject', fn ($subjectQuery) => $subjectQuery->where('name', 'like', "%{$query}%"));
                });
            }

            $courses = $courseQuery
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->orderBy('title')
                ->limit(36)
                ->get();

            foreach ($courses as $course) {
                $items->push([
                    'type' => 'Course',
                    'type_key' => 'course',
                    'title' => $course->title,
                    'subject' => $course->subject->name,
                    'category' => $course->subject->slug,
                    'description' => $course->short_description ?: ($course->description ?: 'Structured visual learning with clear units and lessons.'),
                    'meta' => $course->units_count.' '.($course->units_count === 1 ? 'unit' : 'units').' · '.$course->lessons_count.' '.($course->lessons_count === 1 ? 'lesson' : 'lessons'),
                    'url' => route('courses.show', [
                        'subject' => $course->subject->slug,
                        'course' => $course->slug,
                    ]),
                    'featured' => $course->is_featured,
                ]);
            }
        }

        if ($activeType !== 'course') {
            $lessonQuery = Lesson::query()
                ->published()
                ->whereHas('unit', fn ($unitQuery) => $unitQuery
                    ->published()
                    ->whereHas('course', fn ($courseQuery) => $courseQuery
                        ->published()
                        ->whereHas('subject', fn ($subjectQuery) => $subjectQuery->published())))
                ->with('unit.course.subject');

            if ($activeCategory !== 'all') {
                $lessonQuery->whereHas('unit.course.subject', fn ($subjectQuery) => $subjectQuery->where('slug', $activeCategory));
            }

            if ($query !== '') {
                $lessonQuery->where(function ($builder) use ($query) {
                    $builder
                        ->where('title', 'like', "%{$query}%")
                        ->orWhere('short_description', 'like', "%{$query}%")
                        ->orWhere('content', 'like', "%{$query}%")
                        ->orWhereHas('unit', fn ($unitQuery) => $unitQuery->where('title', 'like', "%{$query}%"))
                        ->orWhereHas('unit.course', fn ($courseQuery) => $courseQuery->where('title', 'like', "%{$query}%"))
                        ->orWhereHas('unit.course.subject', fn ($subjectQuery) => $subjectQuery->where('name', 'like', "%{$query}%"));
                });
            }

            $lessons = $lessonQuery
                ->orderByDesc('updated_at')
                ->orderBy('title')
                ->limit(36)
                ->get();

            foreach ($lessons as $lesson) {
                $course = $lesson->unit->course;
                $subject = $course->subject;

                $items->push([
                    'type' => 'Lesson',
                    'type_key' => 'lesson',
                    'title' => $lesson->title,
                    'subject' => $subject->name,
                    'category' => $subject->slug,
                    'description' => $lesson->short_description ?: 'Open this lesson for visual notes, examples, ISL support and practice where available.',
                    'meta' => $course->title.' · '.$lesson->unit->title,
                    'url' => route('courses.show', [
                        'subject' => $subject->slug,
                        'course' => $course->slug,
                        'lesson' => 'lesson-'.$lesson->id,
                    ]),
                    'featured' => false,
                ]);
            }
        }

        $items = $items
            ->sortBy([
                ['featured', 'desc'],
                ['type_key', 'asc'],
                ['title', 'asc'],
            ])
            ->take(48)
            ->values();

        return view('pages.explore', [
            'subjects' => $subjects,
            'items' => $items,
            'query' => $query,
            'activeCategory' => $activeCategory,
            'activeType' => $activeType,
        ]);
    }

    public function subjects(): View
    {
        $subjects = Subject::query()
            ->published()
            ->withCount([
                'courses as courses_count' => fn ($query) => $query->published(),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('pages.subjects', compact('subjects'));
    }

    public function subject(string $subject): View
    {
        $subjectModel = Subject::query()
            ->published()
            ->where('slug', $subject)
            ->with([
                'courses' => fn ($query) => $query
                    ->published()
                    ->withCount([
                        'units as units_count' => fn ($unitQuery) => $unitQuery->published(),
                        'lessons as lessons_count' => fn ($lessonQuery) => $lessonQuery
                            ->published()
                            ->whereHas('unit', fn ($unitQuery) => $unitQuery->published()),
                    ])
                    ->orderBy('sort_order')
                    ->orderBy('title'),
            ])
            ->firstOrFail();

        $featuredCourse = $subjectModel->courses->firstWhere('is_featured', true)
            ?? $subjectModel->courses->first();

        return view('pages.subject', [
            'subject' => $subjectModel,
            'featuredCourse' => $featuredCourse,
        ]);
    }

    public function course(string $subject, string $course): View|RedirectResponse
    {
        $subjectModel = Subject::query()
            ->published()
            ->where('slug', $subject)
            ->firstOrFail();

        $courseModel = Course::query()
            ->published()
            ->where('subject_id', $subjectModel->id)
            ->where('slug', $course)
            ->with([
                'units' => fn ($unitQuery) => $unitQuery
                    ->published()
                    ->with([
                        'lessons' => fn ($lessonQuery) => $lessonQuery
                            ->published()
                            ->with([
                                'mediaAsset' => fn ($mediaQuery) => $mediaQuery->published(),
                                'practiceResources' => fn ($practiceQuery) => $practiceQuery
                                    ->published()
                                    ->with([
                                        'mediaAsset' => fn ($mediaQuery) => $mediaQuery->published(),
                                    ])
                                    ->orderBy('sort_order')
                                    ->orderBy('title'),
                            ])
                            ->orderBy('sort_order')
                            ->orderBy('title'),
                    ])
                    ->orderBy('sort_order')
                    ->orderBy('title'),
            ])
            ->withCount([
                'units as units_count' => fn ($unitQuery) => $unitQuery->published(),
                'lessons as lessons_count' => fn ($lessonQuery) => $lessonQuery
                    ->published()
                    ->whereHas('unit', fn ($unitQuery) => $unitQuery->published()),
            ])
            ->firstOrFail();

        $subjectCodes = [
            'english' => 'Aa',
            'mathematics' => '123',
            'science' => 'SCI',
            'digital-skills' => 'PC',
            'general-knowledge' => 'GK',
            'life-skills' => 'LS',
        ];

        $lessonMap = collect();

        foreach ($courseModel->units as $unitIndex => $unit) {
            $unitNumber = $unitIndex + 1;

            foreach ($unit->lessons as $lessonIndex => $lessonModel) {
                $lessonNumber = $lessonIndex + 1;
                $stableKey = 'lesson-'.$lessonModel->id;
                $legacyKey = 'unit-'.$unitNumber.'-lesson-'.$lessonNumber;

                $lessonMap->push([
                    'key' => $stableKey,
                    'stable_key' => $stableKey,
                    'legacy_key' => $legacyKey,
                    'unit_number' => $unitNumber,
                    'lesson_number' => $lessonNumber,
                    'unit' => $unit,
                    'lesson' => $lessonModel,
                ]);
            }
        }

        $firstLessonEntry = $lessonMap->first();
        $requestedLessonKey = trim((string) request()->query('lesson', ''));
        $currentLessonEntry = null;
        $previousLessonEntry = null;
        $nextLessonEntry = null;
        $currentLessonIndex = null;

        if ($requestedLessonKey !== '') {
            $currentLessonIndex = $lessonMap->search(
                fn (array $entry) => $entry['stable_key'] === $requestedLessonKey
                    || $entry['legacy_key'] === $requestedLessonKey
            );

            abort_if($currentLessonIndex === false, 404);

            $currentLessonEntry = $lessonMap->get($currentLessonIndex);

            if ($requestedLessonKey !== $currentLessonEntry['stable_key']) {
                return redirect()->route('courses.show', [
                    'subject' => $subjectModel->slug,
                    'course' => $courseModel->slug,
                    'lesson' => $currentLessonEntry['stable_key'],
                ], 301);
            }

            $previousLessonEntry = $currentLessonIndex > 0
                ? $lessonMap->get($currentLessonIndex - 1)
                : null;
            $nextLessonEntry = $currentLessonIndex < $lessonMap->count() - 1
                ? $lessonMap->get($currentLessonIndex + 1)
                : null;
        }

        $currentPosition = $currentLessonIndex === null ? null : $currentLessonIndex + 1;
        $totalLessons = $lessonMap->count();
        $positionProgressPercent = $currentPosition
            ? (int) round(($currentPosition / max(1, $totalLessons)) * 100)
            : 0;

        return view('pages.course', [
            'subject' => [
                'name' => $subjectModel->name,
                'code' => $subjectCodes[$subjectModel->slug] ?? strtoupper(substr($subjectModel->name, 0, 2)),
            ],
            'course' => [
                'title' => $courseModel->title,
                'level' => $courseModel->level,
                'description' => $courseModel->description ?: ($courseModel->short_description ?: 'Structured visual learning with clear lessons and practice.'),
                'units' => $courseModel->units_count,
                'lessons' => $courseModel->lessons_count,
                'outcomes' => [
                    'Understand the core ideas in '.$courseModel->title,
                    'Learn through clear visual explanations and examples',
                    'Use ISL-supported learning where available',
                    'Build confidence through structured practice and revision',
                ],
            ],
            'subjectSlug' => $subjectModel->slug,
            'courseSlug' => $courseModel->slug,
            'courseModel' => $courseModel,
            'publishedUnits' => $courseModel->units,
            'lessonMap' => $lessonMap,
            'firstPublishedLesson' => $firstLessonEntry['lesson'] ?? null,
            'firstLessonKey' => $firstLessonEntry['stable_key'] ?? null,
            'currentLessonEntry' => $currentLessonEntry,
            'previousLessonEntry' => $previousLessonEntry,
            'nextLessonEntry' => $nextLessonEntry,
            'currentLessonModel' => $currentLessonEntry['lesson'] ?? null,
            'currentUnitModel' => $currentLessonEntry['unit'] ?? null,
            'currentPosition' => $currentPosition,
            'totalLessons' => $totalLessons,
            'positionProgressPercent' => $positionProgressPercent,
        ]);
    }
}
