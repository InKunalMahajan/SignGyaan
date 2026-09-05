<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Subject;
use Illuminate\View\View;

class PublicCatalogController extends Controller
{
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

    public function course(string $subject, string $course): View
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
                                'mediaAsset',
                                'practiceResources' => fn ($practiceQuery) => $practiceQuery
                                    ->published()
                                    ->with('mediaAsset')
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

                $lessonMap->push([
                    'key' => 'unit-'.$unitNumber.'-lesson-'.$lessonNumber,
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
                fn (array $entry) => $entry['key'] === $requestedLessonKey
            );

            abort_if($currentLessonIndex === false, 404);

            $currentLessonEntry = $lessonMap->get($currentLessonIndex);
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
            'firstLessonKey' => $firstLessonEntry['key'] ?? null,
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
