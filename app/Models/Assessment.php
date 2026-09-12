<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    use HasFactory;

    public const STATUSES = ['draft', 'published', 'archived'];

    protected $fillable = [
        'course_id',
        'created_by',
        'title',
        'instructions',
        'status',
        'total_marks',
        'passing_marks',
        'duration_minutes',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'total_marks' => 'decimal:2',
            'passing_marks' => 'decimal:2',
            'published_at' => 'datetime',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(AssessmentQuestion::class)->orderBy('position')->orderBy('id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(AssessmentAttempt::class);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
