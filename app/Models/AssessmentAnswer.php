<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'response',
        'question_snapshot',
        'awarded_marks',
        'is_correct',
        'requires_review',
        'feedback',
    ];

    protected function casts(): array
    {
        return [
            'response' => 'array',
            'question_snapshot' => 'array',
            'awarded_marks' => 'decimal:2',
            'is_correct' => 'boolean',
            'requires_review' => 'boolean',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(AssessmentAttempt::class, 'attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(AssessmentQuestion::class, 'question_id');
    }
}
