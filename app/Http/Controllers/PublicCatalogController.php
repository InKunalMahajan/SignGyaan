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

        $firstPublishedLesson = $courseModel->units
            ->flatMap(fn ($unit) => $unit->lessons)
            ->first();

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
            'firstPublishedLesson' => $firstPublishedLesson,
            'firstLessonKey' => $firstPublishedLesson ? 'unit-1-lesson-1' : null,
        ]);
    }
}
