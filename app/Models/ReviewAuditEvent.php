<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewAuditEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'lesson_id',
        'actor_id',
        'event_type',
        'from_review_status',
        'to_review_status',
        'actor_name',
        'actor_role',
        'lesson_title',
        'course_title',
        'unit_title',
        'review_notes',
        'teacher_response',
        'metadata',
        'occurred_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'occurred_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
