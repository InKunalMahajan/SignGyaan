<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{
    use HasFactory;

    public const STATUSES = ['draft', 'published'];

    protected $fillable = [
        'course_unit_id',
        'title',
        'summary',
        'isl_video_url',
        'notes',
        'estimated_minutes',
        'position',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'estimated_minutes' => 'integer',
            'position' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(CourseUnit::class, 'course_unit_id');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
