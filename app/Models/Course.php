<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'created_by',
        'title',
        'slug',
        'level',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function units(): HasMany
    {
        return $this->hasMany(CourseUnit::class)->orderBy('position')->orderBy('id');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class)->orderBy('created_at')->orderBy('id');
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(
            LearningClass::class,
            'class_course_assignments',
            'course_id',
            'learning_class_id'
        )->withPivot('assigned_by', 'assigned_at')->withTimestamps();
    }
}
