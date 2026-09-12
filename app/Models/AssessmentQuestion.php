<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentQuestion extends Model
{
    use HasFactory;

    public const TYPES = [
        'single_choice',
        'multi_select',
        'true_false',
        'fill_blank',
        'matching',
        'short_answer',
    ];

    protected $fillable = [
        'assessment_id',
        'type',
        'prompt',
        'marks',
        'position',
        'config',
        'answer_key',
        'explanation',
    ];

    protected function casts(): array
    {
        return [
            'marks' => 'decimal:2',
            'config' => 'array',
            'answer_key' => 'array',
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class, 'question_id');
    }

    public function requiresManualReview(): bool
    {
        return $this->type === 'short_answer';
    }
}
