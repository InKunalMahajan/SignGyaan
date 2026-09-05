<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonContentBlock extends Model
{
    use HasFactory;

    public const TYPES = [
        'text',
        'key_points',
        'example',
        'image',
        'isl_video',
        'transcript',
        'vocabulary',
        'practice',
        'resource',
    ];

    protected $fillable = [
        'lesson_id',
        'type',
        'title',
        'body',
        'media_asset_id',
        'practice_resource_id',
        'sort_order',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function mediaAsset(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class);
    }

    public function practiceResource(): BelongsTo
    {
        return $this->belongsTo(PracticeResource::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
