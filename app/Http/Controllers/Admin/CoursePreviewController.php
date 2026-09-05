<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CoursePreviewController extends Controller
{
    public function __invoke(Request $request, Course $course): View
    {
        $course->load([
            'subject',
            'units' => fn ($unitQuery) => $unitQuery
                ->with([
                    'lessons' => fn ($lessonQuery) => $lessonQuery
                        ->with([
                            'mediaAsset',
                            'practiceResources' => fn ($practiceQuery) => $practiceQuery
                                ->with(['mediaAsset', 'assessment'])
                                ->orderBy('sort_order')
                                ->orderBy('title'),
                        ])
                        ->orderBy('sort_order')
                        ->orderBy('title'),
                ])
                ->orderBy('sort_order')
                ->orderBy('title'),
        ]);

        // Preview is admin-only. Make linked draft media render without changing database state.
        foreach ($course->units as $unit) {
            foreach ($unit->lessons as $lesson) {
                if ($lesson->mediaAsset) {
                    $lesson->mediaAsset->setAttribute('is_published', true);
                }

                foreach ($lesson->practiceResources as $resource) {
                    if ($resource->mediaAsset) {
                        $resource->mediaAsset->setAttribute('is_published', true);
                    }
                }
            }
        }

        $lessonMap = collect();

        foreach ($course->units as $unitIndex => $unit) {
            foreach ($unit->lessons as $lessonIndex => $lesson) {
                $lessonMap->push([
                    'key' => 'lesson-'.$lesson->id,
                    'stable_key' => 'lesson-'.$lesson->id,
                    'legacy_key' => 'unit-'.($unitIndex + 1).'-lesson-'.($lessonIndex + 1),
                    'unit_number' => $unitIndex + 1,
                    'lesson_number' => $lessonIndex + 1,
                    'unit' => $unit,
                    'lesson' => $lesson,
                ]);
            }
        }

        $requestedLessonKey = trim((string) $request->query('lesson', ''));
        $currentLessonIndex = null;
        $currentLessonEntry = null;

        if ($requestedLessonKey !== '') {
            $currentLessonIndex = $lessonMap->search(fn (array $entry) =>
                $entry['stable_key'] === $requestedLessonKey || $entry['legacy_key'] === $requestedLessonKey
            );

            abort_if($currentLessonIndex === false, 404);
            $currentLessonEntry = $lessonMap->get($currentLessonIndex);
        }

        $subjectCodes = [
            'english' => 'Aa',
            'mathematics' => '123',
            'science' => 'SCI',
            'digital-skills' => 'PC',
            'general-knowledge' => 'GK',
            'life-skills' => 'LS',
        ];

        $currentPosition = $currentLessonIndex === null ? null : $currentLessonIndex + 1;
        $totalLessons = $lessonMap->count();

        return view('admin.courses.preview', [
            'isAdminPreview' => true,
            'subject' => [
                'name' => $course->subject?->name ?? 'Subject',
                'code' => $subjectCodes[$course->subject?->slug] ?? strtoupper(substr($course->subject?->name ?? 'SG', 0, 2)),
            ],
            'course' => [
                'title' => $course->title,
                'level' => $course->level,
                'description' => $course->description ?: ($course->short_description ?: 'Structured visual learning with clear lessons and practice.'),
                'units' => $course->units->count(),
                'lessons' => $lessonMap->count(),
            ],
            'subjectSlug' => $course->subject?->slug ?? 'preview',
            'courseSlug' => $course->slug,
            'courseModel' => $course,
            'publishedUnits' => $course->units,
            'lessonMap' => $lessonMap,
            'firstPublishedLesson' => $lessonMap->first()['lesson'] ?? null,
            'firstLessonKey' => $lessonMap->first()['stable_key'] ?? null,
            'currentLessonEntry' => $currentLessonEntry,
            'previousLessonEntry' => $currentLessonIndex !== null && $currentLessonIndex > 0 ? $lessonMap->get($currentLessonIndex - 1) : null,
            'nextLessonEntry' => $currentLessonIndex !== null && $currentLessonIndex < $lessonMap->count() - 1 ? $lessonMap->get($currentLessonIndex + 1) : null,
            'currentLessonModel' => $currentLessonEntry['lesson'] ?? null,
            'currentUnitModel' => $currentLessonEntry['unit'] ?? null,
            'currentPosition' => $currentPosition,
            'totalLessons' => $totalLessons,
            'positionProgressPercent' => $currentPosition ? (int) round(($currentPosition / max(1, $totalLessons)) * 100) : 0,
        ]);
    }
}
