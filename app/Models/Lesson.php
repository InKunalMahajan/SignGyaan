<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    public const STATUSES = ['draft', 'published'];
    public const REVIEW_STATUSES = ['pending', 'approved', 'changes_requested'];

    protected $fillable = [
        'course_unit_id',
        'title',
        'summary',
        'isl_video_url',
        'notes',
        'estimated_minutes',
        'position',
        'status',
        'review_status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'review_submitted_at',
        'teacher_response',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'estimated_minutes' => 'integer',
            'position' => 'integer',
            'published_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'review_submitted_at' => 'datetime',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(CourseUnit::class, 'course_unit_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeLearnerVisible(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where('review_status', 'approved');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isApproved(): bool
    {
        return $this->review_status === 'approved';
    }

    public function isLearnerVisible(): bool
    {
        return $this->isPublished() && $this->isApproved();
    }

    public function needsReview(): bool
    {
        return $this->review_status === 'pending';
    }

    public function changesRequested(): bool
    {
        return $this->review_status === 'changes_requested';
    }
}
