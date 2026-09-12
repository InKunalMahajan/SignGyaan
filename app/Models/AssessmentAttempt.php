<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentAttempt extends Model
{
    use HasFactory;

    public const STATUSES = ['in_progress', 'submitted', 'pending_review', 'completed'];

    protected $fillable = [
        'assessment_id',
        'learner_id',
        'attempt_number',
        'status',
        'earned_marks',
        'total_marks_snapshot',
        'passing_marks_snapshot',
        'percentage',
        'passed',
        'started_at',
        'submitted_at',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'earned_marks' => 'decimal:2',
            'total_marks_snapshot' => 'decimal:2',
            'passing_marks_snapshot' => 'decimal:2',
            'percentage' => 'decimal:2',
            'passed' => 'boolean',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function learner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'learner_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class, 'attempt_id');
    }

    public function isSubmitted(): bool
    {
        return in_array($this->status, ['submitted', 'pending_review', 'completed'], true);
    }
}
