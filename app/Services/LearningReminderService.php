<?php

namespace App\Services;

use App\Models\LearningProgress;
use App\Models\User;
use Illuminate\Support\Carbon;

class LearningReminderService
{
    public function __construct(
        private InAppNotificationService $notifications,
        private LearningProgressCatalog $catalog,
    ) {
    }

    public function sendDueReminders(int $inactiveDays = 3): int
    {
        $cutoff = now()->subDays($inactiveDays);
        $sent = 0;

        LearningProgress::query()
            ->whereNull('completed_at')
            ->whereNotNull('last_accessed_at')
            ->where('last_accessed_at', '<=', $cutoff)
            ->with('user')
            ->orderBy('id')
            ->chunkById(100, function ($records) use (&$sent, $inactiveDays): void {
                foreach ($records as $progress) {
                    if ($this->sendForProgress($progress, $inactiveDays)) {
                        $sent++;
                    }
                }
            });

        return $sent;
    }

    public function sendForProgress(LearningProgress $progress, int $inactiveDays = 3): bool
    {
        $user = $progress->user;

        if (! $user || ! $progress->last_accessed_at || $progress->completed_at) {
            return false;
        }

        if ($progress->last_accessed_at->isAfter(now()->subDays($inactiveDays))) {
            return false;
        }

        $state = $this->catalog->resolve($progress->subject_slug, $progress->course_slug);

        if (! $state || $state['entries']->isEmpty()) {
            return false;
        }

        $this->catalog->synchronizeRecord($progress, $state);
        $completed = $progress->completed_lessons ?? [];
        $entry = $state['entries']->first(
            fn (array $item) => $item['stable_key'] === $progress->current_lesson_key
                && ! in_array($item['stable_key'], $completed, true)
        ) ?? $state['entries']->first(
            fn (array $item) => ! in_array($item['stable_key'], $completed, true)
        );

        if (! $entry) {
            return false;
        }

        $period = Carbon::now()->format('o-W');

        return $this->notifications->sendOnce(
            user: $user,
            dedupeKey: 'learning-reminder:'.$progress->id.':'.$period,
            category: 'learning',
            title: 'Ready to continue learning?',
            message: 'Continue '.$progress->course_title.' from '.$entry['lesson']->title.'. Your learning place is saved.',
            url: route('courses.show', [
                'subject' => $progress->subject_slug,
                'course' => $progress->course_slug,
                'lesson' => $entry['stable_key'],
            ]),
            actionLabel: 'Continue Learning',
            meta: [
                'event' => 'learning_reminder',
                'course_slug' => $progress->course_slug,
                'lesson_key' => $entry['stable_key'],
                'inactive_days' => $inactiveDays,
            ],
        );
    }
}
