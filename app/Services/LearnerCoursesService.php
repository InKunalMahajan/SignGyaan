<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class LearnerCoursesService
{
    public function __construct(private LearnerDashboardService $dashboard)
    {
    }

    public function build(User $user): array
    {
        $data = $this->dashboard->build($user);
        $courses = $data['progressRecords']
            ->map(fn ($progress) => $this->courseCard($progress))
            ->values();

        return [
            'courses' => $courses,
            'inProgressCourses' => $courses->where('status', 'in-progress')->values(),
            'completedCourses' => $courses->where('status', 'completed')->values(),
            'starterCourses' => $data['starterCourses'],
            'courseSummary' => [
                'total' => $courses->count(),
                'in_progress' => $courses->where('status', 'in-progress')->count(),
                'completed' => $courses->where('status', 'completed')->count(),
                'lessons_completed' => $data['completedLessons'],
                'overall_progress' => $data['overallProgress'],
            ],
        ];
    }

    private function courseCard($progress): array
    {
        $completed = $progress->completedLessonsCount();
        $total = (int) $progress->total_lessons;
        $isCompleted = $progress->completed_at !== null;
        $lessonKey = (string) $progress->current_lesson_key;
        $videoProgress = is_array($progress->video_progress) ? $progress->video_progress : [];
        $video = is_array($videoProgress[$lessonKey] ?? null) ? $videoProgress[$lessonKey] : [];

        return [
            'subject' => $progress->subject_name,
            'subject_slug' => $progress->subject_slug,
            'course_title' => $progress->course_title,
            'course_slug' => $progress->course_slug,
            'course_level' => $progress->course_level ?: 'All levels',
            'status' => $isCompleted ? 'completed' : 'in-progress',
            'completed_lessons' => $completed,
            'total_lessons' => $total,
            'progress_percent' => $progress->progressPercent(),
            'current_unit_title' => $progress->current_unit_title,
            'current_lesson_title' => $progress->current_lesson_title,
            'current_lesson_duration' => $progress->current_lesson_duration,
            'video_watched_percent' => isset($video['watched_percent']) ? (int) $video['watched_percent'] : null,
            'last_accessed_at' => $progress->last_accessed_at,
            'completed_at' => $progress->completed_at,
            'course_url' => route('courses.show', [
                'subject' => $progress->subject_slug,
                'course' => $progress->course_slug,
            ]),
            'resume_url' => route('courses.show', [
                'subject' => $progress->subject_slug,
                'course' => $progress->course_slug,
                'lesson' => $lessonKey,
            ]),
        ];
    }
}
