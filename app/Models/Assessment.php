<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'practice_resource_id',
        'passing_percentage',
        'max_attempts',
        'time_limit_minutes',
        'shuffle_questions',
        'shuffle_options',
        'show_feedback',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'passing_percentage' => 'integer',
            'max_attempts' => 'integer',
            'time_limit_minutes' => 'integer',
            'shuffle_questions' => 'boolean',
            'shuffle_options' => 'boolean',
            'show_feedback' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function practiceResource(): BelongsTo
    {
        return $this->belongsTo(PracticeResource::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(AssessmentQuestion::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(AssessmentAttempt::class)
            ->orderByDesc('attempt_number');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
