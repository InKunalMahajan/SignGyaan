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

    protected $fillable = [
        'unit_id',
        'title',
        'slug',
        'short_description',
        'learning_objectives',
        'content',
        'key_points',
        'example_content',
        'isl_video_url',
        'isl_video_title',
        'isl_video_caption',
        'isl_media_asset_id',
        'estimated_duration_minutes',
        'sort_order',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'estimated_duration_minutes' => 'integer',
            'sort_order' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function mediaAsset(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'isl_media_asset_id');
    }

    public function practiceResources(): HasMany
    {
        return $this->hasMany(PracticeResource::class)
            ->orderBy('sort_order')
            ->orderBy('title');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
