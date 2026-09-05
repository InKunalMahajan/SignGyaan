<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'question_type',
        'prompt',
        'explanation',
        'answer_key',
        'points',
        'sort_order',
        'is_required',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'answer_key' => 'array',
            'points' => 'decimal:2',
            'sort_order' => 'integer',
            'is_required' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(AssessmentOption::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
