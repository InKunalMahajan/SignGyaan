<?php

namespace App\Services;

use App\Models\AssessmentAttempt;
use App\Models\LearningActivity;
use App\Models\User;
use Illuminate\Support\Collection;

class LearnerHistoryService
{
    public function build(User $user): array
    {
        $activityItems = $user->learningActivities()
            ->orderByDesc('occurred_at')
            ->get()
            ->map(fn (LearningActivity $activity) => $this->activityItem($activity));

        $assessmentItems = AssessmentAttempt::query()
            ->where('user_id', $user->id)
            ->where('status', 'submitted')
            ->whereNotNull('submitted_at')
            ->whereHas('assessment', fn ($assessmentQuery) => $assessmentQuery
                ->published()
                ->whereHas('practiceResource', fn ($practiceQuery) => $practiceQuery
                    ->published()
                    ->whereHas('lesson', fn ($lessonQuery) => $lessonQuery
                        ->published()
                        ->whereHas('unit', fn ($unitQuery) => $unitQuery
                            ->published()
                            ->whereHas('course', fn ($courseQuery) => $courseQuery
                                ->published()
                                ->whereHas('subject', fn ($subjectQuery) => $subjectQuery->published()))))))
            ->with('assessment.practiceResource.lesson.unit.course.subject')
            ->orderByDesc('submitted_at')
            ->get()
            ->map(fn (AssessmentAttempt $attempt) => $this->assessmentItem($attempt));

        $items = $activityItems
            ->merge($assessmentItems)
            ->sortByDesc(fn (array $item) => $item['occurred_at'])
            ->values();

        $groupedByDate = $items
            ->groupBy(fn (array $item) => $item['occurred_at']?->toDateString())
            ->map(fn (Collection $dayItems) => $dayItems->values());

        return [
            'historyItems' => $items,
            'historyByDate' => $groupedByDate,
            'historySummary' => [
                'total_events' => $items->count(),
                'lesson_events' => $items->whereIn('type', ['lesson_saved', 'lesson_completed', 'video_progress'])->count(),
                'assessment_events' => $items->where('type', 'assessment_completed')->count(),
                'active_days' => $items->pluck('occurred_at')->filter()->map(fn ($date) => $date->toDateString())->unique()->count(),
            ],
        ];
    }

    private function activityItem(LearningActivity $activity): array
    {
        $metadata = is_array($activity->metadata) ? $activity->metadata : [];
        $courseTitle = data_get($metadata, 'course_title');
        $lessonTitle = data_get($metadata, 'lesson_title');

        $url = null;
        if ($activity->subject_slug && $activity->course_slug) {
            $routeParameters = [
                'subject' => $activity->subject_slug,
                'course' => $activity->course_slug,
            ];

            if ($activity->lesson_key) {
                $routeParameters['lesson'] = $activity->lesson_key;
            }

            $url = route('courses.show', $routeParameters);
        }

        return [
            'type' => $activity->activity_type,
            'title' => $activity->title,
            'course_title' => $courseTitle,
            'lesson_title' => $lessonTitle,
            'detail' => match ($activity->activity_type) {
                'lesson_completed' => 'Lesson completed',
                'lesson_saved' => 'Learning place saved',
                'video_progress' => isset($metadata['watched_percent'])
                    ? 'Lesson video '.$metadata['watched_percent'].'% watched'
                    : 'Lesson video progress saved',
                default => 'Learning activity',
            },
            'occurred_at' => $activity->occurred_at,
            'url' => $url,
            'score' => null,
            'passed' => null,
        ];
    }

    private function assessmentItem(AssessmentAttempt $attempt): array
    {
        $assessment = $attempt->assessment;
        $practice = $assessment?->practiceResource;
        $lesson = $practice?->lesson;
        $course = $lesson?->unit?->course;

        return [
            'type' => 'assessment_completed',
            'title' => $practice?->title ?: 'Assessment completed',
            'course_title' => $course?->title,
            'lesson_title' => $lesson?->title,
            'detail' => ($attempt->passed ? 'Passed' : 'Submitted').' · '.number_format((float) $attempt->percentage, 2).'%',
            'occurred_at' => $attempt->submitted_at,
            'url' => route('assessment-attempts.result', [$assessment, $attempt]),
            'score' => (float) $attempt->percentage,
            'passed' => (bool) $attempt->passed,
        ];
    }
}
