<?php

namespace App\Support;

use App\Models\Lesson;
use App\Models\ReviewAuditEvent;
use App\Models\User;

class ReviewAuditLogger
{
    public function record(
        Lesson $lesson,
        User $actor,
        string $eventType,
        ?string $fromStatus,
        ?string $toStatus,
        array $context = []
    ): ReviewAuditEvent {
        $lesson->loadMissing('unit.course');

        return ReviewAuditEvent::create([
            'lesson_id' => $lesson->id,
            'actor_id' => $actor->id,
            'event_type' => $eventType,
            'from_review_status' => $fromStatus,
            'to_review_status' => $toStatus,
            'actor_name' => $actor->name,
            'actor_role' => $actor->role,
            'lesson_title' => $lesson->title,
            'course_title' => $lesson->unit?->course?->title,
            'unit_title' => $lesson->unit?->title,
            'review_notes' => $context['review_notes'] ?? $lesson->review_notes,
            'teacher_response' => $context['teacher_response'] ?? $lesson->teacher_response,
            'metadata' => $context['metadata'] ?? null,
            'occurred_at' => $context['occurred_at'] ?? now(),
            'created_at' => now(),
        ]);
    }
}
