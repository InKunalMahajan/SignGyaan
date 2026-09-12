<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseMastery extends Model
{
    use HasFactory;

    public const LEVELS = [
        'needs_support',
        'developing',
        'good',
        'mastered',
    ];

    protected $fillable = [
        'learner_id',
        'course_id',
        'lessons_completed',
        'lessons_total',
        'lesson_completion_percentage',
        'assessments_completed',
        'assessment_percentage',
        'mastery_score',
        'mastery_level',
        'evidence_status',
        'last_calculated_at',
    ];

    protected function casts(): array
    {
        return [
            'lessons_completed' => 'integer',
            'lessons_total' => 'integer',
            'lesson_completion_percentage' => 'decimal:2',
            'assessments_completed' => 'integer',
            'assessment_percentage' => 'decimal:2',
            'mastery_score' => 'decimal:2',
            'last_calculated_at' => 'datetime',
        ];
    }

    public function learner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'learner_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function levelLabel(): string
    {
        return match ($this->mastery_level) {
            'developing' => 'Developing',
            'good' => 'Good',
            'mastered' => 'Mastered',
            default => 'Needs Support',
        };
    }
}
