<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningProgress extends Model
{
    use HasFactory;

    protected $table = 'learning_progress';

    protected $fillable = [
        'user_id',
        'subject_slug',
        'subject_name',
        'course_slug',
        'course_title',
        'total_lessons',
        'current_lesson_key',
        'completed_lessons',
        'last_accessed_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_lessons' => 'array',
            'last_accessed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function completedLessonsCount(): int
    {
        return count($this->completed_lessons ?? []);
    }

    public function progressPercent(): int
    {
        if ($this->total_lessons < 1) {
            return 0;
        }

        return (int) min(100, round(($this->completedLessonsCount() / $this->total_lessons) * 100));
    }
}
