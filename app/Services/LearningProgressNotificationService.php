<?php

namespace App\Services;

use App\Models\LearningProgress;
use App\Models\User;

class LearningProgressNotificationService
{
    public function __construct(private InAppNotificationService $notifications)
    {
    }

    public function saved(
        User $user,
        LearningProgress $progress,
        array $currentEntry,
    ): void {
        $lessonKey = $currentEntry['stable_key'];

        $this->notifications->sendOnce(
            user: $user,
            dedupeKey: 'learning-saved:'.$progress->course_slug.':'.$lessonKey,
            category: 'learning',
            title: 'Course saved for later',
            message: 'Your place in '.$progress->course_title.' is saved. Continue from '.$currentEntry['lesson']->title.' when you are ready.',
            url: route('courses.show', [
                'subject' => $progress->subject_slug,
                'course' => $progress->course_slug,
                'lesson' => $lessonKey,
            ]),
            actionLabel: 'Continue Learning',
            meta: [
                'event' => 'learning_saved',
                'course_slug' => $progress->course_slug,
                'lesson_key' => $lessonKey,
                'lesson_id' => $currentEntry['lesson']->id,
            ],
        );
    }

    public function lessonCompleted(
        User $user,
        LearningProgress $progress,
        array $completedEntry,
        ?array $nextEntry,
    ): void {
        $completedLessonKey = $completedEntry['stable_key'];

        if ($nextEntry) {
            $this->notifications->sendOnce(
                user: $user,
                dedupeKey: 'lesson-completed:'.$progress->course_slug.':'.$completedLessonKey,
                category: 'learning',
                title: 'Next lesson ready',
                message: 'You completed '.$completedEntry['lesson']->title.'. Continue with '.$nextEntry['lesson']->title.'.',
                url: route('courses.show', [
                    'subject' => $progress->subject_slug,
                    'course' => $progress->course_slug,
                    'lesson' => $nextEntry['stable_key'],
                ]),
                actionLabel: 'Open Next Lesson',
                meta: [
                    'event' => 'lesson_completed',
                    'course_slug' => $progress->course_slug,
                    'completed_lesson_key' => $completedLessonKey,
                    'next_lesson_key' => $nextEntry['stable_key'],
                    'progress_percent' => $progress->progressPercent(),
                ],
            );

            return;
        }

        $this->notifications->sendOnce(
            user: $user,
            dedupeKey: 'course-completed:'.$progress->course_slug,
            category: 'milestone',
            title: 'Course completed',
            message: 'You completed '.$progress->course_title.'. You can review the lessons any time.',
            url: route('courses.show', [
                'subject' => $progress->subject_slug,
                'course' => $progress->course_slug,
            ]),
            actionLabel: 'Review Course',
            meta: [
                'event' => 'course_completed',
                'course_slug' => $progress->course_slug,
                'completed_lesson_key' => $completedLessonKey,
                'progress_percent' => 100,
            ],
        );
    }
}
