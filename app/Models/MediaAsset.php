<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MediaAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'uploaded_by',
        'title',
        'media_type',
        'source',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'external_url',
        'alt_text',
        'caption',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function lessonVideoUses(): HasMany
    {
        return $this->hasMany(Lesson::class, 'isl_media_asset_id');
    }

    public function practiceResourceUses(): HasMany
    {
        return $this->hasMany(PracticeResource::class, 'media_asset_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function publicUrl(): ?string
    {
        if ($this->source === 'external') {
            return $this->external_url;
        }

        return $this->file_path
            ? url('storage/'.ltrim($this->file_path, '/'))
            : null;
    }

    public function formattedFileSize(): ?string
    {
        if (! $this->file_size) {
            return null;
        }

        $bytes = (float) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $index = 0;

        while ($bytes >= 1024 && $index < count($units) - 1) {
            $bytes /= 1024;
            $index++;
        }

        return number_format($bytes, $index === 0 ? 0 : 1).' '.$units[$index];
    }
}
