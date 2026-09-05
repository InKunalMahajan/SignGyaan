<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PracticeResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'title',
        'slug',
        'kind',
        'resource_type',
        'short_description',
        'instructions',
        'content',
        'answer_key',
        'resource_url',
        'media_asset_id',
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

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function mediaAsset(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class);
    }

    public function assessment(): HasOne
    {
        return $this->hasOne(Assessment::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopePractice(Builder $query): Builder
    {
        return $query->where('kind', 'practice');
    }

    public function scopeResource(Builder $query): Builder
    {
        return $query->where('kind', 'resource');
    }
}
