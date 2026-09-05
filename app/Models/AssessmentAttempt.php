<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'user_id',
        'attempt_number',
        'status',
        'score_points',
        'max_points',
        'percentage',
        'passed',
        'started_at',
        'submitted_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'attempt_number' => 'integer',
            'score_points' => 'decimal:2',
            'max_points' => 'decimal:2',
            'percentage' => 'decimal:2',
            'passed' => 'boolean',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class);
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted' && $this->submitted_at !== null;
    }
}
