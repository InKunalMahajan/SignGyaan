<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LearnerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'avatar_path',
        'education_level',
        'class_grade',
        'institution',
        'preferred_language',
        'communication_mode',
        'captions_enabled',
        'high_contrast',
        'reduced_motion',
        'text_size',
    ];

    protected function casts(): array
    {
        return [
            'captions_enabled' => 'boolean',
            'high_contrast' => 'boolean',
            'reduced_motion' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar_path
            ? Storage::disk('public')->url($this->avatar_path)
            : null;
    }
}
