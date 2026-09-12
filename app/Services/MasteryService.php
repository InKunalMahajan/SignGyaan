<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseMastery;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Support\Collection;

class MasteryService
{
    private array $signalCache = [];

    public function __construct(private AssessmentAnalyticsService $assessmentAnalytics)
    {
    }

    public function calculateFor(User $learner, Course $course): CourseMastery
    {
        $lessons = $this->publishedLessons($course);
        $lessonIds = $lessons->pluck('id');
        $completedLessonIds = LessonProgress::query()
            ->where('learner_id', $learner->id)
            ->where('status', 'completed')
            ->whereIn('lesson_id', $lessonIds)
            ->pluck('lesson_id');

        $lessonTotal = $lessons->count();
        $lessonCompleted = $completedLessonIds->unique()->count();
        $lessonPercentage = $lessonTotal > 0
            ? round(($lessonCompleted / $lessonTotal) * 100, 2)
            : 0.0;

        $signals = $this->signalsFor($learner)
            ->where('course_id', $course->id)
            ->values();

        $assessmentPercentage = null;
        if ($signals->isNotEmpty()) {
            $earned = $signals->sum(fn ($signal) => (float) ($signal['earned_marks'] ?? 0));
            $possible = $signals->sum(fn ($signal) => (float) ($signal['total_marks'] ?? 0));
            $assessmentPercentage = $possible > 0 ? round(($earned / $possible) * 100, 2) : 0.0;
        }

        $masteryScore = $assessmentPercentage === null
            ? $lessonPercentage
            : round(($lessonPercentage * 0.40) + ($assessmentPercentage * 0.60), 2);

        return CourseMastery::updateOrCreate(
            ['learner_id' => $learner->id, 'course_id' => $course->id],
            [
                'lessons_completed' => $lessonCompleted,
                'lessons_total' => $lessonTotal,
                'lesson_completion_percentage' => $lessonPercentage,
                'assessments_completed' => $signals->count(),
                'assessment_percentage' => $assessmentPercentage,
                'mastery_score' => $masteryScore,
                'mastery_level' => $this->levelFor($masteryScore),
                'evidence_status' => $assessmentPercentage === null ? 'learning_only' : 'assessment_supported',
                'last_calculated_at' => now(),
            ]
        )->fresh(['course.subject', 'learner']);
    }

    public function snapshot(User $learner, Course $course): array
    {
        $mastery = $this->calculateFor($learner, $course);
        $course->loadMissing(['subject', 'units.lessons']);
        $completed = LessonProgress::query()
            ->where('learner_id', $learner->id)
            ->where('status', 'completed')
            ->pluck('lesson_id')
            ->flip();
        $started = LessonProgress::query()
            ->where('learner_id', $learner->id)
            ->pluck('status', 'lesson_id');

        $chapters = $course->units
            ->where('is_active', true)
            ->map(function ($unit) use ($completed, $started) {
                $lessons = $unit->lessons->where('status', 'published')->values();
                $total = $lessons->count();
                $done = $lessons->filter(fn ($lesson) => $completed->has($lesson->id))->count();
                $percentage = $total > 0 ? round(($done / $total) * 100, 2) : 0.0;

                return [
                    'id' => $unit->id,
                    'title' => $unit->title,
                    'position' => $unit->position,
                    'lessons_completed' => $done,
                    'lessons_total' => $total,
                    'percentage' => $percentage,
                    'level' => $this->levelFor($percentage),
                    'level_label' => $this->labelFor($percentage),
                    'lessons' => $lessons->map(fn ($lesson) => [
                        'id' => $lesson->id,
                        'title' => $lesson->title,
                        'position' => $lesson->position,
                        'status' => $completed->has($lesson->id)
                            ? 'completed'
                            : ($started->has($lesson->id) ? 'in_progress' : 'not_started'),
                    ])->values(),
                ];
            })
            ->values();

        return [
            'mastery' => $mastery,
            'chapters' => $chapters,
            'recommendations' => $this->recommendations($chapters, $mastery),
        ];
    }

    public function levelFor(float $score): string
    {
        return match (true) {
            $score >= 80 => 'mastered',
            $score >= 60 => 'good',
            $score >= 40 => 'developing',
            default => 'needs_support',
        };
    }

    public function labelFor(float $score): string
    {
        return match ($this->levelFor($score)) {
            'mastered' => 'Mastered',
            'good' => 'Good',
            'developing' => 'Developing',
            default => 'Needs Support',
        };
    }

    public function publishedLessons(Course $course): Collection
    {
        return Lesson::query()
            ->where('status', 'published')
            ->whereHas('unit', fn ($query) => $query
                ->where('course_id', $course->id)
                ->where('is_active', true))
            ->with('unit')
            ->orderBy('course_unit_id')
            ->orderBy('position')
            ->orderBy('id')
            ->get();
    }

    private function signalsFor(User $learner): Collection
    {
        return $this->signalCache[$learner->id]
            ??= $this->assessmentAnalytics->masterySignalsForLearner($learner);
    }

    private function recommendations(Collection $chapters, CourseMastery $mastery): array
    {
        $items = [];
        $firstIncomplete = $chapters
            ->flatMap(fn ($chapter) => collect($chapter['lessons']))
            ->first(fn ($lesson) => $lesson['status'] !== 'completed');

        if ($firstIncomplete) {
            $items[] = [
                'type' => 'lesson',
                'title' => $firstIncomplete['status'] === 'in_progress' ? 'Continue your current lesson' : 'Continue learning',
                'message' => 'Complete the next published lesson to improve course progress.',
                'lesson_id' => $firstIncomplete['id'],
            ];
        }

        if ($mastery->assessment_percentage !== null && (float) $mastery->assessment_percentage < 60) {
            $items[] = [
                'type' => 'assessment_review',
                'title' => 'Review assessment results',
                'message' => 'Your assessment score is below 60%. Review incorrect answers before the next attempt.',
            ];
        } elseif ($mastery->assessment_percentage === null) {
            $items[] = [
                'type' => 'assessment',
                'title' => 'Build assessment evidence',
                'message' => 'Complete a published assessment when available to make your mastery score assessment-supported.',
            ];
        }

        if ($mastery->mastery_level === 'mastered' && ! $firstIncomplete) {
            $items[] = [
                'type' => 'next_course',
                'title' => 'Course mastered',
                'message' => 'You have strong progress in this course. Continue with your next assigned course.',
            ];
        }

        return array_slice($items, 0, 2);
    }
}
